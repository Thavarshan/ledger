<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoUserSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoDatabaseSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_demo_seeders_create_a_rerunnable_financial_workflow(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', DemoUserSeeder::EMAIL)->firstOrFail();

        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertCount(6, $user->accounts);
        $this->assertSame(1, $user->accounts()->where('is_primary', true)->count());
        $this->assertSame(1000, $user->transactions()->count());
        $this->assertTrue(
            $user->accounts()->where('is_active', false)->firstOrFail()->transactions()->exists(),
        );
    }
}
