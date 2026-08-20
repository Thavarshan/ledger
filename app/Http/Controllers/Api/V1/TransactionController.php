<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateTransaction;
use App\Actions\UpdateTransaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\Api\V1\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionIndexQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Attributes\Controllers\Authorize;

/** Exposes authenticated transaction resources for API v1 clients. */
class TransactionController extends Controller
{
    /** List the authenticated user's transactions. */
    #[Authorize('viewAny', Transaction::class)]
    public function index(IndexTransactionRequest $request, TransactionIndexQuery $transactions): AnonymousResourceCollection
    {
        $owner = $this->user($request);
        $perPage = $request->integer('per_page', 20);

        return TransactionResource::collection($transactions->paginate($owner, $request->validated(), $perPage));
    }

    /** Create a transaction on an active owned account. */
    #[Authorize('create', Transaction::class)]
    public function store(StoreTransactionRequest $request, CreateTransaction $create): JsonResponse
    {
        $transaction = $create->handle($this->user($request), $request->validated());

        return TransactionResource::make($transaction)
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('api.v1.transactions.show', $transaction));
    }

    /** Display a transaction with its safe nested account. */
    #[Authorize('view', 'transaction')]
    public function show(Transaction $transaction): TransactionResource
    {
        return TransactionResource::make($transaction->load('account:id,name,currency_code'));
    }

    /** Apply validated transaction changes and safe account reassignment. */
    #[Authorize('update', 'transaction')]
    public function update(UpdateTransactionRequest $request, Transaction $transaction, UpdateTransaction $update): TransactionResource
    {
        return TransactionResource::make($update->handle($transaction, $request->validated()));
    }

    /** Delete an owned transaction. */
    #[Authorize('delete', 'transaction')]
    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json(null, 204);
    }

    /** Resolve the authenticated API principal for transaction operations. */
    private function user(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        return $user;
    }
}
