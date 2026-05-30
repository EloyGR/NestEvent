<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class VenueImageSeeder extends Seeder
{
    public function run(): void
    {
        // Catálogo semilla por local: una imagen principal y galería contextual.
    // Diseñado para validar UI de portada + carrusel en detalle de local.
        // Se incluyen 2-3 imágenes por varios locales para validar carrusel y fallback visual en detalle.
        $imagesByVenueName = [
            'Sala Apolo' => [
                'main' => 'pista-de-baile.webp',
                'gallery' => ['escenario-1.webp', 'equipo-de-sonido-2.webp'],
            ],
            'Palacio de Congresos de Barcelona' => [
                'main' => 'proyector-1.jpeg',
                'gallery' => ['mesas-y-sillas-1.jpg', 'local-interior-2.jpg'],
            ],
            'Teatro Lope de Vega' => [
                'main' => 'escenario-2.jpg',
                'gallery' => ['equipo-de-sonido-1.jpg'],
            ],
            'Centro Cultural Conde Duque' => [
                'main' => 'local-interior-1.jpg',
                'gallery' => ['mesas-y-sillas-2.jpg'],
            ],
            'Espacio Alameda' => [
                'main' => 'terraza-2.webp',
                'gallery' => ['parking-1.webp', 'cocina-1.webp'],
            ],
            'Pabellón de la Navegación' => [
                'main' => 'local-1.webp',
                'gallery' => ['escenario-1.webp', 'cocina-2.jpg'],
            ],
            'Auditorio de Zaragoza' => [
                'main' => 'equipo-de-sonido-1.jpg',
                'gallery' => ['proyector-2.jpg', 'escenario-2.jpg'],
            ],
            'Sala Mozart' => [
                'main' => 'local-interior-4.jpg',
                'gallery' => ['proyector-1.jpeg'],
            ],
            'Palacio de Festivales de Cantabria' => [
                'main' => 'escenario-1.webp',
                'gallery' => ['equipo-de-sonido-2.webp'],
            ],
            'Centro Botín' => [
                'main' => 'local-2.webp',
                'gallery' => ['local-interior-5.jpg', 'proyector-2.jpg'],
            ],
        ];

        foreach ($imagesByVenueName as $venueName => $images) {
            $mainImage = $images['main'];
            $galleryImages = $images['gallery'] ?? [];
            $allImages = array_values(array_unique(array_merge([$mainImage], $galleryImages)));

            $venue = Venue::where('name', $venueName)->first();

            if (! $venue) {
                continue;
            }

            foreach ($allImages as $filename) {
                $imagePath = public_path('storage/venue_images/' . $filename);

                if (! File::exists($imagePath)) {
                    // Omite registros cuando el archivo fisico no existe.
                    continue;
                }

                $alreadyExists = DB::table('venue_images')
                    ->where('venue_id', $venue->venue_id)
                    ->where('image_url', $filename)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                DB::table('venue_images')->insert([
                    'venue_id' => $venue->venue_id,
                    'image_url' => $filename,
                    'main_image' => $filename === $mainImage ? 1 : 0,
                    'upload_date' => now(),
                ]);
            }
        }
    }
}
