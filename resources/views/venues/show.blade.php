@extends('layouts.app')

{{-- Variables derivadas para permisos, agrupaciones y etiquetas de calendario. --}}
@php($title = 'Detalles del Local')
@php($canManageVenue = auth()->check() && (auth()->user()->user_type === 'admin' || auth()->user()->user_id === $venue->manager_id))
@php($extrasByCategory = $venue->extras->sortBy([['category', 'asc'], ['name', 'asc']])->groupBy(fn ($extra) => $extra->category ?: 'Otros'))
@php($dayLabels = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sabado', 0 => 'Domingo'])

@section('content')
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
            <p class="font-semibold">No se pudo guardar la excepcion:</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-2xl bg-brand-bg px-6 py-6">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Detalles del Local</h1>
    </section>

    <section class="mt-6 flex flex-col gap-6 md:flex-row md:items-start">
        {{-- Columna de galería/carrusel con controles condicionales según número de imágenes. --}}
        <div class="flex flex-col md:w-1/3">
            @php($orderedImages = $venue->images->sortByDesc(fn ($image) => (int) $image->main_image)->values())
            @php($hasCarousel = $orderedImages->count() > 1)

            <div class="min-h-64 flex-1 overflow-hidden rounded-xl" data-venue-carousel>
                @if($orderedImages->isNotEmpty())
                    <div class="relative h-full">
                        @foreach($orderedImages as $image)
                            <img
                                src="{{ asset('storage/venue_images/' . $image->image_url) }}"
                                alt="Imagen del local {{ $venue->name }}"
                                class="carousel-slide h-full w-full object-cover"
                                data-carousel-slide
                                data-slide-index="{{ $loop->index }}"
                                style="display: {{ $loop->first ? 'block' : 'none' }};"
                            >
                        @endforeach

                        @if($hasCarousel)
                            <button
                                type="button"
                                class="carousel-prev absolute left-3 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/60 px-3 py-2 text-sm font-bold text-white transition hover:bg-black/80"
                                data-carousel-prev
                                aria-label="Imagen anterior"
                            >
                                &lsaquo;
                            </button>
                            <button
                                type="button"
                                class="carousel-next absolute right-3 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/60 px-3 py-2 text-sm font-bold text-white transition hover:bg-black/80"
                                data-carousel-next
                                aria-label="Imagen siguiente"
                            >
                                &rsaquo;
                            </button>

                            <div class="absolute bottom-3 left-1/2 z-10 flex -translate-x-1/2 items-center gap-2 rounded-full bg-black/35 px-2 py-1"
                                 aria-label="Indicadores del carrusel">
                                @foreach($orderedImages as $dotImage)
                                    <button
                                        type="button"
                                        class="h-2.5 w-2.5 rounded-full border border-white/70 transition"
                                        style="background-color: {{ $loop->first ? 'rgba(255,255,255,0.95)' : 'rgba(255,255,255,0.45)' }};"
                                        data-carousel-dot
                                        data-dot-index="{{ $loop->index }}"
                                        aria-label="Ir a imagen {{ $loop->iteration }}"
                                        aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                    ></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <img src="{{ asset('storage/venue_images/placeholder.jpg') }}"
                        alt="Imagen por defecto del local"
                        class="h-full w-full object-cover">
                @endif
            </div>
            {{-- Acciones contextuales: volver, reservar y gestionar local. --}}
            <div class="mt-4 flex flex-wrap justify-center gap-3">
                <a href="{{ route('venues.index') }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Volver a Locales
                </a>
                @if(session('user_id') && (session('user_type') === 'admin' || session('user_type') === 'event_manager'))
                    <a href="{{ route('bookings.create', ['venue_id' => $venue->venue_id]) }}"
                       class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                        Reservar Este Local
                    </a>
                @endif

                @if($canManageVenue)
                    <a href="{{ route('venues.edit', $venue->venue_id) }}"
                       class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                        Editar Local
                    </a>

                    <form action="{{ route('venues.destroy', $venue->venue_id) }}" method="POST"
                          onsubmit="return confirm('¿Seguro que quieres eliminar este local?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="rounded-lg border border-red-300 bg-red-50 px-4 py-2 font-semibold text-red-700 transition hover:bg-red-100">
                            Eliminar Local
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Columna de información estructurada del local y extras configurados. --}}
        <div class="md:w-2/3">
            <h2 class="text-xl font-bold text-brand-text">{{ $venue->name }}</h2>
            <p class="mt-2 text-brand-text">{{ $venue->description }}</p>

            {{-- Se centraliza el formato de precio para reutilizarlo en móvil y escritorio sin divergencias. --}}
            @php($formattedPricePerHour = !is_null($venue->price_per_hour) ? number_format((float) $venue->price_per_hour, 2, ',', '.') . ' EUR/h' : 'No disponible')

            {{-- Ficha móvil en bloques para mejorar lectura vertical en pantallas estrechas. --}}
            <div class="mt-4 space-y-2 md:hidden">
                @foreach([
                    'Dirección' => $venue->address,
                    'Ciudad' => $venue->city,
                    'Población' => $venue->state ?? 'No disponible',
                    'Código Postal' => $venue->zip_code ?? 'No disponible',
                    'País' => $venue->country,
                    'Capacidad' => $venue->capacity,
                    'Precio por Hora' => $formattedPricePerHour,
                    'Estado del local' => $venue->is_active ? 'Activo' : 'Inactivo',
                ] as $label => $value)
                    <article class="rounded-lg border border-brand-border bg-white px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">{{ $label }}</p>
                        <p class="mt-1 break-words text-sm text-brand-text">{{ $value }}</p>
                    </article>
                @endforeach
                <article class="rounded-lg border border-brand-border bg-white px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Gerente</p>
                    <p class="mt-1 break-words text-sm text-brand-text">
                        @if($venue->manager)
                            <a href="{{ route('users.show', $venue->manager->user_id) }}" class="font-semibold hover:underline">
                                {{ $venue->manager->username }}
                            </a>
                        @else
                            No disponible
                        @endif
                    </p>
                </article>
            </div>

            {{-- Tabla escritorio para comparación rápida de atributos en una sola vista. --}}
            <div class="mt-4 hidden overflow-x-auto rounded-xl border border-brand-border shadow-sm md:block">
                <table class="w-full table-fixed text-left text-sm text-brand-text">
                    <tbody class="bg-white">
                        @foreach([
                            'Dirección' => $venue->address,
                            'Ciudad' => $venue->city,
                            'Población' => $venue->state ?? 'No disponible',
                            'Código Postal' => $venue->zip_code ?? 'No disponible',
                            'País' => $venue->country,
                            'Capacidad' => $venue->capacity,
                            'Precio por Hora' => $formattedPricePerHour,
                            'Estado del local' => $venue->is_active ? 'Activo' : 'Inactivo',
                        ] as $label => $value)
                            <tr class="border-b border-brand-border hover:bg-brand-hover">
                                <th class="w-36 bg-brand-surface px-4 py-3 font-semibold md:w-44">{{ $label }}</th>
                                <td class="px-4 py-3 break-words">{{ $value }}</td>
                            </tr>
                        @endforeach
                        <tr class="hover:bg-brand-hover">
                            <th class="w-36 bg-brand-surface px-4 py-3 font-semibold md:w-44">Gerente</th>
                            <td class="px-4 py-3 break-words">
                                @if($venue->manager)
                                    <a href="{{ route('users.show', $venue->manager->user_id) }}"
                                       class="font-semibold hover:underline">
                                        {{ $venue->manager->username }}
                                    </a>
                                @else
                                    No disponible
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 rounded-xl border border-brand-border bg-brand-form-bg p-4">
                <h3 class="text-sm font-bold uppercase tracking-wide text-brand-form-text">Extras del local</h3>

                @if($extrasByCategory->isNotEmpty())
                    <div class="mt-3 space-y-3">
                        @foreach($extrasByCategory as $category => $extras)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-brand-form-text">{{ $category }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($extras as $extra)
                                        <span class="rounded-full border border-brand-border bg-brand-bg px-3 py-1 text-xs font-semibold text-brand-text">
                                            {{ $extra->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-2 text-sm text-brand-form-text">Este local no tiene extras configurados.</p>
                @endif
            </div>

        </div>
    </section>

    {{-- Bloque de disponibilidad semanal + excepciones y formulario de creación de excepciones. --}}
    <section class="mt-6 rounded-xl border border-brand-border bg-brand-form-bg p-4">
        <details>
            <summary class="cursor-pointer text-sm font-bold uppercase tracking-wide text-brand-form-text">
                Horario del local
            </summary>

            @if ($availability->isEmpty())
                <p class="mt-3 text-sm text-brand-text">Este local no tiene horario configurado.</p>
            @else
                <div class="mt-3 space-y-2 md:hidden">
                    @foreach ($availability as $slot)
                        <article class="rounded-lg border border-brand-border bg-white px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Dia</p>
                            <p class="mt-1 text-sm font-semibold text-brand-text">{{ $dayLabels[$slot->day_of_week] ?? 'Dia ' . $slot->day_of_week }}</p>
                            <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-brand-text/70">Horario</p>
                            <p class="mt-1 text-sm text-brand-text">
                                @if (!$slot->is_available || !$slot->opening_time || !$slot->closing_time)
                                    Cerrado
                                @else
                                    {{ \Carbon\Carbon::parse($slot->opening_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->closing_time)->format('H:i') }}
                                @endif
                            </p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-3 hidden overflow-x-auto rounded-xl border border-brand-border shadow-sm md:block">
                    <table class="w-full text-left text-sm text-brand-text">
                        <thead class="bg-brand-surface">
                            <tr>
                                <th class="border-b border-brand-border px-4 py-3 font-semibold">Dia</th>
                                <th class="border-b border-brand-border px-4 py-3 font-semibold">Horario</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach ($availability as $slot)
                                <tr class="border-b border-brand-border hover:bg-brand-hover">
                                    <td class="px-4 py-3">{{ $dayLabels[$slot->day_of_week] ?? 'Dia ' . $slot->day_of_week }}</td>
                                    <td class="px-4 py-3">
                                        @if (!$slot->is_available || !$slot->opening_time || !$slot->closing_time)
                                            Cerrado
                                        @else
                                            {{ \Carbon\Carbon::parse($slot->opening_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->closing_time)->format('H:i') }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-4">
                <h4 class="text-sm font-bold uppercase tracking-wide text-brand-form-text">Proximas excepciones</h4>

                {{-- Render de próximas excepciones con etiqueta de fecha y horario calculado. --}}
                @if ($upcomingExceptions->isEmpty())
                    <p class="mt-2 text-sm text-brand-text">No hay excepciones proximas para este local.</p>
                @else
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-brand-text">
                        @foreach ($upcomingExceptions as $exception)
                            @php($startDate = \Carbon\Carbon::parse($exception->start_date))
                            @php($endDate = \Carbon\Carbon::parse($exception->end_date))
                            @php($dateLabel = $startDate->isSameDay($endDate)
                                ? $startDate->format('d/m/Y')
                                : $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'))
                            @php($slotLabel = (!is_null($exception->opening_time) && !is_null($exception->closing_time))
                                ? ('Horario excepcional: ' . \Carbon\Carbon::parse($exception->opening_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($exception->closing_time)->format('H:i'))
                                : (!$exception->is_available ? 'Cerrado' : 'Disponibilidad modificada'))

                            <li>
                                <span class="font-semibold">{{ $dateLabel }}</span>
                                <span>- {{ $slotLabel }}</span>
                                @if (!empty($exception->reason))
                                    <span class="block truncate" title="{{ $exception->reason }}">(Motivo: {{ $exception->reason }})</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Formulario visible solo para gestores autorizados del local. --}}
            @if ($canManageVenue)
                <div class="mt-4 rounded-xl border border-brand-border bg-white p-4">
                    <h4 class="text-sm font-bold uppercase tracking-wide text-brand-form-text">Crear excepcion</h4>
                    <p class="mt-1 text-xs text-brand-form-text">Si no marcas disponibilidad parcial, la excepcion bloqueara el rango completo de fechas.</p>

                    <form action="{{ route('venues.exceptions.store', $venue->venue_id) }}" method="POST" class="mt-3 space-y-3">
                        @csrf

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div>
                                <label for="start_date" class="mb-1 block text-sm font-semibold text-brand-text">Fecha inicio</label>
                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    value="{{ old('start_date') }}"
                                    required
                                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                                >
                            </div>
                            <div>
                                <label for="end_date" class="mb-1 block text-sm font-semibold text-brand-text">Fecha fin</label>
                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    value="{{ old('end_date') }}"
                                    required
                                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                                >
                            </div>
                        </div>

                        <label class="inline-flex items-center gap-2 text-sm text-brand-text">
                            <input
                                type="checkbox"
                                name="is_available"
                                value="1"
                                data-exception-partial-toggle
                                @checked(old('is_available'))
                            >
                            <span>Disponibilidad parcial con horario excepcional</span>
                        </label>

                        <div
                            data-exception-partial-fields
                            class="{{ old('is_available') ? '' : 'hidden' }}"
                        >
                            <p class="text-sm font-semibold text-brand-text">Horario de la excepcion</p>

                            <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                                <div>
                                    <label for="exception_opening_time" class="mb-1 block text-sm font-semibold text-brand-text">Apertura</label>
                                    <input
                                        type="time"
                                        id="exception_opening_time"
                                        name="exception_opening_time"
                                        data-exception-partial-time
                                        value="{{ old('exception_opening_time') }}"
                                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                                    >
                                </div>
                                <div>
                                    <label for="exception_closing_time" class="mb-1 block text-sm font-semibold text-brand-text">Cierre</label>
                                    <input
                                        type="time"
                                        id="exception_closing_time"
                                        name="exception_closing_time"
                                        data-exception-partial-time
                                        value="{{ old('exception_closing_time') }}"
                                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                                    >
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="reason" class="mb-1 block text-sm font-semibold text-brand-text">Motivo</label>
                            <input
                                type="text"
                                id="reason"
                                name="reason"
                                value="{{ old('reason') }}"
                                required
                                class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                            >
                        </div>

                        <button
                            type="submit"
                            class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link"
                        >
                            Guardar excepcion
                        </button>
                    </form>
                </div>
            @endif
        </details>
    </section>

@endsection