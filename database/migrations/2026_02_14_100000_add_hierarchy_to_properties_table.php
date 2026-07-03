<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la hiérarchie : immeuble > unités (studio, 2 pièces, appartement, étage, etc.)
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('owner_id')->constrained('properties')->onDelete('cascade');
            $table->string('unit_type')->nullable()->after('type'); // studio, deux_pieces, trois_pieces, appartement, etage, etc.
            $table->string('designation')->nullable()->after('address'); // "Studio A", "Apt 3B", "Étage 2"
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'unit_type', 'designation']);
        });
    }
};
