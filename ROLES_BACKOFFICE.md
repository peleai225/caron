# Rôle de chaque backoffice – Caron

Ce document décrit le **rôle et les accès** de chaque interface (backoffice) selon le rôle utilisateur.

**Mise à jour :** Comptes locataires désactivés. Nouveaux rôles : Chargé de recouvrement, Agent immobilier.

---

## 1. Backoffice **Administrateur** (gestion d'agence)

**Rôles concernés :** `super_admin`, `admin_agence` (DGA), `gestionnaire` (GLOC), `charge_recouvrement`, `agent_immobilier`

**Tableau de bord :** Dashboard principal (stats : propriétaires, biens, locataires actifs, dépenses, biens disponibles, revenus du mois, impayés, commissions ; paiements récents ; contrats expirant ; litiges ; biens actifs / biens disponibles).

**Rôle :** Gérer l'activité de l'agence selon le périmètre de chaque rôle.

| Section | super_admin | admin_agence (DGA) | gestionnaire (GLOC) | charge_recouvrement | agent_immobilier |
|---------|-------------|--------------------|---------------------|---------------------|------------------|
| **Gestion** | ✅ | ✅ | ✅ | Loyers, Litiges | Locataires, Loyers |
| **Rapports** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Documents** | ✅ | ✅ | ✅ | — | — |
| **Finance** | ✅ | ✅ | ✅ | Pénalités, Factures | Factures |
| **Administration** | ✅ | ✅ | ❌ | ❌ | ❌ |

**Restriction :** Seuls **super_admin** et **admin_agence** ont accès à Agences et Logs d'activité.

---

## 2. Backoffice **Propriétaire**

**Rôle concerné :** `proprietaire`  
**Email type :** L'adresse e-mail doit être suivie du nom du bien immobilier géré (ex. proprietaireamarikris@caron.com).

**Tableau de bord :** Dashboard propriétaire (`/owner/dashboard`) : vue d'ensemble de **ses** biens, contrats actifs, revenus, paiements récents et à venir.

**Rôle :** Consulter l'activité liée à ses biens (pas de création/suppression côté agence).

| Section | Accès |
|---------|-------|
| **Vue d'ensemble** | ✅ Tableau de bord propriétaire |
| **Rapports** | ✅ Accès aux rapports |
| **Profil / Notifications** | ✅ |
| **Biens, Locataires, Contrats, Loyers, Litiges** | ❌ Accès refusé |

---

## 3. Backoffice **Comptable**

**Rôle concerné :** `comptable`

**Tableau de bord :** Dashboard comptable (`/accountant/dashboard`) : indicateurs financiers de l'agence.

**Rôle :** Suivre la partie finance et comptabilité, sans gestion opérationnelle.

| Section | Accès |
|---------|-------|
| **Tableau de bord** | ✅ Dashboard comptable |
| **Finance** | ✅ Dépenses, Comptes, Transactions, Factures |
| **Rapports** | ✅ |
| **Profil / Notifications** | ✅ |
| **Gestion opérationnelle** | ❌ |
| **Administration** | ❌ |

---

## Comptes locataires : DÉSACTIVÉS

Les locataires sont des **données** (table `tenants`) sans compte utilisateur. Ils ne se connectent pas à l'application.

---

## Récapitulatif des redirections à la connexion

| Rôle | Redirection après login |
|------|-------------------------|
| `proprietaire` | `/owner/dashboard` |
| `locataire` | Déconnexion + message « Comptes désactivés » |
| `comptable` | `/accountant/dashboard` |
| `super_admin`, `admin_agence`, `gestionnaire`, `charge_recouvrement`, `agent_immobilier` | Dashboard principal |

---

## Identifiants de démonstration (après `php artisan db:seed`)

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Super Admin | admin@caron.com | password |
| DG Agence (DGA) | dga@caron.com | password |
| Gestionnaire locatif (GLOC) | gloc@caron.com | password |
| Chargé de recouvrement | char@caron.com | password |
| Agent immobilier | agentimmo@caron.com | password |
| Comptable | comptable@caron.com | password |
| Propriétaire | proprietaire@caron.com | password |
