@extends('layouts.app')

@php($title = 'Detalles del Evento')
@php($canManageEvent = auth()->check() && (auth()->user()->user_type === 'admin' || auth()->user()->user_id === $event->organizer_id))

@section('content')
    <section class="rounded-2xl bg-brand-bg px-6 py-6">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Detalles del Evento</h1>
    </section>

    <section class="mt-6 space-y-3 rounded-xl border border-brand-border bg-white p-4 shadow-sm md:p-5">
        @foreach([
            'Nombre' => $event->name,
            'Descripción' => $event->description,
            'Fecha de Inicio' => $event->start_datetime,
            'Fecha de Fin' => $event->end_datetime,
            'Organizador' => $event->organizer->username ?? 'No disponible',
            'Tipo de Evento' => $event->event_type ?? 'No disponible',
            'Asistencia Esperada' => $event->expected_attendance ?? 'No disponible',
            'Estado' => $event->status,
        ] as $label => $value)
            <article class="rounded-lg border border-brand-border bg-brand-bg px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">{{ $label }}</p>
                <p class="mt-1 break-words text-sm text-brand-text md:text-base">{{ $value }}</p>
            </article>
        @endforeach
    </section>

    <div class="mt-4 flex flex-wrap gap-3">
        @if (auth()->check() && auth()->user()->user_id === $event->organizer_id)
            <a href="{{ route('bookings.create') }}"
               class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                Reservar Un Local
            </a>
        @endif
        <a href="{{ route('events.index') }}"
           class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
            Volver a Eventos
        </a>

        {{-- Acciones: volver, reservar, editar/eliminar según permisos y sesión. --}}
        @if ($canManageEvent)
            <a href="{{ route('events.edit', $event->event_id) }}"
               class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                Editar Evento
            </a>

            <form action="{{ route('events.destroy', $event->event_id) }}" method="POST"
                  onsubmit="return confirm('¿Seguro que quieres eliminar este evento?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="rounded-lg border border-red-300 bg-red-50 px-4 py-2 font-semibold text-red-700 transition hover:bg-red-100">
                    Eliminar Evento
                </button>
            </form>
        @endif
    </div>
@endsection