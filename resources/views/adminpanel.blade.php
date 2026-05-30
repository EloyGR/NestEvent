@extends('layouts.app')

@php($title = 'Panel Admin')

@section('content')
    {{-- Vista de control global para revisar y moderar el estado de todas las reservas. --}}
    <section class="rounded-2xl bg-brand-bg px-6 py-8">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Panel Admin</h1>
        <p class="mt-2 text-brand-text">
            Bienvenido al panel de administración. Desde aquí podrás gestionar funcionalidades exclusivas para administradores.
        </p>
    </section>

    <section class="mt-6">
        <h2 class="text-xl font-bold text-brand-text">Todas las reservas</h2>

        {{-- Filtros rápidos por estado: el activo se renderiza como texto para evitar recarga redundante. --}}
        <div class="mt-4 mb-4 flex flex-wrap items-center gap-2">
            @if(!$status)
                <span class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition">Todas</span>
            @else
                <a href="{{ route('adminpanel') }}" class="rounded-lg border border-brand-border bg-brand-bg px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">Todas</a>
            @endif

            @if($status === 'pending')
                <span class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition">Pendientes</span>
            @else
                <a href="{{ route('adminpanel', ['status' => 'pending']) }}" class="rounded-lg border border-brand-border bg-brand-bg px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">Pendientes</a>
            @endif

            @if($status === 'confirmed')
                <span class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition">Aprobadas</span>
            @else
                <a href="{{ route('adminpanel', ['status' => 'confirmed']) }}" class="rounded-lg border border-brand-border bg-brand-bg px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">Aprobadas</a>
            @endif

            @if($status === 'cancelled')
                <span class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition">Canceladas</span>
            @else
                <a href="{{ route('adminpanel', ['status' => 'cancelled']) }}" class="rounded-lg border border-brand-border bg-brand-bg px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">Canceladas</a>
            @endif
        </div>

        {{-- Versión móvil en tarjetas para evitar scroll horizontal. --}}
        <div class="mt-4 space-y-3 sm:hidden">
            @forelse ($bookings as $booking)
                <article class="rounded-xl border border-brand-border bg-white p-4 shadow-sm">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Evento</p>
                        @if ($booking->event)
                            <a href="{{ route('events.show', $booking->event->event_id) }}" class="block truncate font-semibold text-brand-text hover:underline" title="{{ $booking->event->name }}">
                                {{ $booking->event->name }}
                            </a>
                        @else
                            <p class="text-sm text-brand-text">No disponible</p>
                        @endif
                    </div>

                    <div class="mt-3 space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Local</p>
                        @if ($booking->venue)
                            <a href="{{ route('venues.show', $booking->venue->venue_id) }}" class="block truncate font-semibold text-brand-text hover:underline" title="{{ $booking->venue->name }}">
                                {{ $booking->venue->name }}
                            </a>
                        @else
                            <p class="text-sm text-brand-text">No disponible</p>
                        @endif
                    </div>

                    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="space-y-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Inicio</p>
                            <p class="text-sm text-brand-text">
                                {{ $booking->start_datetime ? $booking->start_datetime->format('d/m/Y H:i') : 'No disponible' }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Fin</p>
                            <p class="text-sm text-brand-text">
                                {{ $booking->end_datetime ? $booking->end_datetime->format('d/m/Y H:i') : 'No disponible' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Estado</p>
                        @php($isLockedByStartDate = $booking->start_datetime && $booking->start_datetime->lte(now()))
                        @if ($isLockedByStartDate)
                            @php($statusLabel = $booking->booking_status === 'confirmed' ? 'Aprobada' : ($booking->booking_status === 'cancelled' ? 'Cancelada' : 'Pendiente'))
                            <div
                                class="inline-flex items-center gap-2 rounded-lg border border-brand-border bg-white px-3 py-1.5 text-sm text-brand-text opacity-80"
                                title="No se puede modificar el estado una vez iniciada la reserva."
                            >
                                <span>{{ $statusLabel }}</span>
                                <i class="fas fa-lock text-xs" aria-hidden="true"></i>
                            </div>
                        @else
                            <form method="POST" action="{{ route('bookings.updateStatus', $booking->booking_id) }}" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <select
                                    name="booking_status"
                                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-sm text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                                >
                                    <option value="pending" @selected($booking->booking_status === 'pending')>Pendiente</option>
                                    <option value="confirmed" @selected($booking->booking_status === 'confirmed')>Aprobada</option>
                                    <option value="cancelled" @selected($booking->booking_status === 'cancelled')>Cancelada</option>
                                </select>
                                <button
                                    type="submit"
                                    class="w-full rounded-lg border border-brand-border bg-brand-surface px-3 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link"
                                >
                                    Actualizar
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-brand-border bg-white px-4 py-6 text-center text-brand-text">No hay reservas.</div>
            @endforelse
        </div>

        {{-- Tabla de moderación para sm+; en móvil se sustituye por tarjetas. --}}
        <div class="mt-4 hidden overflow-x-auto rounded-xl border border-brand-border shadow-sm sm:block">
            <table class="w-full text-left text-sm text-brand-text">
                <thead class="bg-brand-surface">
                    <tr>
                        <th class="hidden border-b border-brand-border px-4 py-3 font-semibold md:table-cell">#</th>
                        <th class="border-b border-brand-border px-4 py-3 font-semibold">Evento</th>
                        <th class="hidden border-b border-brand-border px-4 py-3 font-semibold sm:table-cell">Local</th>
                        <th class="border-b border-brand-border px-4 py-3 font-semibold">Estado</th>
                        <th class="hidden border-b border-brand-border px-4 py-3 font-semibold lg:table-cell">Factura</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($bookings as $booking)
                        <tr class="border-b border-brand-border transition hover:bg-brand-hover">
                            <td class="hidden px-4 py-3 md:table-cell">{{ $booking->booking_id }}</td>
                            <td class="px-4 py-3">
                                @if ($booking->event)
                                    <a href="{{ route('events.show', $booking->event->event_id) }}" class="block max-w-[10rem] truncate font-medium text-brand-text hover:underline sm:max-w-[13rem] md:max-w-none" title="{{ $booking->event->name }}">
                                        {{ $booking->event->name }}
                                    </a>
                                @else
                                    No disponible
                                @endif
                            </td>
                            <td class="hidden px-4 py-3 sm:table-cell">
                                @if ($booking->venue)
                                    <a href="{{ route('venues.show', $booking->venue->venue_id) }}" class="block max-w-[10rem] truncate font-medium text-brand-text hover:underline sm:max-w-[13rem] md:max-w-none" title="{{ $booking->venue->name }}">
                                        {{ $booking->venue->name }}
                                    </a>
                                @else
                                    No disponible
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php($isLockedByStartDate = $booking->start_datetime && $booking->start_datetime->lte(now()))
                                {{-- Una reserva iniciada queda bloqueada para preservar trazabilidad histórica. --}}
                                @if ($isLockedByStartDate)
                                    @php($statusLabel = $booking->booking_status === 'confirmed' ? 'Aprobada' : ($booking->booking_status === 'cancelled' ? 'Cancelada' : 'Pendiente'))
                                    <div
                                        class="inline-flex items-center gap-2 rounded-lg border border-brand-border bg-white px-3 py-1.5 text-sm text-brand-text opacity-80"
                                        title="No se puede modificar el estado una vez iniciada la reserva."
                                    >
                                        <span>{{ $statusLabel }}</span>
                                        <i class="fas fa-lock text-xs" aria-hidden="true"></i>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('bookings.updateStatus', $booking->booking_id) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select
                                            name="booking_status"
                                            class="rounded-lg border border-brand-border bg-white px-3 py-1.5 text-sm text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                                        >
                                            <option value="pending" @selected($booking->booking_status === 'pending')>Pendiente</option>
                                            <option value="confirmed" @selected($booking->booking_status === 'confirmed')>Aprobada</option>
                                            <option value="cancelled" @selected($booking->booking_status === 'cancelled')>Cancelada</option>
                                        </select>
                                        <button
                                            type="submit"
                                            class="rounded-lg border border-brand-border bg-brand-surface px-3 py-1.5 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link"
                                        >
                                            Actualizar
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="hidden px-4 py-3 lg:table-cell">
                                <span class="cursor-not-allowed text-sm font-semibold text-brand-text opacity-60">Imprimir</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-brand-text">No hay reservas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Paginación del panel de reservas administrativas. --}}
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
@endsection