<?php

namespace Database\Seeders;

use App\Models\Extra;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueExtraSeeder extends Seeder
{
    public function run(): void
    {
        // Mapa de extras por nombre de local.
        $venueExtras = [
            'Sala Apolo' => [
                'sonido-profesional',
                'iluminacion-escenica',
                'proyector',
            ],
            'Palacio de Congresos de Barcelona' => [
                'proyector',
                'sonido-profesional',
                'mesas-y-sillas',
            ],
            'Teatro Lope de Vega' => [
                'iluminacion-escenica',
                'escenario',
                'sonido-profesional',
            ],
            'Centro Cultural Conde Duque' => [
                'mesas-y-sillas',
                'wifi',
                'aparcamiento',
            ],
            'Espacio Alameda' => [
                'aparcamiento',
                'catering',
                'acceso-para-minusvalidos',
            ],
            'Pabellón de la Navegación' => [
                'catering',
                'aparcamiento',
                'escenario',
            ],
            'Auditorio de Zaragoza' => [
                'sonido-profesional',
                'proyector',
                'iluminacion-escenica',
            ],
            'Sala Mozart' => [
                'sonido-profesional',
                'proyector',
                'acceso-para-minusvalidos',
            ],
            'Palacio de Festivales de Cantabria' => [
                'catering',
                'proyector',
                'sonido-profesional',
            ],
            'Centro Botín' => [
                'proyector',
                'iluminacion-escenica',
                'wifi',
            ],
        ];

        foreach ($venueExtras as $venueName => $extraSlugs) {
            $venue = Venue::where('name', $venueName)->first();

            if ($venue) {
                // Sincroniza extras del local segun slugs definidos.
                $extraIds = Extra::whereIn('slug', $extraSlugs)->pluck('extra_id')->all();
                $venue->extras()->sync($extraIds);
            }
        }
    }
}
