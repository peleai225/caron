# Vérification des rôles – Projet Caron (nouvelle spécification)

**Date :** 9 février 2026  
**Source :** Document "Projet Caron – Rôle des administrés"

---

## Synthèse : Écarts majeurs

| Point | État actuel | Nouvelle spécification | Action requise |
|-------|-------------|------------------------|----------------|
| **Comptes locataires** | ✅ Module actif (création, réinit, désactivation) | ❌ **ANNULÉS** | **SUPPRIMER** le module tenant-accounts |
| **Rôle Locataire** | ✅ Existe (dashboard, contrats, paiements, quittances, signalements) | ❌ **N'existe plus** | **SUPPRIMER** rôle + routes + vues locataire |
| **Signalements locataires** | ✅ Locataire connecté crée des signalements | ❌ Locataires n'ont plus de compte | **SUPPRIMER** ou repenser (formulaire public ?) |
| **Chargé de recouvrement** | ❌ N'existe pas | ✅ Nouveau rôle (char@caron.com) | **AJOUTER** rôle + périmètre |
| **Agent immobilier** | ❌ N'existe pas | ✅ Nouveau rôle (agentimmo@caron.com) | **AJOUTER** rôle + périmètre |
| **Comptable** | ✅ Existe | ⚠️ Non mentionné dans le document | À clarifier |
| **Emails propriétaires** | Libre | Doivent inclure le nom du bien | Règle métier à documenter |

---

## 1. Rôles selon la nouvelle spécification

### 1.1 ADMIN → Gestionnaire locatif (GLOC)
- **Email :** gloc@caron.com
- **Correspondance actuelle :** `gestionnaire`
- **Missions :** Locataires, contrats, financière, technique, reporting
- **Statut :** ✅ Existe (gestionnaire)

### 1.2 ADMIN-AGENCE → DG Agence (DGA)
- **Email :** dga@caron.com
- **Correspondance actuelle :** `admin_agence`
- **Missions :** Biens, locataires, contrats, financière, administrative, technique, reporting
- **Statut :** ✅ Existe (admin_agence)

### 1.3 Chargé de recouvrement
- **Email :** char@caron.com
- **Correspondance actuelle :** ❌ **N'existe pas**
- **Missions :** Relances impayés, procédures, contentieux, devis/factures, rapports recouvrement
- **Statut :** ❌ **À créer**

### 1.4 Agent immobilier
- **Email :** agentimmo@caron.com
- **Correspondance actuelle :** ❌ **N'existe pas**
- **Missions :** Locataires, encaissements, impayés, factures, devis, plans de paiement
- **Statut :** ❌ **À créer**

### 1.5 Propriétaire
- **Email :** Proprietaireamarikris@caron.com (exemple)
- **Règle :** L’email doit être suivi du nom du bien immobilier géré par l’agence partenaire
- **Statut :** ✅ Existe (proprietaire)

---

## 2. À supprimer (comptes locataires annulés)

### 2.1 Module « Comptes locataires » (tenant-accounts)
| Élément | Fichier / Route | Action |
|---------|-----------------|--------|
| Route index | `GET /tenant-accounts` | Supprimer |
| Route create | `POST /tenant-accounts/{tenant}/create` | Supprimer |
| Route create-all | `POST /tenant-accounts/create-all` | Supprimer |
| Route reset-password | `POST /tenant-accounts/{tenant}/reset-password` | Supprimer |
| Route deactivate | `DELETE /tenant-accounts/{tenant}/deactivate` | Supprimer |
| Contrôleur | `TenantAccountController` | Supprimer ou désactiver |
| Vue | `tenant-accounts/index.blade.php` | Supprimer |
| Menu sidebar | Lien « Comptes locataires » | Supprimer |
| Seeder | `TenantAccountSeeder` | Supprimer ou désactiver |

