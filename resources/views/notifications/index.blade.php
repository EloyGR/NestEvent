@extends('layouts.app')

{{-- Límites de texto para mantener tarjetas compactas y legibles. --}}
@php($title = 'Notificaciones')
@php($mobileMessageLimit = 90)
@php($desktopMessageLimit = 180)
@php($titleLimit = 70)

@section('content')
    <section class="rounded-2xl px-6 py-6">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Mis notificaciones</h1>
    </section>

    {{-- Listado paginado de notificaciones del usuario autenticado. --}}
    <section class="mt-2 space-y-3">
        {{-- Tarjeta por notificación con estado de lectura y acceso a detalle. --}}
        @forelse ($notifications as $notification)
            <article class="rounded-xl border border-brand-border bg-brand-form-bg p-4 shadow-sm transition hover:-translate-y-0.5 hover:bg-brand-hover hover:shadow-md md:p-5">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between md:gap-6">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start gap-2">
                            <h2 class="min-w-0 text-sm font-semibold text-brand-form-text md:text-base">
                                <a href="{{ route('notifications.show', ['id' => $notification->notification_id, 'from' => request()->fullUrl()]) }}"
                                   class="text-brand-form-text hover:text-brand-link hover:underline">
                                    {{ \Illuminate\Support\Str::limit($notification->title, $titleLimit) }}
                                </a>
                            </h2>
                            @if (! $notification->is_read)
                                <span class="shrink-0 rounded-full bg-brand-surface px-2 py-0.5 text-[11px] font-bold text-brand-text">
                                    Nueva
                                </span>
                            @endif
                        </div>

                        <p class="mt-2 text-sm leading-relaxed text-brand-form-text md:text-[15px]">
                            <span class="md:hidden">{{ \Illuminate\Support\Str::limit($notification->message, $mobileMessageLimit) }}</span>
                            <span class="hidden md:inline">{{ \Illuminate\Support\Str::limit($notification->message, $desktopMessageLimit) }}</span>
                        </p>
                    </div>

                    <div class="flex flex-col items-start gap-1 md:items-end md:pt-1">
                        <span class="whitespace-nowrap text-xs text-brand-form-text md:text-sm">
                            {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                        </span>

                        <a href="{{ route('notifications.show', ['id' => $notification->notification_id, 'from' => request()->fullUrl()]) }}"
                           class="font-semibold text-brand-link hover:text-brand-link-hover hover:underline md:text-sm">
                            Ver detalle
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-brand-border bg-brand-form-bg px-4 py-6 text-center text-brand-text shadow-sm">
                No tienes notificaciones.
            </div>
        @endforelse
    </section>

    {{-- Navegación entre páginas de notificaciones. --}}
    @if ($notifications->hasPages())
        <nav class="mt-4 flex items-center justify-between gap-2">
            @if ($notifications->onFirstPage())
                <span class="cursor-not-allowed rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text opacity-50">
                    &laquo; Anterior
                </span>
            @else
                <a href="{{ $notifications->previousPageUrl() }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    &laquo; Anterior
                </a>
            @endif

            @if ($notifications->hasMorePages())
                <a href="{{ $notifications->nextPageUrl() }}"
                   class="rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Siguiente &raquo;
                </a>
            @else
                <span class="cursor-not-allowed rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text opacity-50">
                    Siguiente &raquo;
                </span>
            @endif
        </nav>
    @endif
@endsection
