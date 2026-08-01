<?php

namespace Tests\Unit\Transactions;

use App\Http\Requests\IndexTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Tests\TestCase;

class TransactionRequestTest extends TestCase
{
    public function test_index_request_defines_search_and_safe_sort_rules(): void
    {
        $rules = (new IndexTransactionRequest)->rules();

        $this->assertArrayHasKey('search', $rules);
        $this->assertArrayHasKey('sort', $rules);
    }

    public function test_store_and_update_requests_define_transaction_rules(): void
    {
        $store = (new StoreTransactionRequest)->rules();
        $update = (new UpdateTransactionRequest)->rules();

        foreach (['account_id', 'direction', 'amount_minor', 'description', 'occurred_at'] as $field) {
            $this->assertArrayHasKey($field, $store);
            $this->assertArrayHasKey($field, $update);
        }

        $this->assertContains('required', $store['direction']);
        $this->assertContains('sometimes', $update['direction']);
        $this->assertCount(3, $store['direction']);
    }
}
