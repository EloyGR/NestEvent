document.querySelector('form').addEventListener('submit', function (e) {
    const startDate = new Date(document.getElementById('start_datetime').value);
    const endDate = new Date(document.getElementById('end_datetime').value);
    const today = new Date();

    // limpia la parte de tiempo para comparar solo fechas
    today.setHours(0, 0, 0, 0);

    if (startDate < today) {
        e.preventDefault();
        alert('La fecha de inicio no puede ser anterior a hoy.');
    }

    if (endDate <= startDate) {
        e.preventDefault();
        alert('La fecha de fin debe ser posterior a la fecha de inicio.');
    }
});