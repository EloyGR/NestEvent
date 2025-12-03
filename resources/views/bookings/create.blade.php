<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva</title>
</head>
<body>
    @include('partials.name')

    <div class="container">
        <h1>Haz tu reserva</h1>
        @php
            $user = auth()->user();

            $isEventManager = $user && $user->user_type === 'event_manager';
            $isLocalManager = $user && $user->user_type === 'local_manager';
            $isAdmin = $user && $user->user_type === 'admin';

            if ($isEventManager) {
                $events = $events->filter(fn($event) => $event->organizer_id === $user->user_id);
            } elseif ($isLocalManager) {
                $venues = $venues->filter(fn($venue) => $venue->manager_id === $user->user_id);
                $events = $events; // Mostrar todos los eventos para local_manager
            } elseif ($isAdmin) {
                // Admin puede ver todos los eventos y locales sin filtros
            }
        @endphp

        <!-- Formulario para crear una reserva -->
        <form action="{{ route('bookings.store') }}" method="POST"> 
            @csrf

            @if ($isEventManager || $isLocalManager || $isAdmin)
                <div class="form-group">
                    <!-- Selección de evento -->
                    <label for="event_id">Evento</label>
                    <select name="event_id" id="event_id" class="form-control">
                        @foreach($events as $event)
                            <option value="{{ $event->event_id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <!-- Selección de local -->
                    <label for="venue_id">Local</label>
                    <select name="venue_id" id="venue_id" class="form-control">
                        @foreach($venues as $venue)
                            <option value="{{ $venue->venue_id }}" {{ isset($selectedVenue) && $selectedVenue == $venue->venue_id ? 'selected' : '' }}>{{ $venue->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <!-- Textarea para anotaciones extras -->
                    <label for="notes">Anotaciones</label>
                    <textarea name="notes" id="notes" class="form-control"></textarea>
                </div>

                <!-- Botón de envío para crear la reserva -->
                <button type="submit" class="btn btn-primary">Reserva ahora</button>
            @else
                <p>No tienes permisos para crear reservas.</p>
            @endif
        </form>
    </div>

    @include('partials.footer')
</body>
</html>
