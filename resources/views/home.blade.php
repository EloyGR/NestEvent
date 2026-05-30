@extends('layouts.app')

@php($title = 'Inicio')

@section('content')
    {{-- Bloque de bienvenida con logo y propuesta de valor. --}}
    <section class="rounded-2xl border border-brand-border bg-brand-bg px-6 py-10 text-center shadow-sm">
        <div class="mx-auto max-w-3xl">
            <img
                src="{{ asset('storage/logo/LOGODARKMODE.png') }}"
                alt="Logo de NestEvent"
                class="mx-auto mb-6 h-24 w-auto md:h-28"
                id="hero-logo"
            >
            <h1 class="text-3xl font-bold tracking-tight text-brand-text md:text-4xl">
                Bienvenido a NestEvent
            </h1>
            <p class="mt-3 text-base text-brand-text md:text-lg">
                Tu plataforma para gestionar eventos y locales
            </p>
        </div>
    </section>

    {{-- Tarjetas de acceso rápido a módulos principales de la plataforma. --}}
    <section class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2">
        <a href="{{ route('events.index') }}"
           class="group block rounded-xl border border-brand-border bg-brand-surface p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <h2 class="text-xl font-semibold text-brand-text group-hover:text-brand-link">
                Eventos Destacados
            </h2>
            <p class="mt-2 text-brand-text">
                Descubre los eventos más populares y recientes.
            </p>
        </a>

        <a href="{{ route('venues.index') }}"
           class="group block rounded-xl border border-brand-border bg-brand-surface p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <h2 class="text-xl font-semibold text-brand-text group-hover:text-brand-link">
                Locales Disponibles
            </h2>
            <p class="mt-2 text-brand-text">
                Encuentra el lugar perfecto para tu próximo evento.
            </p>
        </a>

        <a href="{{ auth()->user() ? route('bookings.index') : route('login') }}"
           class="group block rounded-xl border border-brand-border bg-brand-surface p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <h2 class="text-xl font-semibold text-brand-text group-hover:text-brand-link">
                Gestión de Reservas
            </h2>
            <p class="mt-2 text-brand-text">
                Administra tus reservas con facilidad.
            </p>
        </a>

        <a href="{{ route('sign-in') }}"
           class="group block rounded-xl border border-brand-border bg-brand-surface p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <h2 class="text-xl font-semibold text-brand-text group-hover:text-brand-link">
                Únete a Nosotros
            </h2>
            <p class="mt-2 text-brand-text">
                Regístrate o inicia sesión para comenzar a usar NestEvent.
            </p>
        </a>
    </section>

    {{-- Script de interacción visual para tarjetas de portada. --}}
    <script src="/js/homecard.js"></script>
@endsection