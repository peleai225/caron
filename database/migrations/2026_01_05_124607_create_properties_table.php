<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            $table->foreignId('owner_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['maison', 'immeuble', 'boutique', 'terrain'])->default('maison');
            $table->enum('status', ['libre', 'occupe', 'maintenance'])->default('libre');
            $table->text('address');
            $table->string('city');
            $table->string('neighborhood')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('surface', 10, 2)->nullable(); // en m²
            $table->text('description')->nullable();
            $table->decimal('monthly_rent', 15, 2)->nullable(); // Loyer mensuel de référence
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
