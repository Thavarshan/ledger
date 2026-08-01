<?php

namespace Tests\Unit\Accounts;

use App\Http\Requests\IndexAccountRequest;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Tests\TestCase;

class AccountRequestTest extends TestCase
{
    public function test_index_request_defines_search_and_sort_rules(): void
    {
        $rules = (new IndexAccountRequest)->rules();

        $this->assertArrayHasKey('search', $rules);
        $this->assertArrayHasKey('sort', $rules);
    }

    public function test_store_and_update_requests_use_account_rules(): void
    {
        $storeRules = (new StoreAccountRequest)->rules();
        $updateRules = (new UpdateAccountRequest)->rules();

        $this->assertContains('required', $storeRules['name']);
        $this->assertContains('sometimes', $updateRules['name']);
        $this->assertContains('required', $updateRules['name']);
    }
}
