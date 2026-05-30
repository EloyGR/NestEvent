<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ConfirmedBooking;
use App\Models\Event;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * CRUD de bookings (referencia rapida):
     * - Create: create(), store()
     * - Read: index()
     * - Update: updateStatus()
     * - Delete: no implementado en este controlador
     */
    /**
     * Permite el flujo de reservas solo para admin y gestores de eventos.
     */
    private function canManageBookings(): bool
    {
        $user = auth()->user();

        return $user && in_array($user->user_type, ['admin', 'event_manager'], true);
    }

    private function getAllowedIntervalForDate(int $venueId, Carbon $date): ?array
    {
        // Resuelve disponibilidad por prioridad: excepciones y luego horario semanal.
        $dateString = $date->toDateString();

        $exception = DB::table('availability_exceptions')
            ->where('venue_id', $venueId)
            ->whereDate('start_date', '<=', $dateString)
            ->whereDate('end_date', '>=', $dateString)
            ->orderByDesc('start_date')
            ->first();

        if ($exception) {
            if (! $exception->is_available) {
                return null;
            }

            if ($exception->opening_time && $exception->closing_time) {
                return [
                    'open' => Carbon::createFromFormat('Y-m-d H:i:s', $dateString . ' ' . $exception->opening_time),
                    'close' => Carbon::createFromFormat('Y-m-d H:i:s', $dateString . ' ' . $exception->closing_time),
                    'source' => 'exception',
                ];
            }
        }

        $slot = DB::table('venue_availability')
            ->where('venue_id', $venueId)
            ->where('day_of_week', $date->dayOfWeek)
            ->first();

        if (! $slot || ! $slot->is_available || ! $slot->opening_time || ! $slot->closing_time) {
            return null;
        }

        return [
            'open' => Carbon::createFromFormat('Y-m-d H:i:s', $dateString . ' ' . $slot->opening_time),
            'close' => Carbon::createFromFormat('Y-m-d H:i:s', $dateString . ' ' . $slot->closing_time),
            'source' => 'weekly',
        ];
    }

    private function validateBookingWithinVenueSchedule(int $venueId, Carbon $startDateTime, Carbon $endDateTime): ?string
    {
        // Recorremos día a día para validar reservas que abarcan varios días.
        $startDay = $startDateTime->copy()->startOfDay();
        $endDay = $endDateTime->copy()->startOfDay();

        for ($day = $startDay->copy(); $day->lte($endDay); $day->addDay()) {
            $interval = $this->getAllowedIntervalForDate($venueId, $day);
            if (! $interval) {
                return 'El local no esta disponible en una o mas fechas del rango seleccionado.';
            }

            if ($day->isSameDay($startDateTime) && $startDateTime->lt($interval['open'])) {
                return 'La hora de inicio queda fuera del horario permitido para el local.';
            }

            if ($day->isSameDay($endDateTime) && $endDateTime->gt($interval['close'])) {
                return 'La hora de fin queda fuera del horario permitido para el local.';
            }
        }

        return null;
    }

    private function buildBookingExceptionReason(Booking $booking): string
    {
        return 'Reserva confirmada: ' . ($booking->event?->name ?? 'evento sin nombre') . ' (#' . $booking->booking_id . ')';
    }

    private function upsertBookingAvailabilityException(Booking $booking): void
    {
        // Reflejamos una booking confirmada como excepción no disponible en el calendario.
        if (! $booking->start_datetime || ! $booking->end_datetime) {
            return;
        }

        // Solo bloqueamos agenda de reservas confirmadas no realizadas aun.
        if (Carbon::parse($booking->end_datetime)->lt(now())) {
            return;
        }

        DB::table('availability_exceptions')->updateOrInsert(
            [
                'venue_id' => $booking->venue_id,
                'start_date' => Carbon::parse($booking->start_datetime)->toDateString(),
                'end_date' => Carbon::parse($booking->end_datetime)->toDateString(),
            ],
            [
                'opening_time' => null,
                'closing_time' => null,
                'is_available' => false,
                'reason' => $this->buildBookingExceptionReason($booking),
            ]
        );
    }

    private function deleteBookingAvailabilityException(Booking $booking): void
    {
        // Eliminamos solo excepciones relacionadas con esta booking para no afectar otras reglas.
        if (! $booking->start_datetime || ! $booking->end_datetime) {
            return;
        }

        $bookingIdSuffix = '(#' . $booking->booking_id . ')';

        DB::table('availability_exceptions')
            ->where('venue_id', $booking->venue_id)
            ->whereDate('start_date', Carbon::parse($booking->start_datetime)->toDateString())
            ->whereDate('end_date', Carbon::parse($booking->end_datetime)->toDateString())
            ->where(function ($query) use ($bookingIdSuffix, $booking) {
                $query->where('reason', $this->buildBookingExceptionReason($booking))
                    ->orWhere('reason', 'like', '%' . $bookingIdSuffix);
            })
            ->delete();
    }

    private function syncUpcomingConfirmedBookingExceptions(): void
    {
        DB::transaction(function () {
            // Reconstruye excepciones desde la fuente de verdad para evitar residuos.
            // Limpia excepciones ligadas a reservas confirmadas anteriores o inconsistentes.
            DB::table('availability_exceptions')
                ->where('reason', 'like', 'Reserva confirmada:%')
                ->delete();

            // Reinserta solo reservas confirmadas que aun no han finalizado.
            $confirmedUpcomingBookings = Booking::query()
                ->with('event')
                ->where('booking_status', 'confirmed')
                ->where('end_datetime', '>=', now())
                ->get();

            foreach ($confirmedUpcomingBookings as $booking) {
                $this->upsertBookingAvailabilityException($booking);
            }

            \Log::info('Sync booking availability exceptions completed', [
                'confirmed_upcoming_count' => $confirmedUpcomingBookings->count(),
            ]);
        });
    }

    /**
     * Actualiza el estado de una reserva (solo admin).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        // Solo se aceptan estados esperados para evitar transiciones inválidas.
        $request->validate([
            'booking_status' => 'required|in:pending,confirmed,cancelled',
        ]);

        // Cargamos evento y local para reutilizar sus datos al validar y notificar.
        $booking = Booking::with(['event', 'venue'])->findOrFail($id);

        try {
            // Ejecuta en transaccion: estado, confirmacion, excepciones y notificaciones.
            DB::transaction(function () use ($request, $booking) {
                $newStatus = $request->booking_status;

                if ($newStatus === 'confirmed') {
                    // Validamos solape contra confirmadas activas en el mismo local.
                    $hasOverlap = ConfirmedBooking::query()
                        ->where('venue_id', $booking->venue_id)
                        ->whereNull('cancelled_at')
                        ->where('booking_id', '!=', $booking->booking_id)
                        ->where('start_datetime', '<', $booking->end_datetime)
                        ->where('end_datetime', '>', $booking->start_datetime)
                        ->exists();

                    if ($hasOverlap) {
                        throw new \RuntimeException('Ya existe otra reserva confirmada en ese local para este horario.');
                    }

                    ConfirmedBooking::updateOrCreate(
                        ['booking_id' => $booking->booking_id],
                        [
                            'venue_id' => $booking->venue_id,
                            'start_datetime' => $booking->start_datetime,
                            'end_datetime' => $booking->end_datetime,
                            'confirmed_at' => now(),
                            'cancelled_at' => null,
                        ]
                    );

                    $this->upsertBookingAvailabilityException($booking);
                }

                if (in_array($newStatus, ['pending', 'cancelled'], true)) {
                    // Si se cancela, liberamos la excepción de agenda asociada.
                    ConfirmedBooking::query()
                        ->where('booking_id', $booking->booking_id)
                        ->whereNull('cancelled_at')
                        ->update(['cancelled_at' => now()]);

                    $this->deleteBookingAvailabilityException($booking);
                }

                $booking->booking_status = $newStatus;
                $booking->approved_by = auth()->user()->user_id;
                $booking->approval_date = now();
                $booking->save();

                // Solo notificamos al organizador cuando la reserva queda resuelta: aprobada o cancelada.
                if (in_array($newStatus, ['confirmed', 'cancelled'], true) && $booking->event) {
                    $statusLabel = $newStatus === 'confirmed' ? 'aprobada' : 'cancelada';
                    $actionLabel = $newStatus === 'confirmed' ? 'aprobado' : 'cancelado';
                    $reviewedBy = auth()->user()?->username ?? 'Un administrador';
                    $venueName = $booking->venue?->name ?? 'el local seleccionado';
                    $start = $booking->start_datetime ? $booking->start_datetime->format('d/m/Y H:i') : null;
                    $end = $booking->end_datetime ? $booking->end_datetime->format('d/m/Y H:i') : null;

                    $message = "{$reviewedBy} ha {$actionLabel} la reserva de tu evento '{$booking->event->name}' para '{$venueName}'.";
                    if ($start && $end) {
                        $message .= " Horario: {$start} - {$end}.";
                    }

                    $adminIds = User::query()
                        ->where('user_type', 'admin')
                        ->where('is_active', true)
                        ->pluck('user_id')
                        ->all();

                    $recipientIds = collect(array_merge(
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

                    if (! empty($recipientIds)) {
                        $now = now();
                        // Inserta notificaciones en bloque para todos los destinatarios.
                        $notifications = array_map(function ($userId) use ($statusLabel, $message, $booking, $now) {
                            return [
                                'user_id' => $userId,
                                'title' => "Reserva {$statusLabel}",
                                'message' => $message,
                                'is_read' => false,
                                'notification_type' => 'booking_status_updated',
                                'related_entity_type' => 'booking',
                                'related_entity_id' => $booking->booking_id,
                                'created_at' => $now,
                            ];
                        }, $recipientIds);

                        DB::table('notifications')->insert($notifications);
                    }
                }
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('adminpanel')->with('error', $e->getMessage());
        }

        if ($request->booking_status === 'confirmed') {
            return redirect()->route('adminpanel')->with('success', 'Reserva aprobada correctamente.');
        }

        if ($request->booking_status === 'cancelled') {
            return redirect()->route('adminpanel')->with('success', 'Reserva cancelada y bloque horario liberado correctamente.');
        }

        return redirect()->route('adminpanel')->with('success', 'Estado de la reserva actualizado correctamente.');
    }

    /**
     * Muestra el formulario para crear una nueva reserva.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        if (! $this->canManageBookings()) {
            return redirect()->route('home')->with('error', 'Solo administradores y gestores de eventos pueden crear reservas.');
        }

        $user = auth()->user();

        $events = Event::query();
        if ($user->user_type === 'event_manager') {
            // Un gestor solo puede reservar usando eventos de su propia organización.
            $events->where('organizer_id', $user->user_id);
        }

        $events = $events->get();
        $venues = Venue::all();
        $selectedVenue = $request->query('venue_id');
        $availabilityExceptionsByVenue = DB::table('availability_exceptions')
            // Solo enviamos excepciones no disponibles para bloquear selección en frontend.
            ->select('venue_id', 'start_date', 'end_date', 'reason')
            ->where('is_available', false)
            ->orderBy('start_date')
            ->get()
            ->groupBy('venue_id')
            ->map(function ($rows) {
                return $rows->map(function ($row) {
                    return [
                        'start_date' => $row->start_date,
                        'end_date' => $row->end_date,
                        'reason' => $row->reason,
                    ];
                })->values();
            });

        $venueAvailabilityByVenue = DB::table('venue_availability')
            ->select('venue_id', 'day_of_week', 'opening_time', 'closing_time', 'is_available')
            ->orderBy('venue_id')
            ->orderByRaw("CASE day_of_week
                WHEN 1 THEN 1
                WHEN 2 THEN 2
                WHEN 3 THEN 3
                WHEN 4 THEN 4
                WHEN 5 THEN 5
                WHEN 6 THEN 6
                WHEN 0 THEN 7
                ELSE 8
            END")
            ->get()
            ->groupBy('venue_id')
            ->map(function ($rows) {
                return $rows->map(function ($row) {
                    return [
                        'day_of_week' => (int) $row->day_of_week,
                        'opening_time' => $row->opening_time,
                        'closing_time' => $row->closing_time,
                        'is_available' => (bool) $row->is_available,
                    ];
                })->values();
            });

        // Registrar los datos de los eventos
        \Log::info('Events:', $events->toArray());

        return view('bookings.create', compact('events', 'venues', 'selectedVenue', 'availabilityExceptionsByVenue', 'venueAvailabilityByVenue'));
    }

    /**
     * Almacena una nueva reserva en la base de datos.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (! $this->canManageBookings()) {
            return redirect()->route('home')->with('error', 'Solo administradores y gestores de eventos pueden crear reservas.');
        }

        $user = auth()->user();

        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'venue_id' => 'required|exists:venues,venue_id',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $event = Event::findOrFail($request->event_id);

        // Evita IDOR: un gestor de eventos solo puede reservar con eventos propios.
        if ($user->user_type === 'event_manager' && (int) $event->organizer_id !== (int) $user->user_id) {
            return back()
                ->withInput()
                ->withErrors([
                    'event_id' => 'Solo puedes crear reservas para eventos de tu propiedad.',
                ]);
        }

        $venue = Venue::findOrFail($request->venue_id);

        $existingBooking = Booking::query()
            // Regla de negocio: un evento no duplica reserva sobre el mismo local.
            ->where('event_id', $event->event_id)
            ->where('venue_id', $venue->venue_id)
            ->first();

        if ($existingBooking) {
            return back()
                ->withInput()
                ->withErrors([
                    'venue_id' => 'Ya existe una reserva para este evento y este local.',
                ]);
        }

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->start_date . ' ' . $request->start_time);
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->end_date . ' ' . $request->end_time);

        if ($endDateTime->lte($startDateTime)) {
            return back()
                ->withInput()
                ->withErrors([
                    'end_time' => 'La fecha y hora de fin debe ser posterior al inicio.',
                ]);
        }

        $blockingException = DB::table('availability_exceptions')
            // Bloqueo por excepciones de no disponibilidad dentro del rango solicitado.
            ->where('venue_id', $venue->venue_id)
            ->where('is_available', false)
            ->whereDate('start_date', '<=', $endDateTime->toDateString())
            ->whereDate('end_date', '>=', $startDateTime->toDateString())
            ->orderBy('start_date')
            ->first();

        if ($blockingException) {
            $exceptionStart = Carbon::parse($blockingException->start_date)->format('d/m/Y');
            $exceptionEnd = Carbon::parse($blockingException->end_date)->format('d/m/Y');

            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => "El local tiene una excepcion de disponibilidad entre {$exceptionStart} y {$exceptionEnd}. Selecciona otro rango.",
                ]);
        }

        $scheduleError = $this->validateBookingWithinVenueSchedule((int) $venue->venue_id, $startDateTime, $endDateTime);
        if ($scheduleError) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' => $scheduleError,
                ]);
        }

        DB::transaction(function () use ($event, $venue, $request, $startDateTime, $endDateTime) {
            // Persistimos la booking como pending hasta revisión administrativa.
            $booking = new Booking();
            $booking->event_id = $event->event_id;
            $booking->venue_id = $venue->venue_id;
            $booking->booking_status = 'pending';
            $booking->start_datetime = $startDateTime;
            $booking->end_datetime = $endDateTime;
            $booking->approved_by = null;
            $booking->approval_date = null;
            $booking->notes = $request->notes;
            $booking->save();

            $adminIds = User::query()
                ->where('user_type', 'admin')
                ->where('is_active', true)
                ->pluck('user_id')
                ->all();

            // Notificamos a admins, manager del local y organizador (creador de la reserva), evitando IDs repetidos.
            $recipientIds = collect(array_merge($adminIds, [(int) $venue->manager_id, (int) $event->organizer_id]))
                ->unique()
                ->values()
                ->all();

            if (empty($recipientIds)) {
                return;
            }

            $eventName = $event->name;
            $venueName = $venue->name;
            $start = $startDateTime->format('d/m/Y H:i');
            $end = $endDateTime->format('d/m/Y H:i');

            $message = "Se ha registrado una reserva pendiente para el evento '{$eventName}' en el local '{$venueName}'.";
            if ($start && $end) {
                $message .= " Horario: {$start} - {$end}.";
            }

            $now = now();
            // Construimos todas las filas y hacemos un solo insert para reducir consultas.
            $notifications = array_map(function ($userId) use ($booking, $message, $now) {
                return [
                    'user_id' => $userId,
                    'title' => 'Reserva pendiente',
                    'message' => $message,
                    'is_read' => false,
                    'notification_type' => 'booking_created',
                    'related_entity_type' => 'booking',
                    'related_entity_id' => $booking->booking_id,
                    'created_at' => $now,
                ];
            }, $recipientIds);

            DB::table('notifications')->insert($notifications);
        });

        return redirect()->route('bookings.index')->with('success', '¡Reserva creada con éxito!');
    }

    /**
     * Muestra una lista de las reservas.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (! $this->canManageBookings()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a reservas.');
        }

        $user = auth()->user();

        $query = Booking::with(['event', 'venue', 'approvedBy']);

        if ($user->user_type === 'admin') {
            // Admin ve todas las reservas.
        } elseif ($user->user_type === 'event_manager') {
            // Event manager solo ve reservas vinculadas a sus eventos.
            $query->whereHas('event', function ($q) use ($user) {
                $q->where('organizer_id', $user->user_id);
            });
        } else {
            // Usuarios normales no deberian ver reservas y reciben un conjunto vacio.
            $query->whereRaw('1 = 0');
        }

        $allowedStatuses = ['pending', 'confirmed', 'cancelled'];
        $status = in_array($request->query('status'), $allowedStatuses)
            ? $request->query('status')
            : null;

        if ($status) {
            $query->where('booking_status', $status);
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('bookings.index', compact('bookings', 'status'));
    }

    public function adminPanel(Request $request)
    {
        // Sincronizamos bloqueos de agenda para confirmadas futuras por si faltaba alguna excepcion.
        $this->syncUpcomingConfirmedBookingExceptions();

        $allowedStatuses = ['pending', 'confirmed', 'cancelled'];
        $status = in_array($request->query('status'), $allowedStatuses, true)
            ? $request->query('status')
            : null;

        $bookingsQuery = Booking::with(['event', 'venue'])
            ->orderBy('start_datetime')
            ->orderBy('booking_id');

        if ($status) {
            $bookingsQuery->where('booking_status', $status);
        }

        $bookings = $bookingsQuery
            // Conservamos parámetros de filtro para navegación entre páginas.
            ->paginate(10)
            ->withQueryString();

        return view('adminpanel', compact('bookings', 'status'));
    }
}
