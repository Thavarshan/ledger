<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::table('accounts')->whereNull('account_type')->exists()) {
            throw new RuntimeException('Cannot make accounts.account_type non-null while null values exist.');
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->string('account_type', 50)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('account_type', 50)->nullable()->change();
        });
    }
};
