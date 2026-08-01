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
        Schema::table('accounts', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'accounts_user_created_at_index');
        });

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement('CREATE UNIQUE INDEX accounts_user_primary_unique ON accounts (user_id) WHERE is_primary');
        DB::statement('CREATE INDEX accounts_name_trgm_index ON accounts USING GIN (name gin_trgm_ops)');
        DB::statement('CREATE INDEX accounts_bank_name_trgm_index ON accounts USING GIN (bank_name gin_trgm_ops)');
        DB::statement('CREATE INDEX accounts_holder_name_trgm_index ON accounts USING GIN (account_holder_name gin_trgm_ops)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS accounts_holder_name_trgm_index');
            DB::statement('DROP INDEX IF EXISTS accounts_bank_name_trgm_index');
            DB::statement('DROP INDEX IF EXISTS accounts_name_trgm_index');
            DB::statement('DROP INDEX IF EXISTS accounts_user_primary_unique');
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex('accounts_user_created_at_index');
        });
    }
};
