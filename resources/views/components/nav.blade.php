<header style="background-color: var(--color-brand-blue); position: relative;">
    <div class="max-w-5xl mx-auto flex items-center" style="padding: 1.25rem 1.5rem;">
        <a href="{{ route(app()->getLocale() . '.home') }}" class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" class="h-8 w-auto brightness-0 invert">
        </a>
        <nav class="hidden md:flex items-center gap-8" style="margin-left: auto; font-family: var(--font-sans);">
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.activities') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.diensten') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.services') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.menu') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.contact') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.contact') }}
            </a>
            @php
                $targetLocale  = app()->getLocale() === 'nl' ? 'fr' : 'nl';
                $currentName   = request()->route()?->getName();
                $targetRoute   = $currentName
                    ? preg_replace('/^(nl|fr)\./', $targetLocale . '.', $currentName)
                    : $targetLocale . '.home';
                try {
                    $targetUrl = route($targetRoute, request()->route()?->parameters() ?? []);
                } catch (\Exception) {
                    $targetUrl = route($targetLocale . '.home');
                }
            @endphp
            <a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem; opacity: 0.75;">
               {{ __('nav.language_switch') }}
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
                <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="block text-sm font-semibold" style="color: white">{{ __('nav.activities') }}</a>
                <a href="{{ route(app()->getLocale() . '.diensten') }}" class="block text-sm font-semibold" style="color: white">{{ __('nav.services') }}</a>
                <a href="{{ route(app()->getLocale() . '.weekmenu') }}" class="block text-sm font-semibold" style="color: white">{{ __('nav.menu') }}</a>
                <a href="{{ route(app()->getLocale() . '.contact') }}" class="block text-sm font-semibold" style="color: white">{{ __('nav.contact') }}</a>
                <a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}" class="block text-sm font-semibold" style="color: white; opacity: 0.75;">{{ __('nav.language_switch') }}</a>
            </div>
        </div>
    </div>
</header>
