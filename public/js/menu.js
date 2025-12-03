document.addEventListener('DOMContentLoaded', () => {
    const burgerMenu = document.querySelector('.burger-menu');
    const headerNav = document.querySelector('.header-nav');

    if (!burgerMenu || !headerNav) {
        console.error('Burger menu or header navigation not found');
        return;
    }

    // Ensure the menu is hidden and burger icon is visible on page load
    headerNav.classList.remove('active');
    burgerMenu.style.display = 'block';

    burgerMenu.addEventListener('click', () => {
        headerNav.classList.toggle('active');
    });

    document.addEventListener('click', (event) => {
        const isClickInsideMenu = headerNav.contains(event.target) || burgerMenu.contains(event.target);
        if (headerNav.classList.contains('active') && !isClickInsideMenu) {
            headerNav.classList.remove('active');
        }
    });
});