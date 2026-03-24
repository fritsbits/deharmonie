<header class="bg-white shadow-sm">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="flex items-center gap-3">
            <span class="font-sans font-bold text-xl" style="color: var(--color-brand-green)">De Harmonie</span>
        </a>
        <nav class="hidden md:flex items-center gap-6 text-sm font-semibold">
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               class="hover:underline transition-colors"
               style="color: var(--color-brand-green)">
               {{ __('nav.activities') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.diensten') }}"
               class="hover:underline transition-colors"
               style="color: var(--color-brand-green)">
               {{ __('nav.services') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               class="hover:underline transition-colors"
               style="color: var(--color-brand-green)">
               {{ __('nav.menu') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.contact') }}"
               class="hover:underline transition-colors"
               style="color: var(--color-brand-green)">
               {{ __('nav.contact') }}
            </a>
            @php
                $switchLocale = app()->getLocale() === 'nl' ? 'fr' : 'nl';
                $switchRoute = $switchLocale . '.activiteiten.index';
            @endphp
            <a href="{{ route($switchRoute) }}"
               class="text-sm font-semibold border px-3 py-1 rounded transition-colors"
               style="border-color: var(--color-brand-green); color: var(--color-brand-green)">
               {{ __('nav.language_switch') }}
            </a>
        </nav>
        <!-- Mobile menu toggle -->
        <button x-data @click="$dispatch('toggle-menu')" class="md:hidden p-2 rounded" aria-label="Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <!-- Mobile nav -->
    <div x-data="{ open: false }" @toggle-menu.window="open = !open" x-show="open" class="md:hidden bg-white border-t px-4 py-3 space-y-3">
        <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="block font-semibold">{{ __('nav.activities') }}</a>
        <a href="{{ route(app()->getLocale() . '.diensten') }}" class="block font-semibold">{{ __('nav.services') }}</a>
        <a href="{{ route(app()->getLocale() . '.weekmenu') }}" class="block font-semibold">{{ __('nav.menu') }}</a>
        <a href="{{ route(app()->getLocale() . '.contact') }}" class="block font-semibold">{{ __('nav.contact') }}</a>
        <a href="{{ route($switchRoute) }}" class="block font-semibold">{{ __('nav.language_switch') }}</a>
    </div>
</header>
