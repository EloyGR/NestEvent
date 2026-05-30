<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * CRUD de eventos (referencia rapida):
     * - Create: create(), store()
     * - Read: index(), show(), myEvents()
     * - Update: edit(), update()
     * - Delete: destroy()
     */
    private function canCreateEvents(): bool
    {
        // Aplica permiso de creacion de eventos por rol.
        $user = auth()->user();

        return $user && in_array($user->user_type, ['admin', 'event_manager'], true);
    }

    private function canManageEvent(Event $event): bool
    {
        // Permite gestion si es admin o propietario del evento.
        $user = auth()->user();

        return $user && ($user->user_type === 'admin' || (int) $user->user_id === (int) $event->organizer_id);
    }

    private function notifyAdminsAboutEventAction(string $title, string $message, ?int $eventId = null): void
    {
        // Todas las acciones relevantes de eventos generan trazabilidad para admins.
        $title = preg_replace('/\bnuev[oa]\b\s*/iu', '', $title) ?? $title;
        $title = trim(preg_replace('/\s{2,}/', ' ', $title) ?? $title);

        $adminIds = User::query()
            ->where('user_type', 'admin')
            ->where('is_active', true)
            ->pluck('user_id')
            ->all();

        if (empty($adminIds)) {
            return;
        }

        $now = now();
        $rows = array_map(function (int $adminId) use ($title, $message, $eventId, $now) {
            return [
                'user_id' => $adminId,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
                'notification_type' => 'event_activity',
                'related_entity_type' => 'event',
                'related_entity_id' => $eventId,
                'created_at' => $now,
            ];
        }, $adminIds);

        DB::table('notifications')->insert($rows);
    }

    /**
     * Muestra una lista de eventos paginados.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Event::query();

        // Filtra por texto libre sobre nombre y descripcion.
        if ($request->has('search') && $request->search !== null) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        // Pagina resultados para mantener tiempos de carga estables.
        $events = $query->paginate(5);

        // Retorna la vista con los eventos.
        return view('events.index', compact('events'));
    }

    /**
     * Muestra los detalles de un evento específico.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Busca el evento por ID o devuelve 404.
        $event = Event::findOrFail($id);

        // Retorna la vista de detalle del evento.
        return view('events.show', compact('event'));
    }

    /**
     * Muestra el formulario para crear un nuevo evento.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (! $this->canCreateEvents()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear eventos.');
        }

        // Retorna la vista de creacion.
        return view('events.create');
    }

    /**
     * Almacena un nuevo evento en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (! $this->canCreateEvents()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear eventos.');
        }

        // Valida campos de negocio.
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'event_type' => 'nullable|string|max:50',
            'expected_attendance' => 'nullable|integer|min:1',
            'is_public' => 'required|boolean',
        ]);

        // Vincula el evento al usuario autenticado que lo crea.
        $validatedData['organizer_id'] = auth()->id();

        // Persiste el evento y notifica actividad administrativa.
        $event = Event::create($validatedData);

        $actor = auth()->user()?->username ?? 'Sistema';
        $this->notifyAdminsAboutEventAction(
            'Evento creado',
            "{$actor} ha creado el evento '{$event->name}'.",
            (int) $event->event_id
        );

        // Redirige al indice con mensaje de exito.
        return redirect()->route('events.index')->with('success', 'Evento registrado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un evento.
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);

        if (! $this->canManageEvent($event)) {
            return redirect()->route('events.show', $event->event_id)
                ->with('error', 'No tienes permisos para editar este evento.');
        }

        return view('events.edit', compact('event'));
    }

    /**
     * Actualiza un evento existente.
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        if (! $this->canManageEvent($event)) {
            return redirect()->route('events.show', $event->event_id)
                ->with('error', 'No tienes permisos para editar este evento.');
        }

        // Se vuelve a validar para evitar saltarse reglas vía edición.
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'event_type' => 'nullable|string|max:50',
            'expected_attendance' => 'nullable|integer|min:1',
            'is_public' => 'required|boolean',
        ]);

        $event->update($validatedData);

        $actor = auth()->user()?->username ?? 'Sistema';
        $this->notifyAdminsAboutEventAction(
            'Evento actualizado',
            "{$actor} ha actualizado el evento '{$event->name}'.",
            (int) $event->event_id
        );

        return redirect()->route('events.show', $event->event_id)
            ->with('success', 'Evento actualizado correctamente.');
    }

    /**
     * Elimina un evento si no tiene reservas asociadas.
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if (! $this->canManageEvent($event)) {
            return redirect()->route('events.show', $event->event_id)
                ->with('error', 'No tienes permisos para eliminar este evento.');
        }

        // Protección de integridad: no se elimina si tiene reservas relacionadas.
        if (Booking::where('event_id', $event->event_id)->exists()) {
            return redirect()->route('events.show', $event->event_id)
                ->with('error', 'No puedes eliminar un evento con reservas asociadas.');
        }

        $eventName = $event->name;
        $eventId = (int) $event->event_id;
        $actor = auth()->user()?->username ?? 'Sistema';

        $event->delete();

        $this->notifyAdminsAboutEventAction(
            'Evento eliminado',
            "{$actor} ha eliminado el evento '{$eventName}'.",
            $eventId
        );

        return redirect()->route('events.index')->with('success', 'Evento eliminado correctamente.');
    }

    /**
     * Muestra los eventos del organizador autenticado.
     *
     * @return \Illuminate\View\View
     */
    public function myEvents()
    {
        $userId = auth()->id(); // obtener el ID del usuario autenticado

        // Reutilizamos índice con un scope por organizador autenticado.
        $events = Event::where('organizer_id', $userId)->paginate(10);

        return view('events.index', compact('events')); // Reutilizar la vista index
    }
}