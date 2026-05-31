<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        // Desactiva temporalmente FK solo en MySQL para truncar tablas en orden seguro.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Limpia tablas base para evitar conflictos de claves foraneas y residuos entre reseeds.
        $tablesToTruncate = [
            'extra_venue',
            'confirmed_bookings',
            'bookings',
            'availability_exceptions',
            'venue_availability',
            'venue_images',
            'notifications',
            'events',
            'venues',
            'users',
            'extras',
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }


        // Reactiva FK solo en MySQL antes de ejecutar el pipeline de seeders.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Ejecuta seeders en orden de dependencias entre tablas.
        $this->call([
            ExtraSeeder::class,
            UserSeeder::class,
            VenueSeeder::class,
            VenueAvailabilitySeeder::class,
            AvailabilityExceptionSeeder::class,
            VenueImageSeeder::class,
            EventSeeder::class,
            BookingSeeder::class,
            NotificationSeeder::class,
            VenueExtraSeeder::class,
        ]);
    }
}
