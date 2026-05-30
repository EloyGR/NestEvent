@php
    // Si existe sesión legacy, intentamos recuperar el usuario para personalizar navegación.
    $user = session()->has('user_id') ? \App\Models\User::find(session('user_id')) : null;
    // Calcula cuántas notificaciones siguen pendientes de lectura para el usuario actual.
    $unreadNotificationsCount = $user
        ? \Illuminate\Support\Facades\DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->where('is_read', false)
            ->count()
        : 0;
    $unreadBadgeCount = min($unreadNotificationsCount, 9);
@endphp

{{-- Cabecera principal con navegación responsive. --}}
<header class="header w-full border-b border-brand-border bg-brand-surface text-brand-text">
    <div class="relative mx-auto w-full max-w-6xl px-[10px] py-[10px]">
        <div class="flex items-center">
        <div class="header-logo m-0 inline-block flex-1">
            <a href="{{ route('home') }}" class="inline-flex items-center">
                <img src="{{ asset('storage/logo/LOGO.png') }}" alt="Logo de NestEvent" class="h-[50px] w-auto">
            </a>
        </div>

        {{-- Botón hamburguesa para abrir/cerrar menú en pantallas pequeñas. --}}
        <button
            type="button"
            id="mobile-menu-button"
            class="site-menu-toggle inline-flex h-11 w-11 items-center justify-center rounded-md text-2xl text-brand-text transition hover:bg-brand-hover md:hidden"
            aria-label="Abrir o cerrar menú"
            aria-expanded="false"
            aria-controls="primary-navigation"
        >
            <span class="menu-icon-open">☰</span>
            <span class="menu-icon-close hidden">✕</span>
        </button>

        {{-- Navegación principal: en móvil se despliega; en escritorio se muestra fija. --}}
        <nav
            id="primary-navigation"
            class="site-navigation absolute left-0 top-full z-40 mt-2 w-full overflow-hidden rounded-xl border border-brand-border bg-brand-surface shadow-lg transition-all duration-300 ease-out max-h-0 -translate-y-2 opacity-0 pointer-events-none md:static md:ml-auto md:z-auto md:mt-0 md:w-auto md:max-h-none md:translate-y-0 md:overflow-visible md:rounded-none md:border-0 md:bg-transparent md:opacity-100 md:shadow-none md:pointer-events-auto"
        >
            <ul class="site-navigation-list flex flex-col gap-1 px-4 py-4 md:flex-row md:items-center md:gap-8 md:px-0 md:py-0">
                <li><a class="font-bold text-brand-text no-underline hover:text-brand-link" href="{{ route('home') }}">Inicio</a></li>
                <li><a class="font-bold text-brand-text no-underline hover:text-brand-link" href="{{ route('events.index') }}">Eventos</a></li>
                <li><a class="font-bold text-brand-text no-underline hover:text-brand-link" href="{{ route('venues.index') }}">Locales</a></li>

                {{-- Bloque de enlaces para usuario autenticado. --}}
                @if ($user)
                    @if (session('user_type') === 'admin' || session('user_type') === 'event_manager')
                        <li><a class="font-bold text-brand-text no-underline hover:text-brand-link" href="{{ route('bookings.index') }}">Reservas</a></li>
                    @endif
                    @if (session('user_type') === 'admin')
                        <li><a class="font-bold text-brand-text no-underline hover:text-brand-link" href="{{ route('adminpanel') }}">Panel Admin</a></li>
                    @endif
                    {{-- Acceso a notificaciones con badge de no leídas. --}}
                    <li>
                        <a
                            class="relative inline-flex items-center gap-2 font-bold text-brand-text no-underline hover:text-brand-link md:text-xl"
                            href="{{ route('notifications.index') }}"
                            aria-label="Notificaciones"
                            title="Notificaciones"
                        >
                            <span class="md:hidden">Mensajes</span>
                            <i class="fas fa-envelope hidden md:inline-block" aria-hidden="true"></i>
                            @if ($unreadNotificationsCount > 0)
                                {{-- El badge se muestra solo con notificaciones sin leer y se limita visualmente a 9+. --}}
                                <span
                                    class="absolute -right-3 -top-2 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-bold leading-none text-white"
                                    aria-label="{{ $unreadNotificationsCount }} notificaciones sin leer"
                                >
                                    {{ $unreadNotificationsCount > 9 ? '9+' : $unreadBadgeCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                    {{-- Acceso rápido al perfil del usuario autenticado. --}}
                    <li>
                        <a class="font-bold text-brand-text no-underline hover:text-brand-link" href="{{ route('users.show', $user->user_id) }}">
                            {{ $user->username }} <i class="fas fa-user"></i>
                        </a>
                    </li>
                @else
                    {{-- En modo invitado solo se muestra acceso a login. --}}
                    <li><a class="font-bold text-brand-text no-underline hover:text-brand-link" href="{{ route('login') }}">Iniciar Sesión</a></li>
                @endif

                {{-- Interruptor visual para alternar tema claro/oscuro. --}}
                <li>
                    <button type="button" id="dark-mode-toggle" class="header-nav-list-link font-bold text-brand-text no-underline hover:text-brand-link">
                        Modo Oscuro
                    </button>
                </li>
            </ul>
        </nav>
        </div>
    </div>
</header>

{{-- <script src="{{ asset('js/darkmode.js') }}" defer></script> --}}