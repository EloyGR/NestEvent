@extends('layouts.app')

@php
    $title = 'Crear Reserva';
    // Roles del usuario autenticado para habilitar o restringir acciones de creación.
    $user = auth()->user();
    $isEventManager = $user && $user->user_type === 'event_manager';
    $isAdmin       = $user && $user->user_type === 'admin';

    if ($isEventManager) {
        // Un event_manager solo puede reservar para sus propios eventos.
        $events = $events->filter(fn($event) => $event->organizer_id === $user->user_id);
    }
@endphp

@section('content')
    {{-- Formulario principal de alta de reservas. --}}
    <section class="mx-auto max-w-2xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Haz tu reserva</h1>


        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Corrige los siguientes errores:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Solo admins y event_manager pueden crear reservas. --}}
        @if ($isEventManager || $isAdmin)
            <form action="{{ route('bookings.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="event_id" class="mb-1 block text-sm font-semibold text-brand-text">Evento</label>
                    <select name="event_id" id="event_id"
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border">
                        @foreach($events as $event)
                            <option value="{{ $event->event_id }}" {{ old('event_id') == $event->event_id ? 'selected' : '' }}>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="venue_id" class="mb-1 block text-sm font-semibold text-brand-text">Local</label>
                    <select name="venue_id" id="venue_id"
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border">
                        @foreach($venues as $venue)
                            <option value="{{ $venue->venue_id }}"
                                {{ (isset($selectedVenue) && $selectedVenue == $venue->venue_id) || old('venue_id') == $venue->venue_id ? 'selected' : '' }}>
                                {{ $venue->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Resumen dinámico del horario semanal del local seleccionado. --}}
                <div class="rounded-lg border border-brand-border bg-brand-form-bg p-4">
                    <p class="text-sm font-semibold text-brand-text">Horario del local seleccionado</p>
                    <ul id="venue-schedule-list" class="mt-2 list-disc space-y-1 pl-5 text-sm text-brand-text"></ul>
                    <p id="venue-schedule-empty" class="mt-2 text-sm text-brand-text">Este local no tiene horario semanal configurado.</p>
                </div>

                {{-- Excepciones no disponibles (bloqueos) para guiar al usuario antes de enviar. --}}
                <div class="rounded-lg border border-brand-border bg-brand-form-bg p-4">
                    <p class="text-sm font-semibold text-brand-text">Fechas no disponibles del local</p>
                    <ul id="venue-exceptions-list" class="mt-2 list-disc space-y-1 pl-5 text-sm text-brand-text">
                    </ul>
                    <p id="venue-exceptions-empty" class="mt-2 text-sm text-brand-text">No hay excepciones registradas para este local.</p>
                </div>

                {{-- Captura de fecha/hora de inicio del rango de reserva. --}}
                <div class="rounded-lg border border-brand-border bg-brand-form-bg p-4">
                    <p class="text-sm font-semibold text-brand-text">Inicio de la reserva</p>
                    <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label for="start_date" class="mb-1 block text-sm font-semibold text-brand-text">Fecha</label>
                            <input
                                type="date"
                                name="start_date"
                                id="start_date"
                                value="{{ old('start_date') }}"
                                required
                                class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                            >
                        </div>
                        <div>
                            <label for="start_time" class="mb-1 block text-sm font-semibold text-brand-text">Hora</label>
                            <input
                                type="time"
                                name="start_time"
                                id="start_time"
                                value="{{ old('start_time') }}"
                                required
                                class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                            >
                        </div>
                    </div>
                </div>

                {{-- Captura de fecha/hora de fin del rango de reserva. --}}
                <div class="rounded-lg border border-brand-border bg-brand-form-bg p-4">
                    <p class="text-sm font-semibold text-brand-text">Fin de la reserva</p>
                    <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label for="end_date" class="mb-1 block text-sm font-semibold text-brand-text">Fecha</label>
                            <input
                                type="date"
                                name="end_date"
                                id="end_date"
                                value="{{ old('end_date') }}"
                                required
                                class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                            >
                        </div>
                        <div>
                            <label for="end_time" class="mb-1 block text-sm font-semibold text-brand-text">Hora</label>
                            <input
                                type="time"
                                name="end_time"
                                id="end_time"
                                value="{{ old('end_time') }}"
                                required
                                class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                            >
                        </div>
                    </div>
                </div>

                <p id="date-range-error" class="hidden rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700"></p>

                <div>
                    <label for="notes" class="mb-1 block text-sm font-semibold text-brand-text">Anotaciones</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border">{{ old('notes') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Reservar ahora
                </button>
            </form>
            {{-- Datos serializados para JS: se usan para pintar horario y excepciones en cliente. --}}
            <script id="booking-exceptions-data" type="application/json">@json($availabilityExceptionsByVenue ?? [])</script>
            <script id="booking-availability-data" type="application/json">@json($venueAvailabilityByVenue ?? [])</script>
        @else
            <p class="mt-4 rounded-lg border border-brand-border bg-white px-4 py-3 text-brand-text">
                No tienes permisos para crear reservas.
            </p>
        @endif
    </section>
@endsection
