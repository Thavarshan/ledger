<?php

namespace Tests\Unit\Accounts;

use App\Filters\AccountFilter;
use App\Models\Account;
use Illuminate\Http\Request;
use Tests\TestCase;

class AccountFilterTest extends TestCase
{
    public function test_filter_values_are_normalized_for_case_insensitive_query_parameters(): void
    {
        $request = Request::create('/accounts?country_code=lk&currency_code=lkr');
        $filter = new AccountFilter($request);

        $this->assertSame([
            'country_code' => 'LK',
            'currency_code' => 'LKR',
        ], $filter->getFilterables());
    }

    public function test_invalid_filter_values_are_rejected_by_the_filter(): void
    {
        $request = Request::create('/accounts?currency_code=XXX');
        $filter = new AccountFilter($request);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $filter->apply(Account::query());
    }
}
