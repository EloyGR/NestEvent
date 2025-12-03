<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    @include('partials.name')
    <div class="hero">
        <div class="hero-content">
            <img src="{{ asset('storage/logo/LOGO.png') }}" alt="NestEvent Logo" class="hero-logo" id="hero-logo">
            <h1>Bienvenido a NestEvent</h1>
            <p>Tu plataforma para gestionar eventos y locales</p>
        </div>
    </div>

    <main class="container" style="margin-bottom: 5%">
        <section class="card-grid">
            <div class="card">
                <a href="{{ route('events.index') }}">
                    <h2>Eventos Destacados</h2>
                    <p>Descubre los eventos más populares y recientes.</p>
                </a>
            </div>

            <div class="card">
                <a href="{{ route('venues.index') }}">
                    <h2>Locales Disponibles</h2>
                    <p>Encuentra el lugar perfecto para tu próximo evento.</p>
                </a>
            </div>
            
            <div class="card">
                <a href="{{ auth()->user() ? route('bookings.index') : route('login') }}">
                    <h2>Gestión de Reservas</h2>
                    <p>Administra tus reservas con facilidad.</p>
                </a>
            </div>

            <div class="card">
                <a href="{{ route('sign-in') }}">
                    <h2>Únete a Nosotros</h2>
                    <p>Regístrate o inicia sesión para comenzar a usar NestEvent.</p>
                </a>
            </div>
        </section>
    </main>
    @include('partials.footer')
    <script src="/js/homecard.js"></script>
</body>
</html>