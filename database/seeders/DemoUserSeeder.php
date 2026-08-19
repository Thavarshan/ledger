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
     * Run the database seeds.
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
