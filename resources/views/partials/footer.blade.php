<footer class="fixed bottom-0 left-0 w-full border-t border-brand-border bg-brand-surface text-brand-text">
    <div class="mx-auto max-w-6xl px-4 py-4 text-center">
        <p class="m-0 text-sm">
            Copyright {{ date('Y') }} NestEvent |
            <a href="{{ route('contact') }}" class="text-brand-link hover:text-brand-link-hover hover:underline">Contacto</a> |
            <a href="{{ route('legal.terms') }}" class="text-brand-link hover:text-brand-link-hover hover:underline">Aviso legal y terminos de uso</a> |
            <a href="{{ route('legal.privacy') }}" class="text-brand-link hover:text-brand-link-hover hover:underline">Politica de privacidad</a> |
            <a href="{{ route('legal.cookies') }}" class="text-brand-link hover:text-brand-link-hover hover:underline">Cookies</a>
        </p>
    </div>
</footer>