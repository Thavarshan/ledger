<?php

namespace Tests\Unit\Transactions;

use App\Concerns\HasSearch;
use App\Concerns\HasSorting;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class TransactionScopeTest extends TestCase
{
    public function test_search_scope_handles_empty_and_configured_terms(): void
    {
        $this->assertSame([], Transaction::query()->search(null)->getQuery()->wheres);
        $this->assertCount(1, Transaction::query()->search('rent')->getQuery()->wheres);

        $model = new class extends Model
        {
            use HasSearch;

            protected function searchableColumns(): array
            {
                return [];
            }
        };

        $this->assertSame([], $model->newQuery()->search('rent')->getQuery()->wheres);
    }

    public function test_sort_scope_handles_default_valid_and_unsupported_values(): void
    {
        $default = Transaction::query()->sorted(null)->getQuery()->orders;
        $this->assertSame('occurred_at', $default[0]['column']);
        $this->assertSame('desc', $default[0]['direction']);

        $descending = Transaction::query()->sorted('AMOUNT_MINOR:DESC')->getQuery()->orders;
        $this->assertSame(['column' => 'amount_minor', 'direction' => 'desc'], $descending[0]);

        $ascending = Transaction::query()->sorted('description:unexpected')->getQuery()->orders;
        $this->assertSame(['column' => 'description', 'direction' => 'asc'], $ascending[0]);

        $unsupported = Transaction::query()->sorted('password:desc')->getQuery()->orders;
        $this->assertSame('occurred_at', $unsupported[0]['column']);
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

        $this->assertSame('created_at', $model->newQuery()->sorted('name:asc')->getQuery()->orders[0]['column']);
    }
}
