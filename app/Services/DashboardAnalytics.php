<?php

namespace App\Services;

use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

use function Illuminate\Support\enum_value;

/**
 * Builds the compact, owner-scoped dataset used by the analytical dashboard.
 */
final class DashboardAnalytics
{
    /**
     * Return account balances and six months of cash-flow aggregates.
     *
     * Balances and flows remain grouped by currency because the ledger does not
     * perform foreign-exchange conversion.
     *
     * @param  User  $owner  The authenticated user whose dashboard is built.
     * @return array{
     *     summary: array{accounts_count: int, active_accounts_count: int, transactions_count: int},
     *     currencies: list<string>,
     *     accounts: list<array{id: int, name: string, currency_code: string, balance_minor: string, is_active: bool}>,
     *     cash_flow: list<array{month: string, label: string, currencies: array<string, array{credits_minor: string, debits_minor: string, net_minor: string}>}>
     * }
     */
    public function for(User $owner): array
    {
        $accounts = Account::query()
            ->whereBelongsTo($owner)
            ->select(['id', 'name', 'currency_code', 'is_active'])
            ->withBalance()
            ->orderByDesc('is_primary')
            ->orderBy('name')
            ->get();

        /**
         * Currency values are kept separate because the ledger has no exchange
         * rate source and must not present a misleading combined total.
         *
         * @var list<string> $currencies
         */
        $currencies = array_values($accounts
            ->map(fn (Account $account): string => $this->currency($account))
            ->unique()
            ->values()
            ->all());

        $start = CarbonImmutable::now()->startOfMonth()->subMonths(5);
        $months = collect(range(0, 5))->map(
            fn (int $offset): CarbonImmutable => $start->addMonths($offset),
        );

        $transactions = Transaction::query()
            ->ownedBy($owner)
            ->with('account:id,currency_code')
            ->whereBetween('occurred_at', [$start, $start->addMonths(5)->endOfMonth()])
            ->get(['account_id', 'direction', 'amount_minor', 'occurred_at']);

        return [
            'summary' => [
                'accounts_count' => $accounts->count(),
                'active_accounts_count' => $accounts->where('is_active', true)->count(),
                'transactions_count' => $transactions->count(),
            ],
            'currencies' => $currencies,
            'accounts' => array_values($accounts->map(fn (Account $account): array => [
                'id' => $account->id,
                'name' => $account->name,
                'currency_code' => $this->currency($account),
                'balance_minor' => (string) ($account->balance_minor ?? 0),
                'is_active' => $account->is_active,
            ])->values()->all()),
            'cash_flow' => array_values($months->map(fn (CarbonImmutable $month): array => [
                'month' => $month->format('Y-m'),
                'label' => $month->format('M'),
                'currencies' => $this->monthlyCurrencyTotals($transactions, $month, $currencies),
            ])->all()),
        ];
    }

    /**
     * @param  Collection<int, Transaction>  $transactions
     * @param  list<string>  $currencies
     * @return array<string, array{credits_minor: string, debits_minor: string, net_minor: string}>
     */
    private function monthlyCurrencyTotals(Collection $transactions, CarbonImmutable $month, array $currencies): array
    {
        /**
         * Each currency gets a complete set of integer minor-unit totals.
         *
         * @var array<string, array{credits_minor: string, debits_minor: string, net_minor: string}> $totals
         */
        $totals = [];

        foreach ($currencies as $currency) {
            $monthTransactions = $transactions->filter(
                fn (Transaction $transaction): bool => $this->currency($transaction->account) === $currency
                    && $transaction->occurred_at->isSameMonth($month),
            );
            $credits = $this->sumMinor($monthTransactions, TransactionDirection::CREDIT);
            $debits = $this->sumMinor($monthTransactions, TransactionDirection::DEBIT);

            $totals[$currency] = [
                'credits_minor' => (string) $credits,
                'debits_minor' => (string) $debits,
                'net_minor' => (string) ($credits - $debits),
            ];
        }

        return $totals;
    }

    /**
     * Sum one direction of a month's transactions using integer minor units.
     *
     * @param  Collection<int, Transaction>  $transactions
     */
    private function sumMinor(Collection $transactions, TransactionDirection $direction): int
    {
        $total = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->direction === $direction) {
                $total += $transaction->amount_minor;
            }
        }

        return $total;
    }

    private function currency(Account $account): string
    {
        return is_string(enum_value($account->currency_code)) ? enum_value($account->currency_code) : '';
    }
}
