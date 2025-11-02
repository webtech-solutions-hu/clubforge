<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in specific order due to dependencies
        $this->call([
            RoleSeeder::class,          // 1. Create roles first
            UserSeeder::class,          // 2. Create users with role assignments
            EventSeeder::class,         // 3. Create events
            CommunityDataSeeder::class, // 4. Create posts, comments, likes, participations, results
        ]);

        $this->command->info('Database seeding completed successfully!');
        $this->command->info('You can login with:');
        $this->command->info('  Email: admin@example.com');
        $this->command->info('  Password: password');
    }
}
