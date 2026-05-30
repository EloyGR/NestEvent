@extends('layouts.app')

@php($title = 'Crear Local')

@section('content')
    <section class="mx-auto max-w-2xl rounded-2xl border border-brand-border bg-brand-bg p-6 shadow-sm md:p-8">
        <h1 class="text-2xl font-bold text-brand-text md:text-3xl">Crear Nuevo Local</h1>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">No se pudo registrar el local:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('venues.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="mb-1 block text-sm font-semibold text-brand-text">Nombre del Local</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="description" class="mb-1 block text-sm font-semibold text-brand-text">Descripción</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="address" class="mb-1 block text-sm font-semibold text-brand-text">Dirección</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}" required
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="city" class="mb-1 block text-sm font-semibold text-brand-text">Ciudad</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" required
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>

                <div>
                    <label for="state" class="mb-1 block text-sm font-semibold text-brand-text">Población</label>
                    <input type="text" id="state" name="state" value="{{ old('state') }}"
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>

                <div>
                    <label for="zip_code" class="mb-1 block text-sm font-semibold text-brand-text">Código Postal</label>
                    <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code') }}"
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>

                <div>
                    <label for="country" class="mb-1 block text-sm font-semibold text-brand-text">País</label>
                    <input type="text" id="country" name="country" value="{{ old('country') }}" required
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>

                <div>
                    <label for="capacity" class="mb-1 block text-sm font-semibold text-brand-text">Capacidad</label>
                    {{-- Capacidad solo acepta enteros positivos para evitar aforos invalidos en UI. --}}
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" step="1" inputmode="numeric" required
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>

                <div>
                    <label for="price_per_hour" class="mb-1 block text-sm font-semibold text-brand-text">Precio por Hora</label>
                    {{-- step=1 prioriza incremento por euros con flechas; se mantiene input decimal para edición manual puntual. --}}
                    <input type="number" id="price_per_hour" name="price_per_hour" min="0" step="1" inputmode="decimal" value="{{ old('price_per_hour') }}"
                        class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                </div>
            </div>

            @php($oldExtraIds = collect(old('extras', []))->map(fn ($id) => (int) $id)->all())
            <div class="rounded-xl border border-brand-border bg-brand-form-bg p-4">
                <p class="mb-3 text-sm font-semibold text-brand-form-text">Extras del local</p>

                @forelse ($extrasByCategory as $category => $extras)
                    <div class="mb-4 last:mb-0">
                        <h3 class="mb-2 text-sm font-bold uppercase tracking-wide text-brand-form-text">{{ $category }}</h3>
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                            @foreach ($extras as $extra)
                                <label for="extra_{{ $extra->extra_id }}" class="flex items-start gap-2 rounded-lg border border-brand-border bg-brand-form-bg px-3 py-2 text-sm text-brand-form-text hover:bg-brand-hover">
                                    <input
                                        type="checkbox"
                                        id="extra_{{ $extra->extra_id }}"
                                        name="extras[]"
                                        value="{{ $extra->extra_id }}"
                                        @checked(in_array((int) $extra->extra_id, $oldExtraIds, true))
                                        class="extras-checkbox"
                                    >
                                    <span>{{ $extra->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-brand-form-text">No hay extras disponibles todavía.</p>
                @endforelse
            </div>

            @php($dayLabels = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sabado', 0 => 'Domingo'])
            <div class="rounded-xl border border-brand-border bg-brand-form-bg p-4">
                <p class="mb-1 text-sm font-semibold text-brand-form-text">Horario semanal</p>
                <p class="mb-3 text-xs text-brand-form-text/80">Marca los dias disponibles y define su hora de apertura y cierre.</p>

                <div class="space-y-3" data-venue-schedule>
                    @foreach ($dayLabels as $day => $dayLabel)
                        @php($dayOld = old("schedule.{$day}", []))
                        @php($isAvailable = isset($dayOld['is_available']) ? ((string) $dayOld['is_available'] === '1') : in_array($day, [1,2,3,4,5], true))
                        @php($openingTime = $dayOld['opening_time'] ?? ($isAvailable ? '09:00' : ''))
                        @php($closingTime = $dayOld['closing_time'] ?? ($isAvailable ? '18:00' : ''))
                        <article class="rounded-lg border border-brand-border bg-white p-3" data-schedule-row>
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-semibold text-brand-form-text">{{ $dayLabel }}</p>
                                <label class="inline-flex items-center gap-2 text-sm text-brand-form-text">
                                    <input type="checkbox" name="schedule[{{ $day }}][is_available]" value="1" data-schedule-toggle @checked($isAvailable)>
                                    <span>Abierto</span>
                                </label>
                            </div>
                            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-form-text/70">Apertura</label>
                                    <input
                                        type="time"
                                        name="schedule[{{ $day }}][opening_time]"
                                        data-schedule-opening
                                        value="{{ $openingTime }}"
                                        class="w-full rounded-lg border border-brand-border bg-white px-2 py-2 text-sm outline-none focus:ring-2 focus:ring-brand-border"
                                    >
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-form-text/70">Cierre</label>
                                    <input
                                        type="time"
                                        name="schedule[{{ $day }}][closing_time]"
                                        data-schedule-closing
                                        value="{{ $closingTime }}"
                                        class="w-full rounded-lg border border-brand-border bg-white px-2 py-2 text-sm outline-none focus:ring-2 focus:ring-brand-border"
                                    >
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="venue_image" class="mb-1 block text-sm font-semibold text-brand-text">Imagen del Local</label>
                <input type="file" id="venue_image" name="venue_image" accept="image/*"
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
            </div>

            <div>
                <label for="extra_images" class="mb-1 block text-sm font-semibold text-brand-text">Imagenes Extra (opcional)</label>
                <input type="file" id="extra_images" name="extra_images[]" accept="image/*" multiple
                    class="w-full rounded-lg border border-brand-border bg-white px-3 py-2 text-brand-text outline-none focus:ring-2 focus:ring-brand-border" />
                <p class="mt-1 text-xs text-brand-text/70">Puedes subir varias imagenes extra para este local.</p>
            </div>

            <button type="submit"
                class="w-full rounded-lg border border-brand-border bg-brand-surface px-4 py-2 font-semibold text-brand-text transition hover:bg-brand-hover hover:text-brand-link">
                Registrar Local
            </button>
        </form>
    </section>

    <script src="/js/image_validation.js"></script>
@endsection