### 2.2 Rôle et espace Locataire
| Élément | Fichier / Route | Action |
|---------|-----------------|--------|
| Rôle | `locataire` dans RoleSeeder | Supprimer (ou garder en BDD pour historique) |
| Redirection login | `AuthController` → tenant.dashboard | Supprimer |
| Redirection dashboard | `DashboardController` → tenant.dashboard | Supprimer |
| Routes | `GET /tenant/*` (dashboard, contracts, payments, receipts, reports) | Supprimer |
| Contrôleur | `TenantDashboardController`, `TenantReportController` | Supprimer ou désactiver |
| Middleware | `EnsureTenantRole` | Supprimer si plus utilisé |
| Vues | `tenant/*` | Supprimer |
| RestrictAdminRoutes | Référence « locataire » | Adapter (plus de redirection tenant) |

### 2.3 Signalements locataires (côté locataire)
| Élément | Action |
|---------|--------|
| Routes `/tenant/reports/*` | Supprimer (locataire ne se connecte plus) |
| `TenantReportController` (côté tenant) | Supprimer |
| Vues `tenant/reports/*` | Supprimer |

**Note :** Les signalements côté admin (`/tenant-reports`) peuvent être conservés si une autre source de signalements est prévue (formulaire public, email, etc.). Sinon, à supprimer aussi.

---

## 3. À ajouter

### 3.1 Rôle Chargé de recouvrement
- **Identifiant suggéré :** `charge_recouvrement` ou `recouvrement`
- **Périmètre :**
  - Loyers & Paiements (vue, impayés, relances)
  - Pénalités
  - Litiges (contentieux, procédures)
  - Factures, devis
  - Rapports (impayés, recouvrement)
  - Pas de gestion biens/locataires/contrats (création)
- **Dashboard :** Dédié ou partagé avec focus impayés

### 3.2 Rôle Agent immobilier
- **Identifiant suggéré :** `agent_immobilier`
- **Périmètre :**
  - Locataires (vue, édition, rappels)
  - Loyers & Paiements (encaissements, impayés)
  - Factures, devis
  - Plans de paiement
  - Pas de biens, contrats, dépenses, administration

---

## 4. Données conservées (en base)

- **Table `tenants`** : Conserver — les locataires restent des enregistrements (personnes qui louent), sans compte utilisateur.
- **Colonne `user_id` sur `tenants`** : Peut rester pour compatibilité ou être mise à null si plus de comptes.
- **Table `tenant_reports`** : À trancher selon la stratégie (conservation ou suppression).

---

## 5. Règle emails propriétaires

> L'adresse e-mail des propriétaires doit être suivie du nom du bien immobilier géré par l'agence partenaire.

- À implémenter en validation (format) ou en règle métier documentée.
- Exemple : `proprietaire.bien123@domaine.com` ou `proprietaireamarikris@caron.com`.

---

## 6. Plan d’action recommandé

### Phase 1 – Suppression des comptes locataires
1. Supprimer les routes `tenant-accounts`
2. Supprimer le lien « Comptes locataires » de la sidebar
3. Supprimer les routes `/tenant/*` (dashboard, contracts, payments, receipts, reports)
4. Supprimer les redirections vers `tenant.dashboard` dans Auth et Dashboard
5. Mettre à jour `RestrictAdminRoutes` (supprimer les références locataire si nécessaire)

### Phase 2 – Nouveaux rôles
1. Ajouter le rôle `charge_recouvrement` dans RoleSeeder
2. Ajouter le rôle `agent_immobilier` dans RoleSeeder
3. Créer les middlewares ou logique d’accès par rôle
4. Adapter la sidebar selon le rôle
5. Créer les dashboards ou vues dédiées si besoin

### Phase 3 – Nettoyage
1. Mettre à jour `ROLES_BACKOFFICE.md`
2. Adapter ou supprimer DemoAccountsSeeder (comptes locataires)
3. Valider la règle des emails propriétaires

---

## 7. Points à clarifier

1. **Comptable** : Conserver le rôle `comptable` malgré son absence dans le document ?
2. **Signalements** : Supprimer complètement ou proposer un formulaire public pour les locataires ?
3. **Super_admin** : Conserver tel quel (non mentionné dans le document) ?
