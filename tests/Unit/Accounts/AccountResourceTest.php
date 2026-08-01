<?php

namespace Tests\Unit\Accounts;

use App\Enums\CurrencyCode;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\Request;
use Tests\TestCase;

class AccountResourceTest extends TestCase
{
    public function test_it_serializes_safe_account_data_without_secrets(): void
    {
        $account = new Account;
        $account->forceFill([
            'id' => 1,
            'name' => 'Operating account',
            'currency_code' => CurrencyCode::USD,
            'account_number_last4' => '7890',
            'iban' => 'GB82WEST12345698765432',
            'routing_number' => '123456789',
            'sort_code' => '123456',
        ]);

        $data = (new AccountResource($account))->toArray(Request::create('/'));

        $this->assertSame('USD', $data['currency_code']);
        $this->assertSame($account->account_number_last4, $data['account_number_last4']);
        $this->assertTrue($data['has_iban']);
        $this->assertTrue($data['has_routing_number']);
        $this->assertTrue($data['has_sort_code']);
        $this->assertArrayNotHasKey('account_number', $data);
        $this->assertArrayNotHasKey('iban', $data);
        $this->assertArrayNotHasKey('routing_number', $data);
        $this->assertArrayNotHasKey('sort_code', $data);
        $this->assertSame([
            'id', 'name', 'account_type', 'account_holder_name', 'bank_name',
            'bank_code', 'branch_name', 'branch_code', 'country_code',
            'currency_code', 'account_number_last4', 'has_iban',
            'swift_bic', 'has_routing_number', 'has_sort_code', 'notes',
            'is_primary', 'is_active', 'created_at', 'updated_at',
        ], array_keys($data));
    }

    public function test_it_reports_absent_optional_identifiers(): void
    {
        $account = new Account;
        $account->forceFill([
            'iban' => null,
            'routing_number' => null,
            'sort_code' => null,
        ]);

        $data = (new AccountResource($account))->toArray(Request::create('/'));

        $this->assertFalse($data['has_iban']);
        $this->assertFalse($data['has_routing_number']);
        $this->assertFalse($data['has_sort_code']);
    }
}
