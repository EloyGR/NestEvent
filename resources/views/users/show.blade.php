@extends('layouts.app')

@php
    // Variables de contexto para traducir rol y determinar permisos de edición del propio perfil.
    $title = 'Detalles del Usuario';
    $userType = $user->user_type;
    $userTypeTranslations = [
        'admin' => 'Administrador',
        'event_manager' => 'Gestor de Eventos',
        'local_manager' => 'Gestor de Locales',
        'user' => 'Usuario',
    ];

    $isProfileOwner = auth()->check() && optional(auth()->user())->user_id === $user->user_id;
@endphp

@section('content')
    <section class="rounded-2xl bg-brand-bg px-6 py-6">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Perfil de {{ $user->username }}</h1>
        <p class="mt-2 text-brand-text">Consulta la informacion personal y la actividad relacionada con este usuario.</p>
    </section>

    {{-- Layout en dos columnas: tarjeta de perfil + tabla de datos personales. --}}
    <section class="mt-6 grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
        <article class="rounded-xl border border-brand-border bg-white p-5 text-black shadow-sm lg:col-span-1">
            <div class="mx-auto h-32 w-32 overflow-hidden rounded-full border border-brand-border bg-brand-bg">
                @if($user->profile_picture)
                    <img
                        src="{{ asset('storage/' . $user->profile_picture) }}"
                        alt="Foto de perfil de {{ $user->username }}"
                        class="h-full w-full object-cover"
                    >
                @else
                    <img
                        src="{{ asset('storage/profile_pictures/placeholder.svg') }}"
                        alt="Foto de perfil por defecto"
                        class="h-full w-full object-cover"
                    >
                @endif
            </div>

            <div class="mt-4 text-center">
                <h2 class="text-xl font-bold text-black">{{ $user->username }}</h2>
                <p class="mt-1 text-sm text-black/80">{{ $user->email }}</p>
                <span class="mt-3 inline-block rounded-full border border-brand-border bg-brand-surface px-3 py-1 text-xs font-semibold text-brand-text">
                    {{ $userTypeTranslations[$userType] ?? $userType }}
                </span>
            </div>

            {{-- Acciones sensibles disponibles solo para el propietario del perfil. --}}
            @if($isProfileOwner)
                <div class="mt-5 space-y-4">
                    <a
                        href="{{ route('users.edit', $user->user_id) }}"
                        class="block w-full rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-center font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link"
                    >
                        Editar Perfil
                    </a>

                    <form
                        action="{{ route('users.uploadProfilePicture', $user->user_id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-2"
                    >
                        @csrf
                        <label for="profile_picture" class="block text-left text-sm font-semibold text-black">
                            Subir foto de perfil
                        </label>
                        <input
                            type="file"
                            id="profile_picture"
                            name="profile_picture"
                            accept="image/*"
                            required
                            class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-sm text-black outline-none focus:ring-2 focus:ring-brand-border"
                        >
                        <button
                            type="submit"
                            class="w-full rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link"
                        >
                            Actualizar Foto
                        </button>
                    </form>

                    <form action="{{ route('logout') }}" method="POST" class="flex justify-center">
                        @csrf
                        <button
                            type="submit"
                            class="w-full rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link"
                        >
                            Cerrar Sesion
                        </button>
                    </form>

                    <form action="{{ route('users.destroy', $user->user_id) }}" method="POST"
                          onsubmit="return confirm('¿Seguro que quieres eliminar tu cuenta? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="w-full rounded-lg border border-red-300 bg-red-50 px-4 py-2 font-semibold text-red-700 transition hover:bg-red-100"
                        >
                            Eliminar Cuenta
                        </button>
                    </form>
                </div>
            @endif
        </article>

        <article class="rounded-xl border border-brand-border shadow-sm lg:col-span-2">
            @php
                $profileFields = [
                    'Nombre de usuario' => $user->username,
                    'Email' => $user->email,
                    'Nombre' => $user->first_name,
                    'Apellido' => $user->last_name,
                    'Telefono' => $user->phone ?? 'Telefono no publico',
                    'Tipo de usuario' => $userTypeTranslations[$userType] ?? $userType,
                ];
            @endphp

            <div class="space-y-2 bg-white p-4 md:hidden">
                @foreach($profileFields as $label => $value)
                    <article class="rounded-lg border border-brand-border bg-brand-bg px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">{{ $label }}</p>
                        <p class="mt-1 break-words text-sm text-brand-text">{{ $value }}</p>
                    </article>
                @endforeach
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="w-full table-fixed text-left text-sm text-brand-text">
                    <tbody class="bg-white">
                        @foreach($profileFields as $label => $value)
                            <tr class="border-b border-brand-border hover:bg-brand-hover">
                                <th class="w-32 bg-brand-surface px-4 py-3 font-semibold md:w-44">{{ $label }}</th>
                                <td class="px-4 py-3 break-words">{{ $value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    {{-- Historial de eventos organizados cuando el rol del usuario lo permite. --}}
    @if ($userType === 'admin' || $userType === 'event_manager')
        <section class="mt-8 rounded-2xl border border-brand-border bg-brand-bg px-6 py-6 shadow-sm">
            <h2 class="text-xl font-bold text-brand-text">Eventos Organizados por {{ $user->username }}</h2>

            @if ($events->isEmpty())
                <p class="mt-3 rounded-lg border border-brand-border bg-white px-4 py-3 text-black">
                    Este usuario no tiene eventos organizados.
                </p>
            @else
                <div class="mt-4 space-y-3 md:hidden">
                    @foreach ($events as $event)
                        <article class="rounded-xl border border-brand-border bg-white p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Nombre</p>
                            <a href="{{ route('events.show', $event->event_id) }}" class="mt-1 block truncate font-semibold text-brand-text hover:underline" title="{{ $event->name }}">
                                {{ $event->name }}
                            </a>
                            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-brand-text/70">Fecha de Inicio</p>
                            <p class="mt-1 text-brand-text">{{ $event->start_datetime }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-4 hidden overflow-x-auto rounded-xl border border-brand-border shadow-sm md:block">
                    <table class="w-full text-left text-sm text-brand-text">
                        <thead class="bg-brand-surface">
                            <tr>
                                <th class="border-b border-brand-border px-4 py-3 font-semibold">Nombre</th>
                                <th class="hidden border-b border-brand-border px-4 py-3 font-semibold md:table-cell">Descripcion</th>
                                <th class="border-b border-brand-border px-4 py-3 font-semibold">Fecha de Inicio</th>
                                <th class="hidden border-b border-brand-border px-4 py-3 font-semibold lg:table-cell">Fecha de Fin</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach ($events as $event)
                                <tr class="border-b border-brand-border transition hover:bg-brand-hover">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('events.show', $event->event_id) }}" class="block max-w-[11rem] truncate font-medium text-brand-text hover:underline sm:max-w-[14rem] md:max-w-none" title="{{ $event->name }}">
                                            {{ $event->name }}
                                        </a>
                                    </td>
                                    <td class="hidden px-4 py-3 max-w-xs md:table-cell">
                                        <span class="line-clamp-2 block" title="{{ $event->description }}">{{ $event->description }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $event->start_datetime }}</td>
                                    <td class="hidden px-4 py-3 lg:table-cell">{{ $event->end_datetime }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif

    {{-- Historial de locales gestionados para roles con responsabilidad sobre venues. --}}
    @if ($userType === 'admin' || $userType === 'local_manager')
        <section class="mt-8 rounded-2xl border border-brand-border bg-brand-bg px-6 py-6 shadow-sm">
            <h2 class="text-xl font-bold text-brand-text">Locales gestionados por {{ $user->username }}</h2>

            @if ($venues->isEmpty())
                <p class="mt-3 rounded-lg border border-brand-border bg-white px-4 py-3 text-black">
                    Este usuario no tiene locales asignados.
                </p>
            @else
                <div class="mt-4 space-y-3 md:hidden">
                    @foreach ($venues as $venue)
                        <article class="rounded-xl border border-brand-border bg-white p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-brand-text/70">Nombre</p>
                            <a href="{{ route('venues.show', $venue->venue_id) }}" class="mt-1 block truncate font-semibold text-brand-text hover:underline" title="{{ $venue->name }}">
                                {{ $venue->name }}
                            </a>
                            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-brand-text/70">Ciudad</p>
                            <p class="mt-1 text-brand-text">{{ $venue->city }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-4 hidden overflow-x-auto rounded-xl border border-brand-border shadow-sm md:block">
                    <table class="w-full text-left text-sm text-brand-text">
                        <thead class="bg-brand-surface">
                            <tr>
                                <th class="border-b border-brand-border px-4 py-3 font-semibold">Nombre</th>
                                <th class="hidden border-b border-brand-border px-4 py-3 font-semibold md:table-cell">Direccion</th>
                                <th class="border-b border-brand-border px-4 py-3 font-semibold">Ciudad</th>
                                <th class="hidden border-b border-brand-border px-4 py-3 font-semibold lg:table-cell">Capacidad</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach ($venues as $venue)
                                <tr class="border-b border-brand-border transition hover:bg-brand-hover">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('venues.show', $venue->venue_id) }}" class="block max-w-[11rem] truncate font-medium text-brand-text hover:underline sm:max-w-[14rem] md:max-w-none" title="{{ $venue->name }}">
                                            {{ $venue->name }}
                                        </a>
                                    </td>
                                    <td class="hidden px-4 py-3 max-w-xs md:table-cell">
                                        <span class="line-clamp-2 block" title="{{ $venue->address }}">{{ $venue->address }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $venue->city }}</td>
                                    <td class="hidden px-4 py-3 lg:table-cell">{{ $venue->capacity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
@endsection