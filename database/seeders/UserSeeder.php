<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Catalogo base de usuarios por rol para entorno local.
        $users = [
            // Administradores
            [
                'username'        => 'Eloy.Garcia',
                'email'           => 'eloy.garcia@nestevent.com',
                'password_hash'   => Hash::make('admin1234'),
                'first_name'      => 'Eloy',
                'last_name'       => 'García',
                'phone'           => '+34 600 000 001',
                'profile_picture' => 'profile_pictures/foto-perfil-hombre-1.jpg',
                'user_type'       => 'admin',
                'is_active'       => true,
            ],
            [
                'username'        => 'admin',
                'email'           => 'admin@nestevent.com',
                'password_hash'   => Hash::make('admminnestevent1234'),
                'first_name'      => 'Admin',
                'last_name'       => 'Admin',
                'phone'           => '+34 600 000 002',
                'profile_picture' => null,
                'user_type'       => 'admin',
                'is_active'       => true,
            ],

            // Gestores de eventos
            [
                'username'        => 'ana.garcia',
                'email'           => 'ana.garcia@gmail.com',
                'password_hash'   => Hash::make('event1234'),
                'first_name'      => 'Ana',
                'last_name'       => 'García',
                'phone'           => '+34 611 111 111',
                'profile_picture' => 'profile_pictures/foto-perfil-mujer-1.jpeg',
                'user_type'       => 'event_manager',
                'is_active'       => true,
            ],
            [
                'username'        => 'pedro.lopez',
                'email'           => 'pedro.lopez@gmail.com',
                'password_hash'   => Hash::make('event1234'),
                'first_name'      => 'Pedro',
                'last_name'       => 'López',
                'phone'           => '+34 622 222 222',
                'profile_picture' => 'profile_pictures/foto-perfil-hombre-3.jpg',
                'user_type'       => 'event_manager',
                'is_active'       => true,
            ],
            [
                'username'        => 'laura.sanchez',
                'email'           => 'laura.sanchez@gmail.com',
                'password_hash'   => Hash::make('event1234'),
                'first_name'      => 'Laura',
                'last_name'       => 'Sánchez',
                'phone'           => '+34 633 333 333',
                'profile_picture' => 'profile_pictures/foto-perfil-mujer-2.jpeg',
                'user_type'       => 'event_manager',
                'is_active'       => true,
            ],
            [
                'username'        => 'jorge.castillo',
                'email'           => 'jorge.castillo@gmail.com',
                'password_hash'   => Hash::make('event1234'),
                'first_name'      => 'Jorge',
                'last_name'       => 'Castillo',
                'phone'           => '+34 644 444 441',
                'profile_picture' => 'profile_pictures/foto-perfil-hombre-4.jpeg',
                'user_type'       => 'event_manager',
                'is_active'       => true,
            ],
            [
                'username'        => 'marta.vidal',
                'email'           => 'marta.vidal@gmail.com',
                'password_hash'   => Hash::make('event1234'),
                'first_name'      => 'Marta',
                'last_name'       => 'Vidal',
                'phone'           => '+34 655 444 442',
                'profile_picture' => 'profile_pictures/foto-perfil-mujer-3.jpg',
                'user_type'       => 'event_manager',
                'is_active'       => true,
            ],

            // Gestores de locales
            [
                'username'        => 'jose.fernandez',
                'email'           => 'jose.fernandez@gmail.com',
                'password_hash'   => Hash::make('local1234'),
                'first_name'      => 'José',
                'last_name'       => 'Fernández',
                'phone'           => '+34 644 444 444',
                'profile_picture' => 'profile_pictures/foto-perfil-hombre-5.jpg',
                'user_type'       => 'local_manager',
                'is_active'       => true,
            ],
            [
                'username'        => 'maria.jimenez',
                'email'           => 'maria.jimenez@gmail.com',
                'password_hash'   => Hash::make('local1234'),
                'first_name'      => 'María',
                'last_name'       => 'Jiménez',
                'phone'           => '+34 655 555 555',
                'profile_picture' => 'profile_pictures/foto-perfil-mujer-4.jpeg',
                'user_type'       => 'local_manager',
                'is_active'       => true,
            ],
            [
                'username'        => 'david.romero',
                'email'           => 'david.romero@gmail.com',
                'password_hash'   => Hash::make('local1234'),
                'first_name'      => 'David',
                'last_name'       => 'Romero',
                'phone'           => '+34 666 666 666',
                'profile_picture' => 'profile_pictures/foto-perfil-hombre-6.jpg',
                'user_type'       => 'local_manager',
                'is_active'       => true,
            ],
            [
                'username'        => 'sofia.torres',
                'email'           => 'sofia.torres@gmail.com',
                'password_hash'   => Hash::make('local1234'),
                'first_name'      => 'Sofía',
                'last_name'       => 'Torres',
                'phone'           => '+34 677 777 777',
                'profile_picture' => 'profile_pictures/foto-perfil-mujer-5.jpeg',
                'user_type'       => 'local_manager',
                'is_active'       => true,
            ],
            [
                'username'        => 'miguel.moreno',
                'email'           => 'miguel.moreno@gmail.com',
                'password_hash'   => Hash::make('local1234'),
                'first_name'      => 'Miguel',
                'last_name'       => 'Moreno',
                'phone'           => '+34 688 888 888',
                'profile_picture' => 'profile_pictures/foto-perfil-hombre-2.jpg',
                'user_type'       => 'local_manager',
                'is_active'       => true,
            ],
        ];

        foreach ($users as $userData) {
            // Inserta cada usuario con credenciales y perfil predefinidos.
            User::create($userData);
        }
    }
}