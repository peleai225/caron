<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tesseract OCR Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour la reconnaissance optique de caractères (OCR)
    | utilisant Tesseract OCR.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Chemin vers l'exécutable Tesseract
    |--------------------------------------------------------------------------
    |
    | Si null, le système tentera de détecter automatiquement Tesseract.
    | Sur Windows, vous pouvez spécifier le chemin complet :
    | 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe'
    |
    */
    'tesseract_path' => env('TESSERACT_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | Langue par défaut
    |--------------------------------------------------------------------------
    |
    | Langue(s) à utiliser pour l'OCR. Vous pouvez combiner plusieurs langues
    | avec un '+' (ex: 'fra+eng' pour français et anglais).
    |
    | Langues disponibles : fra, eng, spa, deu, etc.
    | Voir : https://github.com/tesseract-ocr/tessdata
    |
    */
    'default_language' => env('OCR_LANGUAGE', 'fra+eng'),

    /*
    |--------------------------------------------------------------------------
    | Pré-traitement par défaut
    |--------------------------------------------------------------------------
    |
    | Active le pré-traitement de l'image par défaut (améliore la qualité
    | de l'OCR mais prend plus de temps).
    |
    */
    'default_preprocess' => env('OCR_PREPROCESS', true),

    /*
    |--------------------------------------------------------------------------
    | Taille maximale des fichiers
    |--------------------------------------------------------------------------
    |
    | Taille maximale des fichiers uploadés pour l'OCR (en KB).
    |
    */
    'max_file_size' => env('OCR_MAX_FILE_SIZE', 10240), // 10MB

    /*
    |--------------------------------------------------------------------------
    | Formats de fichiers supportés
    |--------------------------------------------------------------------------
    |
    | Formats de fichiers supportés pour l'OCR.
    |
    */
    'supported_formats' => [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff'],
        'document' => ['pdf'],
    ],
];

