@extends('layouts.app')

@php($title = 'Aviso legal y terminos de uso')

@section('content')
    <section class="mx-auto max-w-4xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <header>
            <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Aviso legal y terminos de uso</h1>
            <p class="mt-2 text-sm text-brand-text">Ultima actualizacion: {{ now()->format('d/m/Y') }}</p>
        </header>

        <div class="mt-6 space-y-6">
            <section>
                <h2 class="text-lg font-semibold text-brand-text">1. Sobre {{ config('app.name', 'NestEvent') }}</h2>
                <p class="mt-2 text-brand-text">
                    {{ config('app.name', 'NestEvent') }} es una plataforma web desarrollada como proyecto academico de TFG para la gestion de eventos,
                    locales y reservas. La aplicacion permite registrar cuentas, crear eventos, publicar locales y coordinar solicitudes de reserva.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">2. Acceso y roles de uso</h2>
                <p class="mt-2 text-brand-text">
                    El uso de la plataforma implica la aceptacion de estas condiciones. Existen perfiles funcionales con permisos diferenciados
                    (admin, gestor de eventos y gestor de locales) para consultar, crear, editar o gestionar recursos del sistema.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">3. Obligaciones del usuario</h2>
                <p class="mt-2 text-brand-text">
                    El usuario se compromete a aportar informacion veraz, custodiar sus credenciales de acceso y usar la aplicacion de forma licita,
                    sin introducir contenido fraudulento ni realizar acciones que afecten a la seguridad o disponibilidad del servicio.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">4. Contenido de eventos, locales y reservas</h2>
                <p class="mt-2 text-brand-text">
                    Cada usuario es responsable de la informacion que publica sobre eventos, locales y solicitudes de reserva.
                    {{ config('app.name', 'NestEvent') }} proporciona la herramienta tecnologica y no asume obligaciones contractuales externas entre terceros.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">5. Propiedad intelectual</h2>
                <p class="mt-2 text-brand-text">
                    El codigo, diseno y elementos visuales de la aplicacion, salvo componentes de terceros con su propia licencia,
                    estan protegidos por la normativa de propiedad intelectual.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">6. Disponibilidad y cambios</h2>
                <p class="mt-2 text-brand-text">
                    Al tratarse de un proyecto academico en evolucion, pueden producirse cambios funcionales, tareas de mantenimiento
                    o interrupciones temporales del servicio sin previo aviso.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">7. Legislacion aplicable</h2>
                <p class="mt-2 text-brand-text">
                    Estas condiciones se interpretaran conforme a la normativa aplicable. Para cualquier controversia,
                    las partes se someteran al fuero que corresponda legalmente.
                </p>
            </section>
        </div>

        <div class="mt-8 border-t border-brand-border pt-4">
            <a href="{{ route('home') }}" class="text-brand-link hover:text-brand-link-hover hover:underline">Volver al inicio</a>
        </div>
    </section>
@endsection
