<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas</title>
</head>
<body>
    @include('partials.name')
    <h1>Todas mis reservas</h1>
    <!-- Tabla de reservas -->
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Evento</th>
                <th>Local</th>
                <th>Estado</th>
                <th>Fecha de Reserva</th>
                <th>Aprobado Por</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bookings->sortBy('booking_id') as $booking)
                <tr>
                    <td>
                        @if ($booking->event)
                            <a href="{{ route('events.show', $booking->event->event_id) }}">{{ $booking->event->name }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if ($booking->venue)
                            <a href="{{ route('venues.show', $booking->venue->venue_id) }}">{{ $booking->venue->name }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $booking->booking_status }}</td>
                    <td>{{ $booking->booking_date->format('Y-m-d') }}</td>
                    <td>
                        <!-- Mostrar el usuario que aprobó la reserva -->
                        @if ($booking->approvedBy)
                            <a href="{{ route('users.show', $booking->approvedBy->user_id) }}">{{ $booking->approvedBy->username }}</a>
                        @else
                            No revisado
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No se encontraron reservas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginación -->
    <div>
        {{ $bookings->links('pagination::simple-default') }}
    </div>
    <!-- Botón para crear una nueva reserva -->
    <ul class="pagination">
        @if (auth()->check() && auth()->user()->type !== 'user')
            <li class="page-item">
                <a href="{{ route('bookings.create') }}" class="page-link">Realizar Reserva</a>
            </li>
        @endif
    </ul>
    @include('partials.footer')
</body>
</html>
