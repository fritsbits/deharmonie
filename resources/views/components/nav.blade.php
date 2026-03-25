<header style="background-color: var(--color-brand-blue); position: relative;">
    <div class="flex items-center" style="max-width: 72rem; margin: 0 auto; padding: 1.875rem 1.5rem;">
        <a href="{{ route(app()->getLocale() . '.home') }}" class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" class="h-8 w-auto brightness-0 invert">
        </a>
        <nav class="hidden md:flex items-center gap-8" style="margin-left: auto; font-family: var(--font-sans);">
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ app()->getLocale() === 'fr' ? 'Activités' : 'Activiteiten' }}
            </a>
            <a href="{{ route(app()->getLocale() . '.diensten') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ app()->getLocale() === 'fr' ? 'Services' : 'Diensten' }}
            </a>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               Weekmenu de la Semaine
            </a>
            <a href="{{ route(app()->getLocale() . '.home') }}#contact"
               class="font-semibold hover:opacity-80 transition-opacity"
               style="background: var(--color-brand-dark); color: white; padding: 0.5rem 1.25rem; border-radius: 4px; font-size: 1rem; text-decoration: none;">
               Contact
            </a>
        </nav>
        <!-- Mobile toggle -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="md:hidden p-2" style="color: white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div x-show="open" class="absolute top-full left-0 right-0 px-6 py-4 space-y-3 md:hidden z-50"
                 style="background-color: var(--color-brand-dark)">
                <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="block text-sm font-semibold" style="color: white">{{ app()->getLocale() === 'fr' ? 'Activités' : 'Activiteiten' }}</a>
                <a href="{{ route(app()->getLocale() . '.diensten') }}" class="block text-sm font-semibold" style="color: white">{{ app()->getLocale() === 'fr' ? 'Services' : 'Diensten' }}</a>
                <a href="{{ route(app()->getLocale() . '.weekmenu') }}" class="block text-sm font-semibold" style="color: white">Weekmenu de la Semaine</a>
                <a href="{{ route(app()->getLocale() . '.home') }}#contact" class="block text-sm font-semibold" style="color: var(--color-brand-orange)">Contact</a>
            </div>
        </div>
    </div>
</header>
