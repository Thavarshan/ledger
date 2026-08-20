<?php

namespace Database\Seeders;

use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Seeds a searchable, filterable transaction history for the demo accounts.
 */
class TransactionSeeder extends Seeder
{
    /**
     * Create or refresh the demo transaction history.
     *
     * Named references make each row idempotent, while the generated tail gives
     * search, filtering, sorting, and pagination realistic data volume.
     */
    public function run(): void
    {
        $user = User::query()->where('email', DemoUserSeeder::EMAIL)->firstOrFail();
        $accounts = $user->accounts()->get()->keyBy('name');

        foreach ($this->transactions() as $transaction) {
            $account = $accounts->get($transaction['account']);

            if (! $account instanceof Account) {
                continue;
            }

            Transaction::query()->updateOrCreate(
                ['account_id' => $account->id, 'reference' => $transaction['reference']],
                [
                    'direction' => $transaction['direction'],
                    'amount_minor' => $transaction['amount_minor'],
                    'description' => $transaction['description'],
                    'notes' => $transaction['notes'],
                    'occurred_at' => $transaction['occurred_at'],
                ],
            );
        }
    }

    /**
     * Return the hand-written transactions that explain the demo account data.
     *
     * These entries provide recognizable examples for the dashboard before the
     * deterministic bulk entries are appended.
     *
     * @return list<array{account: string, direction: TransactionDirection, amount_minor: int, description: string, reference: string, notes: string|null, occurred_at: string}>
     */
    private function transactions(): array
    {
        return [
            ['account' => 'Everyday LKR', 'direction' => TransactionDirection::CREDIT, 'amount_minor' => 28500000, 'description' => 'July salary', 'reference' => 'DEMO-LKR-001', 'notes' => 'Monthly salary deposit.', 'occurred_at' => '2026-07-31T03:30:00Z'],
            ['account' => 'Everyday LKR', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 1850000, 'description' => 'Apartment rent', 'reference' => 'DEMO-LKR-002', 'notes' => 'August rent payment.', 'occurred_at' => '2026-08-01T04:15:00Z'],
            ['account' => 'Everyday LKR', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 12450, 'description' => 'Keells grocery run', 'reference' => 'DEMO-LKR-003', 'notes' => null, 'occurred_at' => '2026-08-01T12:40:00Z'],
            ['account' => 'Everyday LKR', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 42000, 'description' => 'Ceylon Electricity Board', 'reference' => 'DEMO-LKR-004', 'notes' => 'Utility bill.', 'occurred_at' => '2026-07-29T08:00:00Z'],
            ['account' => 'Everyday LKR', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 7900, 'description' => 'Dialog mobile recharge', 'reference' => 'DEMO-LKR-005', 'notes' => null, 'occurred_at' => '2026-07-28T11:30:00Z'],
            ['account' => 'Everyday LKR', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 320000, 'description' => 'Transfer to fixed deposit', 'reference' => 'DEMO-LKR-006', 'notes' => 'Monthly savings allocation.', 'occurred_at' => '2026-07-25T05:00:00Z'],
            ['account' => 'Everyday LKR', 'direction' => TransactionDirection::CREDIT, 'amount_minor' => 450000, 'description' => 'Refund from airline', 'reference' => 'DEMO-LKR-007', 'notes' => 'Cancelled flight refund.', 'occurred_at' => '2026-06-18T09:20:00Z'],
            ['account' => 'Everyday LKR', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 15600, 'description' => 'Uber trip', 'reference' => 'DEMO-LKR-008', 'notes' => null, 'occurred_at' => '2026-06-12T14:45:00Z'],
            ['account' => 'Business Current LKR', 'direction' => TransactionDirection::CREDIT, 'amount_minor' => 17500000, 'description' => 'Consulting invoice payment', 'reference' => 'DEMO-BIZ-001', 'notes' => 'Client: Northstar Labs.', 'occurred_at' => '2026-07-30T06:00:00Z'],
            ['account' => 'Business Current LKR', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 650000, 'description' => 'Contractor payment', 'reference' => 'DEMO-BIZ-002', 'notes' => 'July design retainer.', 'occurred_at' => '2026-07-26T07:30:00Z'],
            ['account' => 'Business Current LKR', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 125000, 'description' => 'Coworking membership', 'reference' => 'DEMO-BIZ-003', 'notes' => null, 'occurred_at' => '2026-07-01T04:30:00Z'],
            ['account' => 'Business Current LKR', 'direction' => TransactionDirection::CREDIT, 'amount_minor' => 9500000, 'description' => 'Website project deposit', 'reference' => 'DEMO-BIZ-004', 'notes' => 'Fifty percent advance.', 'occurred_at' => '2026-06-20T08:15:00Z'],
            ['account' => 'Wise USD Wallet', 'direction' => TransactionDirection::CREDIT, 'amount_minor' => 320000, 'description' => 'US client payment', 'reference' => 'DEMO-USD-001', 'notes' => 'June retainer.', 'occurred_at' => '2026-07-30T13:00:00Z'],
            ['account' => 'Wise USD Wallet', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 2900, 'description' => 'GitHub Copilot subscription', 'reference' => 'DEMO-USD-002', 'notes' => null, 'occurred_at' => '2026-07-15T10:00:00Z'],
            ['account' => 'Wise USD Wallet', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 1200, 'description' => 'Figma subscription', 'reference' => 'DEMO-USD-003', 'notes' => null, 'occurred_at' => '2026-07-10T10:00:00Z'],
            ['account' => 'Barclays GBP Account', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 6800, 'description' => 'London Underground travel', 'reference' => 'DEMO-GBP-001', 'notes' => 'Business trip.', 'occurred_at' => '2026-06-05T08:45:00Z'],
            ['account' => 'Barclays GBP Account', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 14500, 'description' => 'Hotel reservation', 'reference' => 'DEMO-GBP-002', 'notes' => 'Travel accommodation.', 'occurred_at' => '2026-06-04T16:30:00Z'],
            ['account' => 'Fixed Deposit 2025', 'direction' => TransactionDirection::CREDIT, 'amount_minor' => 320000, 'description' => 'Fixed deposit interest', 'reference' => 'DEMO-FD-001', 'notes' => 'Quarterly interest credit.', 'occurred_at' => '2026-07-01T03:00:00Z'],
            ['account' => 'Archived Sampath Savings', 'direction' => TransactionDirection::DEBIT, 'amount_minor' => 350000, 'description' => 'Final balance transfer', 'reference' => 'DEMO-ARCH-001', 'notes' => 'Transferred before account closure.', 'occurred_at' => '2025-12-30T05:00:00Z'],
            ['account' => 'Archived Sampath Savings', 'direction' => TransactionDirection::CREDIT, 'amount_minor' => 1850, 'description' => 'Year-end interest', 'reference' => 'DEMO-ARCH-002', 'notes' => 'Historical entry retained for reporting.', 'occurred_at' => '2025-12-31T03:00:00Z'],
            ...$this->generatedTransactions(),
        ];
    }

