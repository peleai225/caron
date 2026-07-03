<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('category', [
                'contract',
                'termination',
                'amendment',
                'notification',
                'legal',
                'receipt',
                'option',
                'sale',
                'management',
                'other'
            ])->default('other');
            $table->string('country')->default('CI');
            $table->text('description')->nullable();
            $table->string('file_path'); // Chemin vers le fichier .docx original
            $table->text('variables')->nullable(); // JSON des variables disponibles
            $table->boolean('is_system')->default(false); // Templates système (non modifiables)
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('version')->default(1);
            $table->foreignId('previous_version_id')->nullable()->constrained('document_templates')->onDelete('set null');
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};

