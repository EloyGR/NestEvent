@extends('layouts.app')

{{-- Etiquetas centralizadas para traducir estados técnicos de la reserva en UI. --}}
@php($title = 'Reservas')
@php($statusLabels = ['pending' => 'Pendiente', 'confirmed' => 'Aprobada', 'cancelled' => 'Cancelada'])

@section('content')
    <section class="rounded-2xl px-6 py-6">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Mis reservas</h1>
    </section>

    {{-- Barra de filtros por estado: mantiene seleccionado el estado activo. --}}
    <section class="mt-2 mb-4 flex flex-wrap items-center gap-2">
        @if(!$status)
            <span class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition">Todas</span>
        @else
            <a href="{{ route('bookings.index') }}" class="rounded-lg border border-brand-border bg-brand-bg px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">Todas</a>
        @endif

        @if($status === 'pending')
            <span class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition">Pendientes</span>
        @else
            <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="rounded-lg border border-brand-border bg-brand-bg px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">Pendientes</a>
        @endif

        @if($status === 'confirmed')
            <span class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition">Aprobadas</span>
        @else
            <a href="{{ route('bookings.index', ['status' => 'confirmed']) }}" class="rounded-lg border border-brand-border bg-brand-bg px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">Aprobadas</a>
        @endif

        @if($status === 'cancelled')
            <span class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition">Canceladas</span>
        @else
            <a href="{{ route('bookings.index', ['status' => 'cancelled']) }}" class="rounded-lg border border-brand-border bg-brand-bg px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">Canceladas</a>
        @endif
    </section>

    {{-- Tarjetas móviles para evitar scroll horizontal. --}}
    <section class="mt-2 space-y-3 sm:hidden">
        @forelse ($bookings as $booking)
            <article class="rounded-xl border border-brand-border bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Evento</p>
                <p class="mt-1 truncate font-semibold text-brand-text" title="{{ $booking->event?->name ?? 'No disponible' }}">
                    {{ $booking->event?->name ?? 'No disponible' }}
                </p>

                <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-brand-text/70">Estado</p>
                <p class="mt-1 text-brand-text">{{ $statusLabels[$booking->booking_status] ?? ucfirst($booking->booking_status) }}</p>

                <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-brand-text/70">Fecha</p>
                <p class="mt-1 text-sm text-brand-text">
                    @if ($booking->start_datetime && $booking->end_datetime)
                        {{ $booking->start_datetime->format('d/m/Y H:i') }} - {{ $booking->end_datetime->format('d/m/Y H:i') }}
                    @elseif ($booking->start_datetime)
                        {{ $booking->start_datetime->format('d/m/Y H:i') }}
                    @else
                        No disponible
                    @endif
                </p>
            </article>
        @empty
            <div class="rounded-xl border border-brand-border bg-white px-4 py-6 text-center text-brand-text">No se encontraron reservas.</div>
        @endforelse
    </section>

    {{-- Tabla principal de reservas para sm+ con relaciones a evento/local/aprobador. --}}
    <section class="mt-2 hidden overflow-x-auto rounded-xl border border-brand-border shadow-sm sm:block">
        <table class="w-full text-left text-sm text-brand-text">
            <thead class="bg-brand-surface">
                <tr>
                    <th class="border-b border-brand-border px-4 py-3 font-semibold">Evento</th>
                    <th class="hidden border-b border-brand-border px-4 py-3 font-semibold sm:table-cell">Local</th>
                    <th class="border-b border-brand-border px-4 py-3 font-semibold">Estado</th>
                    <th class="hidden border-b border-brand-border px-4 py-3 font-semibold md:table-cell">Fecha del evento</th>
                    <th class="hidden border-b border-brand-border px-4 py-3 font-semibold md:table-cell">Aprobado por</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                {{-- Cada fila representa una reserva y adapta visualización según datos disponibles. --}}
                @forelse ($bookings as $booking)
                    <tr class="border-b border-brand-border transition hover:bg-brand-hover">
                        <td class="px-4 py-3">
                            @if ($booking->event)
                                <a href="{{ route('events.show', $booking->event->event_id) }}" class="block max-w-[11rem] truncate font-medium text-brand-text hover:underline sm:max-w-[14rem] md:max-w-none" title="{{ $booking->event->name }}">
                                    {{ $booking->event->name }}
                                </a>
                            @else
                                No disponible
                            @endif
                        </td>
                        <td class="hidden px-4 py-3 sm:table-cell">
                            @if ($booking->venue)
                                <a href="{{ route('venues.show', $booking->venue->venue_id) }}" class="block max-w-[12rem] truncate font-medium text-brand-text hover:underline md:max-w-none" title="{{ $booking->venue->name }}">
                                    {{ $booking->venue->name }}
                                </a>
                            @else
                                No disponible
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $statusLabels[$booking->booking_status] ?? ucfirst($booking->booking_status) }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">
                            @if ($booking->start_datetime && $booking->end_datetime)
                                {{ $booking->start_datetime->format('d/m/Y H:i') }} - {{ $booking->end_datetime->format('d/m/Y H:i') }}
                            @elseif ($booking->start_datetime)
                                {{ $booking->start_datetime->format('d/m/Y H:i') }}
                            @else
                                No disponible
                            @endif
                        </td>
                        <td class="hidden px-4 py-3 md:table-cell">
                            @if ($booking->approvedBy)
                                <a href="{{ route('users.show', $booking->approvedBy->user_id) }}" class="font-medium text-brand-text hover:underline">
                                    {{ $booking->approvedBy->username }}
                                </a>
                            @else
                                No revisado
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-brand-text">No se encontraron reservas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{-- Paginación manual para mantener el estilo visual del proyecto. --}}
    @if ($bookings->hasPages())
        <nav class="mt-4 flex items-center justify-between gap-2">
            @if ($bookings->onFirstPage())
                <span class="cursor-not-allowed rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text opacity-50">
                    &laquo; Anterior
                </span>
            @else
                <a href="{{ $bookings->previousPageUrl() }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    &laquo; Anterior
                </a>
            @endif

            @if ($bookings->hasMorePages())
                <a href="{{ $bookings->nextPageUrl() }}"
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

    {{-- Acción contextual: crear reserva solo para perfiles autorizados. --}}
    @if (session('user_type') === 'admin' || session('user_type') === 'event_manager')
        @if(session('user_id'))
            <div class="mt-4 flex flex-wrap justify-center gap-3">
                <a href="{{ route('bookings.create') }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Realizar Reserva
                </a>
            </div>
        @endif
    @endif
@endsection