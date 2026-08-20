<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Coordinates the deterministic demo dataset seeders.
 *
 * Each child seeder is rerunnable, allowing local and Laravel Cloud preview
 * environments to be refreshed without creating duplicate records.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database in dependency order.
     *
     * The demo user must exist before accounts, and accounts must exist before
     * transactions can be attached to them.
     */
    public function run(): void
    {
        $this->call([
            DemoUserSeeder::class,
            AccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
