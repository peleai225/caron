# 🔐 Système d'Accès aux Dashboards

## 📋 Vue d'ensemble

Le système de gestion immobilière Caron dispose de **4 types de dashboards** avec des accès sécurisés par rôle :

1. **Dashboard Principal** (`/dashboard`) - Pour Super Admin, Admin Agence, Gestionnaire
2. **Dashboard Propriétaire** (`/owner/dashboard`) - Pour les Propriétaires
3. **Dashboard Locataire** (`/tenant/dashboard`) - Pour les Locataires
4. **Dashboard Comptable** (`/accountant/dashboard`) - Pour les Comptables

## 🛡️ Middlewares de Protection

### 1. `EnsureOwnerRole` (role.owner)
- **Fichier**: `app/Http/Middleware/EnsureOwnerRole.php`
- **Vérifications**:
  - L'utilisateur est authentifié
  - L'utilisateur a le rôle `proprietaire` OU
  - L'utilisateur a un compte `Owner` lié via son email
- **Action en cas d'échec**: Redirection vers `/dashboard` avec message d'erreur

### 2. `EnsureTenantRole` (role.tenant)
- **Fichier**: `app/Http/Middleware/EnsureTenantRole.php`
- **Vérifications**:
  - L'utilisateur est authentifié
  - L'utilisateur a le rôle `locataire` OU
  - L'utilisateur a un `tenant_id` associé
- **Action en cas d'échec**: Redirection vers `/dashboard` avec message d'erreur

### 3. `EnsureAccountantRole` (role.accountant)
- **Fichier**: `app/Http/Middleware/EnsureAccountantRole.php`
- **Vérifications**:
  - L'utilisateur est authentifié
  - L'utilisateur a le rôle `comptable`
- **Action en cas d'échec**: Redirection vers `/dashboard` avec message d'erreur

## 🔄 Redirection Automatique

Le `DashboardController` redirige automatiquement les utilisateurs vers leur dashboard approprié :

```php
// Si l'utilisateur est propriétaire → /owner/dashboard
// Si l'utilisateur est locataire → /tenant/dashboard
// Si l'utilisateur est comptable → /accountant/dashboard
// Sinon → Dashboard principal
```

## 📍 Routes Protégées

### Dashboard Propriétaire
```php
Route::prefix('owner')->middleware('role.owner')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index']);
});
```

### Dashboard Locataire
```php
Route::prefix('tenant')->middleware('role.tenant')->group(function () {
    Route::get('/dashboard', [TenantDashboardController::class, 'index']);
    Route::get('/contracts', [TenantDashboardController::class, 'contracts']);
    Route::get('/payments', [TenantDashboardController::class, 'payments']);
    Route::get('/receipts', [TenantDashboardController::class, 'receipts']);
});
```

### Dashboard Comptable
```php
Route::prefix('accountant')->middleware('role.accountant')->group(function () {
    Route::get('/dashboard', [AccountantDashboardController::class, 'index']);
});
```

## 👥 Rôles et Accès

| Rôle | Dashboard Principal | Dashboard Propriétaire | Dashboard Locataire | Dashboard Comptable |
|------|---------------------|------------------------|---------------------|---------------------|
| **Super Admin** | ✅ | ❌ | ❌ | ❌ |
| **Admin Agence** | ✅ | ❌ | ❌ | ❌ |
| **Gestionnaire** | ✅ | ❌ | ❌ | ❌ |
| **Propriétaire** | ❌ | ✅ | ❌ | ❌ |
| **Locataire** | ❌ | ❌ | ✅ | ❌ |
| **Comptable** | ❌ | ❌ | ❌ | ✅ |

## 🔧 Configuration

Les middlewares sont enregistrés dans `bootstrap/app.php` :

```php
$middleware->alias([
    'role.owner' => \App\Http\Middleware\EnsureOwnerRole::class,
    'role.tenant' => \App\Http\Middleware\EnsureTenantRole::class,
    'role.accountant' => \App\Http\Middleware\EnsureAccountantRole::class,
]);
```

## 📝 Notes Importantes

1. **Propriétaires** : Le middleware vérifie d'abord le rôle, puis cherche un compte `Owner` lié par email
2. **Locataires** : Le middleware vérifie d'abord le rôle, puis vérifie la présence d'un `tenant_id`
3. **Comptables** : Le middleware vérifie uniquement le rôle
4. Tous les middlewares redirigent vers le dashboard principal avec un message d'erreur au lieu d'afficher une page 403

## 🚀 Utilisation

Pour protéger une nouvelle route avec un rôle spécifique :

```php
Route::middleware('role.owner')->group(function () {
    // Routes pour propriétaires
});

Route::middleware('role.tenant')->group(function () {
    // Routes pour locataires
});

Route::middleware('role.accountant')->group(function () {
    // Routes pour comptables
});
```

