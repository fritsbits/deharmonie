<style>
@media (max-width: 767px) {
    .footer-inner { grid-template-columns: 1fr !important; gap: 2rem !important; }
    .footer-nav-cols { grid-template-columns: 1fr 1fr !important; }
    .footer-partners { gap: 1.5rem !important; flex-wrap: wrap !important; }
    .footer-bottom { flex-direction: column !important; gap: 0.5rem !important; align-items: flex-start !important; }
}
</style>

<footer style="background-color: var(--color-brand-blue); color: white; font-family: var(--font-body);">

    {{-- Main grid: identity left, nav right --}}
    <div class="footer-inner" style="max-width: 72rem; margin: 0 auto; padding: 3.5rem 1.5rem 2.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">

        {{-- Left: logo, tagline, contact, social --}}
        <div>
            <img src="{{ asset('images/logo.png') }}" alt="De Harmonie"
                 style="display: block; height: 2rem; width: auto; filter: brightness(0) invert(1); margin-bottom: 1rem;">

            <p style="font-size: 1rem; opacity: 0.65; margin-bottom: 1.75rem; line-height: 1.5;">
                {{ __('common.site_tagline') }}
            </p>

            <div style="display: flex; flex-direction: column; gap: 0.3rem; margin-bottom: 1.75rem;">
                <a href="tel:0220328048"
                   style="font-size: 1rem; color: white; opacity: 0.85; text-decoration: underline; text-underline-offset: 3px;">02 203 28 48</a>
                <a href="mailto:info@deharmonie.be"
                   style="font-size: 1rem; color: white; opacity: 0.85; text-decoration: underline; text-underline-offset: 3px;">info@deharmonie.be</a>
            </div>

            <a href="https://www.facebook.com/deharmoniebrussel/" target="_blank" rel="noopener"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 1rem; border-radius: 999px; background: rgba(255,255,255,0.12); color: white; text-decoration: none; font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; transition: background 0.15s;"
               onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                </svg>
                Facebook
            </a>
        </div>

        {{-- Right: navigation --}}
        <div>
            <p style="font-family: var(--font-sans); font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.5; margin-bottom: 1rem;">
                {{ __('common.quick_links') }}
            </p>
            <div class="footer-nav-cols" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem 1.5rem;">
                @foreach ([
                    ['route' => app()->getLocale() . '.activiteiten.index', 'label' => __('nav.activities')],
                    ['route' => app()->getLocale() . '.over-ons',           'label' => __('nav.over_ons')],
                    ['route' => app()->getLocale() . '.weekmenu',           'label' => __('nav.restaurant_menu')],
                    ['route' => app()->getLocale() . '.wie-is-wie',         'label' => __('nav.wie_is_wie')],
                    ['route' => app()->getLocale() . '.diensten',           'label' => __('nav.services')],
                    ['route' => app()->getLocale() . '.contact',            'label' => __('nav.contact')],
                ] as $link)
                    <a href="{{ route($link['route']) }}"
                       style="font-family: var(--font-sans); font-size: 1rem; font-weight: 700; color: white; text-decoration: none; opacity: 0.85; padding: 0.15rem 0; transition: opacity 0.15s;"
                       onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">{{ $link['label'] }}</a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Partner logos strip + bottom bar (shared darker bg) --}}
    <div style="background: rgba(0,0,0,0.1);">
        <div class="footer-partners" style="max-width: 72rem; margin: 0 auto; padding: 1.5rem 1.5rem; display: flex; align-items: center; gap: 2.5rem;">
            <span style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.45; white-space: nowrap;">
                {{ __('common.supported_by') }}
            </span>
            <a href="https://www.vlaanderen.be/" target="_blank" rel="noopener">
                <img src="{{ asset('images/logo-vlaanderen.svg') }}" alt="Vlaanderen"
                     style="height: 2.75rem; width: auto; filter: brightness(0) invert(1); opacity: 0.65;">
            </a>
            <a href="https://regiefonciere.bruxelles.be/nl" target="_blank" rel="noopener">
                <img src="{{ asset('images/logo-nbrussel.png') }}" alt="Brussel"
                     style="height: 2.4rem; width: auto; filter: brightness(0) invert(1); opacity: 0.65;">
            </a>
            <a href="https://regiefonciere.bruxelles.be/nl" target="_blank" rel="noopener">
                <img src="{{ asset('images/logo-grondregie.png') }}" alt="Grondregie Brussel"
                     style="height: 2.2rem; width: auto; opacity: 0.65;">
            </a>
            <a href="https://be.brussels/" target="_blank" rel="noopener">
                <img src="{{ asset('images/logo-bhg.svg') }}" alt="Brussels Hoofdstedelijk Gewest"
                     style="height: 2rem; width: auto; filter: brightness(0) invert(1); opacity: 0.65;">
            </a>
        </div>

        <div class="footer-bottom" style="max-width: 72rem; margin: 0 auto; padding: 0.85rem 1.5rem; display: flex; justify-content: space-between; align-items: center; font-family: var(--font-sans); font-size: 0.8rem; border-top: 1px solid rgba(255,255,255,0.08);">
            <span style="opacity: 0.45;">&copy; {{ date('Y') }} VZW Buurtwerk Noordwijk</span>
            <span style="opacity: 0.85;">
                {{ app()->getLocale() === 'fr' ? 'Site web par' : 'Website door' }}
                <a href="https://frederikvincx.com" target="_blank" rel="noopener"
                   style="color: white; font-weight: 700; text-decoration: underline; text-underline-offset: 2px;">Impact Studio</a>
            </span>
        </div>
    </div>

</footer>
