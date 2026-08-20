<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\CurrencyCode;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds a representative mix of local, foreign, active, and archived accounts.
 */
class AccountSeeder extends Seeder
{
    /**
     * Create or refresh the demo user's representative account portfolio.
     *
     * Existing primary flags are cleared before the designated everyday
     * account is restored as the single primary account.
     */
    public function run(): void
    {
        $user = User::query()->where('email', DemoUserSeeder::EMAIL)->firstOrFail();
        $accounts = [];

        foreach ($this->accounts() as $name => $attributes) {
            $accounts[$name] = $user->accounts()->updateOrCreate(
                ['name' => $name],
                [...$attributes, 'is_primary' => false],
            );
        }

        $user->accounts()->update(['is_primary' => false]);
        $accounts['Everyday LKR']->update(['is_primary' => true]);
    }

    /**
     * Return the deterministic account definitions used by the demo dataset.
     *
     * Values intentionally include a mixture of currencies, account types,
     * optional bank identifiers, and an archived account for UI coverage.
     *
     * @return array<string, array<string, mixed>>
     */
    private function accounts(): array
    {
        return [
            'Everyday LKR' => [
                'account_type' => AccountType::Savings,
                'account_holder_name' => 'Alex Perera',
                'bank_name' => 'Commercial Bank of Ceylon',
                'bank_code' => '7056',
                'branch_name' => 'Colombo City Office',
                'branch_code' => '001',
                'country_code' => 'LK',
                'currency_code' => CurrencyCode::LKR,
                'account_number' => '001234567890',
                'account_number_last4' => '7890',
                'swift_bic' => 'CCEYLKLX',
                'notes' => 'Primary salary and day-to-day spending account.',
                'is_active' => true,
            ],
            'Business Current LKR' => [
                'account_type' => AccountType::Current,
                'account_holder_name' => 'Alex Perera',
                'bank_name' => 'Hatton National Bank',
                'bank_code' => '7083',
                'branch_name' => 'Bambalapitiya',
                'branch_code' => '114',
                'country_code' => 'LK',
                'currency_code' => CurrencyCode::LKR,
                'account_number' => '002345678901',
                'account_number_last4' => '8901',
                'swift_bic' => 'HBLILKLX',
                'notes' => 'Freelance and consulting income.',
                'is_active' => true,
            ],
            'Wise USD Wallet' => [
                'account_type' => AccountType::Current,
                'account_holder_name' => 'Alex Perera',
                'bank_name' => 'Wise',
                'branch_name' => 'New York',
                'country_code' => 'US',
                'currency_code' => CurrencyCode::USD,
                'account_number' => '987654321012',
                'account_number_last4' => '1012',
                'routing_number' => '026073150',
                'swift_bic' => 'CMFGUS33',
                'notes' => 'International client payments and software subscriptions.',
                'is_active' => true,
            ],
            'Barclays GBP Account' => [
                'account_type' => AccountType::Current,
                'account_holder_name' => 'Alex Perera',
                'bank_name' => 'Barclays',
                'branch_name' => 'London',
                'country_code' => 'GB',
                'currency_code' => CurrencyCode::GBP,
                'account_number' => '123456789012',
                'account_number_last4' => '9012',
                'iban' => 'GB29NWBK60161331926819',
                'sort_code' => '200000',
                'swift_bic' => 'BARCGB22',
                'notes' => 'Travel and United Kingdom expenses.',
                'is_active' => true,
            ],
            'Fixed Deposit 2025' => [
                'account_type' => AccountType::FixedDeposit,
                'account_holder_name' => 'Alex Perera',
                'bank_name' => 'People’s Bank',
                'bank_code' => '7135',
                'branch_name' => 'Nugegoda',
                'branch_code' => '084',
                'country_code' => 'LK',
                'currency_code' => CurrencyCode::LKR,
                'account_number' => '003456789012',
                'account_number_last4' => '9012',
                'swift_bic' => 'PSBKLKLX',
                'notes' => 'Twelve-month fixed deposit maturing in December 2026.',
                'is_active' => true,
            ],
            'Archived Sampath Savings' => [
                'account_type' => AccountType::Savings,
                'account_holder_name' => 'Alex Perera',
                'bank_name' => 'Sampath Bank',
                'bank_code' => '7278',
                'branch_name' => 'Maharagama',
                'branch_code' => '062',
                'country_code' => 'LK',
                'currency_code' => CurrencyCode::LKR,
                'account_number' => '004567890123',
                'account_number_last4' => '0123',
                'swift_bic' => 'BSAMLKLX',
                'notes' => 'Retained for historical transactions after closure.',
                'is_active' => false,
            ],
        ];
    }
}
