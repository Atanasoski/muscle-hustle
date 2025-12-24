<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@musclehustle.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'partner_id' => null,
            ]
        );
        $admin->roles()->syncWithoutDetaching(\App\Models\Role::where('slug', 'admin')->first());

        // Create demo user (avoid duplicates) - also an admin
        $demoUser = User::firstOrCreate(
            ['email' => 'atanasoski992@gmail.com'],
            [
                'name' => 'Kiril Atanasoski',
                'password' => Hash::make('kiril123'),
                'email_verified_at' => now(),
                'partner_id' => null,
            ]
        );
        $demoUser->roles()->syncWithoutDetaching(\App\Models\Role::where('slug', 'admin')->first());

        $this->command->info('Admin user created: admin@musclehustle.com (password: password)');
        $this->command->info('User created: atanasoski992@gmail.com (password: kiril123)');
    }
}
