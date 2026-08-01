<?php

namespace Tests\Feature\Transactions;

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_from_every_transaction_route(): void
    {
        $transaction = Transaction::factory()->create();
        $routes = [
            ['get', route('transactions.index')],
            ['get', route('transactions.create')],
            ['post', route('transactions.store')],
            ['get', route('transactions.show', $transaction)],
            ['get', route('transactions.edit', $transaction)],
            ['put', route('transactions.update', $transaction)],
            ['delete', route('transactions.destroy', $transaction)],
        ];

        foreach ($routes as [$method, $url]) {
            $this->{$method}($url)->assertRedirect(route('login'));
        }
    }

    public function test_owner_can_view_index_with_filters_search_sort_and_pagination(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['name' => 'Main account']);
        $match = Transaction::factory()->forAccount($account)->credit()->create([
            'description' => 'Salary payment',
            'occurred_at' => '2026-08-01 10:00:00',
        ]);
        Transaction::factory()->forAccount($account)->debit()->create(['description' => 'Rent payment']);
        Transaction::factory()->create(['description' => 'Other user salary']);

        $response = $this->actingAs($user)
            ->withHeaders($this->inertiaHeaders())
            ->get(route('transactions.index', [
                'account_id' => $account->id,
                'direction' => 'CREDIT',
                'search' => 'salary',
                'occurred_from' => '2026-08-01',
                'occurred_to' => '2026-08-01',
                'sort' => 'OCCURRED_AT:ASC',
                'page' => 1,
            ]));

        $response->assertOk()
            ->assertJsonPath('component', 'transactions/index')
            ->assertJsonCount(1, 'props.transactions.data')
            ->assertJsonPath('props.transactions.data.0.id', $match->id)
            ->assertJsonPath('props.transactions.data.0.amount_minor', (string) $match->amount_minor)
            ->assertJsonPath('props.transactions.meta.path', route('transactions.index'));
    }

    public function test_owner_can_view_create_show_and_edit_contracts(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $transaction = Transaction::factory()->forAccount($account)->create();

        $this->actingAs($user)->withHeaders($this->inertiaHeaders())
            ->get(route('transactions.create'))
            ->assertOk()
            ->assertJsonPath('component', 'transactions/create')
            ->assertJsonPath('props.directions.0', 'credit');

        foreach ([
            route('transactions.show', $transaction) => 'transactions/show',
            route('transactions.edit', $transaction) => 'transactions/edit',
        ] as $url => $component) {
            $this->actingAs($user)->withHeaders($this->inertiaHeaders())
                ->get($url)
                ->assertOk()
                ->assertJsonPath('component', $component)
                ->assertJsonPath('props.transaction.data.account.currency_code', 'LKR');
        }
    }

    public function test_edit_includes_its_inactive_account_without_exposing_other_inactive_accounts(): void
    {
        $user = User::factory()->create();
        $inactive = Account::factory()->for($user)->inactive()->create(['name' => 'Archived account']);
        $active = Account::factory()->for($user)->create(['name' => 'Operating account']);
        $transaction = Transaction::factory()->forAccount($inactive)->create();

        $edit = $this->actingAs($user)->withHeaders($this->inertiaHeaders())
            ->get(route('transactions.edit', $transaction));
        $create = $this->actingAs($user)->withHeaders($this->inertiaHeaders())
            ->get(route('transactions.create'));

        $edit->assertOk()
            ->assertJsonCount(2, 'props.accounts')
            ->assertJsonFragment(['id' => $inactive->id, 'name' => 'Archived account']);
        $create->assertOk()
            ->assertJsonCount(1, 'props.accounts')
            ->assertJsonPath('props.accounts.0.id', $active->id);
    }

    public function test_owner_can_create_update_and_delete_transactions(): void
    {
        $user = User::factory()->create();
        $source = Account::factory()->for($user)->create();
        $target = Account::factory()->for($user)->create();

        $create = $this->actingAs($user)->post(route('transactions.store'), $this->transactionData([
            'account_id' => $source->id,
            'direction' => 'CREDIT',
            'amount_minor' => '9000',
        ]));
        $transaction = Transaction::query()->latest('id')->firstOrFail();

        $create->assertRedirect(route('transactions.show', $transaction));
        $this->assertSame('credit', $transaction->direction->value);

        $update = $this->actingAs($user)->put(route('transactions.update', $transaction), [
            'account_id' => $target->id,
            'direction' => 'debit',
            'amount_minor' => '4500',
            'description' => 'Updated purchase',
            'occurred_at' => '2026-08-02T08:00:00Z',
        ]);

        $transaction->refresh();
        $update->assertRedirect(route('transactions.show', $transaction));
        $this->assertSame($target->id, $transaction->account_id);
        $this->assertSame('debit', $transaction->direction->value);

        $delete = $this->actingAs($user)->delete(route('transactions.destroy', $transaction));

        $delete->assertRedirect(route('transactions.index'));
        $this->assertModelMissing($transaction);
    }

    public function test_owner_can_update_a_transaction_on_its_inactive_account_without_reassigning_it(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->inactive()->create();
        $transaction = Transaction::factory()->forAccount($account)->create();

        $response = $this->actingAs($user)->put(route('transactions.update', $transaction), [
            'account_id' => $account->id,
            'description' => 'Corrected historical entry',
        ]);

        $response->assertRedirect(route('transactions.show', $transaction));
        $this->assertSame('Corrected historical entry', $transaction->refresh()->description);
        $this->assertSame($account->id, $transaction->account_id);
    }

    public function test_invalid_and_inactive_account_inputs_are_rejected(): void
    {
        $user = User::factory()->create();
        $inactive = Account::factory()->for($user)->inactive()->create();
        $otherAccount = Account::factory()->create();

        $this->actingAs($user)->post(route('transactions.store'), $this->transactionData([
            'account_id' => $inactive->id,
        ]))->assertSessionHasErrors('account_id');

        $this->actingAs($user)->post(route('transactions.store'), $this->transactionData([
            'account_id' => $otherAccount->id,
        ]))->assertSessionHasErrors('account_id');

        $this->actingAs($user)->post(route('transactions.store'), $this->transactionData([
            'account_id' => $inactive->id,
            'direction' => 'refund',
            'amount_minor' => 0,
            'description' => str_repeat('x', 256),
            'occurred_at' => 'not-a-date',
        ]))->assertSessionHasErrors([
            'account_id', 'direction', 'amount_minor', 'description', 'occurred_at',
        ]);
    }

    public function test_transaction_index_rejects_an_account_filter_not_owned_by_the_user(): void
    {
        $user = User::factory()->create();
        $otherAccount = Account::factory()->create();

        $this->actingAs($user)
            ->get(route('transactions.index', ['account_id' => $otherAccount->id]))
            ->assertSessionHasErrors('account_id');
    }

    public function test_non_owner_cannot_view_update_or_delete_a_transaction(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $transaction = Transaction::factory()->for(Account::factory()->for($owner))->create();

        $this->actingAs($otherUser)->get(route('transactions.show', $transaction))->assertForbidden();
        $this->actingAs($otherUser)->get(route('transactions.edit', $transaction))->assertForbidden();
        $this->actingAs($otherUser)->put(route('transactions.update', $transaction), [])->assertForbidden();
        $this->actingAs($otherUser)->delete(route('transactions.destroy', $transaction))->assertForbidden();
    }

    /** @param array<string, mixed> $overrides */
    private function transactionData(array $overrides = []): array
    {
        return array_merge([
            'account_id' => Account::factory()->create()->id,
            'direction' => 'debit',
            'amount_minor' => '1250',
            'description' => 'Office supplies',
            'reference' => 'TXN-001',
            'notes' => 'Receipt attached later',
            'occurred_at' => '2026-08-01T10:15:00Z',
        ], $overrides);
    }

    /** @return array<string, string> */
    private function inertiaHeaders(): array
    {
        return [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-Inertia-Version' => app(HandleInertiaRequests::class)->version(Request::create('/')),
        ];
    }
}
