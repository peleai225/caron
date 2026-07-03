<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('litiges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('owner_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference')->nullable();
            $table->string('personnes_concernées')->nullable();
            $table->string('lieu_intervention')->nullable(); // Portes / Lieux / Contacts
            $table->string('type_contrat')->nullable(); // Bail habitation vide, meublé, etc.
            $table->string('nature_litige')->nullable(); // Retards paiement, non-paiement, etc.
            $table->text('description')->nullable();
            $table->json('couts_engages')->nullable(); // huissier, avocat, réparation, dédommagement, transport, autres
            $table->json('pertes_financieres')->nullable(); // loyer_impaye, charges_non_recouvrees, risque_perte_locataire
            $table->text('suivi_commentaires')->nullable();
            $table->string('statut')->default('en_cours'); // en_cours, regle, cloture
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('litiges');
    }
};
