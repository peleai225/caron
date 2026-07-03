<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_type', 50)->default('loyer')->after('period')
                ->comment('loyer, charges_locatives, factures, vente, commission');
            $table->decimal('charges_amount', 15, 2)->default(0)->after('penalty_amount');
            $table->decimal('depense_travaux', 15, 2)->nullable()->after('charges_amount');
            $table->decimal('commission_percent', 5, 2)->nullable()->after('depense_travaux');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'charges_amount', 'depense_travaux', 'commission_percent']);
        });
    }
};
