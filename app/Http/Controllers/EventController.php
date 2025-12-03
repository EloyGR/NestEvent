<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Muestra una lista de eventos paginados.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener los eventos paginados de 5 en 5
        $events = Event::paginate(5);

        // Retornar la vista con los eventos
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
        // Buscar el evento por su ID o lanzar un error 404
        $event = Event::findOrFail($id);

        // Retornar la vista con los datos del evento
        return view('events.show', compact('event'));
    }

    /**
     * Muestra el formulario para crear un nuevo evento.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Vista para crear un evento
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
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'event_type' => 'nullable|string|max:50',
            'expected_attendance' => 'nullable|integer|min:1',
            'is_public' => 'required|boolean',
        ]);

        // Asignar el ID del organizador autenticado
        $validatedData['organizer_id'] = auth()->id();


        // Crear un nuevo evento
        Event::create($validatedData);

        // Redirigir al índice de eventos con un mensaje de éxito
        return redirect()->route('events.index')->with('success', 'Evento registrado exitosamente.');
    }

    /**
     * Muestra los eventos del organizador autenticado.
     *
     * @return \Illuminate\View\View
     */
    public function myEvents()
    {
        $userId = auth()->id(); // obtener el ID del usuario autenticado

        // Obtener eventos donde el organizer_id coincide con el ID del usuario autenticado
        $events = Event::where('organizer_id', $userId)->paginate(10);

        return view('events.index', compact('events')); // Reutilizar la vista index
    }
}