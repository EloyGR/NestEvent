<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Venue;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        // Relaciona username de gestores con su user_id para asignar locales.
        $managers = User::where('user_type', 'local_manager')
            ->pluck('user_id', 'username');

        // Catalogo de locales semilla por gestor.
        // Perfilado hacia un mercado indie/profesional: aforos y precios moderados para demos realistas.
        $venues = [
            // José Fernández
            [
                'name'           => 'Sala Apolo',
                'description'    => 'Sala de conciertos y eventos culturales en el corazón de Barcelona.',
                'address'        => 'Carrer de la Nou de la Rambla, 113',
                'city'           => 'Barcelona',
                'state'          => 'Cataluña',
                'zip_code'       => '08004',
                'country'        => 'España',
                'capacity'       => 420,
                'price_per_hour' => 240.00,
                'manager_id'     => $managers['jose.fernandez'],
                'is_active'      => true,
            ],
            [
                'name'           => 'Palacio de Congresos de Barcelona',
                'description'    => 'Moderno palacio de congresos con múltiples salas y equipamiento de última generación.',
                'address'        => 'Av. de la Reina Maria Cristina, s/n',
                'city'           => 'Barcelona',
                'state'          => 'Cataluña',
                'zip_code'       => '08004',
                'country'        => 'España',
                'capacity'       => 650,
                'price_per_hour' => 360.00,
                'manager_id'     => $managers['jose.fernandez'],
                'is_active'      => true,
            ],

            // María Jiménez
            [
                'name'           => 'Teatro Lope de Vega',
                'description'    => 'Histórico teatro en el centro de Madrid, ideal para espectáculos y presentaciones.',
                'address'        => 'Gran Vía, 57',
                'city'           => 'Madrid',
                'state'          => 'Comunidad de Madrid',
                'zip_code'       => '28013',
                'country'        => 'España',
                'capacity'       => 360,
                'price_per_hour' => 260.00,
                'manager_id'     => $managers['maria.jimenez'],
                'is_active'      => true,
            ],
            [
                'name'           => 'Centro Cultural Conde Duque',
                'description'    => 'Antiguo cuartel reconvertido en centro cultural con grandes espacios al aire libre.',
                'address'        => 'Calle del Conde Duque, 11',
                'city'           => 'Madrid',
                'state'          => 'Comunidad de Madrid',
                'zip_code'       => '28015',
                'country'        => 'España',
                'capacity'       => 280,
                'price_per_hour' => 180.00,
                'manager_id'     => $managers['maria.jimenez'],
                'is_active'      => true,
            ],

            // David Romero
            [
                'name'           => 'Espacio Alameda',
                'description'    => 'Recinto multiusos en Sevilla con jardines y salones para todo tipo de eventos.',
                'address'        => 'Alameda de Hércules, 9',
                'city'           => 'Sevilla',
                'state'          => 'Andalucía',
                'zip_code'       => '41002',
                'country'        => 'España',
                'capacity'       => 220,
                'price_per_hour' => 145.00,
                'manager_id'     => $managers['david.romero'],
                'is_active'      => true,
            ],
            [
                'name'           => 'Pabellón de la Navegación',
                'description'    => 'Emblemático pabellón de la Expo 92 reconvertido en espacio de eventos.',
                'address'        => 'Camino de los Descubrimientos, s/n',
                'city'           => 'Sevilla',
                'state'          => 'Andalucía',
                'zip_code'       => '41092',
                'country'        => 'España',
                'capacity'       => 520,
                'price_per_hour' => 310.00,
                'manager_id'     => $managers['david.romero'],
                'is_active'      => true,
            ],

            // Sofía Torres
            [
                'name'           => 'Auditorio de Zaragoza',
                'description'    => 'Auditorio de referencia en Aragón con excelente acústica para conciertos y conferencias.',
                'address'        => 'Eduardo Ibarra, 3',
                'city'           => 'Zaragoza',
                'state'          => 'Aragón',
                'zip_code'       => '50009',
                'country'        => 'España',
                'capacity'       => 580,
                'price_per_hour' => 330.00,
                'manager_id'     => $managers['sofia.torres'],
                'is_active'      => true,
            ],
            [
                'name'           => 'Sala Mozart',
                'description'    => 'Sala de cámara del Auditorio de Zaragoza, perfecta para eventos íntimos.',
                'address'        => 'Eduardo Ibarra, 3',
                'city'           => 'Zaragoza',
                'state'          => 'Aragón',
                'zip_code'       => '50009',
                'country'        => 'España',
                'capacity'       => 170,
                'price_per_hour' => 120.00,
                'manager_id'     => $managers['sofia.torres'],
                'is_active'      => true,
            ],

            // Miguel Moreno
            [
                'name'           => 'Palacio de Festivales de Cantabria',
                'description'    => 'Moderno palacio de festivales con vistas al mar Cantábrico.',
                'address'        => 'Gamazo, s/n',
                'city'           => 'Santander',
                'state'          => 'Cantabria',
                'zip_code'       => '39004',
                'country'        => 'España',
                'capacity'       => 620,
                'price_per_hour' => 340.00,
                'manager_id'     => $managers['miguel.moreno'],
                'is_active'      => true,
            ],
            [
                'name'           => 'Centro Botín',
                'description'    => 'Centro de artes inaugurado en 2017, espacio vanguardista para exposiciones y eventos.',
                'address'        => 'Muelle de Albareda, s/n',
                'city'           => 'Santander',
                'state'          => 'Cantabria',
                'zip_code'       => '39004',
                'country'        => 'España',
                'capacity'       => 260,
                'price_per_hour' => 190.00,
                'manager_id'     => $managers['miguel.moreno'],
                'is_active'      => true,
            ],
        ];

        foreach ($venues as $venueData) {
            // Inserta cada local con su gestor asignado.
            Venue::create($venueData);
        }
    }
}