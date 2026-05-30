<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'NestEvent' }}</title>
    <link rel="icon" href="{{ asset('storage/logo/FAVICON.ico') }}" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-brand-bg text-brand-text">
    {{-- Cabecera global con navegación y estado de sesión del usuario. --}}
    @include('partials.name')

    {{-- Contenedor principal: cada vista inyecta su contenido. --}}
    <main class="mx-auto max-w-6xl px-4 py-6 pb-32 md:pb-28">
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Pie global compartido por todas las páginas. --}}
    @include('partials.footer')
    {{-- Script de tema oscuro. --}}
    <script src="{{ asset('js/darkmode.js') }}" defer></script>
</body>
</html>