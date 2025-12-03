<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
</head>
<body>
    @include('partials.name')
    <h1>Registrarse</h1>

    <!-- Mostrar mensaje de éxito -->
    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <!-- Mostrar errores de validación -->
    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <!-- Formulario de Registro -->
    <form action="{{ route('sign-in') }}" method="POST">
        @csrf
        <label for="username">Nombre de Usuario:</label>
        <input type="text" id="username" name="username" required>
        <br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br><br>

        <label for="first_name">Nombre:</label>
        <input type="text" id="first_name" name="first_name" required>
        <br><br>

        <label for="last_name">Apellido:</label>
        <input type="text" id="last_name" name="last_name" required>
        <br><br>

        <button type="submit">Registrarse</button>
    </form>
    @include('partials.footer')
</body>
</html>