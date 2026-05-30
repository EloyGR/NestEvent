@extends('layouts.app')

@php($title = 'Editar Evento')

@section('content')
    {{-- Formulario de edición: reutiliza datos existentes tras errores. --}}
    <section class="mx-auto max-w-2xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Editar Evento</h1>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">No se pudo actualizar el evento:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('events.update', $event->event_id) }}" method="POST" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-1 block text-sm font-semibold text-brand-text">Nombre del Evento</label>
                <input type="text" id="name" name="name" value="{{ old('name', $event->name) }}" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="description" class="mb-1 block text-sm font-semibold text-brand-text">Descripcion</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border">{{ old('description', $event->description) }}</textarea>
            </div>

            <div>
                <label for="start_datetime" class="mb-1 block text-sm font-semibold text-brand-text">Fecha y Hora de Inicio</label>
                <input type="datetime-local" id="start_datetime" name="start_datetime"
                       value="{{ old('start_datetime', optional($event->start_datetime)->format('Y-m-d\\TH:i')) }}" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="end_datetime" class="mb-1 block text-sm font-semibold text-brand-text">Fecha y Hora de Fin</label>
                <input type="datetime-local" id="end_datetime" name="end_datetime"
                       value="{{ old('end_datetime', optional($event->end_datetime)->format('Y-m-d\\TH:i')) }}" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="event_type" class="mb-1 block text-sm font-semibold text-brand-text">Tipo de Evento</label>
                <input type="text" id="event_type" name="event_type" value="{{ old('event_type', $event->event_type) }}"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="expected_attendance" class="mb-1 block text-sm font-semibold text-brand-text">Asistencia Esperada</label>
                {{-- Mantenemos la misma validación de UI que en creación para consistencia funcional. --}}
                <input type="number" id="expected_attendance" name="expected_attendance" value="{{ old('expected_attendance', $event->expected_attendance) }}" min="1" step="1" inputmode="numeric"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="is_public" class="mb-1 block text-sm font-semibold text-brand-text">Es Publico?</label>
                <select id="is_public" name="is_public"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border">
                    <option value="1" {{ old('is_public', $event->is_public ? 1 : 0) == 1 ? 'selected' : '' }}>Si</option>
                    <option value="0" {{ old('is_public', $event->is_public ? 1 : 0) == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>

            {{-- Acciones finales de guardado o cancelación. --}}
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                    class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Guardar Cambios
                </button>
                <a href="{{ route('events.show', $event->event_id) }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Cancelar
                </a>
            </div>
        </form>
    </section>
@endsection
