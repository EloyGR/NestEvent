@extends('layouts.app')

{{-- El título se adapta al modo de vista (todos los eventos o solo los del usuario). --}}
@php($title = request()->routeIs('events.myEvents') ? 'Mis Eventos' : 'Eventos')

@section('content')
    {{-- Buscador por texto libre (nombre y descripción). --}}
    <section class="rounded-2xl px-6 py-6">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">
            {{ request()->routeIs('events.myEvents') ? 'Mis eventos' : 'Eventos' }}
        </h1>

        {{-- Formulario de busqueda --}}
        <form method="GET" action="{{ route('events.index') }}" class="mt-4">
            <input type="text" name="search" placeholder="Buscar eventos..." value="{{ request('search') }}" class="form-input w-full max-w-md rounded-lg border border-brand-border px-4 py-2 text-sm">
            <button type="submit" class="mt-2 rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">Buscar</button>
        </form>
    </section>

    {{-- Tarjetas móviles para evitar scroll horizontal. --}}
    <section class="mt-6 space-y-3 sm:hidden">
        @forelse ($events as $event)
            <article class="rounded-xl border border-brand-border bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Nombre</p>
                <a href="{{ route('events.show', $event->event_id) }}" class="mt-1 block truncate font-semibold text-brand-text hover:underline" title="{{ $event->name }}">
                    {{ $event->name }}
                </a>

                <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-brand-text/70">Fecha de inicio</p>
                <p class="mt-1 text-brand-text">{{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y') }}</p>
            </article>
        @empty
            <div class="rounded-xl border border-brand-border bg-white px-4 py-6 text-center text-brand-text">
                No se encontraron eventos.
            </div>
        @endforelse
    </section>

    {{-- Tabla de eventos para sm+; cada fila enlaza a detalle del evento. --}}
    <section class="mt-6 hidden overflow-x-auto rounded-xl border border-brand-border shadow-sm sm:block">
        <table class="w-full text-left text-sm text-brand-text">
            <thead class="bg-brand-surface">
                <tr>
                    <th class="border-b border-brand-border px-4 py-3 font-semibold">Nombre</th>
                    <th class="hidden border-b border-brand-border px-4 py-3 font-semibold sm:table-cell">Descripción</th>
                    <th class="border-b border-brand-border px-4 py-3 font-semibold">Fecha de Inicio</th>
                    <th class="hidden border-b border-brand-border px-4 py-3 font-semibold md:table-cell">Fecha de Fin</th>
                    <th class="hidden border-b border-brand-border px-4 py-3 font-semibold md:table-cell">Organizador</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                {{-- Recorrido de resultados paginados devueltos por el controlador. --}}
                @forelse ($events as $event)
                    <tr class="border-b border-brand-border transition hover:bg-brand-hover">
                        <td class="px-4 py-3">
                            <a href="{{ route('events.show', $event->event_id) }}"
                               class="block max-w-[12rem] truncate font-medium text-brand-text hover:underline sm:max-w-[16rem] md:max-w-none"
                               title="{{ $event->name }}">
                                {{ $event->name }}
                            </a>
                        </td>
                        <td class="hidden px-4 py-3 sm:table-cell">
                            <span class="block max-w-[16rem] truncate md:max-w-xs" title="{{ $event->description }}">{{ $event->description }}</span>
                        </td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y') }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ \Carbon\Carbon::parse($event->end_datetime)->format('d/m/Y') }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">
                            @if ($event->organizer)
                                <a href="{{ route('users.show', $event->organizer->user_id) }}"
                                   class="font-medium text-brand-text hover:underline">
                                    {{ $event->organizer->username }}
                                </a>
                            @else
                                No disponible
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-brand-text">
                            No se encontraron eventos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{-- <div class="mt-4">
        {{ $events->links('pagination::simple-default') }}
    </div> --}}

    {{-- Navegación de páginas con botones anterior/siguiente. --}}
    @if ($events->hasPages())
        <nav class="mt-4 flex items-center justify-between gap-2">
            {{-- Anterior --}}
            @if ($events->onFirstPage())
                <span class="cursor-not-allowed rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text opacity-50">
                    &laquo; Anterior
                </span>
            @else
                <a href="{{ $events->previousPageUrl() }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    &laquo; Anterior
                </a>
            @endif

            {{-- Siguiente --}}
            @if ($events->hasMorePages())
                <a href="{{ $events->nextPageUrl() }}"
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

    {{-- Acciones de gestión visibles solo para usuarios con permisos de creación. --}}
    @if (session('user_type') === 'admin' || session('user_type') === 'event_manager')
        @if(session('user_id'))
            <div class="mt-4 flex flex-wrap justify-center gap-3">
                <a href="{{ route('events.create') }}"
                class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Registra Tu Próximo Evento
                </a>
                @if (!request()->routeIs('events.myEvents'))
                    <a href="{{ route('events.myEvents') }}"
                    class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                        Mis Eventos
                    </a>
                @else
                <a href="{{ route('events.index') }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Todos los eventos
                </a>
                @endif
        </div>
        @endif
    @endif
@endsection