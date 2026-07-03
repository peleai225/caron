# 👥 Comptes de Démonstration - Caron Gestion Immobilière

## 📋 Comptes Créés

### 🔐 Identifiants de Connexion

#### 👤 PROPRIÉTAIRE
- **Email**: `proprietaire@caron.com`
- **Mot de passe**: `password`
- **Rôle**: Propriétaire
- **Dashboard**: `/owner/dashboard`
- **Données**:
  - Nom: Jean Dupont
  - 2 biens immobiliers
  - Contrats actifs avec locataires
  - Historique de paiements

#### 👤 LOCATAIRE 1
- **Email**: `locataire1@caron.com`
- **Mot de passe**: `password`
- **Rôle**: Locataire
- **Dashboard**: `/tenant/dashboard`
- **Données**:
  - Nom: Marie Kouassi
  - 1 contrat actif
  - Historique de paiements (6 mois)
  - Paiements à venir

#### 👤 LOCATAIRE 2
- **Email**: `locataire2@caron.com`
- **Mot de passe**: `password`
- **Rôle**: Locataire
- **Dashboard**: `/tenant/dashboard`
- **Données**:
  - Nom: Amadou Traoré
  - 1 contrat actif
  - Historique de paiements (3 mois)
  - Paiements à venir

#### 👤 COMPTABLE
- **Email**: `comptable@caron.com`
- **Mot de passe**: `password`
- **Rôle**: Comptable
- **Dashboard**: `/accountant/dashboard`

#### 👤 ADMINISTRATEUR
- **Email**: `admin@caron.com`
- **Mot de passe**: `password`
- **Rôle**: Super Admin
- **Dashboard**: `/dashboard`

#### 👤 ADMIN AGENCE
- **Email**: `admin-agence@caron.com`
- **Mot de passe**: `password`
- **Rôle**: Admin Agence
- **Dashboard**: `/dashboard`

#### 👤 GESTIONNAIRE
- **Email**: `gestionnaire@caron.com`
- **Mot de passe**: `password`
- **Rôle**: Gestionnaire
- **Dashboard**: `/dashboard`

## 🏠 Données de Démonstration

### Propriétés Créées
1. **Appartement Cocody Angré**
   - Type: Maison
   - Adresse: Cocody Angré 7ème Tranche, Abidjan
   - Loyer: 150,000 FCFA/mois
   - 3 chambres, 2 salles de bain, 120 m²
   - Statut: Occupé

2. **Villa Yopougon**
   - Type: Maison
   - Adresse: Yopougon Sicogi, Abidjan
   - Loyer: 250,000 FCFA/mois
   - 4 chambres, 3 salles de bain, 200 m²
   - Statut: Occupé

### Contrats Créés
- **Contrat 1**: Locataire 1 (Marie Kouassi) - Appartement Cocody
  - Durée: 12 mois (6 mois écoulés, 6 mois restants)
  - Loyer: 150,000 FCFA/mois
  - 6 paiements effectués
  - 1 paiement en attente

- **Contrat 2**: Locataire 2 (Amadou Traoré) - Villa Yopougon
  - Durée: 12 mois (3 mois écoulés, 9 mois restants)
  - Loyer: 250,000 FCFA/mois
  - 3 paiements effectués

### Paiements Créés
- **Locataire 1**: 6 paiements complétés + 1 en attente
- **Locataire 2**: 3 paiements complétés
- Échéanciers créés pour les 3 prochains mois pour chaque contrat

## 🚀 Comment Utiliser

### 1. Créer les Comptes
```bash
php artisan db:seed --class=DemoAccountsSeeder
```

### 2. Se Connecter
1. Aller sur `/login`
2. Utiliser l'un des identifiants ci-dessus
3. Vous serez automatiquement redirigé vers votre dashboard approprié

### 3. Tester les Interfaces

#### Interface Propriétaire
- Se connecter avec `proprietaire@caron.com`
- Accéder à `/owner/dashboard`
- Voir:
  - Statistiques de ses biens
  - Contrats actifs
  - Paiements reçus et en attente
  - Historique complet

#### Interface Locataire
- Se connecter avec `locataire1@caron.com` ou `locataire2@caron.com`
- Accéder à `/tenant/dashboard`
- Voir:
  - Contrats actifs
  - Prochains paiements
  - Historique des paiements
  - Quittances disponibles

## 📝 Notes

- Tous les mots de passe sont: `password`
- Les données sont créées pour l'agence "Agence Principale"
- Les dates sont relatives à la date actuelle
- Les montants sont en FCFA (Franc CFA)

## 🔄 Réinitialiser les Données

Pour recréer les comptes de démonstration:
```bash
php artisan db:seed --class=DemoAccountsSeeder
```

Le seeder utilise `firstOrCreate`, donc il ne créera pas de doublons si les comptes existent déjà.

