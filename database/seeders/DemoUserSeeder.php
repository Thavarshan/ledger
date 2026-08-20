<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates the stable user used by the local application demo.
 */
class DemoUserSeeder extends Seeder
{
    public const string EMAIL = 'demo@ledger.test';

    /**
     * Create or refresh the stable user used by demo data.
     *
     * updateOrCreate keeps repeated deploy-time seeding idempotent while the
     * fixed email gives the dependent seeders a deterministic owner.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => 'Alex Perera',
                'email_verified_at' => now(),
                'password' => 'password',
            ],
        );
    }
}
