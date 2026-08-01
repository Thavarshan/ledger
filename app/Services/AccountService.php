<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Creates and updates user-owned bank accounts.
 */
final class AccountService
{
    /**
     * Create an account for the given owner.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(User $owner, array $data): Account
    {
        $data = $this->prepare($data);

        return DB::transaction(function () use ($owner, $data): Account {
            if (($data['is_primary'] ?? false) === true) {
                $this->clearPrimaryAccount($owner->id);
            }

            return $owner->accounts()->create($data);
        });
    }

    /**
     * Update an existing account.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Account $account, array $data): Account
    {
        $data = $this->prepare($data);

        return DB::transaction(function () use ($account, $data): Account {
            if (($data['is_primary'] ?? false) === true) {
                $this->clearPrimaryAccount($account->user_id, $account->id);
            }

            $account->update($data);

            return $account->refresh();
        });
    }

    /**
     * Derive display metadata from the sensitive account number.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function prepare(array $data): array
    {
        unset($data['account_number_last4']);

        if (array_key_exists('account_number', $data)) {
            $data['account_number_last4'] = Str::substr((string) $data['account_number'], -4);
        }

        return $data;
    }

    /**
     * Remove the primary designation from a user's other accounts.
     */
    private function clearPrimaryAccount(int $userId, ?int $exceptAccountId = null): void
    {
        Account::query()
            ->where('user_id', $userId)
            ->where('is_primary', true)
            ->when($exceptAccountId, fn ($query) => $query->whereKeyNot($exceptAccountId))
            ->update(['is_primary' => false]);
    }
}
