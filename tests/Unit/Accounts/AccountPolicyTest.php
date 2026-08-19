<?php

namespace Tests\Unit\Accounts;

use App\Models\Account;
use App\Models\User;
use App\Policies\AccountPolicy;
use PHPUnit\Framework\TestCase;

class AccountPolicyTest extends TestCase
{
    public function test_view_any_and_create_are_allowed(): void
    {
        $policy = new AccountPolicy;
        $user = new User;
        $user->setAttribute('id', 1);

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->create($user));
    }

    public function test_owner_can_manage_an_account(): void
    {
        $policy = new AccountPolicy;
        $user = new User;
        $user->setAttribute('id', 1);
        $account = new Account;
        $account->setAttribute('user_id', 1);

        $this->assertTrue($policy->view($user, $account));
        $this->assertTrue($policy->update($user, $account));
        $this->assertTrue($policy->delete($user, $account));
    }

    public function test_non_owner_cannot_manage_an_account(): void
    {
        $policy = new AccountPolicy;
        $user = new User;
        $user->setAttribute('id', 1);
        $account = new Account;
        $account->setAttribute('user_id', 2);

        $this->assertFalse($policy->view($user, $account));
        $this->assertFalse($policy->update($user, $account));
        $this->assertFalse($policy->delete($user, $account));
    }
}
