<?php

namespace Tests\Unit\Accounts;

use App\Enums\CurrencyCode;
use App\Models\Account;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AccountModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_casts_currency_sensitive_fields_and_flags(): void
    {
        $casts = (new Account)->getCasts();

        $this->assertSame(CurrencyCode::class, $casts['currency_code']);
        $this->assertSame('encrypted', $casts['account_number']);
        $this->assertSame('encrypted', $casts['iban']);
        $this->assertSame('encrypted', $casts['routing_number']);
        $this->assertSame('encrypted', $casts['sort_code']);
        $this->assertSame('boolean', $casts['is_primary']);
        $this->assertSame('boolean', $casts['is_active']);
    }

    public function test_it_belongs_to_a_user_and_exposes_search_configuration(): void
    {
        $account = Account::factory()->make();
        $user = $account->user;

        $this->assertSame('user_id', $account->user()->getForeignKeyName());
        $this->assertSame('user_id', $user->accounts()->getForeignKeyName());
        $this->assertSame(['name', 'bank_name', 'account_holder_name'], $this->invoke($account, 'searchableColumns'));
        $this->assertSame([
            'name' => 'name',
            'bank_name' => 'bank_name',
            'country_code' => 'country_code',
            'currency_code' => 'currency_code',
            'created_at' => 'created_at',
        ], $this->invoke($account, 'sortableColumns'));
    }

    private function invoke(Account $account, string $method): mixed
    {
        $reflection = new \ReflectionMethod($account, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke($account);
    }
}
