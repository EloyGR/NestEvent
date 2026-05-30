@extends('layouts.app')

@php($title = 'Iniciar Sesión')

@section('content')
    {{-- Formulario de autenticación principal para usuarios existentes. --}}
    <section class="mx-auto max-w-xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Iniciar Sesión</h1>
        <p class="mt-2 text-brand-text">Accede a tu cuenta de NestEvent.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">No se pudo iniciar sesión:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-semibold text-brand-text">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-semibold text-brand-text">Contraseña</label>
                <input id="password" name="password" type="password" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <button type="submit"
                class="w-full rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                Entrar
            </button>
        </form>

        {{-- CTA secundario para llevar a registro cuando no existe cuenta. --}}
        <div class="mt-6 border-t border-brand-border pt-4 text-center">
            <p class="text-sm text-brand-text">¿No tienes cuenta todavía?</p>
            <a href="{{ route('sign-in') }}"
               class="mt-3 inline-block w-full rounded-lg border border-brand-border bg-white px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                Crear Cuenta
            </a>
        </div>
    </section>
@endsection