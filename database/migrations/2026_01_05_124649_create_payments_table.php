<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('payment_schedule_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('penalty_amount', 15, 2)->default(0);
            $table->date('payment_date');
            $table->string('period'); // Format: YYYY-MM (ex: 2024-01)
            $table->enum('payment_method', ['cash', 'cynetpay', 'wave', 'orange_money', 'mtn_money', 'bank_transfer', 'check'])->default('cash');
            $table->string('reference')->nullable(); // Référence de transaction
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
