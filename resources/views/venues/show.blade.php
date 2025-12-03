<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del local</title>
</head>
<body style="margin-bottom: 50px;">
    @include('partials.name')
    <h1>Detalles del Local</h1>
    <div class="container">
        <!-- Main Content Section -->
        <div class="venue-details">
            <!-- Primary Image -->
            <div class="venue-image">
                @php
                    $primaryImage = $venue->images->firstWhere('is_primary', true);
                @endphp

                @if($primaryImage)
                    <img src="{{ asset('storage/' . $primaryImage->image_url) }}" class="primary-image" alt="Imagen del local {{ $venue->name }}">
                @else
                    <img src="{{ asset('storage/venue_images/placeholder.jpg') }}" class="primary-image" alt="Imagen por defecto del local">
                @endif

                <!-- Action Buttons -->
                <ul class="pagination">
                    <li class="page-item">
                        <a href="{{ route('venues.index') }}" class="page-link">Volver a Locales</a>
                    </li>
                    @if(session('user_id'))
                        <li class="page-item">
                            <a href="{{ route('bookings.create', ['venue_id' => $venue->venue_id]) }}" class="page-link">Reservar Este Local</a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Venue Information -->
            <div class="venue-info">
                <h1>{{ $venue->name }}</h1>
                <p>{{ $venue->description }}</p>

                <table class="venue-table">
                    <tr>
                        <th>Dirección</th>
                        <td>{{ $venue->address }}</td>
                    </tr>
                    <tr>
                        <th>Ciudad</th>
                        <td>{{ $venue->city }}</td>
                    </tr>
                    <tr>
                        <th>Estado</th>
                        <td>{{ $venue->state ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Código Postal</th>
                        <td>{{ $venue->zip_code ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>País</th>
                        <td>{{ $venue->country }}</td>
                    </tr>
                    <tr>
                        <th>Capacidad</th>
                        <td>{{ $venue->capacity }}</td>
                    </tr>
                    <tr>
                        <th>Precio por Hora</th>
                        <td>{{ $venue->price_per_hour ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Gerente</th>
                        <td>
                            @if($venue->manager)
                                <a href="{{ route('users.show', $venue->manager->user_id) }}">{{ $venue->manager->username }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Estado</th>
                        <td>{{ $venue->is_active ? 'Activo' : 'Inactivo' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    </div>

    @include('partials.footer')
</body>
</html>