// Controla carruseles de imagen en detalle de local.
const initVenueShowCarousel = () => {
    document.querySelectorAll('[data-venue-carousel]').forEach((carousel) => {
        if (carousel.dataset.carouselInitialized === '1') {
            // Evita inicializacion duplicada en renders parciales.
            return;
        }

        carousel.dataset.carouselInitialized = '1';

        const slides = Array.from(carousel.querySelectorAll('[data-carousel-slide]'));

        if (slides.length <= 1) {
            return;
        }

        const prevButton = carousel.querySelector('[data-carousel-prev]');
        const nextButton = carousel.querySelector('[data-carousel-next]');
        const dots = Array.from(carousel.querySelectorAll('[data-carousel-dot]'));
        let currentIndex = 0;

        const render = () => {
            // Muestra solo la slide activa y sincroniza indicadores.
            slides.forEach((slide, index) => {
                slide.style.display = index === currentIndex ? 'block' : 'none';
            });

            dots.forEach((dot, index) => {
                const isActive = index === currentIndex;
                dot.style.backgroundColor = isActive ? 'rgba(255,255,255,0.95)' : 'rgba(255,255,255,0.45)';
                dot.setAttribute('aria-current', isActive ? 'true' : 'false');
            });
        };

        prevButton?.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            render();
        });

        nextButton?.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % slides.length;
            render();
        });

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                const targetIndex = Number(dot.dataset.dotIndex);

                if (!Number.isNaN(targetIndex) && targetIndex >= 0 && targetIndex < slides.length) {
                    currentIndex = targetIndex;
                    render();
                }
            });
        });

        render();
    });
};

document.addEventListener('DOMContentLoaded', initVenueShowCarousel);
