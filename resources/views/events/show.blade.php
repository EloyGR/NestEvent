<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Evento</title>
</head>
<body>
    @include('partials.name')
    <h1>Detalles del Evento</h1>
    <!-- Tabla de detalles del evento -->
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Nombre</th>
            <td>{{ $event->name }}</td>
        </tr>
        <tr>
            <th>Descripción</th>
            <td>{{ $event->description }}</td>
        </tr>
        <tr>
            <th>Fecha de Inicio</th>
            <td>{{ $event->start_datetime }}</td>
        </tr>
        <tr>
            <th>Fecha de Fin</th>
            <td>{{ $event->end_datetime }}</td>
        </tr>
        <tr>
            <th>Organizador</th>
            <td>{{ $event->organizer->username ?? 'N/A' }}</td>
        </tr>   
        <tr>
            <th>Tipo de Evento</th>
            <td>{{ $event->event_type ?? 'N/A' }}</td>
        <tr>
            <th>Asistencia Esperada</th>
            <td>{{ $event->expected_attendance ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $event->status }}</td>
        </tr>
    </table>
    <ul class="pagination">
        @if (auth()->check() && auth()->user()->user_id === $event->organizer_id)
            <li class="page-item">
                <a href="{{ route('bookings.create') }}" class="page-link">Reservar Un Local</a>
            </li>
        @endif
        <li class="page-item">
            <a href="{{ route('events.index') }}" class="page-link">Volver a Eventos</a>
        </li>
    </ul>
    @include('partials.footer')
</body>
</html>