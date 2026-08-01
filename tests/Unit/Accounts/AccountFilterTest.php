<?php

namespace Tests\Unit\Accounts;

use App\Filters\AccountFilter;
use Illuminate\Http\Request;
use Tests\TestCase;

class AccountFilterTest extends TestCase
{
    public function test_filter_values_are_applied_without_mutating_the_request(): void
    {
        $request = Request::create('/accounts?country_code=lk&currency_code=lkr');
        $filter = new AccountFilter($request);

        $this->assertSame([
            'country_code' => 'lk',
            'currency_code' => 'lkr',
        ], $filter->getFilterables());
    }

    public function test_filter_does_not_own_query_validation(): void
    {
        $request = Request::create('/accounts?currency_code=XXX');
        $filter = new AccountFilter($request);

        $this->assertSame(['currency_code' => 'XXX'], $filter->getFilterables());
    }
}
