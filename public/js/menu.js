// Gestiona apertura/cierre del menu en movil y sincroniza estado en escritorio.
document.addEventListener('DOMContentLoaded', () => {
    const burgerMenu = document.querySelector('.burger-menu');
    const headerNav = document.querySelector('.header-nav');

    if (!burgerMenu || !headerNav) {
        console.error('Burger menu or header navigation not found');
        return;
    }

    const mobileBreakpoint = 768; // Punto de corte equivalente a md.

    const closeMobileMenu = () => {
        headerNav.classList.add('hidden');
        headerNav.classList.remove('flex', 'flex-col');
        burgerMenu.setAttribute('aria-expanded', 'false');
    };

    const openMobileMenu = () => {
        headerNav.classList.remove('hidden');
        headerNav.classList.add('flex', 'flex-col');
        burgerMenu.setAttribute('aria-expanded', 'true');
    };

    const syncByScreenSize = () => {
        if (window.innerWidth >= mobileBreakpoint) {
            // En escritorio, la navegacion queda siempre visible.
            headerNav.classList.remove('hidden', 'flex-col');
            headerNav.classList.add('flex');
            burgerMenu.setAttribute('aria-expanded', 'false');
        } else {
            // En movil, el menu inicia oculto.
            closeMobileMenu();
        }
    };

    // Inicializa estado segun el tamano actual de pantalla.
    syncByScreenSize();

    burgerMenu.addEventListener('click', (event) => {
        event.stopPropagation();

        const isHidden = headerNav.classList.contains('hidden');
        if (isHidden) {
            openMobileMenu();
        } else {
            closeMobileMenu();
        }
    });

    document.addEventListener('click', (event) => {
        if (window.innerWidth >= mobileBreakpoint) return;

        const isClickInsideMenu = headerNav.contains(event.target) || burgerMenu.contains(event.target);
        if (!isClickInsideMenu) {
            closeMobileMenu();
        }
    });

    window.addEventListener('resize', syncByScreenSize);
});