@extends('layouts.app')

@php($title = 'Contacto')

@section('content')
    {{-- Formulario de contacto: genera notificación interna para el equipo administrador. --}}
    <section class="mx-auto max-w-3xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Contacto</h1>
        <p class="mt-2 text-sm text-brand-text">
            Envia tu consulta y el equipo de administracion la recibira en notificaciones.
        </p>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Corrige los siguientes errores:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Se precargan nombre/email cuando hay usuario autenticado. --}}
        <form action="{{ route('contact.store') }}" method="POST" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="mb-1 block text-sm font-semibold text-brand-text">Nombre</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))) }}"
                    required
                    maxlength="100"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                >
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-semibold text-brand-text">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $user->email ?? '') }}"
                    required
                    maxlength="150"
                    autocomplete="email"
                    pattern="^[^\s@]+@[^\s@]+\.[^\s@]{2,}$"
                    title="Introduce un email valido con dominio, por ejemplo usuario@dominio.com"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                >
            </div>

            <div>
                <label for="subject" class="mb-1 block text-sm font-semibold text-brand-text">Asunto</label>
                <input
                    type="text"
                    name="subject"
                    id="subject"
                    value="{{ old('subject') }}"
                    required
                    maxlength="150"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                >
            </div>

            <div>
                <label for="message" class="mb-1 block text-sm font-semibold text-brand-text">Mensaje</label>
                <textarea
                    name="message"
                    id="message"
                    rows="6"
                    required
                    maxlength="2000"
                    aria-describedby="message-help message-counter @error('message') message-error @enderror"
                    class="w-full rounded-lg border @error('message') border-red-400 @else border-brand-border @enderror bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border"
                >{{ old('message') }}</textarea>
                <p id="message-help" class="mt-1 text-xs text-brand-text/80">Maximo 2000 caracteres.</p>
                <p id="message-counter" class="mt-1 text-xs text-brand-text/70" aria-live="polite"></p>
                @error('message')
                    <p id="message-error" class="mt-1 text-sm font-semibold text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link"
            >
                Enviar mensaje
            </button>
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const messageField = document.getElementById('message');
            const counter = document.getElementById('message-counter');
            const emailField = document.getElementById('email');
            const maxLength = 2000;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

            if (!messageField || !counter) return;

            const validateEmailField = () => {
                if (!emailField) return;

                const value = emailField.value.trim();

                if (value === '') {
                    emailField.setCustomValidity('El email es obligatorio.');
                    return;
                }

                if (!emailPattern.test(value)) {
                    emailField.setCustomValidity('Introduce un email valido con dominio, por ejemplo usuario@dominio.com.');
                    return;
                }

                emailField.setCustomValidity('');
            };

            const updateCounter = () => {
                const currentLength = messageField.value.length;
                const remaining = maxLength - currentLength;
                counter.textContent = `Caracteres: ${currentLength}/${maxLength}`;

                if (remaining < 0) {
                    counter.classList.remove('text-brand-text/70');
                    counter.classList.add('text-red-700', 'font-semibold');
                } else {
                    counter.classList.remove('text-red-700', 'font-semibold');
                    counter.classList.add('text-brand-text/70');
                }
            };

            if (emailField) {
                emailField.addEventListener('input', validateEmailField);
                emailField.addEventListener('blur', validateEmailField);
                validateEmailField();
            }

            messageField.addEventListener('input', updateCounter);
            updateCounter();
        });
    </script>
@endsection