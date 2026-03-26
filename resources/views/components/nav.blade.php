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
            <div class="flex items-center gap-1.5" style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 600;">
                {{-- Globe icon (Heroicons outline GlobeAltIcon) --}}
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                     style="color: white; opacity: 0.75; flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
                {{-- Current locale (not a link) --}}
                <span style="color: white; opacity: 1;">{{ $currentLocaleLabel }}</span>
                {{-- Separator --}}
                <span aria-hidden="true" style="color: white; opacity: 0.4; font-size: 0.75rem;">/</span>
                {{-- Other locale (link) --}}
                <a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}"
                   style="color: white; opacity: 0.6; text-decoration: underline;"
                   class="hover:opacity-90 transition-opacity">{{ $targetLocaleLabel }}</a>
            </div>
        </nav>
        <!-- Mobile toggle -->
        <div x-data="{ open: false }" class="md:hidden" style="margin-left: auto;">
            <button @click="open = !open" :aria-expanded="open" aria-label="{{ __('nav.open_menu') }}"
                    class="flex items-center gap-2 font-semibold"
                    style="color: white; font-family: var(--font-sans); font-size: 1rem; background: none; border: none; padding: 0.5rem 0; cursor: pointer;">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                Menu
            </button>
            <div x-show="open" class="absolute top-full left-0 right-0 z-50"
                 style="background-color: var(--color-brand-blue); padding: 0.5rem 1.5rem 2rem;">
                <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans); border-bottom: 1px solid rgba(255,255,255,0.15);">{{ __('nav.activities') }}</a>
                <a href="{{ route(app()->getLocale() . '.weekmenu') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans); border-bottom: 1px solid rgba(255,255,255,0.15);">{{ __('nav.restaurant_menu') }}</a>
                <a href="{{ route(app()->getLocale() . '.diensten') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans); border-bottom: 1px solid rgba(255,255,255,0.15);">{{ __('nav.services') }}</a>
                <a href="{{ route(app()->getLocale() . '.over-ons') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans); border-bottom: 1px solid rgba(255,255,255,0.15);">{{ __('nav.over_ons') }}</a>
                <a href="{{ route(app()->getLocale() . '.contact') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans);">{{ __('nav.contact') }}</a>
                {{-- Mobile language toggle --}}
                <div class="flex items-center gap-2" style="padding: 1rem 0; border-top: 1px solid rgba(255,255,255,0.15);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                         style="color: white; opacity: 0.7; flex-shrink: 0;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                    <span style="color: white; font-weight: 600; font-size: 1.125rem; font-family: var(--font-sans);">{{ $currentLocaleLabel }}</span>
                    <span aria-hidden="true" style="color: white; opacity: 0.4; font-size: 0.875rem;">/</span>
                    <a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}"
                       style="color: white; opacity: 0.65; font-weight: 600; font-size: 1.125rem; font-family: var(--font-sans); text-decoration: underline;"
                       class="hover:opacity-90 transition-opacity">{{ $targetLocaleLabel }}</a>
                </div>
            </div>
        </div>
    </div>
</header>
