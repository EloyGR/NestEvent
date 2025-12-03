<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Usuario</title>
</head>
<body>
    @include('partials.name')
    <h1>Detalles del Usuario</h1>

    <div class="user-page">
        <div class="user-info">
            @if($user->profile_picture)
                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Foto de perfil de {{ $user->username }}">
            @else
                <img src="{{ asset('storage/profile_pictures/placeholder.svg') }}" alt="Foto de perfil por defecto">
            @endif

            <div class="user-details">
                <table>
                    <tr>
                        <th>Nombre de usuario</th>
                        <td>{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Nombre</th>
                        <td>{{ $user->first_name }}</td>
                    </tr>
                    <tr>
                        <th>Apellido</th>
                        <td>{{ $user->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Teléfono</th>
                        <td>{{ $user->phone ?? 'Teléfono no público' }}</td>
                    </tr>
                    <tr>
                        <th>Tipo de Usuario</th>
                        <td>
                            @php
                                $userTypeTranslations = [
                                    'admin' => 'Administrador',
                                    'event_manager' => 'Gestor de Eventos',
                                    'local_manager' => 'Gestor de Locales',
                                    'user' => 'Usuario',
                                ];
                            @endphp
                            {{ $userTypeTranslations[$user->user_type] ?? $user->user_type }}
                        </td>
                    </tr>
                </table>
            </div>

            @if(auth()->check() && auth()->id() === $user->user_id)
                <!--<div class="change-pfp-form">
                    <form action="{{ route('users.uploadProfilePicture', $user->user_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="profile_picture">Selecciona una imagen:</label>
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*" required>
                        </div>
                        <div>
                            <button type="submit">Subir Imagen</button>
                        </div>
                    </form>
                </div>-->

                <nav>
                    <ul class="pagination">
                        <li class="page-item">
                            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                                @csrf
                                <button type="submit" class="page-link">Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                </nav>
            @endif
        </div>

        <div class="user-events-venues">
            @php
                $userType = $user->user_type;
            @endphp

            @if ($userType === 'admin' || $userType === 'event_manager')
                @if (!$events->isEmpty())
                    <h2>Eventos Organizados por {{ $user->username }}</h2>
                    <table border="1" cellpadding="10" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $event)
                                <tr>
                                    <td>
                                        <a href="{{ route('events.show', $event->event_id) }}">{{ $event->name }}</a>
                                    </td>
                                    <td>{{ $event->description }}</td>
                                    <td>{{ $event->start_datetime }}</td>
                                    <td>{{ $event->end_datetime }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif

            @if ($userType === 'admin' || $userType === 'local_manager')
                @if (!$venues->isEmpty())
                    <h2>Locales gestionados por {{ $user->username }}</h2>
                    <table border="1" cellpadding="10" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Ciudad</th>
                                <th>Capacidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($venues as $venue)
                                <tr>
                                    <td>
                                        <a href="{{ route('venues.show', $venue->venue_id) }}">{{ $venue->name }}</a>
                                    </td>
                                    <td>{{ $venue->address }}</td>
                                    <td>{{ $venue->city }}</td>
                                    <td>{{ $venue->capacity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @include('partials.footer')
</body>
</html>