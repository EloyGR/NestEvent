<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Evento</title>
</head>
<body>
    @include('partials.name')

    <h1>Crear Nuevo Evento</h1>
    <!-- Formulario para crear un nuevo evento -->
    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Nombre del Evento:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div>
            <label for="description">Descripción:</label>
            <textarea id="description" name="description"></textarea>
        </div>

        <div>
            <label for="start_datetime">Fecha y Hora de Inicio:</label>
            <input type="datetime-local" id="start_datetime" name="start_datetime" required>
        </div>

        <div>
            <label for="end_datetime">Fecha y Hora de Fin:</label>
            <input type="datetime-local" id="end_datetime" name="end_datetime" required>
        </div>

        <div>
            <label for="event_type">Tipo de Evento:</label>
            <input type="text" id="event_type" name="event_type">
        </div>

        <div>
            <label for="expected_attendance">Asistencia Esperada:</label>
            <input type="number" id="expected_attendance" name="expected_attendance">
        </div>

        <div>
            <label for="is_public">¿Es Público?:</label>
            <select id="is_public" name="is_public">
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <button type="submit">Crear Evento</button>
    </form>

    @include('partials.footer')
    <script src="/js/timedate.js"></script>
</body>
</html>