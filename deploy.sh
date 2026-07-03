#!/usr/bin/env bash
# =============================================================
# Script de déploiement — Caron Immobilier
# Usage : bash deploy.sh
# Prérequis : PHP 8.2+, Composer, Node 20+, MySQL, Redis
# =============================================================
set -e

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$APP_DIR"

echo "==> [1/10] Mise en maintenance..."
php artisan down --render="errors.503" --retry=60

echo "==> [2/10] Mise à jour du code (git pull)..."
git pull origin main

echo "==> [3/10] Installation des dépendances PHP (production)..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> [4/10] Build des assets front-end..."
npm ci
npm run build

echo "==> [5/10] Cache de configuration / routes / vues / events..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> [6/10] Migrations base de données..."
php artisan migrate --force

echo "==> [7/10] Mise à jour des rôles et permissions..."
php artisan db:seed --class=RoleSeeder --force

echo "==> [8/10] Lien symbolique storage → public..."
php artisan storage:link --force 2>/dev/null || true

echo "==> [9/10] Permissions dossiers..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "==> [10/10] Redémarrage des workers + sortie de maintenance..."
supervisorctl restart caron-worker:* 2>/dev/null || true
supervisorctl restart caron-scheduler 2>/dev/null || true
php artisan up

echo ""
echo "✓ Déploiement terminé avec succès."
