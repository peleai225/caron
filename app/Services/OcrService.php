<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OcrService
{
    /**
     * Chemin vers l'exécutable Tesseract (null = auto-détection)
     */
    protected ?string $tesseractPath = null;

    /**
     * Langue pour l'OCR (fra = français, eng = anglais)
     */
    protected string $language = 'fra+eng';

    public function __construct()
    {
        // Détecter automatiquement le chemin Tesseract sur Windows
        if (PHP_OS_FAMILY === 'Windows') {
            // Chemins communs pour Tesseract sur Windows
            $possiblePaths = [
                'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
                'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
                'C:\\laragon\\bin\\tesseract\\tesseract.exe',
            ];

            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $this->tesseractPath = $path;
                    break;
                }
            }
        }
    }

    /**
     * Extrait le texte d'une image
     */
    public function extractText(string $imagePath, ?string $language = null): string
    {
        try {
            $fullPath = $this->getFullPath($imagePath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception("Le fichier image n'existe pas: {$fullPath}");
            }

            $ocr = new TesseractOCR($fullPath);
            
            // Configurer la langue
            if ($language) {
                $ocr->lang($language);
            } else {
                $ocr->lang($this->language);
            }

            // Configurer le chemin Tesseract si nécessaire
            if ($this->tesseractPath) {
                $ocr->executable($this->tesseractPath);
            }

            // Améliorer la qualité de l'OCR
            $ocr->psm(6); // Assume a single uniform block of text
            $ocr->oem(3); // Default, based on what is available

            $text = $ocr->run();

            return trim($text);
        } catch (\Exception $e) {
            Log::error('Erreur OCR: ' . $e->getMessage(), [
                'image_path' => $imagePath,
                'exception' => $e,
            ]);
            
            throw new \Exception("Erreur lors de l'extraction OCR: " . $e->getMessage());
        }
    }

    /**
     * Extrait le texte d'une image avec pré-traitement
     */
    public function extractTextWithPreprocessing(string $imagePath, ?string $language = null): string
    {
        try {
            $fullPath = $this->getFullPath($imagePath);
            
            // Pré-traiter l'image pour améliorer l'OCR
            $processedPath = $this->preprocessImage($fullPath);
            
            try {
                $text = $this->extractText($processedPath, $language);
            } finally {
                // Supprimer l'image temporaire
                if ($processedPath !== $fullPath && file_exists($processedPath)) {
                    unlink($processedPath);
                }
            }

            return $text;
        } catch (\Exception $e) {
            Log::error('Erreur OCR avec pré-traitement: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Pré-traite une image pour améliorer l'OCR
     */
    protected function preprocessImage(string $imagePath): string
    {
        try {
            // Vérifier si GD est disponible
            if (!extension_loaded('gd')) {
                Log::warning('Extension GD non disponible, utilisation de l\'image originale');
                return $imagePath;
            }

            // Charger l'image avec GD
            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) {
                return $imagePath;
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $type = $imageInfo[2];

            // Créer l'image selon le type
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($imagePath);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($imagePath);
                    break;
                case IMAGETYPE_GIF:
                    $source = imagecreatefromgif($imagePath);
                    break;
                default:
                    return $imagePath;
            }

            // Redimensionner si trop grande
            $maxSize = 2000;
            if ($width > $maxSize || $height > $maxSize) {
                $ratio = min($maxSize / $width, $maxSize / $height);
                $newWidth = (int)($width * $ratio);
                $newHeight = (int)($height * $ratio);
                
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($source);
                $source = $resized;
                $width = $newWidth;
                $height = $newHeight;
            }

            // Convertir en niveaux de gris et améliorer le contraste
            $processed = imagecreatetruecolor($width, $height);
            
            for ($x = 0; $x < $width; $x++) {
                for ($y = 0; $y < $height; $y++) {
                    $rgb = imagecolorat($source, $x, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    
                    // Convertir en niveaux de gris
                    $gray = (int)(0.299 * $r + 0.587 * $g + 0.114 * $b);
                    
                    // Augmenter le contraste
                    $gray = min(255, max(0, ($gray - 128) * 1.5 + 128));
                    
                    $color = imagecolorallocate($processed, $gray, $gray, $gray);
                    imagesetpixel($processed, $x, $y, $color);
                }
            }

            // Sauvegarder l'image traitée
            $processedPath = storage_path('app/temp/ocr_' . uniqid() . '.png');
            $directory = dirname($processedPath);
            
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            imagepng($processed, $processedPath, 9);
            imagedestroy($source);
            imagedestroy($processed);

            return $processedPath;
        } catch (\Exception $e) {
            Log::warning('Impossible de pré-traiter l\'image, utilisation de l\'original: ' . $e->getMessage());
            return $imagePath;
        }
    }

    /**
     * Extrait le texte d'un PDF (première page)
     */
    public function extractTextFromPdf(string $pdfPath): string
    {
        try {
            $fullPath = $this->getFullPath($pdfPath);
            
            // Convertir la première page du PDF en image
            $imagePath = $this->convertPdfToImage($fullPath);
            
            try {
                $text = $this->extractTextWithPreprocessing($imagePath);
            } finally {
                // Supprimer l'image temporaire
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            return $text;
        } catch (\Exception $e) {
            Log::error('Erreur OCR PDF: ' . $e->getMessage());
            throw new \Exception("Erreur lors de l'extraction OCR du PDF: " . $e->getMessage());
        }
    }

    /**
     * Convertit la première page d'un PDF en image
     */
    protected function convertPdfToImage(string $pdfPath): string
    {
        // Note: Cette fonction nécessite Imagick ou une autre bibliothèque
        // Pour l'instant, on retourne une erreur si Imagick n'est pas disponible
        if (!extension_loaded('imagick')) {
            throw new \Exception("L'extension Imagick est requise pour traiter les PDF. Installez php-imagick.");
        }

        try {
            $imagick = new \Imagick();
            $imagick->setResolution(300, 300); // Haute résolution pour meilleure qualité OCR
            $imagick->readImage($pdfPath . '[0]'); // Première page seulement
            $imagick->setImageFormat('png');

            $imagePath = storage_path('app/temp/pdf_' . uniqid() . '.png');
            $directory = dirname($imagePath);
            
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $imagick->writeImage($imagePath);
            $imagick->clear();
            $imagick->destroy();

            return $imagePath;
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la conversion PDF en image: " . $e->getMessage());
        }
    }

    /**
     * Vérifie si Tesseract est disponible
     */
    public function isAvailable(): bool
    {
        try {
            $ocr = new TesseractOCR();
            
            if ($this->tesseractPath) {
                $ocr->executable($this->tesseractPath);
            }

            // Tester avec une image simple
            $testImage = $this->createTestImage();
            
            try {
                $ocr->image($testImage);
                $ocr->lang('eng');
                $ocr->run();
                return true;
            } finally {
                if (file_exists($testImage)) {
                    unlink($testImage);
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Crée une image de test simple
     */
    protected function createTestImage(): string
    {
        $imagePath = storage_path('app/temp/test_ocr_' . uniqid() . '.png');
        $directory = dirname($imagePath);
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Créer une image simple avec du texte
        $image = imagecreate(200, 50);
        $background = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagestring($image, 5, 10, 15, 'TEST OCR', $textColor);
        imagepng($image, $imagePath);
        imagedestroy($image);

        return $imagePath;
    }

    /**
     * Obtient le chemin complet d'un fichier
     */
    protected function getFullPath(string $path): string
    {
        // Si c'est déjà un chemin absolu
        if (file_exists($path)) {
            return $path;
        }

        // Si c'est un chemin relatif depuis storage/app/public
        $publicPath = storage_path('app/public/' . $path);
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        // Si c'est un chemin relatif depuis storage/app
        $storagePath = storage_path('app/' . $path);
        if (file_exists($storagePath)) {
            return $storagePath;
        }

        return $path;
    }

    /**
     * Définit le chemin vers Tesseract
     */
    public function setTesseractPath(string $path): self
    {
        $this->tesseractPath = $path;
        return $this;
    }

    /**
     * Définit la langue pour l'OCR
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }
}

