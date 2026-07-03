<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OcrService;
use Illuminate\Support\Facades\Storage;

class ExtractTextFromImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocr:extract 
                            {path : Chemin vers l\'image ou le PDF}
                            {--preprocess : Utiliser le pré-traitement de l\'image}
                            {--lang= : Langue pour l\'OCR (fra, eng, etc.)}
                            {--output= : Fichier de sortie pour sauvegarder le texte}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extrait le texte d\'une image ou d\'un PDF en utilisant OCR';

    protected OcrService $ocrService;

    public function __construct(OcrService $ocrService)
    {
        parent::__construct();
        $this->ocrService = $ocrService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->argument('path');
        $preprocess = $this->option('preprocess');
        $language = $this->option('lang');
        $outputFile = $this->option('output');

        // Vérifier si Tesseract est disponible
        if (!$this->ocrService->isAvailable()) {
            $this->error('Tesseract OCR n\'est pas disponible. Veuillez installer Tesseract.');
            $this->info('Téléchargement: https://github.com/UB-Mannheim/tesseract/wiki');
            return Command::FAILURE;
        }

        $this->info("Traitement de: {$path}");

        try {
            // Définir la langue si fournie
            if ($language) {
                $this->ocrService->setLanguage($language);
            }

            // Déterminer le type de fichier
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            
            if ($extension === 'pdf') {
                $this->info('Extraction du texte depuis un PDF...');
                $text = $this->ocrService->extractTextFromPdf($path);
            } else {
                $this->info('Extraction du texte depuis une image...');
                if ($preprocess) {
                    $text = $this->ocrService->extractTextWithPreprocessing($path);
                } else {
                    $text = $this->ocrService->extractText($path);
                }
            }

            // Afficher le texte extrait
            $this->newLine();
            $this->info('Texte extrait:');
            $this->line('─────────────────────────────────────');
            $this->line($text);
            $this->line('─────────────────────────────────────');
            $this->newLine();

            // Sauvegarder dans un fichier si demandé
            if ($outputFile) {
                file_put_contents($outputFile, $text);
                $this->info("Texte sauvegardé dans: {$outputFile}");
            }

            $this->info('✓ Extraction terminée avec succès!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Erreur: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

