<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\ConfirmedBooking;
use App\Models\Event;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // Gestores objetivo para poblar reservas semilla.
        $managerUsernames = [
            'ana.garcia',
            'pedro.lopez',
            'laura.sanchez',
            'jorge.castillo',
            'marta.vidal',
        ];

        // Obtiene usuarios y locales base para construir reservas de ejemplo.

        // Genera un CASE para mantener el orden de los usernames en PostgreSQL
        $orderCases = 'CASE';
        foreach ($managerUsernames as $i => $username) {
            $orderCases .= " WHEN username = '$username' THEN $i";
        }
        $orderCases .= ' END';

        $managers = User::query()
            ->whereIn('username', $managerUsernames)
            ->orderByRaw($orderCases)
            ->get();

        $venues = Venue::query()
            ->select(['venue_id', 'capacity'])
            ->orderByDesc('capacity')
            ->get();

        if ($managers->isEmpty() || $venues->isEmpty()) {
            return;
        }

        $adminId = User::query()
            ->where('user_type', 'admin')
            ->orderBy('user_id')
            ->value('user_id');

        $globalVenueOffset = 0;

        // Helper de selección: prioriza capacidad suficiente y evita repetir local cuando es posible.
        $pickVenue = function (int $expected, ?int $excludeVenueId = null) use ($venues, &$globalVenueOffset) {
            $candidate = $venues->first(function ($venue) use ($expected, $excludeVenueId) {
                return (int) $venue->capacity >= $expected
                    && ($excludeVenueId === null || (int) $venue->venue_id !== $excludeVenueId);
            });

            if ($candidate) {
                return $candidate;
            }

            for ($i = 0; $i < $venues->count(); $i++) {
                $fallback = $venues[($globalVenueOffset + $i) % $venues->count()];
                if ($excludeVenueId === null || (int) $fallback->venue_id !== $excludeVenueId) {
                    $globalVenueOffset = ($globalVenueOffset + $i + 1) % max(1, $venues->count());
                    return $fallback;
                }
            }

            return $venues[0];
        };

        // Helper de creación: centraliza consistencia temporal y metadatos según estado.
        $createSeedBooking = function (Event $event, int $venueId, string $status) use ($adminId): void {
            $eventStart = Carbon::parse($event->start_datetime);
            $eventEnd = Carbon::parse($event->end_datetime);

            // La reserva se ubica dentro del rango del evento.
            $start = $eventStart->copy()->addHour();
            $end = $start->copy()->addHours(4);

            if ($end->gt($eventEnd)) {
                $end = $eventEnd->copy()->subMinutes(30);
            }

            if ($end->lte($start)) {
                $start = $eventStart->copy();
                $end = $eventEnd->copy();
            }

            $booking = Booking::create([
                'event_id' => $event->event_id,
                'venue_id' => $venueId,
                'booking_status' => $status,
                'start_datetime' => $start,
                'end_datetime' => $end,
                'approved_by' => in_array($status, ['confirmed', 'cancelled'], true) ? $adminId : null,
                'approval_date' => in_array($status, ['confirmed', 'cancelled'], true) ? now() : null,
                'notes' => $status === 'pending'
                    ? 'Reserva semilla pendiente de revisión administrativa.'
                    : ($status === 'cancelled'
                        ? 'Reserva semilla cancelada para pruebas de moderación.'
                        : 'Reserva semilla confirmada para validar disponibilidad.'),
            ]);

            if ($status === 'confirmed') {
                ConfirmedBooking::create([
                    'booking_id' => $booking->booking_id,
                    'venue_id' => $venueId,
                    'start_datetime' => $start,
                    'end_datetime' => $end,
                    'confirmed_at' => now(),
                    'cancelled_at' => null,
                ]);
            }
        };

        foreach ($managers as $index => $manager) {
            $events = Event::query()
                ->where('organizer_id', $manager->user_id)
                ->orderBy('event_id')
                ->take(2)
                ->get();

            if ($events->count() < 2) {
                continue;
            }

            $firstEvent = $events[0];
            $secondEvent = $events[1];

            $firstExpected = max(50, (int) ($firstEvent->expected_attendance ?? 0));
            $secondExpected = max(50, (int) ($secondEvent->expected_attendance ?? 0));

            $firstVenueId = (int) $pickVenue($firstExpected)->venue_id;
            $secondVenueId = (int) $pickVenue($secondExpected, $firstVenueId)->venue_id;

            $createSeedBooking($firstEvent, $firstVenueId, 'confirmed');

            // Mezcla controlada de estados para cubrir panel admin, filtros y notificaciones.
            $secondStatus = $index % 3 === 0 ? 'cancelled' : ($index % 2 === 0 ? 'pending' : 'confirmed');
            $createSeedBooking($secondEvent, $secondVenueId, $secondStatus);
        }
    }
}
