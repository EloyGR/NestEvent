<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locales</title>
</head>
<body>
    @include('partials.name')
    <h1>Todos los locales</h1>
    <!-- Mostrar los locales -->
    <table class="venues-table" border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Ciudad</th>
                <th>Capacidad</th>
                <th>Gerente</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($venues as $venue)
                <tr>
                    <td>
                        <a href="{{ route('venues.show', $venue->venue_id) }}">{{ $venue->name }}</a>
                    </td>
                    <td>{{ $venue->address }}</td>
                    <td>{{ $venue->city }}</td>
                    <td>{{ $venue->capacity }}</td>
                    <td>
                        <!-- Mostrar el gerente del local -->
                        @if ($venue->manager)
                            <a href="{{ route('users.show', $venue->manager->user_id) }}">{{ $venue->manager->username }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No se encontraron locales.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginación -->
    <div>
        {{ $venues->links('pagination::simple-default') }}
    </div>
    <!-- Botón para crear un nuevo local, visible solo para admin o venue-manager -->
    @if (session('user_type') === 'admin' || session('user_type') === 'local_manager')
        @if(session('user_id')) 
            <nav>
                <ul class="pagination">
                    <li class="page-item">
                        <a href="{{ route('venues.create') }}" class="page-link">Da de alta tu local</a>
                    </li>
                    <li class="page-item">
                        <a href="{{ route('venues.myVenues') }}" class="page-link">Mis Locales</a>
                    </li>
                </ul>
            </nav>
        @endif
    @endif
    @include('partials.footer')
</body>
</html>