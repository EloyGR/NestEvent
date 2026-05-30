@extends('layouts.app')

@php
    // El rol llega desde selector previo y condiciona texto + valor oculto del formulario.
    $selectedRole = $selectedRole ?? old('user_type');
    $roleLabels = [
        'event_manager' => 'Gestor de Eventos',
        'local_manager' => 'Gestor de Locales',
    ];
    $roleLabel = $roleLabels[$selectedRole] ?? 'Gestor';
    $title = 'Registro';
@endphp

@section('content')
    {{-- Formulario de alta para managers con rol fijado en hidden input. --}}
    <section class="mx-auto max-w-xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Crear Cuenta</h1>
        <p class="mt-2 text-brand-text">Alta como {{ $roleLabel }} en NestEvent.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">No se pudo completar el registro:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('sign-in.submit') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            {{-- Campo técnico para enviar el tipo de usuario elegido en la vista anterior. --}}
            <input type="hidden" name="user_type" value="{{ $selectedRole }}">

            <div>
                <label for="username" class="mb-1 block text-sm font-semibold text-brand-text">Usuario</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-semibold text-brand-text">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="first_name" class="mb-1 block text-sm font-semibold text-brand-text">Nombre</label>
                    <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>

                <div>
                    <label for="last_name" class="mb-1 block text-sm font-semibold text-brand-text">Apellido</label>
                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-semibold text-brand-text">Contraseña</label>
                <input id="password" name="password" type="password" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="profile_picture" class="mb-1 block text-sm font-semibold text-brand-text">Foto de perfil (opcional)</label>
                <input id="profile_picture" name="profile_picture" type="file" accept="image/*"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <button type="submit"
                class="w-full rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                Registrarme
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('sign-in') }}" class="text-sm font-semibold text-brand-text hover:underline">
                Volver a tipo de cuenta
            </a>
        </div>
    </section>
@endsection