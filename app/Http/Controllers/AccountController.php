<?php

namespace App\Http\Controllers;

use App\Actions\CreateAccount;
use App\Actions\UpdateAccount;
use App\Enums\AccountType;
use App\Enums\CurrencyCode;
use App\Http\Requests\IndexAccountRequest;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\AccountSummaryResource;
use App\Models\Account;
use App\Models\User;
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
    /**
     * Display the authenticated user's paginated account list.
     *
     * Filtering, sorting, and balance aggregation remain in the query service
     * so the controller only coordinates the request and Inertia response.
     */
    #[Authorize('viewAny', Account::class)]
    public function index(IndexAccountRequest $request, AccountIndexQuery $accounts): Response
    {
        $owner = $request->user();

        if (! $owner instanceof User) {
            abort(401);
        }

        return Inertia::render('accounts/index', [
            'accounts' => AccountSummaryResource::collection($accounts->paginate(
                $owner,
                $request->validated(),
            )),
            ...$this->formOptions(),
        ]);
    }

    /**
     * Display the account creation form and its supported enum options.
     */
    #[Authorize('create', Account::class)]
    public function create(): Response
    {
        return Inertia::render('accounts/create', $this->formOptions());
    }

    /**
     * Create an account owned by the authenticated user.
     *
     * The action manages primary-account replacement and sensitive-field
     * preparation before persistence.
     */
    #[Authorize('create', Account::class)]
    public function store(StoreAccountRequest $request, CreateAccount $create): RedirectResponse
    {
        $owner = $request->user();

        if (! $owner instanceof User) {
            abort(401);
        }

        $account = $create->handle($owner, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Account created.')]);

        return to_route('accounts.show', $account);
    }

    /**
     * Display one account with its derived balance.
     */
    #[Authorize('view', 'account')]
    public function show(Account $account): Response
    {
        return Inertia::render('accounts/show', ['account' => AccountResource::make($account->loadBalance())]);
    }

    /**
     * Display the account editing form with the current account values.
     */
    #[Authorize('update', 'account')]
    public function edit(Account $account): Response
    {
        return Inertia::render('accounts/edit', [
            ...$this->formOptions(),
            'account' => AccountResource::make($account->loadBalance()),
        ]);
    }

    /**
     * Apply validated changes while preserving account invariants.
     *
     * Authorization is performed by the route attribute before this method is
     * called, and the action handles the cross-account domain rules.
     */
    #[Authorize('update', 'account')]
    public function update(UpdateAccountRequest $request, Account $account, UpdateAccount $update): RedirectResponse
    {
        $account = $update->handle($account, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Account updated.')]);

        return to_route('accounts.show', $account);
    }

    /**
     * Delete an account owned by the authenticated user.
     */
    #[Authorize('delete', 'account')]
    public function destroy(Account $account): RedirectResponse
    {
        $account->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Account deleted.')]);

        return to_route('accounts.index');
    }

    /**
     * @return array{accountTypes: list<string|int>, currencies: list<string|int>}
     */
    private function formOptions(): array
    {
        return [
            'accountTypes' => AccountType::values(),
            'currencies' => CurrencyCode::values(),
        ];
    }
}
