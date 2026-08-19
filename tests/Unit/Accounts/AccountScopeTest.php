<?php

namespace Tests\Unit\Accounts;

use App\Concerns\HasSearch;
use App\Concerns\HasSorting;
use App\Models\Account;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class AccountScopeTest extends TestCase
{
    public function test_search_scope_handles_empty_and_configured_terms(): void
    {
        $blank = Account::query()->search(null);
        $this->assertSame([], $blank->getQuery()->wheres);

        $configured = Account::query()->search('bank');
        $this->assertCount(1, $configured->getQuery()->wheres);

        $emptyModel = new class extends Model
        {
            use HasSearch;

            protected function searchableColumns(): array
            {
                return [];
            }
        };

        $this->assertSame([], $emptyModel->newQuery()->search('bank')->getQuery()->wheres);
    }

    public function test_sort_scope_handles_default_valid_and_unsupported_values(): void
    {
        $default = Account::query()->sorted(null)->getQuery()->orders;
        $this->assertSame('created_at', $default[0]['column']);
        $this->assertSame('desc', $default[0]['direction']);

        $descending = Account::query()->sorted('NAME:DESC')->getQuery()->orders;
        $this->assertSame(['column' => 'name', 'direction' => 'desc'], $descending[0]);

        $ascending = Account::query()->sorted('currency_code:unexpected')->getQuery()->orders;
        $this->assertSame(['column' => 'currency_code', 'direction' => 'asc'], $ascending[0]);

        $unsupported = Account::query()->sorted('password:desc')->getQuery()->orders;
        $this->assertSame('created_at', $unsupported[0]['column']);
    }

    public function test_sort_scope_handles_a_model_without_sortable_columns(): void
    {
        $model = new class extends Model
        {
            use HasSorting;

            protected function sortableColumns(): array
            {
                return [];
            }
        };

        $orders = $model->newQuery()->sorted('name:asc')->getQuery()->orders;

        $this->assertSame('created_at', $orders[0]['column']);
    }
}
