@extends('layouts.app')

@section('content')
    @php
        // Datos base de la notificación
        $rawMessage = trim((string) data_get($notification, 'message', ''));
        $titleValue = trim((string) data_get($notification, 'title', ''));
        $notificationType = (string) data_get($notification, 'notification_type', '');
        $relatedType = (string) data_get($notification, 'related_entity_type', '');
        $relatedId = data_get($notification, 'related_entity_id');

        $fromLabel = 'Sistema NestEvent';
        $fromEmail = '';
        $subjectLine = $titleValue !== '' ? $titleValue : 'Notificacion';
        $bodyText = $rawMessage;
        $renderAsHtml = false;

        // Expresión regular para extraer remitente/asunto/cuerpo de mensajes de contacto.
        $contactPattern = '/^Mensaje de contacto de\s+(.+?)\s+\(([^)]+)\)\.\s*Asunto:\s*(.+?)\.\s*Contenido:\s*(.*)$/su';
        if (preg_match($contactPattern, $rawMessage, $matches)) {
            $contactName = trim((string) ($matches[1] ?? ''));
            $contactEmail = trim((string) ($matches[2] ?? ''));
            $parsedSubject = trim((string) ($matches[3] ?? ''));
            $parsedBody = trim((string) ($matches[4] ?? ''));

            if ($contactName !== '') {
                $fromLabel = $contactName;
            }

            if ($contactEmail !== '') {
                $fromEmail = $contactEmail;
            }

            if ($parsedSubject !== '') {
                $subjectLine = $parsedSubject;
            }

            if ($parsedBody !== '') {
                $bodyText = $parsedBody;
            }
        }

        // Si la notificación está asociada a una booking, se intentan inyectar enlaces al evento/local.
        $booking = $booking ?? null;
        $isBookingNotification = $relatedType === 'booking' && $booking;

        if ($isBookingNotification) {
            $eventName = $booking->event?->name ?? null;
            $venueName = $booking->venue?->name ?? null;
            $eventLink = $booking->event ? route('events.show', $booking->event->event_id) : null;
            $venueLink = $booking->venue ? route('venues.show', $booking->venue->venue_id) : null;

            $escapedMessage = e($rawMessage);

            if ($eventName && $eventLink) {
                $escapedEventName = e($eventName);
                $escapedMessage = str_replace(
                    ["&#039;{$escapedEventName}&#039;", "&quot;{$escapedEventName}&quot;"],
                    '<a href="' . e($eventLink) . '" class="font-semibold text-brand-link hover:text-brand-link-hover hover:underline">' . $escapedEventName . '</a>',
                    $escapedMessage
                );
            }

            if ($venueName && $venueLink) {
                $escapedVenueName = e($venueName);
                $escapedMessage = str_replace(
                    ["&#039;{$escapedVenueName}&#039;", "&quot;{$escapedVenueName}&quot;"],
                    '<a href="' . e($venueLink) . '" class="font-semibold text-brand-link hover:text-brand-link-hover hover:underline">' . $escapedVenueName . '</a>',
                    $escapedMessage
                );
            }

            $bodyText = $escapedMessage;
            $renderAsHtml = true;
        }
    @endphp

    {{-- Composición tipo correo: cabecera de metadatos + cuerpo de mensaje. --}}
    <article class="mt-2 overflow-hidden rounded-xl border border-brand-border bg-white shadow-sm">
        <header class="border-b border-brand-border bg-brand-surface/45 px-5 py-4 md:px-6">
            <p class="mt-1 text-sm text-brand-text/80">
                {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
            </p>
        </header>

        <div class="space-y-4 px-5 py-5 md:px-6 md:py-6">
            {{-- Encabezados del mensaje (remitente, correo, fecha). --}}
            <section class="rounded-lg border border-brand-border bg-brand-bg">
                <dl class="divide-y divide-brand-border text-sm text-brand-text">
                    <div class="grid grid-cols-12 gap-2 px-4 py-3">
                        <dt class="col-span-3 font-semibold md:col-span-2">De</dt>
                        <dd class="col-span-9 md:col-span-10 break-all">{{ $fromLabel }}</dd>
                    </div>

                    <div class="grid grid-cols-12 gap-2 px-4 py-3">
                        <dt class="col-span-3 font-semibold md:col-span-2">Correo</dt>
                        <dd class="col-span-9 md:col-span-10 break-all">
                            {{ $fromEmail !== '' ? $fromEmail : 'No disponible' }}
                        </dd>
                    </div>

                    <div class="grid grid-cols-12 gap-2 px-4 py-3">
                        <dt class="col-span-3 font-semibold md:col-span-2">Fecha</dt>
                        <dd class="col-span-9 md:col-span-10">
                            {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- Cuerpo principal. --}}
            <section>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-brand-text/80">Mensaje</h3>
                <div class="mt-2 rounded-lg border border-brand-border bg-white px-4 py-4">
                    @if ($renderAsHtml)
                        <p class="whitespace-pre-line break-words leading-relaxed text-brand-text">{!! $bodyText !!}</p>
                    @else
                        <p class="whitespace-pre-line break-words leading-relaxed text-brand-text">{{ $bodyText }}</p>
                    @endif
                </div>
            </section>

            <div class="pt-2">
                <a href="{{ $backUrl }}"
                   class="inline-flex rounded-lg border border-brand-border bg-brand-surface px-4 py-2 text-sm font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                    Volver
                </a>
            </div>
        </div>
    </article>
@endsection
