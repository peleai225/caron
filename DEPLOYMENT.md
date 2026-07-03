# Guide de déploiement — Caron Immobilier

## Prérequis serveur

- PHP 8.2+ avec extensions : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`, `zip`, `intl`
- MySQL 8.0+ ou MariaDB 10.6+
- Redis 6+
- Node.js 20+ / npm
- Composer 2+
- Nginx
- Supervisor
- Tesseract OCR (module OCR documents) : `apt install tesseract-ocr tesseract-ocr-fra`

---

## Première installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/votre-org/caron.git /var/www/caron
cd /var/www/caron
```

### 2. Configurer l'environnement

```bash
cp .env.example .env
nano .env
php artisan key:generate
```

Variables obligatoires dans `.env` :

| Variable | Description |
|----------|-------------|
| `APP_URL` | URL publique ex. `https://caron.mondomaine.com` |
| `APP_KEY` | Généré automatiquement par `key:generate` |
| `DB_DATABASE` | Nom de la base MySQL |
| `DB_USERNAME` | Utilisateur MySQL |
| `DB_PASSWORD` | Mot de passe MySQL |
| `REDIS_HOST` | Hôte Redis (souvent `127.0.0.1`) |
| `MAIL_HOST` | Optionnel au démarrage — configurable dans l'interface admin |
| `MONEYFUSION_API_KEY` | Clé API MoneyFusion (si paiements en ligne) |

> La configuration SMTP peut aussi être faite directement dans l'interface :
> **Paramètres → Email** une fois connecté en super_admin ou admin_agence.

### 3. Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

### 4. Base de données

```bash
php artisan migrate --force
php artisan db:seed --force
```

Cela crée les rôles, permissions, et les comptes de démonstration.

> **IMPORTANT : Changer tous les mots de passe des comptes seed immédiatement.**

### 5. Storage

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Cache de production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Configuration Nginx

Copier `nginx.conf` dans `/etc/nginx/sites-available/caron` :

```bash
cp /var/www/caron/nginx.conf /etc/nginx/sites-available/caron
# Adapter server_name, root, et chemins SSL
nano /etc/nginx/sites-available/caron
ln -s /etc/nginx/sites-available/caron /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
```

SSL avec Let's Encrypt :

```bash
apt install certbot python3-certbot-nginx
certbot --nginx -d votre-domaine.com
```

---

## Configuration Supervisor (queues + scheduler)

```bash
cp /var/www/caron/supervisor.conf /etc/supervisor/conf.d/caron.conf
supervisorctl reread
supervisorctl update
supervisorctl start caron-worker:*
supervisorctl start caron-scheduler
```

Vérifier :

```bash
supervisorctl status
# caron-worker:caron-worker_00   RUNNING
# caron-worker:caron-worker_01   RUNNING
# caron-scheduler                RUNNING
```

---

## Déploiements suivants (mise à jour)

```bash
cd /var/www/caron
bash deploy.sh
```

Le script `deploy.sh` exécute automatiquement :
1. Maintenance mode ON
2. `git pull origin main`
3. `composer install --no-dev`
4. `npm run build`
5. Cache config / routes / vues / events
6. `php artisan migrate --force`
7. `php artisan db:seed --class=RoleSeeder --force` (synchronise les permissions)
8. `php artisan storage:link`
9. Permissions dossiers
10. Redémarrage workers Supervisor + maintenance mode OFF

---

## Comptes par défaut (seed)

| Email | Mot de passe | Rôle |
|-------|-------------|------|
| admin@caron.com | password | super_admin |
| dga@caron.com | password | admin_agence |
| gloc@caron.com | password | gestionnaire |
| comptable@caron.com | password | comptable |
| char@caron.com | password | charge_recouvrement |
| agentimmo@caron.com | password | agent_immobilier |

> Changer tous ces mots de passe immédiatement après la première connexion.
> Les nouveaux utilisateurs sont créés via **Administration → Utilisateurs → Inviter**.

---

## Configuration email (première connexion)

Connectez-vous en super_admin, allez dans **Paramètres → Email** et renseignez :
- Hôte SMTP (ex. `smtp-relay.brevo.com`)
- Port `587`, Chiffrement `TLS`
- Identifiants de votre fournisseur
- Adresse et nom expéditeur

Cliquez **Tester** pour valider avant de sauvegarder.

Fournisseurs gratuits recommandés : **Brevo** (300/jour), **Resend** (3000/mois).

---

## Checklist avant mise en ligne

- [ ] `APP_DEBUG=false` dans `.env`
- [ ] `APP_ENV=production` dans `.env`
- [ ] `APP_KEY` généré
- [ ] `APP_URL` correct (avec `https://`)
- [ ] Base de données migrée et seedée
- [ ] `php artisan storage:link` exécuté
- [ ] Permissions `storage/` et `bootstrap/cache/` en 775, owner `www-data`
- [ ] SSL actif (HTTPS)
- [ ] Nginx opérationnel (`nginx -t` sans erreur)
- [ ] Supervisor actif (3 processus : 2 workers + 1 scheduler)
- [ ] Configuration SMTP testée depuis l'interface (Paramètres → Email)
- [ ] Mot de passe de tous les comptes seed changés
- [ ] MoneyFusion configuré si paiements en ligne activés
- [ ] Backup automatique de la base configuré côté hébergeur
- [ ] Tesseract OCR installé si le module OCR est utilisé
