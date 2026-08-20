<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the durable store used by API idempotency middleware.
 *
 * The user/key uniqueness constraint prevents two concurrent requests from
 * claiming the same retry key for one account.
 */
return new class extends Migration
{
    /**
     * Create the idempotency record table and its per-user uniqueness constraint.
     */
    public function up(): void
    {
        Schema::create('api_idempotency_keys', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('key', 128);
            $table->string('method', 10);
            $table->string('route_name', 150);
            $table->char('request_hash', 64);
            $table->unsignedSmallInteger('response_status');
            $table->json('response_body');
            $table->json('response_headers')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
            $table->unique(['user_id', 'key']);
        });
    }

    /**
     * Remove the idempotency record table during rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_idempotency_keys');
    }
};
