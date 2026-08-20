<?php

namespace App\Http\Controllers;

use App\Actions\CreateTransaction;
use App\Actions\UpdateTransaction;
use App\Http\Requests\IndexTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionFormOptions;
use App\Services\TransactionIndexQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles Inertia CRUD requests for account-owned transactions.
 */
class TransactionController extends Controller
{
    /** Display the authenticated user's filtered transaction list. */
    #[Authorize('viewAny', Transaction::class)]
    public function index(IndexTransactionRequest $request, TransactionIndexQuery $transactions, TransactionFormOptions $options): Response
    {
        $owner = $request->user();

        if (! $owner instanceof User) {
            abort(401);
        }

        return Inertia::render('transactions/index', [
            'transactions' => TransactionResource::collection($transactions->paginate(
                $owner,
                $request->validated(),
            )),
            ...$options->for($owner),
        ]);
    }

    /** Display the transaction creation form. */
    #[Authorize('create', Transaction::class)]
    public function create(Request $request, TransactionFormOptions $options): Response
    {
        $owner = $request->user();

        if (! $owner instanceof User) {
            abort(401);
        }

        return Inertia::render('transactions/create', $options->for($owner));
    }

    /** Create a transaction on an active owned account. */
    #[Authorize('create', Transaction::class)]
    public function store(StoreTransactionRequest $request, CreateTransaction $create): RedirectResponse
    {
        $owner = $request->user();

        if (! $owner instanceof User) {
            abort(401);
        }

        $transaction = $create->handle($owner, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction created.')]);

        return to_route('transactions.show', $transaction);
    }

    /** Display one transaction with its account option data. */
    #[Authorize('view', 'transaction')]
    public function show(Transaction $transaction): Response
    {
        return Inertia::render('transactions/show', [
            'transaction' => TransactionResource::make($transaction->load('account:id,name,currency_code')),
        ]);
    }

    /** Display the transaction editing form. */
    #[Authorize('update', 'transaction')]
    public function edit(Request $request, Transaction $transaction, TransactionFormOptions $options): Response
    {
        $owner = $request->user();

        if (! $owner instanceof User) {
            abort(401);
        }

        return Inertia::render('transactions/edit', [
            ...$options->for($owner, $transaction),
            'transaction' => TransactionResource::make($transaction->load('account:id,name,currency_code')),
        ]);
    }

    /** Apply validated transaction changes and any safe account reassignment. */
    #[Authorize('update', 'transaction')]
    public function update(UpdateTransactionRequest $request, Transaction $transaction, UpdateTransaction $update): RedirectResponse
    {
        $transaction = $update->handle($transaction, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction updated.')]);

        return to_route('transactions.show', $transaction);
    }

    /** Delete a transaction owned through the authenticated user's account. */
    #[Authorize('delete', 'transaction')]
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction deleted.')]);

        return to_route('transactions.index');
    }
}
