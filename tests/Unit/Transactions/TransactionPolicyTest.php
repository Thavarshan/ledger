<?php

namespace Tests\Unit\Transactions;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Policies\TransactionPolicy;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TransactionPolicyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_view_any_and_create_are_allowed(): void
    {
        $policy = new TransactionPolicy;
        $user = new User;

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->create($user));
    }

    public function test_owner_can_manage_a_transaction(): void
    {
        $account = Account::factory()->create();
        $transaction = Transaction::factory()->forAccount($account)->create();
        $policy = new TransactionPolicy;

        foreach (['view', 'update', 'delete'] as $ability) {
            $this->assertTrue($policy->{$ability}($account->user, $transaction));
        }
    }

    public function test_non_owner_cannot_manage_a_transaction(): void
    {
        $account = Account::factory()->create();
        $transaction = Transaction::factory()->forAccount($account)->create();
        $otherUser = User::factory()->create();
        $policy = new TransactionPolicy;

        foreach (['view', 'update', 'delete'] as $ability) {
            $this->assertFalse($policy->{$ability}($otherUser, $transaction));
        }
    }
}
