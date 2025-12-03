document.addEventListener('DOMContentLoaded', () => {
    const burgerMenu = document.querySelector('.burger-menu');
    const headerNav = document.querySelector('.header-nav');

    if (!burgerMenu || !headerNav) {
        console.error('Burger menu or header navigation not found');
        return;
    }

    console.log('Burger menu and header navigation found'); // Debugging log

    burgerMenu.addEventListener('click', () => {
        console.log('Burger menu clicked'); // Debugging log
        headerNav.classList.toggle('active');
        console.log('Header navigation active state:', headerNav.classList.contains('active')); // Debugging log
    });

    document.addEventListener('click', (event) => {
        const isClickInsideMenu = headerNav.contains(event.target) || burgerMenu.contains(event.target);
        if (!isClickInsideMenu) {
            headerNav.classList.remove('active');
            console.log('Clicked outside menu, menu hidden'); // Debugging log
        }
    });
});