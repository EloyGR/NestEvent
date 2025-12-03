<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //User::factory()->count(10)->create();
        // Create an admin user with specific details
        User::create([
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password_hash' => Hash::make('admin'),
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'phone' => null,
            'profile_picture' => null,
            'user_type' => 'admin',
            'is_active' => true,
        ]);

        // Create 1 event managers
        User::factory(1)->create([
            'username' => 'event_user',
            'email' => 'event@event.com',
            'password_hash' => Hash::make('event'),
            'user_type' => 'event_manager',
        ]);

        // Create 1 local managers
        User::factory(1)->create([
            'username' => 'local_user',
            'email' => 'local@local.com',
            'password_hash' => Hash::make('local'),
            'user_type' => 'local_manager',
        ]);

        // Create 1 regular users
        User::factory(1)->create([
            'username' => 'normal_user',
            'email' => 'normal@normal.com',
            'password_hash' => Hash::make('normal'),
            'user_type' => 'user',
        ]);
    }
}