<?php

use App\Enums\CurrencyCode;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            // An account belongs to the authenticated user who registered it.
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('account_type', 50)->nullable();
            $table->string('account_holder_name')->nullable();

            // Institution and branch details. `bank_code` and `branch_code`
            // support Sri Lankan bank/branch identifiers, while the other
            // fields cover international institutions.
            $table->string('bank_name', 150);
            $table->string('bank_code', 20)->nullable();
            $table->string('branch_name', 150)->nullable();
            $table->string('branch_code', 20)->nullable();
            $table->string('country_code', 2);

            // ISO 4217 currency code. Keep this list aligned with the
            // currencies supported by the application's business rules.
            $table->enum('currency_code', CurrencyCode::values());

            // Sensitive identifiers should be encrypted at the model layer.
            // Text columns leave room for Laravel's encrypted cast output.
            $table->text('account_number')->nullable();
            $table->string('account_number_last4', 4)->nullable();
            $table->text('iban')->nullable();
            $table->string('swift_bic', 11)->nullable();
            $table->text('routing_number')->nullable();
            $table->text('sort_code')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);

            $table->index(['user_id', 'country_code']);
            $table->index(['user_id', 'currency_code']);
            $table->index(['user_id', 'is_active']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
