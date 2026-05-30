<?php

namespace Database\Seeders;

use App\Models\Extra;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExtraSeeder extends Seeder
{
    public function run(): void
    {
        // Catalogo de extras disponibles para asociar a locales.
        $extras = [
            [
                'name' => 'Catering',
                'slug' => 'catering',
                'description' => 'Servicio de comida y bebida incluido',
                'icon' => null,
                'category' => 'Servicios',
            ],
            [
                'name' => 'Aparcamiento',
                'slug' => 'aparcamiento',
                'description' => 'Espacio de aparcamiento para vehículos',
                'icon' => null,
                'category' => 'Servicios',
            ],
            [
                'name' => 'WiFi',
                'slug' => 'wifi',
                'description' => 'Conexión a internet de alta velocidad',
                'icon' => null,
                'category' => 'Servicios',
            ],
            [
                'name' => 'Seguridad',
                'slug' => 'seguridad',
                'description' => 'Servicio de vigilancia y seguridad',
                'icon' => null,
                'category' => 'Servicios',
            ],
            [
                'name' => 'Proyector',
                'slug' => 'proyector',
                'description' => 'Proyector y pantalla para presentaciones',
                'icon' => null,
                'category' => 'Equipamiento',
            ],
            [
                'name' => 'Sonido profesional',
                'slug' => 'sonido-profesional',
                'description' => 'Sistema de audio profesional',
                'icon' => null,
                'category' => 'Equipamiento',
            ],
            [
                'name' => 'Iluminación escénica',
                'slug' => 'iluminacion-escenica',
                'description' => 'Luces profesionales y efectos especiales',
                'icon' => null,
                'category' => 'Equipamiento',
            ],
            [
                'name' => 'Escenario',
                'slug' => 'escenario',
                'description' => 'Escenario montado y equipado',
                'icon' => null,
                'category' => 'Equipamiento',
            ],
            [
                'name' => 'Mesas y sillas',
                'slug' => 'mesas-y-sillas',
                'description' => 'Mobiliario completo',
                'icon' => null,
                'category' => 'Equipamiento',
            ],
            [
                'name' => 'Piscina',
                'slug' => 'piscina',
                'description' => 'Acceso a piscina climatizada',
                'icon' => null,
                'category' => 'Instalaciones',
            ],
            [
                'name' => 'Cocina',
                'slug' => 'cocina',
                'description' => 'Cocina profesional completamente equipada',
                'icon' => null,
                'category' => 'Instalaciones',
            ],
            [
                'name' => 'Bar',
                'slug' => 'bar',
                'description' => 'Barra de bar funcional',
                'icon' => null,
                'category' => 'Instalaciones',
            ],
            [
                'name' => 'Spa',
                'slug' => 'spa',
                'description' => 'Servicios de spa y bienestar',
                'icon' => null,
                'category' => 'Instalaciones',
            ],
            [
                'name' => 'Gimnasio',
                'slug' => 'gimnasio',
                'description' => 'Instalaciones deportivas y gimnasio',
                'icon' => null,
                'category' => 'Instalaciones',
            ],
            [
                'name' => 'Acceso para minusválidos',
                'slug' => 'acceso-para-minusvalidos',
                'description' => 'Rampa de acceso y ascensor',
                'icon' => null,
                'category' => 'Accesibilidad',
            ],
            [
                'name' => 'Aseos adaptados',
                'slug' => 'aseos-adaptados',
                'description' => 'Baños con accesibilidad para discapacitados',
                'icon' => null,
                'category' => 'Accesibilidad',
            ],
            [
                'name' => 'Estacionamiento adaptado',
                'slug' => 'estacionamiento-adaptado',
                'description' => 'Plazas de aparcamiento reservadas',
                'icon' => null,
                'category' => 'Accesibilidad',
            ],
            [
                'name' => 'Transporte público cercano',
                'slug' => 'transporte-publico-cercano',
                'description' => 'Próximo a paradas de autobús o metro',
                'icon' => null,
                'category' => 'Ubicación',
            ],
            [
                'name' => 'Centro ciudad',
                'slug' => 'centro-ciudad',
                'description' => 'Ubicado en el centro de la ciudad',
                'icon' => null,
                'category' => 'Ubicación',
            ],
            [
                'name' => 'Junto a autopista',
                'slug' => 'junto-a-autopista',
                'description' => 'Acceso fácil a autopista',
                'icon' => null,
                'category' => 'Ubicación',
            ],
        ];

        foreach ($extras as $extraData) {
            // Inserta cada extra del catalogo.
            Extra::create($extraData);
        }
    }
}
