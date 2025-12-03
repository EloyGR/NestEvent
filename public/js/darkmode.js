// darkmode.js
function applyDarkMode(isDarkMode) {
    const root = document.documentElement;
    const logo = document.querySelector('.header-logo img'); // Updated selector to target the logo image
    const heroLogo = document.getElementById('hero-logo'); // Ensure heroLogo is defined

    if (isDarkMode) {
        // Apply dark mode
        root.style.setProperty('--dark-text', '#ffffff'); // White text for readability
        root.style.setProperty('--honey-yellow', '#333333'); // Dark gray background
        root.style.setProperty('--light-honey', '#222222'); // Darker background
        root.style.setProperty('--golden-border', '#555555'); // Gray border
        root.style.setProperty('--muted-honey', '#888888'); // Muted gray for links
        root.style.setProperty('--hover-honey', '#444444'); // Intermediate color
        root.style.setProperty('--table-header-bg', '#444444'); // Dark background for table headers

        if (logo) {
            logo.src = '/storage/logo/LOGODARKMODE.png';
        }

        if (heroLogo) {
            heroLogo.src = '/storage/logo/LOGODARKMODE.png';
        }
        
    } else {
        // Apply light mode
        root.style.setProperty('--dark-text', '#402E25'); // Dark text for readability
        root.style.setProperty('--honey-yellow', '#FFD44A'); // Softer honey yellow
        root.style.setProperty('--light-honey', '#fffbee'); // Light honey color
        root.style.setProperty('--golden-border', '#d4a017'); // Golden honey border
        root.style.setProperty('--muted-honey', '#a67c00'); // Muted honey for links
        root.style.setProperty('--hover-honey', '#ffeb99'); // Intermediate color
        root.style.setProperty('--table-header-bg', '#FFD44A'); // Light background for table headers

        if (logo) {
            logo.src = '/storage/logo/LOGO.png';
        }

        if (heroLogo) {
            heroLogo.src = '/storage/logo/LOGO.png';
        }
    }
}

function toggleDarkMode() {
    const isDarkMode = localStorage.getItem('darkMode') === 'true';

    // Toggle the mode
    localStorage.setItem('darkMode', !isDarkMode);
    applyDarkMode(!isDarkMode);

    // Update the toggle button text
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.textContent = !isDarkMode ? 'Modo Claro' : 'Modo Oscuro';
    }
}

// Apply the saved mode on page load
document.addEventListener('DOMContentLoaded', () => {
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    applyDarkMode(isDarkMode);

    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', toggleDarkMode);
    }
});