    /**
     * Generate 980 deterministic transactions to produce a realistic, rerunnable
     * thousand-row dataset for search, filtering, sorting, and pagination.
     *
     * @return list<array{account: string, direction: TransactionDirection, amount_minor: int, description: string, reference: string, notes: string|null, occurred_at: string}>
     */
    private function generatedTransactions(): array
    {
        $accounts = [
            'Everyday LKR',
            'Business Current LKR',
            'Wise USD Wallet',
            'Barclays GBP Account',
            'Fixed Deposit 2025',
        ];
        $creditDescriptions = [
            'Client payment received',
            'Salary deposit',
            'Bank interest credit',
            'Refund received',
            'Consulting invoice payment',
        ];
        $debitDescriptions = [
            'Grocery shopping',
            'Utility bill payment',
            'Software subscription',
            'Transport expense',
            'Restaurant payment',
            'Mobile service payment',
            'Insurance premium',
        ];
        $amounts = [1500, 3500, 7500, 12000, 18500, 25000, 48000, 95000, 175000, 320000];
        $start = CarbonImmutable::parse('2026-07-31T18:00:00Z');
        $transactions = [];

        for ($number = 21; $number <= 1000; $number++) {
            $direction = $number % 5 === 0 ? TransactionDirection::CREDIT : TransactionDirection::DEBIT;
            $description = $direction === TransactionDirection::CREDIT
                ? $creditDescriptions[$number % count($creditDescriptions)]
                : $debitDescriptions[$number % count($debitDescriptions)];

            $transactions[] = [
                'account' => $accounts[$number % count($accounts)],
                'direction' => $direction,
                'amount_minor' => $amounts[$number % count($amounts)],
                'description' => $description,
                'reference' => sprintf('DEMO-BULK-%04d', $number),
                'notes' => $number % 4 === 0 ? 'Generated demo ledger entry.' : null,
                'occurred_at' => $start->subHours($number * 6)->toIso8601String(),
            ];
        }

        return $transactions;
    }
}
