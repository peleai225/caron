<?php

namespace App\Http\Controllers;

use App\Services\OcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OcrController extends Controller
{
    protected OcrService $ocrService;

    public function __construct(OcrService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    /**
     * Affiche la page d'upload pour OCR
     */
    public function index()
    {
        $isAvailable = $this->ocrService->isAvailable();
        
        return view('ocr.index', compact('isAvailable'));
    }

    /**
     * Traite un fichier uploadé avec OCR
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'preprocess' => 'boolean',
            'language' => 'nullable|string|in:fra,eng,fra+eng',
        ]);

        // Vérifier si Tesseract est disponible
        if (!$this->ocrService->isAvailable()) {
            return back()->withErrors([
                'error' => 'Tesseract OCR n\'est pas disponible. Veuillez installer Tesseract sur votre système.'
            ]);
        }

        try {
            // Sauvegarder le fichier
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = 'ocr_' . Str::random(40) . '.' . $extension;
            $path = $file->storeAs('ocr', $filename, 'public');

            $fullPath = storage_path('app/public/' . $path);

            // Définir la langue si fournie
            if ($request->filled('language')) {
                $this->ocrService->setLanguage($request->language);
            }

            // Extraire le texte
            $text = '';
            if ($extension === 'pdf') {
                $text = $this->ocrService->extractTextFromPdf($fullPath);
            } else {
                if ($request->boolean('preprocess')) {
                    $text = $this->ocrService->extractTextWithPreprocessing($fullPath);
                } else {
                    $text = $this->ocrService->extractText($fullPath);
                }
            }

            // Sauvegarder le texte extrait
            $textPath = 'ocr/results/' . Str::random(40) . '.txt';
            Storage::disk('public')->put($textPath, $text);

            return view('ocr.result', [
                'text' => $text,
                'originalFile' => $file->getClientOriginalName(),
                'textPath' => $textPath,
            ]);

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Erreur lors du traitement OCR: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Télécharge le texte extrait
     */
    public function download(string $path)
    {
        $path = basename($path);
        $fullPath = storage_path('app/public/ocr/' . $path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->download($fullPath, 'texte_extrait.txt');
    }
}

