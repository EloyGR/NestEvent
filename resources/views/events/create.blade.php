@extends('layouts.app')

@php($title = 'Crear Evento')

@section('content')
    {{-- Formulario de alta de evento con campos básicos de planificación. --}}
    <section class="mx-auto max-w-2xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Crear Nuevo Evento</h1>

        <form action="{{ route('events.store') }}" method="POST" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="mb-1 block text-sm font-semibold text-brand-text">Nombre del Evento</label>
                <input type="text" id="name" name="name" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="description" class="mb-1 block text-sm font-semibold text-brand-text">Descripción</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"></textarea>
            </div>

            <div>
                <label for="start_datetime" class="mb-1 block text-sm font-semibold text-brand-text">Fecha y Hora de Inicio</label>
                <input type="datetime-local" id="start_datetime" name="start_datetime" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="end_datetime" class="mb-1 block text-sm font-semibold text-brand-text">Fecha y Hora de Fin</label>
                <input type="datetime-local" id="end_datetime" name="end_datetime" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="event_type" class="mb-1 block text-sm font-semibold text-brand-text">Tipo de Evento</label>
                <input type="text" id="event_type" name="event_type"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="expected_attendance" class="mb-1 block text-sm font-semibold text-brand-text">Asistencia Esperada</label>
                {{-- Solo enteros positivos para evitar valores negativos o fraccionarios desde UI. --}}
                <input type="number" id="expected_attendance" name="expected_attendance" value="{{ old('expected_attendance') }}" min="1" step="1" inputmode="numeric"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="is_public" class="mb-1 block text-sm font-semibold text-brand-text">¿Es Público?</label>
                <select id="is_public" name="is_public"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border">
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>

            <button type="submit"
                class="w-full rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                Crear Evento
            </button>
        </form>
    </section>

    {{-- Validaciones/ayudas de fecha y hora en cliente. --}}
    <script src="/js/timedate.js"></script>
@endsection