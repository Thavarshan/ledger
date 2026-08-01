<?php

namespace Tests\Unit\Transactions;

use App\Filters\TransactionFilter;
use Illuminate\Http\Request;
use Tests\TestCase;

class TransactionFilterTest extends TestCase
{
    public function test_filter_exposes_the_request_values_without_normalizing_them(): void
    {
        $filter = new TransactionFilter(Request::create('/transactions?direction=DEBIT&occurred_from=2026-08-01'));

        $this->assertSame([
            'direction' => 'DEBIT',
            'occurred_from' => '2026-08-01',
        ], $filter->getFilterables());
    }

    public function test_filter_does_not_own_query_validation(): void
    {
        $filter = new TransactionFilter(Request::create('/transactions?direction=refund'));

        $this->assertSame(['direction' => 'refund'], $filter->getFilterables());
    }
}
