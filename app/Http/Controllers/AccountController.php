<?php

namespace App\Http\Controllers;

use App\Actions\CreateAccount;
use App\Actions\DeleteAccount;
use App\Actions\UpdateAccount;
use App\Filters\AccountFilter;
use App\Http\Requests\IndexAccountRequest;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\AccountSummaryResource;
use App\Models\Account;
use App\Services\AccountFormOptions;
use App\Services\AccountIndexQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles Inertia CRUD requests for user-owned bank accounts.
 */
class AccountController extends Controller
{
    #[Authorize('viewAny', Account::class)]
    public function index(IndexAccountRequest $request, AccountIndexQuery $accounts, AccountFormOptions $options): Response
    {
        return Inertia::render('accounts/index', [
            'accounts' => AccountSummaryResource::collection($accounts->paginate(
                $request->user(),
                new AccountFilter($request),
                $request->string('search')->toString(),
                $request->string('sort')->toString(),
            )),
            ...$options->all(),
        ]);
    }

    #[Authorize('create', Account::class)]
    public function create(AccountFormOptions $options): Response
    {
        return Inertia::render('accounts/create', $options->all());
    }

    #[Authorize('create', Account::class)]
    public function store(StoreAccountRequest $request, CreateAccount $create): RedirectResponse
    {
        $account = $create->handle($request->user(), $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Account created.')]);

        return to_route('accounts.show', $account);
    }

    #[Authorize('view', 'account')]
    public function show(Account $account): Response
    {
        return Inertia::render('accounts/show', ['account' => AccountResource::make($account)]);
    }

    #[Authorize('update', 'account')]
    public function edit(Account $account, AccountFormOptions $options): Response
    {
        return Inertia::render('accounts/edit', [
            ...$options->all(),
            'account' => AccountResource::make($account),
        ]);
    }

    #[Authorize('update', 'account')]
    public function update(UpdateAccountRequest $request, Account $account, UpdateAccount $update): RedirectResponse
    {
        $account = $update->handle($request->user(), $account, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Account updated.')]);

        return to_route('accounts.show', $account);
    }

    #[Authorize('delete', 'account')]
    public function destroy(Account $account, DeleteAccount $delete): RedirectResponse
    {
        $delete->handle($account);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Account deleted.')]);

        return to_route('accounts.index');
    }
}
