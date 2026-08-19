<?php

namespace Tests\Unit\Accounts;

use App\Services\AccountIndexQuery;
use Database\Factories\AccountFactory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AccountIndexQueryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_account_criteria_are_applied_without_leaking_other_users(): void
    {
        $match = AccountFactory::new()->create([
            'name' => 'Operating account',
            'account_type' => 'savings',
            'country_code' => 'LK',
            'currency_code' => 'LKR',
        ]);
        $user = $match->user;

        AccountFactory::new()->for($user)->create([
            'name' => 'Other account',
            'account_type' => 'current',
        ]);
        AccountFactory::new()->create(['name' => 'Operating account']);

        $accounts = app(AccountIndexQuery::class)->paginate($user, [
            'account_type' => 'savings',
            'country_code' => 'LK',
            'currency_code' => 'LKR',
            'search' => 'Operating',
            'sort' => 'name:asc',
        ]);

        $this->assertSame([$match->id], $accounts->getCollection()->modelKeys());
    }

    public function test_invalid_sort_falls_back_to_newest_accounts(): void
    {
        $oldest = AccountFactory::new()->create();
        $user = $oldest->user;
        $newest = AccountFactory::new()->for($user)->create();

        $accounts = app(AccountIndexQuery::class)->paginate($user, ['sort' => 'not_allowed:asc']);

        $this->assertSame([$newest->id, $oldest->id], $accounts->getCollection()->modelKeys());
    }
}
