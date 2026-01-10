<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full system administrator with all permissions',
            ],
            [
                'name' => 'Partner Admin',
                'slug' => 'partner_admin',
                'description' => 'Can manage their partner organization and users',
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user with standard permissions',
            ],
            [
                'name' => 'Trainer',
                'slug' => 'trainer',
                'description' => 'Personal trainer who can manage clients and workouts',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
