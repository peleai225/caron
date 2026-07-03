<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('owner_id')->nullable()->constrained()->onDelete('set null');
            $table->string('contract_number')->unique();
            $table->decimal('rent_amount', 15, 2);
            $table->decimal('deposit', 15, 2)->default(0); // Caution
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('payment_frequency', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->integer('payment_day')->default(1); // Jour du mois pour le paiement
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('draft');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('pdf_path')->nullable(); // Chemin vers le PDF du contrat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
