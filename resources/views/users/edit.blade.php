@extends('layouts.app')

@php($title = 'Editar Perfil')

@section('content')
    {{-- Actualización de datos personales básicos del perfil. --}}
    <section class="mx-auto max-w-2xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Editar Perfil</h1>
        <p class="mt-2 text-brand-text">Actualiza tu informacion personal.</p>

        @if ($errors->profileUpdate->any())
            <div class="mt-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">No se pudo guardar:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->profileUpdate->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user->user_id) }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="username" class="mb-1 block text-sm font-semibold text-brand-text">Usuario</label>
                <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-semibold text-brand-text">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="first_name" class="mb-1 block text-sm font-semibold text-brand-text">Nombre</label>
                    <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name) }}" required
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>

                <div>
                    <label for="last_name" class="mb-1 block text-sm font-semibold text-brand-text">Apellido</label>
                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" required
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>
            </div>

            <div>
                <label for="phone" class="mb-1 block text-sm font-semibold text-brand-text">Telefono</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <button type="submit"
                    class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Guardar Cambios
                </button>
                <a href="{{ route('users.show', $user->user_id) }}"
                   class="rounded-lg border border-brand-border bg-white px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Cancelar
                </a>
            </div>
        </form>
    </section>

    {{-- Cambio de contraseña aislado para separar validaciones y errores. --}}
    <section class="mx-auto mt-6 max-w-2xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <h2 class="text-xl font-bold text-brand-text md:text-2xl">Cambiar Contrasena</h2>
        <p class="mt-2 text-brand-text">Gestiona tu contrasena en un bloque independiente.</p>

        @if ($errors->passwordUpdate->any())
            <div class="mt-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">No se pudo actualizar la contrasena:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->passwordUpdate->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.password.update', $user->user_id) }}" class="mt-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="current_password" class="mb-1 block text-sm font-semibold text-brand-text">Contrasena Actual</label>
                <input id="current_password" name="current_password" type="password" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="password" class="mb-1 block text-sm font-semibold text-brand-text">Nueva Contrasena</label>
                    <input id="password" name="password" type="password" required
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-semibold text-brand-text">Confirmar Contrasena</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>
            </div>

            <button type="submit"
                class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                Actualizar Contrasena
            </button>
        </form>
    </section>
@endsection
