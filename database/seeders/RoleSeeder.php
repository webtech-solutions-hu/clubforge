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
                'avatar' => 'https://ui-avatars.com/api/?name=Admin&color=FFFFFF&background=DC2626&size=200',
                'description' => 'Full system access with supervisor privileges',
                'is_supervisor' => true,
            ],
            [
                'name' => 'Owner',
                'slug' => 'owner',
                'avatar' => 'https://ui-avatars.com/api/?name=Owner&color=FFFFFF&background=F59E0B&size=200',
                'description' => 'Organization owner with management capabilities',
                'is_supervisor' => false,
            ],
            [
                'name' => 'Game Master',
                'slug' => 'game-master',
                'avatar' => 'https://ui-avatars.com/api/?name=GM&color=FFFFFF&background=3B82F6&size=200',
                'description' => 'Manages games and gaming activities',
                'is_supervisor' => false,
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'avatar' => 'https://ui-avatars.com/api/?name=Member&color=FFFFFF&background=10B981&size=200',
                'description' => 'Regular member with standard access',
                'is_supervisor' => false,
            ],
            [
                'name' => 'Guest',
                'slug' => 'guest',
                'avatar' => 'https://ui-avatars.com/api/?name=Guest&color=FFFFFF&background=6B7280&size=200',
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
