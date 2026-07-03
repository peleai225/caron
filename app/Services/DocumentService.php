<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Models\Agency;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\OcrService;

class DocumentService
{
    protected OcrService $ocrService;

    public function __construct(OcrService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    /**
     * Extrait le texte d'un fichier .docx
     */
    public function extractTextFromDocx(string $filePath): string
    {
        try {
            $phpWord = IOFactory::load($filePath);
            $text = '';
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            
            return trim($text);
        } catch (\Exception $e) {
            \Log::error('Erreur extraction texte DOCX: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Détecte les variables dans un texte (format {{VARIABLE}})
     */
    public function detectVariables(string $text): array
    {
        preg_match_all('/\{\{([A-Z_]+)\}\}/', $text, $matches);
        return array_unique($matches[1] ?? []);
    }

    /**
     * Génère un document .docx à partir d'un template
     */
    public function generateDocx(DocumentTemplate $template, array $variables, ?string $outputPath = null): string
    {
        $sourcePath = $template->getFullPathAttribute();
        
        if (!file_exists($sourcePath)) {
            throw new \Exception("Le fichier template n'existe pas: {$sourcePath}");
        }

        $templateProcessor = new TemplateProcessor($sourcePath);
        
        // Remplacer les variables
        foreach ($variables as $key => $value) {
            // Supprimer les accolades si présentes
            $key = str_replace(['{{', '}}'], '', $key);
            try {
                $templateProcessor->setValue($key, $value ?? '');
            } catch (\Exception $e) {
                \Log::warning("Variable non trouvée dans template: {$key}");
            }
        }

        // Générer le chemin de sortie
        if (!$outputPath) {
            $filename = Str::slug($template->name) . '_' . now()->format('Y-m-d_His') . '.docx';
            $outputPath = 'documents/generated/' . $filename;
        }

        $fullOutputPath = storage_path('app/public/' . $outputPath);
        
        // Créer le dossier si nécessaire
        $directory = dirname($fullOutputPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $templateProcessor->saveAs($fullOutputPath);

        return $outputPath;
    }

    /**
     * Génère un PDF à partir d'un document .docx
     */
    public function generatePdfFromDocx(string $docxPath, ?string $outputPath = null): string
    {
        // Convertir .docx en HTML puis en PDF
        // Note: Cette méthode nécessite LibreOffice ou une alternative
        // Pour l'instant, on va utiliser une approche différente
        
        // Alternative: Extraire le texte et créer un PDF simple
        $text = $this->extractTextFromDocx(storage_path('app/public/' . $docxPath));
        
        if (!$outputPath) {
            $filename = pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';
            $outputPath = 'documents/generated/' . $filename;
        }

        $pdf = Pdf::loadHTML($this->formatTextForPdf($text));
        $fullOutputPath = storage_path('app/public/' . $outputPath);
        
        $directory = dirname($fullOutputPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->save($fullOutputPath);

        return $outputPath;
    }

    /**
     * Génère un PDF directement depuis un template avec variables
     */
    public function generatePdf(DocumentTemplate $template, array $variables, ?string $outputPath = null): string
    {
        // Générer d'abord le .docx
        $docxPath = $this->generateDocx($template, $variables);
        
        // Convertir en PDF
        return $this->generatePdfFromDocx($docxPath, $outputPath);
    }

    /**
     * Formate le texte pour le PDF
     */
    private function formatTextForPdf(string $text): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 40px; }
        h1 { color: #333; }
        p { margin-bottom: 10px; }
    </style>
</head>
<body>
    ' . nl2br(e($text)) . '
</body>
</html>';
        
        return $html;
    }

    /**
     * Prépare les variables standard pour un contrat
     */
    public function prepareContractVariables($contract, ?Agency $agency = null): array
    {
        $agency = $agency ?? $contract->agency ?? Agency::first();
        
        $variables = [
            'CONTRACT_NUMBER' => $contract->contract_number ?? '',
            'TENANT_NAME' => $contract->tenant->first_name . ' ' . $contract->tenant->last_name ?? '',
            'TENANT_FIRST_NAME' => $contract->tenant->first_name ?? '',
            'TENANT_LAST_NAME' => $contract->tenant->last_name ?? '',
            'TENANT_PHONE' => $contract->tenant->phone ?? '',
            'TENANT_EMAIL' => $contract->tenant->email ?? '',
            'TENANT_ADDRESS' => $contract->tenant->address ?? '',
            'OWNER_NAME' => $contract->owner ? ($contract->owner->first_name . ' ' . $contract->owner->last_name) : '',
            'OWNER_PHONE' => $contract->owner->phone ?? '',
            'OWNER_EMAIL' => $contract->owner->email ?? '',
            'PROPERTY_ADDRESS' => $contract->property->address ?? '',
            'PROPERTY_CITY' => $contract->property->city ?? '',
            'PROPERTY_TYPE' => $contract->property->type ?? '',
            'RENT_AMOUNT' => number_format($contract->rent_amount ?? 0, 0, ',', ' ') . ' FCFA',
            'DEPOSIT' => number_format($contract->deposit ?? 0, 0, ',', ' ') . ' FCFA',
            'START_DATE' => $contract->start_date ? $contract->start_date->format('d/m/Y') : '',
            'END_DATE' => $contract->end_date ? $contract->end_date->format('d/m/Y') : '',
            'PAYMENT_DAY' => $contract->payment_day ?? '',
            'PAYMENT_FREQUENCY' => $contract->payment_frequency ?? '',
            'AGENCY_NAME' => $agency->name ?? '',
            'AGENCY_ADDRESS' => $agency->address ?? '',
            'AGENCY_PHONE' => $agency->phone ?? '',
            'AGENCY_EMAIL' => $agency->email ?? '',
            'TODAY_DATE' => now()->format('d/m/Y'),
            'CURRENT_YEAR' => now()->year,
        ];

        return $variables;
    }

    /**
     * Prépare les variables pour une notification
     */
    public function prepareNotificationVariables($contract, string $type = 'default'): array
    {
        $variables = $this->prepareContractVariables($contract);
        
        // Variables spécifiques aux notifications
        $variables['NOTIFICATION_TYPE'] = $type;
        $variables['NOTIFICATION_DATE'] = now()->format('d/m/Y');
        
        return $variables;
    }

    /**
     * Extrait le texte d'une image avec OCR
     */
    public function extractTextFromImage(string $imagePath, bool $usePreprocessing = true): string
    {
        if ($usePreprocessing) {
            return $this->ocrService->extractTextWithPreprocessing($imagePath);
        }
        
        return $this->ocrService->extractText($imagePath);
    }

    /**
     * Extrait le texte d'un PDF avec OCR
     */
    public function extractTextFromPdfWithOcr(string $pdfPath): string
    {
        return $this->ocrService->extractTextFromPdf($pdfPath);
    }

    /**
     * Importe un fichier .docx et crée un template
     */
    public function importTemplate(string $filePath, array $data): DocumentTemplate
    {
        // Copier le fichier dans storage
        $filename = basename($filePath);
        $storagePath = 'documents/templates/' . $filename;
        
        $fullStoragePath = storage_path('app/public/' . $storagePath);
        $directory = dirname($fullStoragePath);
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        copy($filePath, $fullStoragePath);
        
        // Extraire le texte et détecter les variables
        $text = $this->extractTextFromDocx($fullStoragePath);
        $variables = $this->detectVariables($text);
        
        // Créer le template
        return DocumentTemplate::create([
            'agency_id' => $data['agency_id'] ?? null,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'category' => $data['category'] ?? 'other',
            'country' => $data['country'] ?? 'CI',
            'description' => $data['description'] ?? null,
            'file_path' => $storagePath,
            'variables' => $variables,
            'is_system' => $data['is_system'] ?? false,
            'is_default' => $data['is_default'] ?? false,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }
}

