<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'Full system access with supervisor privileges',
                'is_supervisor' => true,
            ],
            [
                'name' => 'Owner',
                'slug' => 'owner',
                'description' => 'Organization owner with management capabilities',
                'is_supervisor' => false,
            ],
            [
                'name' => 'Game Master',
                'slug' => 'game-master',
                'description' => 'Manages games and gaming activities',
                'is_supervisor' => false,
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Regular member with standard access',
                'is_supervisor' => false,
            ],
            [
                'name' => 'Guest',
                'slug' => 'guest',
                'description' => 'Limited access for new or unverified users',
                'is_supervisor' => false,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
    }
}
