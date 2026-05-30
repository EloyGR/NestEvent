@extends('layouts.app')

{{-- El título cambia según la ruta para reutilizar esta vista en listado general y "Mis Locales". --}}
@php($title = request()->routeIs('venues.myVenues') ? 'Mis Locales' : 'Locales')

@section('content')
    {{-- Variables de filtros normalizadas para que la vista no dependa del tipo de dato original. --}}
    @php($searchText = trim((string) request('q', '')))
    @php($selectedExtraIds = collect($selectedExtraIds ?? [])->map(fn ($id) => (int) $id)->all())
    @php($selectedExtrasLookup = collect($selectedExtraIds)->mapWithKeys(fn ($id) => [(string) $id => true]))
    @php($selectedExtrasCount = count($selectedExtraIds))

    {{-- Formulario de filtros por texto y extras. --}}
    <section class="rounded-2xl px-4 py-5 md:px-6 md:py-6">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">
            {{ request()->routeIs('venues.myVenues') ? 'Mis locales' : 'Todos los locales' }}
        </h1>

        <form method="GET" action="{{ request()->routeIs('venues.myVenues') ? route('venues.myVenues') : route('venues.index') }}"
              class="mt-4 space-y-3">
            <div>
                <input
                    type="text"
                    name="q"
                    value="{{ $searchText }}"
                    placeholder="Buscar por nombre, direccion, ciudad o descripcion"
                    class="w-full rounded-lg border border-brand-border bg-brand-bg px-4 py-2 text-sm text-brand-text placeholder:text-brand-text/60 focus:border-brand-link focus:outline-none focus:ring-2 focus:ring-brand-link/30"
                >
            </div>

            <div class="flex flex-col gap-2 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex gap-2">
                    <button type="submit"
                            class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                        Aplicar filtros
                    </button>
                    @if ($searchText !== '' || $selectedExtrasCount > 0)
                        <a href="{{ request()->routeIs('venues.myVenues') ? route('venues.myVenues') : route('venues.index') }}"
                           class="rounded-lg border border-brand-border bg-brand-bg px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                            Limpiar
                        </a>
                    @endif
                </div>

                {{-- Filtro por extras agrupados por categoría. --}}
                <details class="w-full rounded-lg border border-brand-border bg-brand-bg lg:w-80">
                    <summary class="cursor-pointer list-none px-4 py-2 text-sm font-semibold text-brand-text">
                        Filtrar por extras @if ($selectedExtrasCount > 0) ({{ $selectedExtrasCount }}) @endif
                    </summary>

                    <div class="max-h-56 space-y-3 overflow-y-auto border-t border-brand-border px-4 py-3">
                        @forelse (($extrasByCategory ?? collect()) as $category => $extras)
                            <div>
                                <p class="mb-1 text-xs font-bold uppercase tracking-wide text-brand-text/70">{{ $category }}</p>
                                <div class="space-y-2">
                                    @foreach ($extras as $extra)
                                        <label class="flex items-center gap-2 text-sm text-brand-text">
                                            <input
                                                type="checkbox"
                                                name="extras[]"
                                                value="{{ $extra->extra_id }}"
                                                @checked($selectedExtrasLookup->has((string) $extra->extra_id))
                                                class="h-4 w-4 rounded border-brand-border bg-brand-bg text-brand-link focus:ring-brand-link/30"
                                            >
                                            <span>{{ $extra->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-brand-text/70">No hay extras disponibles.</p>
                        @endforelse
                    </div>
                </details>
            </div>
        </form>

        @if ($searchText !== '' || $selectedExtrasCount > 0)
            <p class="mt-2 text-sm text-brand-text/80">
                Filtros activos:
                @if ($searchText !== '')
                    <span class="font-semibold text-brand-text">texto "{{ $searchText }}"</span>
                @endif
                @if ($selectedExtrasCount > 0)
                    <span class="font-semibold text-brand-text">{{ $searchText !== '' ? ', ' : '' }}{{ $selectedExtrasCount }} extra(s)</span>
                @endif
            </p>
        @endif
    </section>

    {{-- Resultados de locales con imagen principal, precio y datos del gestor. --}}
    <section class="mt-6 space-y-4">
        @forelse ($venues as $venue)
            @php($mainImage = $venue->images->firstWhere('main_image', 1) ?? $venue->images->first())

            {{-- Tarjeta responsive unica: en mobile se apila y en desktop se divide imagen + contenido. --}}
            <article class="overflow-hidden rounded-2xl border border-brand-border bg-brand-bg shadow-sm transition hover:shadow-md">
                <a href="{{ route('venues.show', $venue->venue_id) }}" class="block md:flex">
                    <div class="relative h-52 w-full shrink-0 overflow-hidden bg-brand-surface md:h-auto md:w-80">
                        @if ($mainImage)
                            <img
                                src="{{ asset('storage/venue_images/' . $mainImage->image_url) }}"
                                alt="Imagen principal de {{ $venue->name }}"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-brand-surface text-sm font-semibold text-brand-text/70">
                                Sin imagen principal
                            </div>
                        @endif

                        <span class="absolute bottom-3 left-3 rounded-md bg-black/70 px-2 py-1 text-xs font-semibold text-white">
                            {{ $venue->capacity }} personas
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col gap-2 p-4 md:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="text-lg font-bold leading-tight text-brand-text md:text-xl">
                                {{ $venue->name }}
                            </h2>

                            @if (!is_null($venue->price_per_hour))
                                {{-- Precio normalizado a 2 decimales para mantener coherencia visual en listados monetarios. --}}
                                <div class="shrink-0 text-right">
                                    <p class="text-2xl font-extrabold leading-none text-brand-text">
                                        {{ number_format((float) $venue->price_per_hour, 2, ',', '.') }}
                                        <span class="text-base font-semibold">EUR</span>
                                    </p>
                                    <p class="text-xs font-medium text-brand-text/80">/hora</p>
                                </div>
                            @endif
                        </div>

                        <p class="text-sm font-medium text-brand-text/90">
                            {{ $venue->address }}, {{ $venue->city }}
                        </p>

                        <p class="line-clamp-2 text-sm text-brand-text/85">
                            {{ $venue->description ?: 'Espacio disponible para organizar eventos en una ubicacion destacada.' }}
                        </p>

                        <div class="mt-auto flex flex-wrap items-center gap-3 pt-2 text-sm text-brand-text/80">
                            <span class="rounded-md border border-brand-border bg-brand-surface px-2 py-1">
                                {{ $venue->city }}
                            </span>
                            @if ($venue->manager)
                                <span>Gestiona: {{ $venue->manager->username }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </article>
        @empty
            <div class="rounded-xl border border-brand-border bg-brand-bg px-4 py-6 text-center text-brand-text">
                No se encontraron locales.
            </div>
        @endforelse
    </section>

    {{-- Paginación manual del listado de locales. --}}
    @if ($venues->hasPages())
        <nav class="mt-4 flex items-center justify-between gap-2">
            @if ($venues->onFirstPage())
                <span class="cursor-not-allowed rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text opacity-50">
                    &laquo; Anterior
                </span>
            @else
                <a href="{{ $venues->previousPageUrl() }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    &laquo; Anterior
                </a>
            @endif

            @if ($venues->hasMorePages())
                <a href="{{ $venues->nextPageUrl() }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Siguiente &raquo;
                </a>
            @else
                <span class="cursor-not-allowed rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text opacity-50">
                    Siguiente &raquo;
                </span>
            @endif
        </nav>
    @endif

    {{-- Acciones exclusivas para perfiles que gestionan locales. --}}
    @if (session('user_type') === 'admin' || session('user_type') === 'local_manager')
        @if(session('user_id'))
            <div class="mt-4 flex flex-wrap justify-center gap-3">
                <a href="{{ route('venues.create') }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Da de alta tu local
                </a>

                @if (!request()->routeIs('venues.myVenues'))
                    <a href="{{ route('venues.myVenues') }}"
                       class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                        Mis Locales
                    </a>
                @else
                    <a href="{{ route('venues.index') }}"
                       class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                        Todos los locales
                    </a>
                @endif
            </div>
        @endif
    @endif
@endsection