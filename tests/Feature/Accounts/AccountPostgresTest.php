<?php

namespace Tests\Feature\Accounts;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountPostgresTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Account PostgreSQL integration tests require the pgsql connection.');
        }
    }

    public function test_postgresql_extensions_and_account_indexes_exist(): void
    {
        $extension = DB::table('pg_extension')->where('extname', 'pg_trgm')->exists();
        $indexes = collect(DB::select("SELECT indexname, indexdef FROM pg_indexes WHERE tablename = 'accounts'"));
        $definitions = $indexes->pluck('indexdef', 'indexname');

        $this->assertTrue($extension);
        $this->assertArrayHasKey('accounts_user_created_at_index', $definitions->all());
        $this->assertArrayHasKey('accounts_user_primary_unique', $definitions->all());
        $this->assertArrayHasKey('accounts_name_trgm_index', $definitions->all());
        $this->assertArrayHasKey('accounts_bank_name_trgm_index', $definitions->all());
        $this->assertArrayHasKey('accounts_holder_name_trgm_index', $definitions->all());
        $this->assertStringContainsString('WHERE is_primary', strtoupper($definitions['accounts_user_primary_unique']));
    }

    public function test_postgresql_enforces_one_primary_account_per_user(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->create(['is_primary' => true]);

        $this->expectException(QueryException::class);
        Account::factory()->for($user)->create(['is_primary' => true]);
    }
}
