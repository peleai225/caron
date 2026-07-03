<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM(
            'cash','cynetpay','wave','orange_money','mtn_money','bank_transfer','check','moneyfusion'
        ) NOT NULL DEFAULT 'cash'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM(
            'cash','cynetpay','wave','orange_money','mtn_money','bank_transfer','check'
        ) NOT NULL DEFAULT 'cash'");
    }
};
