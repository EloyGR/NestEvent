<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VenueAvailabilitySeeder extends Seeder
{
    /**
     * Seed the venue availability table.
     */
    public function run(): void
    {
        // Carga locales para aplicar plantillas de horario semanal.
        $venues = Venue::query()->select(['venue_id', 'name'])->get();

        if ($venues->isEmpty()) {
            return;
        }

        // Convencion de dias: 0=Domingo, 1=Lunes, ..., 6=Sabado.
        $templates = [
            'sala_apolo' => [
                0 => ['10:00:00', '17:00:00', true],
                1 => ['09:00:00', '20:00:00', true],
                2 => ['09:00:00', '20:00:00', true],
                3 => ['09:00:00', '20:00:00', true],
                4 => ['09:00:00', '20:00:00', true],
                5 => ['09:00:00', '20:00:00', true],
                6 => ['10:00:00', '18:00:00', true],
            ],
            'palacio_congresos' => [
                0 => [null, null, false],
                1 => ['08:00:00', '21:00:00', true],
                2 => ['08:00:00', '21:00:00', true],
                3 => ['08:00:00', '21:00:00', true],
                4 => ['08:00:00', '21:00:00', true],
                5 => ['08:00:00', '21:00:00', true],
                6 => ['09:00:00', '18:00:00', true],
            ],
            'teatro_lope' => [
                0 => ['11:00:00', '20:00:00', true],
                1 => [null, null, false],
                2 => ['11:00:00', '23:00:00', true],
                3 => ['11:00:00', '23:00:00', true],
                4 => ['11:00:00', '23:00:00', true],
                5 => ['11:00:00', '23:00:00', true],
                6 => ['11:00:00', '23:00:00', true],
            ],
            'centro_cultural' => [
                0 => ['10:00:00', '18:00:00', true],
                1 => ['09:00:00', '22:00:00', true],
                2 => ['09:00:00', '22:00:00', true],
                3 => ['09:00:00', '22:00:00', true],
                4 => ['09:00:00', '22:00:00', true],
                5 => ['09:00:00', '22:00:00', true],
                6 => ['10:00:00', '22:00:00', true],
            ],
            'espacio_alameda' => [
                0 => ['10:00:00', '20:00:00', true],
                1 => ['10:00:00', '22:00:00', true],
                2 => ['10:00:00', '22:00:00', true],
                3 => ['10:00:00', '22:00:00', true],
                4 => ['10:00:00', '22:00:00', true],
                5 => ['10:00:00', '23:30:00', true],
                6 => ['10:00:00', '23:30:00', true],
            ],
            'pabellon_navegacion' => [
                0 => [null, null, false],
                1 => ['09:00:00', '19:00:00', true],
                2 => ['09:00:00', '19:00:00', true],
                3 => ['09:00:00', '19:00:00', true],
                4 => ['09:00:00', '19:00:00', true],
                5 => ['09:00:00', '19:00:00', true],
                6 => ['10:00:00', '18:00:00', true],
            ],
            'auditorio_zaragoza' => [
                0 => ['10:00:00', '18:00:00', true],
                1 => ['09:00:00', '21:00:00', true],
                2 => ['09:00:00', '21:00:00', true],
                3 => ['09:00:00', '21:00:00', true],
                4 => ['09:00:00', '21:00:00', true],
                5 => ['09:00:00', '21:00:00', true],
                6 => ['10:00:00', '21:00:00', true],
            ],
            'sala_mozart' => [
                0 => ['11:00:00', '19:00:00', true],
                1 => [null, null, false],
                2 => ['16:00:00', '22:00:00', true],
                3 => ['16:00:00', '22:00:00', true],
                4 => ['16:00:00', '22:00:00', true],
                5 => ['16:00:00', '22:00:00', true],
                6 => ['11:00:00', '22:00:00', true],
            ],
            'palacio_festivales' => [
                0 => ['10:00:00', '14:00:00', true],
                1 => ['10:00:00', '20:00:00', true],
                2 => ['10:00:00', '20:00:00', true],
                3 => ['10:00:00', '20:00:00', true],
                4 => ['10:00:00', '20:00:00', true],
                5 => ['10:00:00', '20:00:00', true],
                6 => ['10:00:00', '22:00:00', true],
            ],
            'centro_botin' => [
                0 => ['10:00:00', '21:00:00', true],
                1 => [null, null, false],
                2 => ['10:00:00', '20:00:00', true],
                3 => ['10:00:00', '20:00:00', true],
                4 => ['10:00:00', '20:00:00', true],
                5 => ['10:00:00', '20:00:00', true],
                6 => ['10:00:00', '21:00:00', true],
            ],
            // Plantilla por defecto para locales no mapeados.
            'default' => [
                0 => [null, null, false],
                1 => ['09:00:00', '19:00:00', true],
                2 => ['09:00:00', '19:00:00', true],
                3 => ['09:00:00', '19:00:00', true],
                4 => ['09:00:00', '19:00:00', true],
                5 => ['09:00:00', '19:00:00', true],
                6 => ['10:00:00', '15:00:00', true],
            ],
        ];

        // Mapa de nombre de local a plantilla de horario.
        $venueTemplateMap = [
            'Sala Apolo' => 'sala_apolo',
            'Palacio de Congresos de Barcelona' => 'palacio_congresos',
            'Teatro Lope de Vega' => 'teatro_lope',
            'Centro Cultural Conde Duque' => 'centro_cultural',
            'Espacio Alameda' => 'espacio_alameda',
            'Pabellón de la Navegación' => 'pabellon_navegacion',
            'Auditorio de Zaragoza' => 'auditorio_zaragoza',
            'Sala Mozart' => 'sala_mozart',
            'Palacio de Festivales de Cantabria' => 'palacio_festivales',
            'Centro Botín' => 'centro_botin',
        ];

        foreach ($venues as $venue) {
            $templateKey = $venueTemplateMap[$venue->name] ?? 'default';
            $schedule = $templates[$templateKey] ?? $templates['default'];

            // Inserta o actualiza horario por dia para cada local.
            foreach ($schedule as $dayOfWeek => [$openingTime, $closingTime, $isAvailable]) {
                DB::table('venue_availability')->updateOrInsert(
                    [
                        'venue_id' => $venue->venue_id,
                        'day_of_week' => $dayOfWeek,
                    ],
                    [
                        'opening_time' => $openingTime,
                        'closing_time' => $closingTime,
                        'is_available' => $isAvailable,
                    ]
                );
            }
        }
    }
}
