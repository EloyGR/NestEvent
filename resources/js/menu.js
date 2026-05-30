// Gestiona navegacion responsive con accesibilidad y cierre contextual.
document.addEventListener('DOMContentLoaded', () => {
    const burgerMenu = document.getElementById('mobile-menu-button');
    const headerNav = document.getElementById('primary-navigation');

    if (!burgerMenu || !headerNav) return;

    const mobileBreakpoint = 768;
    const menuIconOpen = burgerMenu.querySelector('.menu-icon-open');
    const menuIconClose = burgerMenu.querySelector('.menu-icon-close');
    const openClasses = ['max-h-[26rem]', 'opacity-100', 'pointer-events-auto', 'translate-y-0'];
    const closeClasses = ['max-h-0', 'opacity-0', 'pointer-events-none', '-translate-y-2'];

    const setMenuState = (isOpen) => {
        // Alterna clases de transicion para apertura y cierre del panel movil.
        headerNav.classList.remove(...(isOpen ? closeClasses : openClasses));
        headerNav.classList.add(...(isOpen ? openClasses : closeClasses));
        burgerMenu.setAttribute('aria-expanded', String(isOpen));
        burgerMenu.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');

        if (isMobileView()) {
            document.body.classList.toggle('overflow-hidden', isOpen);
        }

        if (menuIconOpen && menuIconClose) {
            menuIconOpen.classList.toggle('hidden', isOpen);
            menuIconClose.classList.toggle('hidden', !isOpen);
        }
    };

    const isMobileView = () => window.innerWidth < mobileBreakpoint;

    const syncByScreenSize = () => {
        if (isMobileView()) {
            // En movil inicia cerrado.
            setMenuState(false);
        } else {
            // En escritorio restablece estado base sin bloquear scroll.
            burgerMenu.setAttribute('aria-expanded', 'false');
            burgerMenu.setAttribute('aria-label', 'Abrir menú');
            document.body.classList.remove('overflow-hidden');
            if (menuIconOpen && menuIconClose) {
                menuIconOpen.classList.remove('hidden');
                menuIconClose.classList.add('hidden');
            }
        }
    };

    burgerMenu.addEventListener('click', (event) => {
        event.stopPropagation();
        if (!isMobileView()) return;

        const isOpen = burgerMenu.getAttribute('aria-expanded') === 'true';
        setMenuState(!isOpen);
    });

    document.addEventListener('pointerdown', (event) => {
        if (!isMobileView()) return;

        const isOpen = burgerMenu.getAttribute('aria-expanded') === 'true';
        if (!isOpen) return;

        const target = event.target instanceof Element ? event.target : null;
        if (!target) return;

        const isClickInsideMenu = Boolean(target.closest('#primary-navigation, #mobile-menu-button'));
        if (!isClickInsideMenu) {
            setMenuState(false);
        }
    });

    headerNav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (!isMobileView()) return;

            // Respeta clicks modificados (nueva pestaña, etc.).
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) {
                return;
            }

            // Cierra el panel en el mismo ciclo de evento para evitar estados inconsistentes en móvil.
            setMenuState(false);
        });
    });

    const darkModeToggle = document.getElementById('dark-mode-toggle');
    darkModeToggle?.addEventListener('click', () => {
        if (isMobileView()) {
            setMenuState(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (!isMobileView()) return;
        if (event.key !== 'Escape') return;

        // Permite cerrar menu con tecla Escape.
        setMenuState(false);
    });

    window.addEventListener('resize', syncByScreenSize);
    syncByScreenSize();
});