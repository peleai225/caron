# 📄 Guide d'Installation OCR (Tesseract)

Ce guide explique comment installer et configurer Tesseract OCR pour utiliser la fonctionnalité de reconnaissance optique de caractères dans l'application.

## 🎯 Qu'est-ce que Tesseract OCR ?

Tesseract OCR est un moteur de reconnaissance optique de caractères open-source développé par Google. Il permet d'extraire du texte à partir d'images et de documents PDF scannés.

## 📥 Installation

### Windows (Laragon)

1. **Télécharger Tesseract**
   - Allez sur : https://github.com/UB-Mannheim/tesseract/wiki
   - Téléchargez la dernière version Windows (ex: `tesseract-ocr-w64-setup-5.x.x.exe`)

2. **Installer Tesseract**
   - Exécutez le fichier d'installation
   - **Important** : Notez le chemin d'installation (généralement `C:\Program Files\Tesseract-OCR\`)
   - Cochez "Add to PATH" si disponible

3. **Télécharger les données de langue**
   - Pendant l'installation, sélectionnez les langues nécessaires :
     - **fra** (Français) - **Recommandé**
     - **eng** (Anglais) - **Recommandé**
   - Ou téléchargez-les manuellement depuis : https://github.com/tesseract-ocr/tessdata

4. **Configurer dans Laravel (optionnel)**
   - Ajoutez dans votre `.env` :
   ```env
   TESSERACT_PATH=C:\Program Files\Tesseract-OCR\tesseract.exe
   OCR_LANGUAGE=fra+eng
   ```

### Linux (Ubuntu/Debian)

```bash
sudo apt-get update
sudo apt-get install tesseract-ocr
sudo apt-get install tesseract-ocr-fra  # Français
sudo apt-get install tesseract-ocr-eng  # Anglais
```

### macOS

```bash
brew install tesseract
brew install tesseract-lang  # Pour toutes les langues
```

## ✅ Vérification de l'installation

### Via la ligne de commande

```bash
tesseract --version
```

### Via Laravel

```bash
php artisan tinker
```

```php
$ocrService = app(\App\Services\OcrService::class);
$ocrService->isAvailable(); // Devrait retourner true
```

## 🚀 Utilisation

### Interface Web

1. Accédez à `/ocr` dans l'application
2. Uploadez une image (JPG, PNG) ou un PDF
3. Cliquez sur "Extraire le texte"
4. Le texte extrait s'affichera et pourra être téléchargé

### Commande Artisan

```bash
# Extraction simple
php artisan ocr:extract path/to/image.jpg

# Avec pré-traitement
php artisan ocr:extract path/to/image.jpg --preprocess

# Spécifier la langue
php artisan ocr:extract path/to/image.jpg --lang=fra

# Sauvegarder dans un fichier
php artisan ocr:extract path/to/image.jpg --output=resultat.txt
```

### Dans le code PHP

```php
use App\Services\OcrService;

$ocrService = app(OcrService::class);

// Extraction simple
$text = $ocrService->extractText('path/to/image.jpg');

// Avec pré-traitement (améliore la qualité)
$text = $ocrService->extractTextWithPreprocessing('path/to/image.jpg');

// Depuis un PDF
$text = $ocrService->extractTextFromPdf('path/to/document.pdf');
```

## 🔧 Dépannage

### Erreur : "Tesseract OCR n'est pas disponible"

1. Vérifiez que Tesseract est installé :
   ```bash
   tesseract --version
   ```

2. Sur Windows, vérifiez le chemin dans `.env` :
   ```env
   TESSERACT_PATH=C:\Program Files\Tesseract-OCR\tesseract.exe
   ```

3. Redémarrez votre serveur Laravel/Laragon

### Erreur : "Language 'fra' not found"

1. Installez les données de langue pour le français
2. Vérifiez que les fichiers `.traineddata` sont dans le dossier `tessdata` de Tesseract

### Qualité d'OCR médiocre

1. Utilisez le pré-traitement : `--preprocess` ou cochez l'option dans l'interface
2. Assurez-vous que l'image est de bonne qualité (résolution suffisante, contraste clair)
3. Utilisez des images en niveaux de gris ou noir et blanc
4. Évitez les images floues ou avec beaucoup de bruit

## 📚 Ressources

- Documentation Tesseract : https://tesseract-ocr.github.io/
- GitHub Tesseract : https://github.com/tesseract-ocr/tesseract
- Données de langue : https://github.com/tesseract-ocr/tessdata
- Package PHP utilisé : https://github.com/thiagoalessio/tesseract_ocr

## 💡 Conseils pour de meilleurs résultats

1. **Qualité de l'image** : Utilisez des images de haute résolution (300 DPI minimum)
2. **Contraste** : Assurez-vous d'avoir un bon contraste entre le texte et l'arrière-plan
3. **Orientation** : Le texte doit être droit (pas de rotation)
4. **Pré-traitement** : Activez-le pour améliorer les résultats sur les images de qualité moyenne
5. **Langue** : Spécifiez la langue correcte pour de meilleurs résultats

