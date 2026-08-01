<?php

namespace Database\Factories;

use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Generates transaction records for tests and seed data.
 *
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'direction' => fake()->randomElement(TransactionDirection::cases()),
            'amount_minor' => fake()->numberBetween(100, 500000),
            'description' => fake()->sentence(3),
            'reference' => fake()->optional()->bothify('TXN-########'),
            'notes' => fake()->optional()->sentence(),
            'occurred_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Indicate that the transaction is a credit.
     */
    public function credit(): static
    {
        return $this->state(['direction' => TransactionDirection::CREDIT]);
    }

    /**
     * Indicate that the transaction is a debit.
     */
    public function debit(): static
    {
        return $this->state(['direction' => TransactionDirection::DEBIT]);
    }

    /**
     * Set the account that owns the transaction.
     */
    public function forAccount(Account $account): static
    {
        return $this->state(['account_id' => $account->id]);
    }
}
