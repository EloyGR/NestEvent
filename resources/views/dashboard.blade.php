<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    @include('partials.name')
    <h1>Bienvenido, {{ $user->first_name }}!</h1>
    <p>Has iniciado sesión como {{ $user->email }}.</p>

    <!-- Formulario Cambio de Contraseña -->
    <h2>Cambiar Contraseña</h2>
    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form action="{{ route('change-password') }}" method="POST">
        @csrf
        <label for="current_password">Contraseña Actual:</label>
        <input type="password" id="current_password" name="current_password" required>
        <br><br>
        <label for="new_password">Nueva Contraseña:</label>
        <input type="password" id="new_password" name="new_password" required>
        <br><br>
        <button type="submit">Cambiar Contraseña</button>
    </form>

    <!-- Formulario de Cierre de Sesión -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Cerrar Sesión</button>
    </form>
    @include('partials.footer')
</body>
</html>