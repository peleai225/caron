<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pour MySQL, on doit modifier l'enum en recréant la colonne
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `payments` MODIFY COLUMN `payment_method` ENUM('cash', 'cynetpay', 'wave', 'orange_money', 'mtn_money', 'bank_transfer', 'check') DEFAULT 'cash'");
        } else {
            // Pour PostgreSQL ou autres SGBD
            Schema::table('payments', function (Blueprint $table) {
                $table->enum('payment_method', ['cash', 'cynetpay', 'wave', 'orange_money', 'mtn_money', 'bank_transfer', 'check'])->default('cash')->change();
            });
        }
    }

    public function down(): void
    {
        // Retirer 'cynetpay' de l'enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `payments` MODIFY COLUMN `payment_method` ENUM('cash', 'wave', 'orange_money', 'mtn_money', 'bank_transfer', 'check') DEFAULT 'cash'");
        } else {
            Schema::table('payments', function (Blueprint $table) {
                $table->enum('payment_method', ['cash', 'wave', 'orange_money', 'mtn_money', 'bank_transfer', 'check'])->default('cash')->change();
            });
        }
    }
};
