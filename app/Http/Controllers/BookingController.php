<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Muestra el formulario para crear una nueva reserva.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $events = Event::all();
        $venues = Venue::all();
        $selectedVenue = $request->query('venue_id');

        // Registrar los datos de los eventos
        \Log::info('Events:', $events->toArray());

        return view('bookings.create', compact('events', 'venues', 'selectedVenue'));
    }

    /**
     * Almacena una nueva reserva en la base de datos.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'venue_id' => 'required|exists:venues,venue_id',
        ]);

        $booking = new Booking();
        $booking->event_id = $request->event_id;
        $booking->venue_id = $request->venue_id;
        $booking->booking_status = 'pending'; // Pendiente de aprobación
        $booking->booking_date = now();
        $venue = Venue::findOrFail($request->venue_id);
        $booking->approved_by = null;
        $booking->approval_date = null;
        $booking->notes = $request->notes;
        $booking->save();

        return redirect()->route('bookings.index')->with('success', '¡Reserva creada con éxito!');
    }

    /**
     * Muestra una lista de las reservas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $bookings = Booking::whereHas('event', function ($query) {
            $query->where('organizer_id', Auth::id());
        })->with(['event', 'venue', 'approvedBy'])->paginate(10);

        return view('bookings.index', compact('bookings'));
    }
}
