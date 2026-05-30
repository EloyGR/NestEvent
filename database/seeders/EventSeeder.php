<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Relaciona username de gestores con su user_id para construir eventos.
        $organizers = User::where('user_type', 'event_manager')
            ->pluck('user_id', 'username');

        // Catalogo de eventos semilla por organizador.
        $events = [
            // Ana García
            [
                'name'                => 'Festival de Jazz de Barcelona',
                'description'         => 'Tres días de jazz en vivo con artistas nacionales e internacionales en el Palacio de Congresos.',
                'start_datetime'      => '2026-06-14 18:00:00',
                'end_datetime'        => '2026-06-16 23:30:00',
                'organizer_id'        => $organizers['ana.garcia'],
                'event_type'          => 'Música',
                'expected_attendance' => 900,
                'is_public'           => true,
                'status'              => 'approved',
            ],
            [
                'name'                => 'Feria de Emprendimiento Tech',
                'description'         => 'Encuentro de startups y inversores con ponencias, talleres y zona de networking.',
                'start_datetime'      => '2026-07-10 09:00:00',
                'end_datetime'        => '2026-07-10 20:00:00',
                'organizer_id'        => $organizers['ana.garcia'],
                'event_type'          => 'Negocio',
                'expected_attendance' => 500,
                'is_public'           => true,
                'status'              => 'pending',
            ],

            // Pedro López
            [
                'name'                => 'Congreso Nacional de Arquitectura',
                'description'         => 'Congreso anual que reúne a los principales arquitectos del país para debatir tendencias y proyectos.',
                'start_datetime'      => '2026-09-22 10:00:00',
                'end_datetime'        => '2026-09-24 18:00:00',
                'organizer_id'        => $organizers['pedro.lopez'],
                'event_type'          => 'Congreso',
                'expected_attendance' => 700,
                'is_public'           => true,
                'status'              => 'approved',
            ],
            [
                'name'                => 'Gala Benéfica Cruz Roja',
                'description'         => 'Cena de gala con subasta solidaria para recaudar fondos destinados a proyectos humanitarios.',
                'start_datetime'      => '2026-11-05 20:00:00',
                'end_datetime'        => '2026-11-05 23:59:00',
                'organizer_id'        => $organizers['pedro.lopez'],
                'event_type'          => 'Gala',
                'expected_attendance' => 250,
                'is_public'           => false,
                'status'              => 'approved',
            ],

            // Laura Sánchez
            [
                'name'                => 'Exposición de Arte Contemporáneo',
                'description'         => 'Muestra de obras de artistas emergentes españoles con visitas guiadas y talleres.',
                'start_datetime'      => '2026-06-01 10:00:00',
                'end_datetime'        => '2026-06-30 20:00:00',
                'organizer_id'        => $organizers['laura.sanchez'],
                'event_type'          => 'Arte',
                'expected_attendance' => 1200,
                'is_public'           => true,
                'status'              => 'approved',
            ],
            [
                'name'                => 'Jornadas de Sostenibilidad',
                'description'         => 'Jornadas enfocadas en movilidad sostenible, energías renovables y economía circular.',
                'start_datetime'      => '2026-10-15 09:00:00',
                'end_datetime'        => '2026-10-16 18:00:00',
                'organizer_id'        => $organizers['laura.sanchez'],
                'event_type'          => 'Conferencia',
                'expected_attendance' => 400,
                'is_public'           => true,
                'status'              => 'pending',
            ],

            // Jorge Castillo
            [
                'name'                => 'Torneo de Esports LAN Party',
                'description'         => 'Competición presencial de videojuegos con más de 200 participantes y premios en metálico.',
                'start_datetime'      => '2026-08-08 10:00:00',
                'end_datetime'        => '2026-08-09 22:00:00',
                'organizer_id'        => $organizers['jorge.castillo'],
                'event_type'          => 'Esports',
                'expected_attendance' => 350,
                'is_public'           => true,
                'status'              => 'approved',
            ],
            [
                'name'                => 'Presentación de Producto Innova S.L.',
                'description'         => 'Evento corporativo para el lanzamiento de la nueva línea de productos tecnológicos.',
                'start_datetime'      => '2026-09-03 11:00:00',
                'end_datetime'        => '2026-09-03 14:00:00',
                'organizer_id'        => $organizers['jorge.castillo'],
                'event_type'          => 'Corporativo',
                'expected_attendance' => 150,
                'is_public'           => false,
                'status'              => 'approved',
            ],

            // Marta Vidal
            [
                'name'                => 'Semana de la Danza Española',
                'description'         => 'Programación de espectáculos de flamenco, danza clásica y contemporánea durante una semana.',
                'start_datetime'      => '2026-07-20 19:00:00',
                'end_datetime'        => '2026-07-26 22:30:00',
                'organizer_id'        => $organizers['marta.vidal'],
                'event_type'          => 'Danza',
                'expected_attendance' => 800,
                'is_public'           => true,
                'status'              => 'approved',
            ],
            [
                'name'                => 'Forum de Gastronomía del Norte',
                'description'         => 'Encuentro gastronómico con chefs reconocidos, showcookings y degustaciones.',
                'start_datetime'      => '2026-12-12 12:00:00',
                'end_datetime'        => '2026-12-13 20:00:00',
                'organizer_id'        => $organizers['marta.vidal'],
                'event_type'          => 'Gastronomía',
                'expected_attendance' => 600,
                'is_public'           => true,
                'status'              => 'pending',
            ],
        ];

        foreach ($events as $eventData) {
            // Inserta cada evento con su organizador y metadatos.
            Event::create($eventData);
        }
    }
}