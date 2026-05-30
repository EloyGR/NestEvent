// Controla el formulario de excepcion parcial en la vista de local.
const initVenueExceptionForm = () => {
    const toggle = document.querySelector('[data-exception-partial-toggle]');
    const fieldsContainer = document.querySelector('[data-exception-partial-fields]');

    if (!toggle || !fieldsContainer) {
        return;
    }

    const timeInputs = Array.from(fieldsContainer.querySelectorAll('[data-exception-partial-time]'));

    const render = () => {
        const isPartial = toggle.checked;
        fieldsContainer.classList.toggle('hidden', !isPartial);

        // Deshabilita inputs ocultos para evitar envios accidentales.
        timeInputs.forEach((input) => {
            input.disabled = !isPartial;
        });
    };

    toggle.addEventListener('change', render);
    render();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVenueExceptionForm);
} else {
    initVenueExceptionForm();
}
