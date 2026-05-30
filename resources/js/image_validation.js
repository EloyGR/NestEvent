// Valida dimensiones minimas de imagen principal en cliente (600x600).
document.getElementById('venue_image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const img = new Image();

    img.onload = function() {
        if (img.width < 600 || img.height < 600) {
            alert('La imagen debe tener al menos 600x600 píxeles.');
            event.target.value = ''; // Limpia el input para forzar nueva seleccion.
        }
    };

    img.src = URL.createObjectURL(file);
});