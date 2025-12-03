<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Local</title>
</head>
<body>
    @include('partials.name')

    <h1>Crear Nuevo Local</h1>
    <!-- Formulario para crear un nuevo local -->
    <form action="{{ route('venues.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="name">Nombre del Local:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div>
            <label for="description">Descripción:</label>
            <textarea id="description" name="description"></textarea>
        </div>

        <div>
            <label for="address">Dirección:</label>
            <input type="text" id="address" name="address" required>
        </div>

        <div>
            <label for="city">Ciudad:</label>
            <input type="text" id="city" name="city" required>
        </div>

        <div>
            <label for="state">Estado:</label>
            <input type="text" id="state" name="state">
        </div>

        <div>
            <label for="zip_code">Código Postal:</label>
            <input type="text" id="zip_code" name="zip_code">
        </div>

        <div>
            <label for="country">País:</label>
            <input type="text" id="country" name="country" required>
        </div>

        <div>
            <label for="capacity">Capacidad:</label>
            <input type="number" id="capacity" name="capacity" required>
        </div>

        <div>
            <label for="price_per_hour">Precio por Hora:</label>
            <input type="number" id="price_per_hour" name="price_per_hour" step="0.01">
        </div>

        <div>
            <label for="venue_image">Imagen del Local:</label>
            <input type="file" id="venue_image" name="venue_image" accept="image/*">
        </div>

        <button type="submit">Registrar Local</button>
    </form>

    <script src="/js/image_validation.js"></script>

    @include('partials.footer')
</body>
</html>