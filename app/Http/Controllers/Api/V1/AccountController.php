<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateAccount;
use App\Actions\UpdateAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexAccountRequest;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\Api\V1\AccountResource;
use App\Models\Account;
use App\Models\User;
use App\Services\AccountIndexQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Attributes\Controllers\Authorize;

/** Exposes authenticated account resources for API v1 clients. */
class AccountController extends Controller
{
    /** List the authenticated user's accounts. */
    #[Authorize('viewAny', Account::class)]
    public function index(IndexAccountRequest $request, AccountIndexQuery $accounts): AnonymousResourceCollection
    {
        $owner = $this->user($request);
        $perPage = $request->integer('per_page', 20);

        return AccountResource::collection($accounts->paginate($owner, $request->validated(), $perPage));
    }

    /** Create an account owned by the authenticated user. */
    #[Authorize('create', Account::class)]
    public function store(StoreAccountRequest $request, CreateAccount $create): JsonResponse
    {
        $account = $create->handle($this->user($request), $request->validated())->loadBalance();

        return AccountResource::make($account)
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('api.v1.accounts.show', $account));
    }

    /** Display one account with its derived balance. */
    #[Authorize('view', 'account')]
    public function show(Account $account): AccountResource
    {
        return AccountResource::make($account->loadBalance());
    }

    /** Apply validated account changes while preserving invariants. */
    #[Authorize('update', 'account')]
    public function update(UpdateAccountRequest $request, Account $account, UpdateAccount $update): AccountResource
    {
        return AccountResource::make($update->handle($account, $request->validated())->loadBalance());
    }

    /** Delete an owned account. */
    #[Authorize('delete', 'account')]
    public function destroy(Account $account): JsonResponse
    {
        $account->delete();

        return response()->json(null, 204);
    }

    /** Resolve the authenticated API principal for account operations. */
    private function user(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        return $user;
    }
}
