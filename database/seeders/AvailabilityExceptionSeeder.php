<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AvailabilityExceptionSeeder extends Seeder
{
    /**
     * Seed the availability exceptions table.
     */
    public function run(): void
    {
        // Reinicia excepciones para generar un estado reproducible.
        DB::table('availability_exceptions')->truncate();

        // Catalogo de excepciones por nombre de local.
        $exceptionsByVenue = [
            'Sala Apolo' => [
                ['start_date' => '2026-07-22', 'end_date' => '2026-07-22', 'reason' => 'Mantenimiento de sonido'],
                ['start_date' => '2026-08-11', 'end_date' => '2026-08-12', 'reason' => 'Montaje tecnico externo'],
                ['start_date' => '2026-10-03', 'end_date' => '2026-10-05', 'reason' => 'Festival interno del recinto'],
            ],
            'Palacio de Congresos de Barcelona' => [
                ['start_date' => '2026-07-28', 'end_date' => '2026-07-30', 'reason' => 'Congreso institucional propio'],
                ['start_date' => '2026-09-18', 'end_date' => '2026-09-18', 'reason' => 'Revision integral de climatizacion'],
                ['start_date' => '2026-11-09', 'end_date' => '2026-11-10', 'reason' => 'Actualizacion de red y audiovisuales'],
                ['start_date' => '2027-01-14', 'end_date' => '2027-01-14', 'reason' => 'Inventario anual de instalaciones'],
            ],
            'Teatro Lope de Vega' => [
                ['start_date' => '2026-08-03', 'end_date' => '2026-08-04', 'reason' => 'Ensayo general de temporada'],
                ['start_date' => '2026-09-26', 'end_date' => '2026-09-26', 'reason' => 'Ajuste de telon y tramoya'],
                ['start_date' => '2026-12-01', 'end_date' => '2026-12-02', 'reason' => 'Grabacion audiovisual interna'],
            ],
            'Centro Cultural Conde Duque' => [
                ['start_date' => '2026-07-24', 'end_date' => '2026-07-25', 'reason' => 'Reacondicionamiento de patios'],
                ['start_date' => '2026-10-14', 'end_date' => '2026-10-16', 'reason' => 'Encuentro municipal de centros culturales'],
                ['start_date' => '2027-02-06', 'end_date' => '2027-02-06', 'reason' => 'Limpieza tecnica extraordinaria'],
            ],
            'Espacio Alameda' => [
                ['start_date' => '2026-08-09', 'end_date' => '2026-08-09', 'reason' => 'Tratamiento preventivo de jardines'],
                ['start_date' => '2026-09-13', 'end_date' => '2026-09-15', 'reason' => 'Montaje de feria local'],
                ['start_date' => '2026-11-27', 'end_date' => '2026-11-27', 'reason' => 'Revision electrica obligatoria'],
                ['start_date' => '2027-03-05', 'end_date' => '2027-03-06', 'reason' => 'Reforma ligera de salones'],
            ],
            'Pabellón de la Navegación' => [
                ['start_date' => '2026-08-20', 'end_date' => '2026-08-22', 'reason' => 'Exposicion itinerante propia'],
                ['start_date' => '2026-10-29', 'end_date' => '2026-10-29', 'reason' => 'Inspeccion de estructura y cubiertas'],
                ['start_date' => '2027-01-21', 'end_date' => '2027-01-22', 'reason' => 'Actualizacion de recorridos internos'],
            ],
            'Auditorio de Zaragoza' => [
                ['start_date' => '2026-07-31', 'end_date' => '2026-07-31', 'reason' => 'Calibracion acustica de sala principal'],
                ['start_date' => '2026-09-07', 'end_date' => '2026-09-08', 'reason' => 'Montaje de ciclo sinfonico propio'],
                ['start_date' => '2026-12-12', 'end_date' => '2026-12-14', 'reason' => 'Encuentro coral del auditorio'],
                ['start_date' => '2027-02-18', 'end_date' => '2027-02-18', 'reason' => 'Mantenimiento de camerinos'],
            ],
            'Sala Mozart' => [
                ['start_date' => '2026-08-17', 'end_date' => '2026-08-17', 'reason' => 'Pruebas de grabacion de camara'],
                ['start_date' => '2026-10-11', 'end_date' => '2026-10-12', 'reason' => 'Ciclo pedagogico interno'],
                ['start_date' => '2027-01-09', 'end_date' => '2027-01-09', 'reason' => 'Ajuste de iluminacion escenica'],
            ],
            'Palacio de Festivales de Cantabria' => [
                ['start_date' => '2026-08-26', 'end_date' => '2026-08-27', 'reason' => 'Foro cultural institucional'],
                ['start_date' => '2026-11-03', 'end_date' => '2026-11-04', 'reason' => 'Mantenimiento de butacas y pasillos'],
                ['start_date' => '2027-03-12', 'end_date' => '2027-03-13', 'reason' => 'Preparacion de temporada especial'],
            ],
            'Centro Botín' => [
                ['start_date' => '2026-09-01', 'end_date' => '2026-09-01', 'reason' => 'Cambio de montaje expositivo'],
                ['start_date' => '2026-10-23', 'end_date' => '2026-10-24', 'reason' => 'Intervencion tecnica en salas'],
                ['start_date' => '2026-12-20', 'end_date' => '2026-12-21', 'reason' => 'Produccion de evento interno'],
                ['start_date' => '2027-02-27', 'end_date' => '2027-02-27', 'reason' => 'Revision preventiva de climatizacion'],
            ],
        ];

        // Resuelve venues en un solo query para reducir consultas repetidas.
        $venues = Venue::query()->whereIn('name', array_keys($exceptionsByVenue))->get()->keyBy('name');

        foreach ($exceptionsByVenue as $venueName => $exceptions) {
            $venue = $venues->get($venueName);

            if (! $venue) {
                continue;
            }

            // Inserta o actualiza excepciones por rango de fechas.
            foreach ($exceptions as $exception) {
                DB::table('availability_exceptions')->updateOrInsert(
                    [
                        'venue_id' => $venue->venue_id,
                        'start_date' => $exception['start_date'],
                        'end_date' => $exception['end_date'],
                    ],
                    [
                        'opening_time' => null,
                        'closing_time' => null,
                        'is_available' => false,
                        'reason' => $exception['reason'],
                    ]
                );
            }
        }
    }
}
