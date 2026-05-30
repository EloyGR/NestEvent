// Utilidades para parseo y formato de fecha/hora en la vista de crear reserva.
const toDateTime = (dateValue, timeValue) => {
    if (!dateValue || !timeValue) return null;

    const parsed = new Date(`${dateValue}T${timeValue}`);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
};

const parseExceptionStart = (dateString) => new Date(`${dateString}T00:00:00`);
const parseExceptionEnd = (dateString) => new Date(`${dateString}T23:59:59`);

const formatDate = (dateString) => {
    const parts = dateString.split('-');
    if (parts.length !== 3) {
        return dateString;
    }

    return `${parts[2]}/${parts[1]}/${parts[0]}`;
};

const formatHour = (timeString) => {
    if (!timeString) return '';
    return String(timeString).slice(0, 5);
};

const dayLabel = (dayOfWeek) => {
    const labels = {
        1: 'Lunes',
        2: 'Martes',
        3: 'Miercoles',
        4: 'Jueves',
        5: 'Viernes',
        6: 'Sabado',
        0: 'Domingo',
    };

    return labels[dayOfWeek] || `Dia ${dayOfWeek}`;
};

const initBookingCreate = () => {
    // Referencias a campos, contenedores y datos serializados por Blade.
    const venueSelect = document.getElementById('venue_id');
    const startDateInput = document.getElementById('start_date');
    const startTimeInput = document.getElementById('start_time');
    const endDateInput = document.getElementById('end_date');
    const endTimeInput = document.getElementById('end_time');
    const errorBox = document.getElementById('date-range-error');
    const exceptionsList = document.getElementById('venue-exceptions-list');
    const emptyState = document.getElementById('venue-exceptions-empty');
    const exceptionsDataNode = document.getElementById('booking-exceptions-data');
    const scheduleList = document.getElementById('venue-schedule-list');
    const scheduleEmptyState = document.getElementById('venue-schedule-empty');
    const availabilityDataNode = document.getElementById('booking-availability-data');
    const form = venueSelect ? venueSelect.closest('form') : null;

    if (!venueSelect || !startDateInput || !startTimeInput || !endDateInput || !endTimeInput || !form || !errorBox || !exceptionsList || !emptyState || !exceptionsDataNode || !scheduleList || !scheduleEmptyState || !availabilityDataNode) {
        // Sale silenciosamente si la pagina actual no contiene este formulario.
        return;
    }

    let exceptionsByVenue = {};
    let availabilityByVenue = {};
    try {
        exceptionsByVenue = JSON.parse(exceptionsDataNode.textContent || '{}');
    } catch {
        exceptionsByVenue = {};
    }

    try {
        availabilityByVenue = JSON.parse(availabilityDataNode.textContent || '{}');
    } catch {
        availabilityByVenue = {};
    }

    const getVenueExceptions = () => {
        const venueId = venueSelect.value;
        return exceptionsByVenue[venueId] || [];
    };

    const getVenueSchedule = () => {
        const venueId = venueSelect.value;
        return availabilityByVenue[venueId] || [];
    };

    const renderVenueSchedule = () => {
        // Pinta horario semanal del local seleccionado.
        const slots = getVenueSchedule();
        scheduleList.innerHTML = '';

        if (!slots.length) {
            scheduleEmptyState.classList.remove('hidden');
            return;
        }

        scheduleEmptyState.classList.add('hidden');

        slots.forEach((slot) => {
            const li = document.createElement('li');
            const label = dayLabel(slot.day_of_week);
            const range = slot.is_available && slot.opening_time && slot.closing_time
                ? `${formatHour(slot.opening_time)} - ${formatHour(slot.closing_time)}`
                : 'Cerrado';

            li.textContent = `${label}: ${range}`;
            scheduleList.appendChild(li);
        });
    };

    const renderVenueExceptions = () => {
        // Pinta excepciones de disponibilidad del local seleccionado.
        const exceptions = getVenueExceptions();
        exceptionsList.innerHTML = '';

        if (!exceptions.length) {
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');

        exceptions.forEach((exception) => {
            const li = document.createElement('li');
            const startLabel = formatDate(exception.start_date);
            const endLabel = formatDate(exception.end_date);
            const reason = exception.reason ? ` (${exception.reason})` : '';

            li.textContent = startLabel === endLabel
                ? `${startLabel}${reason}`
                : `${startLabel} - ${endLabel}${reason}`;

            exceptionsList.appendChild(li);
        });
    };

    const findBlockingException = (startDate, endDate) => {
        // Detecta solape entre el rango solicitado y excepciones bloqueantes.
        const exceptions = getVenueExceptions();

        return exceptions.find((exception) => {
            const exceptionStart = parseExceptionStart(exception.start_date);
            const exceptionEnd = parseExceptionEnd(exception.end_date);

            return startDate <= exceptionEnd && endDate >= exceptionStart;
        }) || null;
    };

    const setDateRangeError = (message) => {
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
        startDateInput.setCustomValidity(message);
        startTimeInput.setCustomValidity(message);
        endDateInput.setCustomValidity(message);
        endTimeInput.setCustomValidity(message);
    };

    const clearDateRangeError = () => {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
        startDateInput.setCustomValidity('');
        startTimeInput.setCustomValidity('');
        endDateInput.setCustomValidity('');
        endTimeInput.setCustomValidity('');
    };

    const validateSelectedRange = () => {
        // Valida orden temporal y conflictos de disponibilidad.
        const startDate = toDateTime(startDateInput.value, startTimeInput.value);
        const endDate = toDateTime(endDateInput.value, endTimeInput.value);

        if (!startDate || !endDate) {
            clearDateRangeError();
            return true;
        }

        if (endDate <= startDate) {
            setDateRangeError('La fecha y hora de fin debe ser posterior al inicio.');
            return false;
        }

        const conflict = findBlockingException(startDate, endDate);

        if (!conflict) {
            clearDateRangeError();
            return true;
        }

        const startLabel = formatDate(conflict.start_date);
        const endLabel = formatDate(conflict.end_date);
        const message = startLabel === endLabel
            ? `El local no esta disponible el ${startLabel}.`
            : `El local no esta disponible entre ${startLabel} y ${endLabel}.`;

        setDateRangeError(message);
        return false;
    };

    venueSelect.addEventListener('change', () => {
        renderVenueSchedule();
        renderVenueExceptions();
        validateSelectedRange();
    });

    startDateInput.addEventListener('change', validateSelectedRange);
    startTimeInput.addEventListener('change', validateSelectedRange);
    endDateInput.addEventListener('change', validateSelectedRange);
    endTimeInput.addEventListener('change', validateSelectedRange);

    form.addEventListener('submit', (event) => {
        if (!validateSelectedRange()) {
            event.preventDefault();
            startDateInput.reportValidity();
            return;
        }

        // Confirmacion final para reforzar reglas de negocio antes de enviar.
        const confirmed = window.confirm('Antes de enviar la reserva, recuerda que el rango elegido debe cumplir el horario permitido del local y sus excepciones de disponibilidad.');
        if (!confirmed) {
            event.preventDefault();
        }
    });

    renderVenueSchedule();
    renderVenueExceptions();
    validateSelectedRange();
};

document.addEventListener('DOMContentLoaded', initBookingCreate);
