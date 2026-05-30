// Gestiona el tema visual (claro/oscuro) y sincroniza logos y etiqueta del toggle.
function updateToggleLabel(isDarkMode) {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.textContent = isDarkMode ? 'Modo Claro' : 'Modo Oscuro';
    }
}

function updateLogos(isDarkMode) {
    const logo = document.querySelector('.header-logo img');
    const heroLogo = document.getElementById('hero-logo');
    const logoPath = isDarkMode ? '/storage/logo/LOGODARKMODE.png' : '/storage/logo/LOGO.png';

    if (logo) {
        logo.src = logoPath;
    }

    if (heroLogo) {
        heroLogo.src = logoPath;
    }
}

function applyDarkMode(isDarkMode) {
    // Aplica la clase de tema en raiz y refresca elementos dependientes.
    const root = document.documentElement;
    root.classList.toggle('dark', isDarkMode);
    updateLogos(isDarkMode);
    updateToggleLabel(isDarkMode);
}

function toggleDarkMode(event) {
    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
    }

    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    const nextDarkMode = !isDarkMode;
    // Persiste preferencia local para mantener el modo entre recargas.
    localStorage.setItem('darkMode', String(nextDarkMode));
    applyDarkMode(nextDarkMode);
}

// Aplica el modo guardado al cargar la pagina y conecta el evento del toggle.
document.addEventListener('DOMContentLoaded', () => {
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    applyDarkMode(isDarkMode);

    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', toggleDarkMode);
    }
});