<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Enums\CurrencyCode;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Generates account records for tests and seed data.
 *
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accountNumber = $this->numericString(12);

        return [
            'user_id' => User::factory(),
            'name' => fake()->name().' Account',
            'account_type' => fake()->randomElement(AccountType::cases()),
            'account_holder_name' => fake()->name(),
            'bank_name' => fake()->randomElement([
                'Bank of Ceylon',
                'Commercial Bank of Ceylon',
                'Hatton National Bank',
                'People’s Bank',
                'Sampath Bank',
            ]),
            'bank_code' => $this->numericString(4),
            'branch_name' => fake()->city().' Branch',
            'branch_code' => $this->numericString(3),
            'country_code' => 'LK',
            'currency_code' => CurrencyCode::LKR,
            'account_number' => $accountNumber,
            'account_number_last4' => substr($accountNumber, -4),
            'iban' => null,
            'swift_bic' => 'BANKLK'.strtoupper($this->numericString(5)),
            'routing_number' => null,
            'sort_code' => null,
            'notes' => null,
            'is_primary' => false,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the account is held outside Sri Lanka.
     */
    public function foreign(): static
    {
        return $this->state(function (array $attributes): array {
            $accountNumber = $this->numericString(12);
            $countryCodeValue = fake()->randomElement(['AU', 'CA', 'GB', 'IN', 'SG', 'US']);
            $countryCode = is_string($countryCodeValue) ? $countryCodeValue : 'US';

            return [
                'country_code' => $countryCode,
                'currency_code' => fake()->randomElement([
                    CurrencyCode::AUD,
                    CurrencyCode::CAD,
                    CurrencyCode::GBP,
                    CurrencyCode::INR,
                    CurrencyCode::SGD,
                    CurrencyCode::USD,
                ]),
                'account_number' => $accountNumber,
                'account_number_last4' => substr($accountNumber, -4),
                'bank_code' => null,
                'branch_code' => null,
                'iban' => $countryCode.$this->numericString(22),
                'swift_bic' => 'BANK'.$countryCode.'XXX',
                'routing_number' => $this->numericString(9),
                'sort_code' => $this->numericString(6),
            ];
        });
    }

    /**
     * Indicate that the account is the owner's primary account.
     */
    public function primary(): static
    {
        return $this->state(['is_primary' => true]);
    }

    /**
     * Indicate that the account is inactive.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * Generate a numeric string while preserving leading zeroes.
     *
     * This is used for account identifiers whose display format is fixed-width.
     */
    private function numericString(int $length): string
    {
        $value = '';

        for ($index = 0; $index < $length; $index++) {
            $value .= random_int(0, 9);
        }

        return $value;
    }
}
