<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database. 
     */
    public function run(): void
    {
        // User::factory(10)->create();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Asegurarse de que las tablas estén vacías antes de sembrar para evitar conflictos con foreign keys
        DB::table('event_attendees')->truncate();
        DB::table('bookings')->truncate();
        DB::table('notifications')->truncate();
        DB::table('users')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            UserSeeder::class,
            VenueSeeder::class,
            EventSeeder::class,
        ]);
    }
}
