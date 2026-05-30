<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Extra;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VenueController extends Controller
{
    // Orden numerico usado en formularios y persistencia de disponibilidad semanal.
    private const WEEK_DAYS = [1, 2, 3, 4, 5, 6, 0];

    /**
     * CRUD de venues (referencia rapida):
     * - Create: create(), store()
     * - Read: index(), show(), myVenues()
     * - Update: edit(), update()
     * - Delete: destroy()
     */
    private function getGroupedExtras()
    {
        // Agrupa extras por categoria para facilitar seleccion en vistas create y edit.
        return Extra::query()
            ->orderByRaw("CASE WHEN category IS NULL OR category = '' THEN 1 ELSE 0 END")
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy(function (Extra $extra) {
                return $extra->category ?: 'Otros';
            });
    }

    private function canCreateVenues(): bool
    {
        $user = auth()->user();

        return $user && in_array($user->user_type, ['admin', 'local_manager'], true);
    }

    private function canManageVenue(Venue $venue): bool
    {
        $user = auth()->user();

        return $user && ($user->user_type === 'admin' || (int) $user->user_id === (int) $venue->manager_id);
    }

    private function notifyAdminsAboutVenueAction(string $title, string $message, ?int $venueId = null): void
    {
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
        $rows = array_map(function (int $adminId) use ($title, $message, $venueId, $now) {
            return [
                'user_id' => $adminId,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
                'notification_type' => 'venue_activity',
                'related_entity_type' => 'venue',
                'related_entity_id' => $venueId,
                'created_at' => $now,
            ];
        }, $adminIds);

        DB::table('notifications')->insert($rows);
    }

    private function applyTextFilter(Builder $query, ?string $search): Builder
    {
        // Aplica filtro global por nombre, direccion, ciudad y descripcion.
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use ($search) {
            $like = '%' . $search . '%';

            $subQuery->where('name', 'like', $like)
                ->orWhere('address', 'like', $like)
                ->orWhere('city', 'like', $like)
                ->orWhere('description', 'like', $like);
        });
    }

    private function getSelectedExtraIds(Request $request): array
    {
        // Normaliza ids recibidos por query string y elimina valores inválidos/repetidos.
        return collect((array) $request->query('extras', []))
            ->map(fn ($extraId) => (int) $extraId)
            ->filter(fn (int $extraId) => $extraId > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function applyExtrasFilter(Builder $query, array $selectedExtraIds): Builder
    {
        if (empty($selectedExtraIds)) {
            return $query;
        }

        return $query->whereHas('extras', function (Builder $extrasQuery) use ($selectedExtraIds) {
            $extrasQuery->whereIn('extras.extra_id', $selectedExtraIds);
        });
    }

    private function storeExtraImages(Request $request, int $venueId): void
    {
        // Guarda imagenes secundarias y crea su registro asociado.
        if (! $request->hasFile('extra_images')) {
            return;
        }

        foreach ((array) $request->file('extra_images') as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $file->storeAs('venue_images', $filename, 'public');

            DB::table('venue_images')->insert([
                'venue_id' => $venueId,
                'image_url' => $filename,
                'main_image' => 0,
                'upload_date' => now(),
            ]);
        }
    }

    private function normalizeAndValidateSchedule(array $rawSchedule): array
    {
        // Estandariza horario semanal y valida coherencia de horas.
        $normalizedSchedule = [];
        $hasOpenDay = false;
        $errors = [];

        foreach (self::WEEK_DAYS as $day) {
            $dayData = (array) ($rawSchedule[$day] ?? []);
            $isAvailable = isset($dayData['is_available']) && (string) $dayData['is_available'] === '1';

            $openingTime = isset($dayData['opening_time']) ? trim((string) $dayData['opening_time']) : null;
            $closingTime = isset($dayData['closing_time']) ? trim((string) $dayData['closing_time']) : null;

            if (! $isAvailable) {
                $normalizedSchedule[$day] = [
                    'opening_time' => null,
                    'closing_time' => null,
                    'is_available' => false,
                ];
                continue;
            }

            $hasOpenDay = true;

            if ($openingTime === null || $openingTime === '' || $closingTime === null || $closingTime === '') {
                $errors["schedule.{$day}.opening_time"] = 'Debes indicar hora de apertura y cierre para cada dia marcado como disponible.';
                continue;
            }

            if (strtotime($closingTime) <= strtotime($openingTime)) {
                $errors["schedule.{$day}.closing_time"] = 'La hora de cierre debe ser posterior a la hora de apertura.';
                continue;
            }

            $normalizedSchedule[$day] = [
                'opening_time' => $openingTime . ':00',
                'closing_time' => $closingTime . ':00',
                'is_available' => true,
            ];
        }

        if (! $hasOpenDay) {
            $errors['schedule'] = 'Debes configurar horario disponible al menos para un dia de la semana.';
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $normalizedSchedule;
    }

    private function upsertVenueAvailability(int $venueId, array $schedule): void
    {
        // Ejecuta upsert por dia para reutilizar logica entre create y update.
        foreach ($schedule as $dayOfWeek => $slot) {
            DB::table('venue_availability')->updateOrInsert(
                [
                    'venue_id' => $venueId,
                    'day_of_week' => (int) $dayOfWeek,
                ],
                [
                    'opening_time' => $slot['opening_time'],
                    'closing_time' => $slot['closing_time'],
                    'is_available' => (bool) $slot['is_available'],
                ]
            );
        }
    }

    private function getVenueAvailabilityMap(int $venueId)
    {
        return DB::table('venue_availability')
            ->where('venue_id', $venueId)
            ->get()
            ->keyBy('day_of_week');
    }

    private function normalizeExceptionTimes(bool $isAvailable, ?string $openingTime, ?string $closingTime): array
    {
        // Si la excepcion no esta disponible, los horarios deben quedar nulos.
        if (! $isAvailable) {
            return [null, null];
        }

        if ($openingTime === null || $openingTime === '' || $closingTime === null || $closingTime === '') {
            throw ValidationException::withMessages([
                'exception_opening_time' => 'Debes indicar apertura y cierre si la excepcion mantiene disponibilidad parcial.',
            ]);
        }

        if (strtotime($closingTime) <= strtotime($openingTime)) {
            throw ValidationException::withMessages([
                'exception_closing_time' => 'La hora de cierre excepcional debe ser posterior a la apertura.',
            ]);
        }

        return [$openingTime . ':00', $closingTime . ':00'];
    }

    /**
     * Muestra una lista de lugares (venues) paginados.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Reutiliza filtros de texto y extras con paginacion consistente.
        $search = $request->query('q');
        $selectedExtraIds = $this->getSelectedExtraIds($request);
        $extrasByCategory = $this->getGroupedExtras();

        $venues = $this->applyExtrasFilter(
            $this->applyTextFilter(Venue::query()->with(['manager', 'images']), $search),
            $selectedExtraIds
        )
            ->paginate(6)
            ->withQueryString();

        return view('venues.index', compact('venues', 'extrasByCategory', 'selectedExtraIds'));
    }

    /**
     * Muestra los detalles de un lugar específico.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Carga el local con sus imagenes y extras.
        $venue = Venue::with(['images', 'extras'])->findOrFail($id);

        $availability = DB::table('venue_availability')
            ->where('venue_id', $venue->venue_id)
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
            ->get();

        $upcomingExceptions = DB::table('availability_exceptions')
            ->where('venue_id', $venue->venue_id)
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->orderBy('end_date')
            ->limit(12)
            ->get();

        // Retorna la vista de detalle del local.
        return view('venues.show', compact('venue', 'availability', 'upcomingExceptions'));
    }

    /**
     * Muestra el formulario para crear un nuevo lugar.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (! $this->canCreateVenues()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear locales.');
        }

        $extrasByCategory = $this->getGroupedExtras();

        return view('venues.create', compact('extrasByCategory'));
    }

    /**
     * Almacena un nuevo lugar en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (! $this->canCreateVenues()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear locales.');
        }

        // Valida datos base, extras, imagenes y horario.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:50',
            'price_per_hour' => 'nullable|numeric|min:0',
            'venue_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'extra_images' => 'nullable|array',
            'extra_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'extras' => 'nullable|array',
            'extras.*' => 'integer|exists:extras,extra_id',
            'schedule' => 'required|array',
            'schedule.*.opening_time' => 'nullable|date_format:H:i',
            'schedule.*.closing_time' => 'nullable|date_format:H:i',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser un texto valido.',
            'integer' => 'El campo :attribute debe ser un numero entero.',
            'numeric' => 'El campo :attribute debe ser un numero valido.',
            'array' => 'El campo :attribute debe ser una lista valida.',
            'min.numeric' => 'El campo :attribute debe ser al menos :min.',
            'min.string' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max.string' => 'El campo :attribute no puede superar los :max caracteres.',
            'image' => 'El archivo de :attribute debe ser una imagen valida.',
            'mimes' => 'El archivo de :attribute debe ser de tipo: :values.',
            'exists' => 'El valor seleccionado para :attribute no es valido.',
            'date_format' => 'El campo :attribute debe tener el formato HH:MM.',
            'schedule.required' => 'Debes configurar un horario semanal para el local.',
        ], [
            'name' => 'nombre',
            'description' => 'descripcion',
            'address' => 'direccion',
            'city' => 'ciudad',
            'capacity' => 'capacidad',
            'state' => 'estado',
            'zip_code' => 'codigo postal',
            'country' => 'pais',
            'price_per_hour' => 'precio por hora',
            'venue_image' => 'imagen principal',
            'extra_images' => 'imagenes adicionales',
            'extra_images.*' => 'imagenes adicionales',
            'extras' => 'extras',
            'extras.*' => 'extra',
            'schedule' => 'horario semanal',
            'schedule.*.opening_time' => 'hora de apertura',
            'schedule.*.closing_time' => 'hora de cierre',
        ]);

        // Valida horario semanal en un paso separado para errores mas precisos.
        $normalizedSchedule = $this->normalizeAndValidateSchedule((array) $request->input('schedule', []));

        $selectedExtraIds = $validatedData['extras'] ?? [];
        unset($validatedData['extras']);

        // Registra datos entrantes para depuracion.
        \Log::info('Incoming request data:', $request->all());

        // Registra metadatos de archivo si se recibio imagen principal.
        if ($request->hasFile('venue_image')) {
            \Log::info('File upload detected:', [
                'original_name' => $request->file('venue_image')->getClientOriginalName(),
                'mime_type' => $request->file('venue_image')->getMimeType(),
                'size' => $request->file('venue_image')->getSize(),
            ]);
        } else {
            \Log::warning('No file uploaded with the request.');
        }

        // Registra resultado de validacion para diagnostico.
        \Log::info('Validation passed data:', $validatedData);

        // Asigna como manager inicial al usuario autenticado.
        $validatedData['manager_id'] = auth()->id();

        // Persiste local y dependencias en pasos explicitos.
        $venue = Venue::create($validatedData);

        $this->upsertVenueAvailability((int) $venue->venue_id, $normalizedSchedule);

        $actor = auth()->user()?->username ?? 'Sistema';
        $this->notifyAdminsAboutVenueAction(
            'Local creado',
            "{$actor} ha creado el local '{$venue->name}'.",
            (int) $venue->venue_id
        );

        $venue->extras()->sync($selectedExtraIds);

        if ($request->hasFile('venue_image')) {
            $file = $request->file('venue_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('venue_images', $filename, 'public');

            DB::table('venue_images')->insert([
                'venue_id' => $venue->venue_id,
                'image_url' => $filename,
                'main_image' => 1,
                'upload_date' => now(),
            ]);
        }

        $this->storeExtraImages($request, (int) $venue->venue_id);

        // Redirige al indice con mensaje de exito.
        return redirect()->route('venues.index')->with('success', 'Local registrado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un local.
     */
    public function edit($id)
    {
        $venue = Venue::with('extras')->findOrFail($id);

        if (! $this->canManageVenue($venue)) {
            return redirect()->route('venues.show', $venue->venue_id)
                ->with('error', 'No tienes permisos para editar este local.');
        }

        $extrasByCategory = $this->getGroupedExtras();
        $selectedExtraIds = $venue->extras->pluck('extra_id')->all();
        $availabilityMap = $this->getVenueAvailabilityMap((int) $venue->venue_id);

        return view('venues.edit', compact('venue', 'extrasByCategory', 'selectedExtraIds', 'availabilityMap'));
    }

    /**
     * Actualiza los datos del local.
     */
    public function update(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);

        if (! $this->canManageVenue($venue)) {
            return redirect()->route('venues.show', $venue->venue_id)
                ->with('error', 'No tienes permisos para editar este local.');
        }

        // Reutiliza validacion de create para evitar divergencias con edit.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:50',
            'price_per_hour' => 'nullable|numeric|min:0',
            'venue_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'extra_images' => 'nullable|array',
            'extra_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'extras' => 'nullable|array',
            'extras.*' => 'integer|exists:extras,extra_id',
            'schedule' => 'required|array',
            'schedule.*.opening_time' => 'nullable|date_format:H:i',
            'schedule.*.closing_time' => 'nullable|date_format:H:i',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser un texto valido.',
            'integer' => 'El campo :attribute debe ser un numero entero.',
            'numeric' => 'El campo :attribute debe ser un numero valido.',
            'array' => 'El campo :attribute debe ser una lista valida.',
            'min.numeric' => 'El campo :attribute debe ser al menos :min.',
            'min.string' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max.string' => 'El campo :attribute no puede superar los :max caracteres.',
            'image' => 'El archivo de :attribute debe ser una imagen valida.',
            'mimes' => 'El archivo de :attribute debe ser de tipo: :values.',
            'exists' => 'El valor seleccionado para :attribute no es valido.',
            'date_format' => 'El campo :attribute debe tener el formato HH:MM.',
            'schedule.required' => 'Debes configurar un horario semanal para el local.',
        ], [
            'name' => 'nombre',
            'description' => 'descripcion',
            'address' => 'direccion',
            'city' => 'ciudad',
            'capacity' => 'capacidad',
            'state' => 'estado',
            'zip_code' => 'codigo postal',
            'country' => 'pais',
            'price_per_hour' => 'precio por hora',
            'venue_image' => 'imagen principal',
            'extra_images' => 'imagenes adicionales',
            'extra_images.*' => 'imagenes adicionales',
            'extras' => 'extras',
            'extras.*' => 'extra',
            'schedule' => 'horario semanal',
            'schedule.*.opening_time' => 'hora de apertura',
            'schedule.*.closing_time' => 'hora de cierre',
        ]);

        $normalizedSchedule = $this->normalizeAndValidateSchedule((array) $request->input('schedule', []));

        $selectedExtraIds = $validatedData['extras'] ?? [];
        unset($validatedData['extras']);

        $venue->update($validatedData);
        $venue->extras()->sync($selectedExtraIds);
        $this->upsertVenueAvailability((int) $venue->venue_id, $normalizedSchedule);

        $actor = auth()->user()?->username ?? 'Sistema';
        $this->notifyAdminsAboutVenueAction(
            'Local actualizado',
            "{$actor} ha actualizado el local '{$venue->name}'.",
            (int) $venue->venue_id
        );

        if ($request->hasFile('venue_image')) {
            DB::table('venue_images')
                ->where('venue_id', $venue->venue_id)
                ->update(['main_image' => 0]);

            $file = $request->file('venue_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('venue_images', $filename, 'public');

            DB::table('venue_images')->insert([
                'venue_id' => $venue->venue_id,
                'image_url' => $filename,
                'main_image' => 1,
                'upload_date' => now(),
            ]);
        }

        $this->storeExtraImages($request, (int) $venue->venue_id);

        return redirect()->route('venues.show', $venue->venue_id)
            ->with('success', 'Local actualizado correctamente.');
    }

    public function storeException(Request $request, $id)
    {
        // Crea excepciones de disponibilidad para bloqueos o aperturas parciales.
        $venue = Venue::findOrFail($id);

        if (! $this->canManageVenue($venue)) {
            return redirect()->route('venues.show', $venue->venue_id)
                ->with('error', 'No tienes permisos para gestionar excepciones de este local.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_available' => 'nullable|boolean',
            'exception_opening_time' => 'nullable|date_format:H:i',
            'exception_closing_time' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:255',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'date' => 'El campo :attribute debe ser una fecha valida.',
            'after_or_equal' => 'El campo :attribute debe ser una fecha igual o posterior a :date.',
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'date_format' => 'El campo :attribute debe tener el formato HH:MM.',
            'string' => 'El campo :attribute debe ser un texto valido.',
            'max.string' => 'El campo :attribute no puede superar los :max caracteres.',
        ], [
            'start_date' => 'fecha de inicio',
            'end_date' => 'fecha de fin',
            'is_available' => 'disponibilidad',
            'exception_opening_time' => 'hora de apertura de la excepcion',
            'exception_closing_time' => 'hora de cierre de la excepcion',
            'reason' => 'motivo',
        ]);

        $isAvailable = (bool) ($request->boolean('is_available'));
        [$openingTime, $closingTime] = $this->normalizeExceptionTimes(
            $isAvailable,
            $validated['exception_opening_time'] ?? null,
            $validated['exception_closing_time'] ?? null
        );

        $endDate = $validated['end_date'] ?? $validated['start_date'];

        DB::table('availability_exceptions')->insert([
            'venue_id' => $venue->venue_id,
            'start_date' => $validated['start_date'],
            'end_date' => $endDate,
            'opening_time' => $openingTime,
            'closing_time' => $closingTime,
            'is_available' => $isAvailable,
            'reason' => $validated['reason'],
        ]);

        return redirect()->route('venues.show', $venue->venue_id)
            ->with('success', 'Excepcion de disponibilidad creada correctamente.');
    }

    /**
     * Elimina el local y sus imágenes si no tiene reservas asociadas.
     */
    public function destroy($id)
    {
        $venue = Venue::with('images')->findOrFail($id);

        if (! $this->canManageVenue($venue)) {
            return redirect()->route('venues.show', $venue->venue_id)
                ->with('error', 'No tienes permisos para eliminar este local.');
        }

        // Impide eliminar un local que participa en reservas.
        if (Booking::where('venue_id', $venue->venue_id)->exists()) {
            return redirect()->route('venues.show', $venue->venue_id)
                ->with('error', 'No puedes eliminar un local con reservas asociadas.');
        }

        foreach ($venue->images as $image) {
            Storage::disk('public')->delete('venue_images/' . $image->image_url);
        }

        $venueName = $venue->name;
        $venueId = (int) $venue->venue_id;
        $actor = auth()->user()?->username ?? 'Sistema';

        DB::table('venue_images')->where('venue_id', $venue->venue_id)->delete();
        $venue->delete();

        $this->notifyAdminsAboutVenueAction(
            'Local eliminado',
            "{$actor} ha eliminado el local '{$venueName}'.",
            $venueId
        );

        return redirect()->route('venues.index')->with('success', 'Local eliminado correctamente.');
    }

    /**
     * Muestra los lugares del administrador autenticado.
     *
     * @return \Illuminate\View\View
     */

    public function myVenues(Request $request)
    {
        // Reutiliza index limitado a locales del manager autenticado.
        $userId = auth()->id();
        $search = $request->query('q');
        $selectedExtraIds = $this->getSelectedExtraIds($request);
        $extrasByCategory = $this->getGroupedExtras();

        $venues = $this->applyExtrasFilter(
            $this->applyTextFilter(
                Venue::query()
                    ->with(['manager', 'images'])
                    ->where('manager_id', $userId),
                $search
            ),
            $selectedExtraIds
        )
            ->paginate(6)
            ->withQueryString();

        return view('venues.index', compact('venues', 'extrasByCategory', 'selectedExtraIds'));
    }
}