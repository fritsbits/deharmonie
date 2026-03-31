<header style="background-color: var(--color-brand-blue); position: relative;">
    <div class="flex items-center" style="max-width: 72rem; margin: 0 auto; padding: 1.875rem 1.5rem;">
        <a href="{{ route(app()->getLocale() . '.home') }}" class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" class="h-8 w-auto brightness-0 invert">
        </a>
        <nav class="hidden md:flex items-center gap-8" style="margin-left: auto; font-family: var(--font-sans);">
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.activities') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.restaurant_menu') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.diensten') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.services') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.over-ons') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.over_ons') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.contact') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.contact') }}
            </a>
            @php
                $targetLocale = app()->getLocale() === 'nl' ? 'fr' : 'nl';
                $currentLocaleLabel = strtoupper(app()->getLocale());
                $targetLocaleLabel  = strtoupper($targetLocale);
                $currentName = request()->route()?->getName();
                $targetRoute = $currentName
                    ? preg_replace('/^(nl|fr)\./', $targetLocale . '.', $currentName)
                    : $targetLocale . '.home';
                try {
                    $targetUrl = route($targetRoute, request()->route()?->parameters() ?? []);
                } catch (\Exception) {
                    $targetUrl = route($targetLocale . '.home');
                }
            @endphp
            {{-- Divider --}}
            <span aria-hidden="true" style="width: 1px; height: 1.25rem; background: rgba(255,255,255,0.35); margin: 0 0.25rem;"></span>
            {{-- Language toggle --}}
            <div class="flex items-center gap-2" style="font-family: var(--font-sans); font-size: 1rem; font-weight: 600; letter-spacing: 0.04em;">
                {{-- Globe icon (Lucide Globe) --}}
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                     style="color: white; opacity: 0.7; flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
                {{-- Current locale (not a link) --}}
                <span style="color: white;">{{ $currentLocaleLabel }}</span>
                {{-- Separator --}}
                <span aria-hidden="true" style="color: white; opacity: 0.35; font-size: 0.75rem; letter-spacing: 0;">/</span>
                {{-- Other locale (link) --}}
                <a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}"
                   style="color: white; opacity: 0.8; text-decoration: underline; text-underline-offset: 2px; text-decoration-thickness: 1px;"
                   class="hover:opacity-100 transition-opacity">{{ $targetLocaleLabel }}</a>
            </div>
        </nav>
        <!-- Mobile toggle -->
        <div x-data="{ open: false }" class="md:hidden" style="margin-left: auto;">
            <button @click="open = !open" :aria-expanded="open" aria-label="{{ __('nav.open_menu') }}" aria-controls="mobile-menu"
                    class="flex items-center gap-2 font-semibold"
                    style="color: white; font-family: var(--font-sans); font-size: 1rem; background: none; border: none; padding: 0.5rem 0; cursor: pointer;">
                {{-- Hamburger icon (shown when closed) --}}
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                {{-- X icon (shown when open) --}}
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Menu
            </button>
            <div id="mobile-menu" x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="absolute top-full left-0 right-0 z-50"
                 style="background-color: var(--color-brand-blue); padding: 0.5rem 1.5rem 2rem;">
                <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans); border-bottom: 1px solid rgba(255,255,255,0.15);">{{ __('nav.activities') }}</a>
                <a href="{{ route(app()->getLocale() . '.weekmenu') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans); border-bottom: 1px solid rgba(255,255,255,0.15);">{{ __('nav.restaurant_menu') }}</a>
                <a href="{{ route(app()->getLocale() . '.diensten') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans); border-bottom: 1px solid rgba(255,255,255,0.15);">{{ __('nav.services') }}</a>
                <a href="{{ route(app()->getLocale() . '.over-ons') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans); border-bottom: 1px solid rgba(255,255,255,0.15);">{{ __('nav.over_ons') }}</a>
                <a href="{{ route(app()->getLocale() . '.contact') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans);">{{ __('nav.contact') }}</a>
                {{-- Mobile language toggle --}}
                <div class="flex items-center gap-2" style="padding: 1rem 0; border-top: 1px solid rgba(255,255,255,0.15); letter-spacing: 0.04em;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                         style="color: white; opacity: 0.7; flex-shrink: 0;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                    <span style="color: white; font-weight: 600; font-size: 1.125rem; font-family: var(--font-sans);">{{ $currentLocaleLabel }}</span>
                    <span aria-hidden="true" style="color: white; opacity: 0.35; font-size: 0.875rem; letter-spacing: 0;">/</span>
                    <a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}"
                       style="color: white; opacity: 0.8; font-weight: 600; font-size: 1.125rem; font-family: var(--font-sans); text-decoration: underline; text-underline-offset: 2px; text-decoration-thickness: 1px;"
                       class="hover:opacity-100 transition-opacity">{{ $targetLocaleLabel }}</a>
                </div>
            </div>
        </div>
    </div>
</header>
