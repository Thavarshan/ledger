<?php

namespace App\Http\Controllers;

use App\Enums\CurrencyCode;
use App\Filters\AccountFilter;
use App\Http\Requests\IndexAccountRequest;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles Inertia CRUD requests for user-owned bank accounts.
 */
class AccountController extends Controller
{
    public function __construct(private readonly AccountService $accounts)
    {
    }

    /**
     * Display a listing of the resource.
     */
    #[Authorize('viewAny', Account::class)]
    public function index(
        IndexAccountRequest $request,
        AccountFilter $filter,
    ): Response {
        $accounts = Account::query()
            ->whereBelongsTo($request->user())
            ->filter($filter)
            ->search($request->string('search')->toString())
            ->sorted($request->string('sort')->toString())
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('accounts/index', [
            'accounts' => AccountResource::collection($accounts),
            'accountTypes' => Account::TYPES,
            'currencies' => CurrencyCode::values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    #[Authorize('create', Account::class)]
    public function create(): Response
    {
        return Inertia::render('accounts/create', $this->formOptions());
    }

    /**
     * Store a newly created resource in storage.
     */
    #[Authorize('create', Account::class)]
    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $account = $this->accounts->create(
            $request->user(),
            $request->validated(),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Account created.'),
        ]);

        return to_route('accounts.show', $account);
    }

    /**
     * Display the specified resource.
     */
    #[Authorize('view', 'account')]
    public function show(Account $account): Response
    {
        return Inertia::render('accounts/show', [
            'account' => AccountResource::make($account),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    #[Authorize('update', 'account')]
    public function edit(Account $account): Response
    {
        return Inertia::render('accounts/edit', [
            ...$this->formOptions(),
            'account' => AccountResource::make($account),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    #[Authorize('update', 'account')]
    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $this->accounts->update($account, $request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Account updated.'),
        ]);

        return to_route('accounts.show', $account);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[Authorize('delete', 'account')]
    public function destroy(Account $account): RedirectResponse
    {
        $account->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Account deleted.'),
        ]);

        return to_route('accounts.index');
    }

    /**
     * Get the options required by the account create and edit forms.
     *
     * @return array{accountTypes: list<string>, currencies: list<string|int>}
     */
    private function formOptions(): array
    {
        return [
            'accountTypes' => Account::TYPES,
            'currencies' => CurrencyCode::values(),
        ];
    }
}
