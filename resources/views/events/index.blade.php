<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
</head>
<body>
    @include('partials.name')
    <h1>
        {{ request()->routeIs('events.myEvents') ? 'Todos mis eventos' : 'Todos los eventos' }}
    </h1>

    <!-- Mostrar los eventos -->
    <table class="events-table" border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha de Inicio</th>
                <th>Fecha de Fin</th>
                <th>Organizador</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($events as $event)
                <tr>
                    <td>
                        <!-- Enlace a la página de detalles del evento -->
                        <a href="{{ route('events.show', $event->event_id) }}">{{ $event->name }}</a>
                    </td>
                    <td>{{ $event->description }}</td>
                    <td>{{ $event->start_datetime }}</td>
                    <td>{{ $event->end_datetime }}</td>
                    <td>
                        @if ($event->organizer)
                            <a href="{{ route('users.show', $event->organizer->user_id) }}">{{ $event->organizer->username }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No se encontraron eventos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginación -->
    <div>
        {{ $events->links('pagination::simple-default') }}
    </div>

    <!-- Botón para crear un nuevo evento, visible solo para admin o event-manager -->
    @if (session('user_type') === 'admin' || session('user_type') === 'event_manager')
        <nav>
            @if(session('user_id')) 
                <ul class="pagination">
                    <li class="page-item">
                        <a href="{{ route('events.create') }}" class="page-link">Registra Tu Proximo Evento</a>
                    </li>
                    
                    @if (!request()->routeIs('events.myEvents'))
                        <li class="page-item">
                            <a href="{{ route('events.myEvents') }}" class="page-link">Mis Eventos</a>
                        </li>
                    @endif
                </ul>
            @endif
        </nav>
    @endif
    
    @include('partials.footer')
</body>
</html>