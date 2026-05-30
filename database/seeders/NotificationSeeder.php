<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Admins activos que reciben notificaciones globales de actividad.
        $adminIds = User::query()
            ->where('user_type', 'admin')
            ->where('is_active', true)
            ->pluck('user_id')
            ->all();

        $bookings = Booking::with(['event', 'venue', 'approvedBy'])->orderBy('booking_id')->get();
        $events = Event::query()->orderBy('event_id')->get();
        $venues = Venue::query()->orderBy('venue_id')->get();

        if ($bookings->isEmpty() && $events->isEmpty() && $venues->isEmpty()) {
            return;
        }

        $now = now();
        $notifications = [];

        foreach ($bookings as $booking) {
            $eventName = $booking->event?->name ?? 'evento sin nombre';
            $venueName = $booking->venue?->name ?? 'local sin nombre';
            $start = $booking->start_datetime ? $booking->start_datetime->format('d/m/Y H:i') : null;
            $end = $booking->end_datetime ? $booking->end_datetime->format('d/m/Y H:i') : null;

            // Notificacion de creacion de reserva para admins, manager y organizador.
            $recipientIds = $adminIds;

            if ($booking->venue?->manager_id) {
                $recipientIds[] = (int) $booking->venue->manager_id;
            }

            if ($booking->event?->organizer_id) {
                $recipientIds[] = (int) $booking->event->organizer_id;
            }

            $recipientIds = collect($recipientIds)->unique()->values()->all();

            $createdMessage = "Se ha registrado una reserva pendiente para el evento '{$eventName}' en el local '{$venueName}'.";
            if ($start && $end) {
                $createdMessage .= " Horario: {$start} - {$end}.";
            }

            foreach ($recipientIds as $userId) {
                $notifications[] = [
                    'user_id' => $userId,
                    'title' => 'Reserva pendiente',
                    'message' => $createdMessage,
                    'is_read' => true,
                    'notification_type' => 'booking_created',
                    'related_entity_type' => 'booking',
                    'related_entity_id' => $booking->booking_id,
                    'created_at' => $now,
                ];
            }

            // Notificacion de cambio de estado para admins, manager y organizador.
            if (in_array($booking->booking_status, ['confirmed', 'cancelled'], true) && $booking->event) {
                $statusLabel = $booking->booking_status === 'confirmed' ? 'aprobada' : 'cancelada';
                $reviewedBy = $booking->approvedBy?->username ?? 'Un administrador';
                $resolvedMessage = "{$reviewedBy} ha {$statusLabel} la reserva del evento '{$eventName}' para '{$venueName}'.";
                if ($start && $end) {
                    $resolvedMessage .= " Horario: {$start} - {$end}.";
                }

                $statusRecipientIds = collect(array_merge(
                    $adminIds,
                    [
                        (int) $booking->event->organizer_id,
                        (int) ($booking->venue?->manager_id ?? 0),
                    ]
                ))
                    ->filter(fn (int $id) => $id > 0)
                    ->unique()
                    ->values()
                    ->all();

                foreach ($statusRecipientIds as $userId) {
                    $notifications[] = [
                        'user_id' => $userId,
                        'title' => "Reserva {$statusLabel}",
                        'message' => $resolvedMessage,
                        'is_read' => true,
                        'notification_type' => 'booking_status_updated',
                        'related_entity_type' => 'booking',
                        'related_entity_id' => $booking->booking_id,
                        'created_at' => $now,
                    ];
                }
            }
        }

        if (! empty($adminIds)) {
            foreach ($events as $event) {
                foreach ($adminIds as $adminId) {
                    $notifications[] = [
                        'user_id' => $adminId,
                        'title' => 'Evento creado',
                        'message' => "Se ha creado el evento '{$event->name}'.",
                        'is_read' => true,
                        'notification_type' => 'event_activity',
                        'related_entity_type' => 'event',
                        'related_entity_id' => $event->event_id,
                        'created_at' => $now,
                    ];
                }
            }

            foreach ($venues as $venue) {
                foreach ($adminIds as $adminId) {
                    $notifications[] = [
                        'user_id' => $adminId,
                        'title' => 'Local creado',
                        'message' => "Se ha creado el local '{$venue->name}'.",
                        'is_read' => true,
                        'notification_type' => 'venue_activity',
                        'related_entity_type' => 'venue',
                        'related_entity_id' => $venue->venue_id,
                        'created_at' => $now,
                    ];
                }
            }
        }

        if (! empty($notifications)) {
            // Inserta todo el lote en una sola operacion.
            DB::table('notifications')->insert($notifications);
        }
    }
}
