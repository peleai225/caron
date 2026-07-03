<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('invitation_token', 64)->nullable()->after('bio');
            $table->timestamp('invitation_expires_at')->nullable()->after('invitation_token');
            $table->boolean('is_active')->default(true)->after('invitation_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['invitation_token', 'invitation_expires_at', 'is_active']);
        });
    }
};
