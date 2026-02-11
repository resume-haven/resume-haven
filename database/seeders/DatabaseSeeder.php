<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Persistence\RoleModel;
use App\Infrastructure\Persistence\UserModel;
use App\Infrastructure\Persistence\ResumeModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // UserModel::factory(10)->create();

        $adminRole = RoleModel::query()->firstOrCreate(['name' => 'admin']);

        $user = UserModel::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user->roles()->syncWithoutDetaching([$adminRole->id]);

        ResumeModel::query()
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);
    }
}
