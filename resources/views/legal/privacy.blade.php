@extends('layouts.app')

@php($title = 'Politica de privacidad')

@section('content')
    <section class="mx-auto max-w-4xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <header>
            <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Politica de privacidad</h1>
            <p class="mt-2 text-sm text-brand-text">Ultima actualizacion: {{ now()->format('d/m/Y') }}</p>
        </header>

        <div class="mt-6 space-y-6">
            <section>
                <h2 class="text-lg font-semibold text-brand-text">1. Responsable del tratamiento</h2>
                <p class="mt-2 text-brand-text">
                    El responsable del tratamiento es el equipo desarrollador de {{ config('app.name', 'NestEvent') }} dentro del contexto del proyecto academico TFG.
                    Hasta su despliegue definitivo, este entorno tiene fines de desarrollo, demostracion y evaluacion.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">2. Datos que recopilamos</h2>
                <p class="mt-2 text-brand-text">
                    Para operar la plataforma se tratan, entre otros, los siguientes datos: nombre de usuario, email, hash de contrasena,
                    nombre y apellidos, telefono (opcional), foto de perfil (opcional), rol de usuario, datos de eventos,
                    datos de locales, estado de reservas y metadatos de participacion/notificaciones asociados al uso de la aplicacion.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">3. Finalidad y base legal</h2>
                <p class="mt-2 text-brand-text">
                    Los datos se utilizan para autenticar usuarios, gestionar perfiles, publicar eventos y locales, tramitar reservas,
                    aplicar controles por rol y mantener la seguridad de la sesion. La base de legitimacion principal es la ejecucion del servicio,
                    el interes legitimo en la seguridad operativa y, en su caso, el consentimiento para datos opcionales como imagen de perfil.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">4. Destinatarios y transferencias</h2>
                <p class="mt-2 text-brand-text">
                    No se preve la cesion comercial de datos personales a terceros. Los datos podran tratarse por proveedores tecnicos
                    necesarios para alojamiento, almacenamiento o mantenimiento, bajo las garantias contractuales correspondientes.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">5. Conservacion de datos</h2>
                <p class="mt-2 text-brand-text">
                    Los datos se conservan mientras exista relacion activa con la cuenta y durante el tiempo necesario para atender responsabilidades
                    tecnicas o legales. Cuando proceda, los datos se eliminaran o anonimizaran de forma segura.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">6. Derechos de las personas usuarias</h2>
                <p class="mt-2 text-brand-text">
                    Puedes solicitar acceso, rectificacion, supresion, oposicion, limitacion del tratamiento y portabilidad,
                    asi como retirar consentimientos otorgados, a traves de los canales de contacto habilitados por el proyecto.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-brand-text">7. Seguridad</h2>
                <p class="mt-2 text-brand-text">
                    {{ config('app.name', 'NestEvent') }} aplica medidas razonables de seguridad tecnicas y organizativas,
                    incluyendo control de acceso por autenticacion y gestion de sesion para reducir riesgos de acceso no autorizado.
                </p>
            </section>
        </div>

        <div class="mt-8 border-t border-brand-border pt-4">
            <a href="{{ route('home') }}" class="text-brand-link hover:text-brand-link-hover hover:underline">Volver al inicio</a>
        </div>
    </section>
@endsection
