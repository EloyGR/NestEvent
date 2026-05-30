@extends('layouts.app')

@php($title = 'Politica de cookies')

@section('content')
    <section class="mx-auto max-w-4xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <header>
            <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Politica de cookies</h1>
            <p class="mt-2 text-sm text-brand-text">Ultima actualizacion: {{ now()->format('d/m/Y') }}</p>
        </header>

        <div class="mt-6 space-y-6">
            <section>
                <h2 class="text-lg font-semibold text-brand-text">1. Que son las cookies</h2>
                <p class="mt-2 text-brand-text">
                    Las cookies son pequenos archivos que se almacenan en tu dispositivo para recordar informacion sobre tu navegacion.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">2. Cookies tecnicas utilizadas en {{ config('app.name', 'NestEvent') }}</h2>
                <p class="mt-2 text-brand-text">
                    La aplicacion utiliza cookies tecnicas necesarias para autenticacion, seguridad y mantenimiento de sesion
                    (por ejemplo, cookie de sesion de Laravel y cookie de proteccion CSRF cuando aplica en formularios o peticiones).
                    Estas cookies son necesarias para el funcionamiento basico de la plataforma.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">3. Preferencias locales del navegador</h2>
                <p class="mt-2 text-brand-text">
                    Ademas de cookies, el sitio guarda la preferencia de modo oscuro en el almacenamiento local del navegador
                    (clave <span class="font-semibold">darkMode</span>) para recordar la apariencia seleccionada.
                    Este dato no se usa para publicidad ni perfilado comercial.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">4. Cookies de analitica o publicidad</h2>
                <p class="mt-2 text-brand-text">
                    En el estado actual del proyecto TFG no se han configurado cookies de publicidad comportamental.
                    Si en el futuro se incorporan servicios de analitica o terceros, esta politica se actualizara para informar su finalidad,
                    proveedor y base de legitimacion.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">5. Gestion y desactivacion</h2>
                <p class="mt-2 text-brand-text">
                    Puedes bloquear o eliminar cookies desde tu navegador. Debes tener en cuenta que la desactivacion de cookies tecnicas
                    puede impedir el inicio de sesion o afectar funciones esenciales como formularios y protecciones de seguridad.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">6. Actualizaciones de esta politica</h2>
                <p class="mt-2 text-brand-text">
                    Esta politica puede modificarse para adaptarse a cambios tecnicos, funcionales o normativos.
                    Se recomienda su revision periodica.
                </p>
            </section>
        </div>

        <div class="mt-8 border-t border-brand-border pt-4">
            <a href="{{ route('home') }}" class="text-brand-link hover:text-brand-link-hover hover:underline">Volver al inicio</a>
        </div>
    </section>
@endsection
