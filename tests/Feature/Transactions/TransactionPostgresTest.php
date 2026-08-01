<?php

namespace Tests\Feature\Transactions;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionPostgresTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Transaction PostgreSQL integration tests require the pgsql connection.');
        }
    }

    public function test_postgresql_constraint_and_indexes_exist(): void
    {
        $indexes = collect(DB::select("SELECT indexname, indexdef FROM pg_indexes WHERE tablename = 'transactions'"));
        $definitions = $indexes->pluck('indexdef', 'indexname');

        $this->assertTrue(DB::table('pg_extension')->where('extname', 'pg_trgm')->exists());
        foreach ([
            'transactions_account_occurred_index',
            'transactions_account_direction_occurred_index',
            'transactions_account_amount_index',
            'transactions_description_trgm_index',
            'transactions_reference_trgm_index',
        ] as $index) {
            $this->assertArrayHasKey($index, $definitions->all());
        }

        $constraints = collect(DB::select("SELECT conname FROM pg_constraint WHERE conrelid = 'transactions'::regclass"));
        $this->assertContains('transactions_amount_minor_positive', $constraints->pluck('conname')->all());
    }

    public function test_postgresql_rejects_non_positive_amounts(): void
    {
        $account = Account::factory()->create();

        $this->expectException(QueryException::class);
        DB::table('transactions')->insert([
            'account_id' => $account->id,
            'direction' => 'credit',
            'amount_minor' => 0,
            'description' => 'Invalid',
            'occurred_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_account_deletion_cascades_to_transactions(): void
    {
        $account = Account::factory()->create();
        $transaction = Transaction::factory()->forAccount($account)->create();

        $account->delete();

        $this->assertModelMissing($transaction);
    }
}
