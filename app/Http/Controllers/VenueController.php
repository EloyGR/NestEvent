<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    /**
     * Muestra una lista de lugares (venues) paginados.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener todos los lugares paginados (5 por página)
        $venues = Venue::paginate(5);

        // Retornar la vista con los lugares
        return view('venues.index', compact('venues'));
    }

    /**
     * Muestra los detalles de un lugar específico.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Load the venue with its images
        $venue = Venue::with('images')->findOrFail($id);

        // Pass the venue to the view
        return view('venues.show', compact('venue'));
    }

    /**
     * Muestra el formulario para crear un nuevo lugar.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Retornar la vista para crear un lugar
        return view('venues.create');
    }

    /**
     * Almacena un nuevo lugar en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:50',
            'price_per_hour' => 'nullable|numeric|min:0',
            'venue_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Debugging: Log the incoming request data
        \Log::info('Incoming request data:', $request->all());

        // Debugging: Check if the file is being uploaded
        if ($request->hasFile('venue_image')) {
            \Log::info('File upload detected:', [
                'original_name' => $request->file('venue_image')->getClientOriginalName(),
                'mime_type' => $request->file('venue_image')->getMimeType(),
                'size' => $request->file('venue_image')->getSize(),
            ]);
        } else {
            \Log::warning('No file uploaded with the request.');
        }

        // Debugging: Log validation results
        \Log::info('Validation passed data:', $validatedData);

        // Asignar el ID del administrador autenticado
        $validatedData['manager_id'] = auth()->id();

        // Crear un nuevo lugar (venue)
        $venue = Venue::create($validatedData);

        if ($request->hasFile('venue_image')) {
            $file = $request->file('venue_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('venue_images', $filename, 'public');

            \DB::table('venue_images')->insert([
                'venue_id' => $venue->venue_id,
                'image_url' => $filePath,
                'is_primary' => true,
                'upload_date' => now(),
            ]);
        }

        // Redirigir al índice de lugares con un mensaje de éxito
        return redirect()->route('venues.index')->with('success', 'Local registrado exitosamente.');
    }

    /**
     * Muestra los lugares del administrador autenticado.
     *
     * @return \Illuminate\View\View
     */

    public function myVenues()
    {
        return 'Route is working!';
    }

    /*public function myVenues()
    {
        $userId = auth()->id();

        // Obtener los locales donde el manager_id coincide con el ID del usuario autenticado
        $venues = Venue::where('manager_id', $userId)->paginate(10);

        return view('venues.index', compact('venues')); // Reutilizar la vista index
    }*/
}