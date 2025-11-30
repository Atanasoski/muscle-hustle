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
        // Create demo user (avoid duplicates)
        User::firstOrCreate(
            ['email' => 'atanasoski992@gmail.com'],
            [
                'name' => 'Kiril Atanasoski',
                'password' => Hash::make('kiril123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('User created: atanasoski992@gmail.com');
    }
}
