# Vérification complète – Espace Propriétaires

**Date :** 9 février 2026  
**Référence :** Cahier des charges – Éléments requis pour les propriétaires

---

## 1. Informations financières

### ✅ Montant des loyers et charges perçus et à percevoir
| Élément | Statut | Détail |
|---------|--------|--------|
| Loyers perçus (encaissés) | ✅ | Dashboard : Revenus du mois, Total revenus, Paiements récents |
| Loyers à percevoir | ✅ | Paiements à venir (échéances), Paiements en attente |
| Charges incluses | ⚠️ | Les paiements incluent `amount` + `charges_amount` (Payment.totalAmount) mais le dashboard affiche uniquement `amount` dans les stats et listes — les charges perçues ne sont pas sommées |

**Action recommandée :** Inclure `charges_amount` dans le calcul des revenus affichés (utiliser `total_amount` ou `amount + charges_amount`).

---

### ✅ Historique des paiements (encaissés, en attente, en retard)
| Élément | Statut | Détail |
|---------|--------|--------|
| Paiements encaissés | ✅ | Onglet Vue d'ensemble + Paiements, `recentPayments` |
| Paiements en attente | ✅ | `pendingPayments` (status=pending) |
| Paiements en retard | ✅ | `overduePayments` (PaymentSchedule en retard) |

**Bug identifié :** Dans l’onglet Paiements, pour les paiements en attente, la vue utilise `$payment->due_date` alors que le modèle `Payment` n’a pas ce champ — erreur possible. Remplacer par `$payment->payment_date` ou `$payment->paymentSchedule?->due_date`.

---

### ❌ Suivi des charges (dépenses)
| Élément | Statut | Détail |
|---------|--------|--------|
| Dépenses liées aux biens | ❌ | Aucune vue ni route pour que le propriétaire voie les dépenses (Expense) de ses biens |

**Action requise :** Ajouter un bloc/onglet « Dépenses » sur le dashboard propriétaire, filtré par `owner_id` via les biens (`Property::where('owner_id', $owner->id)` puis `Expense::whereIn('property_id', $propertyIds)`).

---

### ⚠️ Rapport de gestion
| Élément | Statut | Détail |
|---------|--------|--------|
| Accès rapports | ✅ | Route `/reports` accessible aux propriétaires |
| Filtrage par propriétaire | ❌ | **Bug critique** : Le `ReportService::generateFinancialReport()` reçoit `agencyId` et affiche les données de **toute l’agence**, pas uniquement les biens du propriétaire |

**Action requise :** Pour les propriétaires, modifier `ReportController` et `ReportService` pour filtrer paiements, échéances et dépenses par `owner_id` (via `contract.property.owner_id`).

---

## 2. Gestion des biens

### ✅ Liste des biens immobiliers (appartements, maisons, locaux)
| Élément | Statut | Détail |
|---------|--------|--------|
| Liste des biens | ✅ | Onglet « Mes Biens » avec type, adresse, loyer, contrats actifs |
| Types (maison, immeuble, boutique, etc.) | ✅ | Affichage du `type` du bien |

---

### ❌ État des lieux (entrée et sortie)
| Élément | Statut | Détail |
|---------|--------|--------|
| État des lieux entrée | ❌ | Aucun modèle, migration ou fonctionnalité |
| État des lieux sortie | ❌ | Idem |

**Action requise :** Créer un module « État des lieux » (modèle, migration, interface) lié au contrat ou au bien, avec type entrée/sortie, date, remarques, éventuellement pièces jointes.

---

### ⚠️ Suivi des échéances (contrats, paiements, travaux)
| Élément | Statut | Détail |
|---------|--------|--------|
| Contrats | ✅ | Onglet Contrats, dates début/fin |
| Paiements | ✅ | Paiements à venir, en attente, en retard |
| Travaux | ❌ | Le champ `depense_travaux` existe dans Payment mais n’est pas affiché ni détaillé pour le propriétaire ; pas de vue dédiée « travaux » |

---

## 3. E-mail des propriétaires

### Convention : nom du bien dans l’e-mail
> *« L'adresse e-mail des propriétaires doit être suivie du nom du bien immobilier géré par l'agence partenaire. »*  
> Exemple : `proprietaireamarikris@caron.com`

| Élément | Statut | Détail |
|---------|--------|--------|
| Convention documentée | ✅ | ROLES_BACKOFFICE.md, ligne 31 |
| Validation technique | ❌ | Aucune validation à la création des propriétaires |
| Affichage e-mail + bien | ❌ | Aucun endroit où l’e-mail du propriétaire est affiché avec le nom du bien |

**Action recommandée :**
- Rappeler la convention dans la doc (Owner, création de compte).
- Lors de la création/édition d’un Owner, rappeler le format d’e-mail conseillé.
- Dans les vues où l’e-mail du propriétaire apparaît (ex. backoffice agence), afficher à côté le(s) bien(s) géré(s).

---

## 4. Récapitulatif des actions

| Priorité | Action |
|----------|--------|
| Haute | Filtrer les rapports par propriétaire (ReportController / ReportService) |
| Haute | Corriger `$payment->due_date` → `$payment->payment_date` (ou `paymentSchedule?->due_date`) dans `owner/dashboard.blade.php` |
| Moyenne | Ajouter un bloc « Dépenses » pour les biens du propriétaire |
| Moyenne | Inclure les charges (`charges_amount`) dans les montants affichés |
| Basse | Créer le module « État des lieux » |
| Basse | Documenter / valider la convention e-mail propriétaire + nom du bien |

---

## 5. Synthèse

| Catégorie | Conforme | Partiel | Manquant |
|-----------|----------|---------|----------|
| Infos financières | 2 | 2 | 1 |
| Gestion biens | 1 | 1 | 1 |
| E-mail | 0 | 0 | 2 |
| **Total** | **3** | **3** | **4** |

**Éléments principaux à corriger :**
1. Rapports non filtrés par propriétaire.
2. Bug sur `due_date` pour les paiements en attente.
3. Pas de suivi des dépenses pour le propriétaire.
4. Pas d’état des lieux (entrée/sortie).
