<?php

namespace App\Http\Controllers;

use App\Actions\CreateTransaction;
use App\Actions\DeleteTransaction;
use App\Actions\UpdateTransaction;
use App\Filters\TransactionFilter;
use App\Http\Requests\IndexTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
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
    #[Authorize('viewAny', Transaction::class)]
    public function index(IndexTransactionRequest $request, TransactionIndexQuery $transactions, TransactionFormOptions $options): Response
    {
        return Inertia::render('transactions/index', [
            'transactions' => TransactionResource::collection($transactions->paginate(
                $request->user(),
                new TransactionFilter($request),
                $request->string('search')->toString(),
                $request->string('sort')->toString(),
            )),
            ...$options->for($request->user()),
        ]);
    }

    #[Authorize('create', Transaction::class)]
    public function create(Request $request, TransactionFormOptions $options): Response
    {
        return Inertia::render('transactions/create', $options->for($request->user()));
    }

    #[Authorize('create', Transaction::class)]
    public function store(StoreTransactionRequest $request, CreateTransaction $create): RedirectResponse
    {
        $transaction = $create->handle($request->user(), $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction created.')]);

        return to_route('transactions.show', $transaction);
    }

    #[Authorize('view', 'transaction')]
    public function show(Transaction $transaction): Response
    {
        return Inertia::render('transactions/show', [
            'transaction' => TransactionResource::make($transaction->load('account:id,name,currency_code')),
        ]);
    }

    #[Authorize('update', 'transaction')]
    public function edit(Request $request, Transaction $transaction, TransactionFormOptions $options): Response
    {
        return Inertia::render('transactions/edit', [
            ...$options->for($request->user(), $transaction),
            'transaction' => TransactionResource::make($transaction->load('account:id,name,currency_code')),
        ]);
    }

    #[Authorize('update', 'transaction')]
    public function update(UpdateTransactionRequest $request, Transaction $transaction, UpdateTransaction $update): RedirectResponse
    {
        $transaction = $update->handle($request->user(), $transaction, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction updated.')]);

        return to_route('transactions.show', $transaction);
    }

    #[Authorize('delete', 'transaction')]
    public function destroy(Transaction $transaction, DeleteTransaction $delete): RedirectResponse
    {
        $delete->handle($transaction);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction deleted.')]);

        return to_route('transactions.index');
    }
}
