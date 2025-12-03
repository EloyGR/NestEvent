<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome CSS -->
<link rel="icon" href="{{ asset('storage/logo/FAVICON.ico') }}" type="image/x-icon">

<header class="header">
    <!-- Logo and Site Name -->
    <div class="header-logo">
        <a href="{{ route('home') }}">
            <img src="{{ asset('storage/logo/LOGO.png') }}" alt="NestEvent Logo" style="height: 50px; width: auto;">
        </a>
    </div>
    <!-- Burger Menu for Phones -->
    <div class="burger-menu">☰</div>
    <!-- Navegación -->
    <nav class="header-nav">
        <ul class="header-nav-list">
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('events.index') }}">Eventos</a></li>
            <li><a href="{{ route('venues.index') }}">Locales</a></li>
            <!-- Gestión de sesión de usuario -->
            @if (session()->has('user_id'))
                @php
                    // Obtener el usuario actual
                    $user = \App\Models\User::find(session('user_id'));
                @endphp
                @if (session('user_type') !== 'user')
                    <li><a href="{{ route('bookings.index') }}">Reservas</a></li>
                @endif
                <li>
                    <a href="{{ route('users.show', $user->user_id) }}">
                        {{ $user->username }} <i class="fas fa-user"></i>
                    </a>
                </li>
            @else
                <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
            @endif
            <!-- Dark Mode Toggle -->
            <li>
                <a href="#" id="dark-mode-toggle" class="header-nav-list-link">Modo Oscuro</a>
            </li>
        </ul>
    </nav>
</header>
<!-- Include menu.js script -->
<script src="{{ asset('js/menu.js') }}" defer></script>
<!-- Include darkmode.js script -->
<script src="{{ asset('js/darkmode.js') }}" defer></script>