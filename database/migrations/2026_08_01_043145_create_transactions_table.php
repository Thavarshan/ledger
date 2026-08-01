<?php

use App\Enums\TransactionDirection;
use App\Models\Account;
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
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Account::class)->constrained()->cascadeOnDelete();
            $table->enum('direction', TransactionDirection::values());
            $table->bigInteger('amount_minor');
            $table->string('description');
            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestampTz('occurred_at', 6);
            $table->timestamps();

            $table->index(['account_id', 'occurred_at'], 'transactions_account_occurred_index');
            $table->index(
                ['account_id', 'direction', 'occurred_at'],
                'transactions_account_direction_occurred_index',
            );
            $table->index(['account_id', 'amount_minor'], 'transactions_account_amount_index');
        });

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement(
            'ALTER TABLE transactions ADD CONSTRAINT transactions_amount_minor_positive CHECK (amount_minor > 0)',
        );
        DB::statement(
            'CREATE INDEX transactions_description_trgm_index ON transactions USING GIN (description gin_trgm_ops)',
        );
        DB::statement(
            'CREATE INDEX transactions_reference_trgm_index ON transactions USING GIN (reference gin_trgm_ops)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS transactions_reference_trgm_index');
            DB::statement('DROP INDEX IF EXISTS transactions_description_trgm_index');
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_amount_minor_positive');
        }

        Schema::dropIfExists('transactions');
    }
};
