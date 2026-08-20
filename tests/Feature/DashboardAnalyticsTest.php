<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\CurrencyCode;
use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    /** Dashboard responses include owner-scoped balances and six monthly flow buckets. */
    public function test_dashboard_includes_analytical_props(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create([
            'account_type' => AccountType::Savings,
            'currency_code' => CurrencyCode::USD,
        ]);
        Transaction::factory()->for($account)->create([
            'direction' => TransactionDirection::CREDIT,
            'amount_minor' => 12500,
            'occurred_at' => now()->startOfMonth()->addDay(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->has('analytics.summary')
            ->where('analytics.summary.accounts_count', 1)
            ->where('analytics.summary.transactions_count', 1)
            ->where('analytics.currencies', ['USD'])
            ->has('analytics.accounts', 1)
            ->has('analytics.cash_flow', 6)
            ->where('analytics.cash_flow.5.currencies.USD.credits_minor', '12500'));
    }
}
