// Controla filas de horario en formularios de alta y edicion de locales.
// Si un dia esta cerrado, deshabilita apertura/cierre para no enviarlos.

function updateRowState(row) {
    const toggle = row.querySelector('[data-schedule-toggle]');
    const openingInput = row.querySelector('[data-schedule-opening]');
    const closingInput = row.querySelector('[data-schedule-closing]');

    if (!toggle || !openingInput || !closingInput) {
        return;
    }

    const isOpen = toggle.checked;

    // Regla de UX:
    // - Dia abierto: inputs de hora activos.
    // - Dia cerrado: inputs deshabilitados (el navegador no los envia).
    openingInput.disabled = !isOpen;
    closingInput.disabled = !isOpen;

    // Mantiene feedback visual coherente con el estado interactivo.
    row.classList.toggle('opacity-70', !isOpen);

    // Mejora accesibilidad para anunciar contexto de fila deshabilitada.
    row.setAttribute('aria-disabled', (!isOpen).toString());
}

function initVenueScheduleTables() {
    const tables = document.querySelectorAll('[data-venue-schedule]');

    tables.forEach((table) => {
        const rows = table.querySelectorAll('[data-schedule-row]');

        rows.forEach((row) => {
            const toggle = row.querySelector('[data-schedule-toggle]');
            if (!toggle) {
                return;
            }

            // Inicializa estado segun datos renderizados desde servidor.
            updateRowState(row);

            // Reacciona en tiempo real a cambios del usuario.
            toggle.addEventListener('change', () => updateRowState(row));
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVenueScheduleTables);
} else {
    initVenueScheduleTables();
}
