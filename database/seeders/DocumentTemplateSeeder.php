<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;
use App\Services\DocumentService;
use Illuminate\Support\Str;

class DocumentTemplateSeeder extends Seeder
{
    protected $documentService;

    public function __construct()
    {
        $this->documentService = app(DocumentService::class);
    }

    public function run(): void
    {
        $templates = [
            // CONTRATS DE LOCATION
            [
                'name' => 'Bail (Contrat de location)',
                'category' => 'contract',
                'file' => 'Bail (Contrat de location).docx',
            ],
            [
                'name' => 'Contrat de sous-location',
                'category' => 'contract',
                'file' => 'Contrat de sous-location.docx',
            ],
            [
                'name' => 'Contrat de sous-location avec consentement du propriétaire',
                'category' => 'contract',
                'file' => 'Contrat de sous-location avec consentement du propriétaire.docx',
            ],
            [
                'name' => 'Entente de principe sur location',
                'category' => 'contract',
                'file' => 'Entente de principe sur location.docx',
            ],

            // RÉSILIATIONS
            [
                'name' => 'Accord de résiliation de bail commercial',
                'category' => 'termination',
                'file' => 'Accord de résiliation de bail commercial.docx',
            ],
            [
                'name' => 'Accord de résilisation de bail',
                'category' => 'termination',
                'file' => 'Accord de résilisation de bail.docx',
            ],
            [
                'name' => 'Accord de résilisation d\'un contrat de vente immobilier',
                'category' => 'termination',
                'file' => 'Accord de résilisation d_un contrat de vente immobilier.docx',
            ],
            [
                'name' => 'Contrat de résiliation de bail commercial',
                'category' => 'termination',
                'file' => 'Contrat de résiliation de bail commercial.docx',
            ],

            // AVENANTS
            [
                'name' => 'Avenant au bail commercial',
                'category' => 'amendment',
                'file' => 'Avenant au bail commercial.docx',
            ],
            [
                'name' => 'Avenant au contrat de bail commercial (Au bénéfice des locataires)',
                'category' => 'amendment',
                'file' => 'Avenant au contrat de bail commercial_Au bénéfice des locataires.docx',
            ],
            [
                'name' => 'Avenant au contrat de bail',
                'category' => 'amendment',
                'file' => 'Avenant au contrat de bail.docx',
            ],
            [
                'name' => 'Avenant au contrat de location',
                'category' => 'amendment',
                'file' => 'Avenant au contrat de location.docx',
            ],
            [
                'name' => 'Complément au contrat de location',
                'category' => 'amendment',
                'file' => 'Complément au contrat de location.docx',
            ],
            [
                'name' => 'Prorogation de contrat de bail',
                'category' => 'amendment',
                'file' => 'Prorogation de contrat de bail.docx',
            ],

            // NOTIFICATIONS
            [
                'name' => 'Notification de frais de retard exigibles',
                'category' => 'notification',
                'file' => 'Notification de frais de retard exigibles.docx',
            ],
            [
                'name' => 'Notification de violation de contrat de location',
                'category' => 'notification',
                'file' => 'Notification de violation de contrat de location.docx',
            ],
            [
                'name' => 'Notification d\'exercice d\'option de renouvellement de bail',
                'category' => 'notification',
                'file' => 'Notification d_exercice d_option de renouvellement de bail.docx',
            ],
            [
                'name' => 'Demande de quitter les lieux avant le déclenchement de procédures judiciaires',
                'category' => 'notification',
                'file' => 'Demande de quitter les lieux avant le déclenchement de procédures judiciaires.docx',
            ],

            // OPTIONS
            [
                'name' => 'Option d\'achat de propriété immobilière',
                'category' => 'option',
                'file' => 'Option d_achat de propriété immobilière.docx',
            ],
            [
                'name' => 'Option d\'extension d\'espace loué',
                'category' => 'option',
                'file' => 'Option d_extension d_espace loué.docx',
            ],
            [
                'name' => 'Option sur contrat de bail',
                'category' => 'option',
                'file' => 'Option sur contrat de bail.docx',
            ],

            // VENTES
            [
                'name' => 'Contrat de vente de propriété commerciale',
                'category' => 'sale',
                'file' => 'Contrat de vente de propriété commerciale.docx',
            ],
            [
                'name' => 'Promesse de vente',
                'category' => 'sale',
                'file' => 'Promesse de vente.docx',
            ],

            // AUTRES DOCUMENTS
            [
                'name' => 'Accord d\'autorisation de sous-location',
                'category' => 'other',
                'file' => 'Accord d_autorisation de sous-location.docx',
            ],
            [
                'name' => 'Billet à ordre avec hypothèque',
                'category' => 'legal',
                'file' => 'Billet à ordre avec hypothèque.docx',
            ],
            [
                'name' => 'Cautionnement de contrat de location',
                'category' => 'legal',
                'file' => 'Cautionnement de contrat de location.docx',
            ],
            [
                'name' => 'Cession de contrat de location',
                'category' => 'other',
                'file' => 'Cession de contrat de location.docx',
            ],
            [
                'name' => 'Consentement à la cession de bail de location',
                'category' => 'other',
                'file' => 'Consentement à la cession de bail de location.docx',
            ],
            [
                'name' => 'Contrat de gestion immobilière',
                'category' => 'management',
                'file' => 'Contrat de gestion immobilière.docx',
            ],
            [
                'name' => 'Contrat d\'embauche d\'agent immobilier',
                'category' => 'management',
                'file' => 'Contrat d_embauche d_agent immmobilier.docx',
            ],
            [
                'name' => 'Contrat d\'hypothèque',
                'category' => 'legal',
                'file' => 'Contrat d_hypothèque.docx',
            ],
            [
                'name' => 'Demande au sous-locataire de contracter une police d\'assurance',
                'category' => 'other',
                'file' => 'Demande au sous-locataire de contracter une police d_assurance.docx',
            ],
            [
                'name' => 'Demande de modification de lotissement et de zonage',
                'category' => 'legal',
                'file' => 'Demande de modification de lotissement et de zonage.docx',
            ],
            [
                'name' => 'Fiche d\'analyse des conditions de location',
                'category' => 'other',
                'file' => 'Fiche d_analyse des conditions de location.docx',
            ],
            [
                'name' => 'Liste de vérification des clauses du contrat immobilier',
                'category' => 'other',
                'file' => 'Liste de vérification des clauses du contrat immobilier.docx',
            ],
            [
                'name' => 'Rapport d\'inspection d\'une propriété par un acheteur',
                'category' => 'other',
                'file' => 'Rapport d_inspection d_une propriété par un acheteur.docx',
            ],
            [
                'name' => 'Reçu de dépôt de caution de location',
                'category' => 'receipt',
                'file' => 'Reçu de dépôt de caution de location.docx',
            ],
        ];

        $basePath = base_path('fwd');

        foreach ($templates as $templateData) {
            $filePath = $basePath . '/' . $templateData['file'];
            
            if (!file_exists($filePath)) {
                $this->command->warn("Fichier non trouvé: {$filePath}");
                continue;
            }

            // Vérifier si le template existe déjà
            $slug = Str::slug($templateData['name']);
            $existing = DocumentTemplate::where('slug', $slug)->where('is_system', true)->first();
            
            if ($existing) {
                $this->command->info("Template déjà existant: {$templateData['name']}");
                continue;
            }

            try {
                // Copier le fichier dans storage
                $filename = basename($filePath);
                $storagePath = 'documents/templates/' . $filename;
                $fullStoragePath = storage_path('app/public/' . $storagePath);
                $directory = dirname($fullStoragePath);
                
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                copy($filePath, $fullStoragePath);
                
                // Essayer d'extraire les variables si ZipArchive est disponible
                $variables = [];
                if (class_exists('ZipArchive')) {
                    try {
                        $text = $this->documentService->extractTextFromDocx($fullStoragePath);
                        $variables = $this->documentService->detectVariables($text);
                    } catch (\Exception $e) {
                        // Si l'extraction échoue, on continue sans variables
                        $this->command->warn("  ⚠ Variables non extraites pour {$templateData['name']}");
                    }
                } else {
                    $this->command->warn("  ⚠ Extension ZipArchive non disponible - variables non extraites");
                }
                
                // Créer le template
                DocumentTemplate::create([
                    'name' => $templateData['name'],
                    'slug' => Str::slug($templateData['name']),
                    'category' => $templateData['category'],
                    'country' => 'CI',
                    'description' => null,
                    'file_path' => $storagePath,
                    'variables' => $variables,
                    'is_system' => true,
                    'is_active' => true,
                ]);

                $this->command->info("✓ Importé: {$templateData['name']}");
            } catch (\Exception $e) {
                $this->command->error("✗ Erreur pour {$templateData['name']}: " . $e->getMessage());
            }
        }
    }
}

