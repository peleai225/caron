<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Litige extends Model
{
    protected $table = 'litiges';

    protected $fillable = [
        'agency_id',
        'contract_id',
        'tenant_id',
        'property_id',
        'owner_id',
        'reference',
        'personnes_concernées',
        'lieu_intervention',
        'type_contrat',
        'nature_litige',
        'description',
        'couts_engages',
        'pertes_financieres',
        'suivi_commentaires',
        'statut',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'couts_engages' => 'array',
        'pertes_financieres' => 'array',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public static function typesContrat(): array
    {
        return [
            'bail_habitation_vide' => 'Bail d\'habitation vide',
            'bail_meuble' => 'Bail meublé',
            'bail_mixte' => 'Bail mixte',
            'bail_commercial' => 'Bail commercial',
            'bail_professionnel' => 'Bail professionnel',
            'bail_saisonnier' => 'Bail saisonnier',
            'contrat_temps_partiel' => 'Contrat à temps partiel',
            'contrat_interim' => 'Contrat d\'intérim',
            'contrat_chantier' => 'Contrat de chantier / d\'opération',
            'cdd' => 'CDD',
            'cdi' => 'CDI',
            'autre' => 'Autre',
        ];
    }

    public static function naturesLitige(): array
    {
        return [
            'retard_paiement' => 'Retards de paiement',
            'non_paiement' => 'Non-paiement',
            'contestation_montant' => 'Contestation du montant',
            'depot_garantie_non_rendu' => 'Dépôt de garantie non rendu',
            'contestation_reparation' => 'Contestation de la réparation',
            'refus_paiement' => 'Refus de paiement',
            'factures_impayees' => 'Factures impayées',
            'non_respect_clauses' => 'Non-respect des clauses',
            'sous_location_interdite' => 'Sous-location interdite',
            'resiliation_anticipée_contestee' => 'Résiliation anticipée contestée',
            'travaux_non_realises' => 'Travaux non réalisés',
            'logement_insalubre' => 'Logement insalubre',
            'degradations' => 'Dégradations',
            'etat_lieux_conteste' => 'État des lieux contesté',
            'contestation_ag' => 'Contestation des décisions d\'AG',
            'repartition_charges' => 'Répartition des charges',
            'conflits_parties_communes' => 'Conflits sur parties communes',
            'contestation_taxes_foncieres' => 'Contestation de taxes foncières',
            'litiges_urbanisme' => 'Litiges avec l\'urbanisme ou permis de construire',
            'maintenance' => 'Non-exécution des contrats de maintenance',
            'facturation_abusive' => 'Facturation abusive',
            'retards_travaux' => 'Retards de travaux',
            'nuisances_sonores' => 'Nuisances sonores',
            'troubles_voisinage' => 'Troubles de voisinage',
            'non_respect_reglement' => 'Non-respect du règlement intérieur',
            'convocation_police' => 'Convocation police',
            'convocation_gendarmerie' => 'Convocation gendarmerie',
            'assignation_justice' => 'Assignation en justice',
            'mise_en_demeure_gestion' => 'Mise en demeure par la gestion',
            'mise_en_demeure_huissier' => 'Mise en demeure par voie d\'huissier',
            'autre' => 'Autres',
        ];
    }

    public static function statutsSuivi(): array
    {
        return [
            'reglement_amiable' => 'Règlement à l\'amiable',
            'decision_judiciaire' => 'Règlement par décision judiciaire',
            'conciliation_mediation' => 'Conciliation / Médiation',
            'reconciliation' => 'Réconciliation',
            'paiement_effectue_loyer' => 'Paiement effectué (loyer)',
            'paiement_effectue_charges' => 'Paiement effectué (charges)',
            'paiement_effectue_factures' => 'Paiement effectué (factures)',
            'factures_reglees' => 'Factures réglées',
            'saisie_biens' => 'Saisie de biens',
            'fin_litige' => 'Fin du litige',
            'retrait_convocation' => 'Retrait de la convocation',
            'abandon_poursuite' => 'Abandon de poursuite',
            'reparations_effectuees' => 'Réparations effectuées',
            'remise_etat_logement' => 'Remise en état du logement',
            'indemnisation' => 'Indemnisation',
            'resiliation_bail' => 'Résiliation du bail',
            'execution_volontaire' => 'Exécution volontaire',
            'transaction' => 'Transaction',
            'mise_en_demeure_envoyee' => 'Mise en demeure envoyée',
            'mediation_en_cours' => 'Médiation en cours',
            'travaux_programmes' => 'Travaux programmés',
            'accord_amiable' => 'Accord amiable trouvé',
        ];
    }
}
