@extends('layouts.app')

@php($title = 'Selecciona Tipo de Cuenta')

@section('content')
    {{-- Paso previo al registro: define el rol de gestor antes de abrir el formulario. --}}
    <section class="rounded-2xl border border-brand-border bg-brand-bg px-6 py-8 shadow-sm">
        <h1 class="text-center text-2xl font-bold text-brand-text md:text-3xl">Crear Cuenta</h1>
        <p class="mt-2 text-center text-brand-text">
            Selecciona el tipo de gestor con el que quieres darte de alta.
        </p>

        {{-- Cada tarjeta redirige al mismo formulario con distinto parámetro role. --}}
        <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
            <a
                href="{{ route('sign-in.form', ['role' => 'event_manager']) }}"
                class="flex min-h-40 items-center justify-center rounded-xl border border-brand-border bg-brand-surface px-8 py-10 text-center text-xl font-bold text-brand-text transition hover:bg-brand-hover hover:text-brand-link"
            >
                Alta como Gestor de Eventos
            </a>

            <a
                href="{{ route('sign-in.form', ['role' => 'local_manager']) }}"
                class="flex min-h-40 items-center justify-center rounded-xl border border-brand-border bg-brand-surface px-8 py-10 text-center text-xl font-bold text-brand-text transition hover:bg-brand-hover hover:text-brand-link"
            >
                Alta como Gestor de Locales
            </a>
        </div>
    </section>
@endsection
