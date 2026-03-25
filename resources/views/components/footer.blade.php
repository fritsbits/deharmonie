<footer style="background-color: var(--color-brand-blue); color: white;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 3rem 1.5rem; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2.5rem;">

        {{-- Left: VZW info + links --}}
        <div>
            <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" style="height: 2rem; width: auto; margin-bottom: 1.25rem; filter: brightness(0) invert(1);">
            <p style="font-size: 1rem; line-height: 1.6; opacity: 0.8; margin-bottom: 0.5rem;">
                VZW Buurtwerk Noordwijk<br>
                Antwerpsesteenweg 24<br>
                1000 Brussel
            </p>
            <p style="font-size: 1rem; opacity: 0.8; margin-bottom: 0.25rem;">
                <a href="tel:0220328048" style="color: white; text-decoration: none;">02 203 28 48</a>
            </p>
            <p style="font-size: 1rem; opacity: 0.8; margin-bottom: 1.5rem;">
                <a href="mailto:info@deharmonie.be" style="color: white; text-decoration: none;">info@deharmonie.be</a>
            </p>
            <p style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.6; margin-bottom: 0.5rem;">
                Snel naar
            </p>
            <p style="font-size: 1rem; opacity: 0.75; margin-bottom: 0.2rem;">
                <a href="{{ route(app()->getLocale() . '.diensten') }}" style="color: white; text-decoration: none;">Diensten</a>
            </p>
            <p style="font-size: 1rem; opacity: 0.75;">
                <a href="{{ route(app()->getLocale() . '.wie-is-wie') }}" style="color: white; text-decoration: none;">Wie is wie</a>
            </p>
        </div>

        {{-- Center: Met steun van --}}
        <div>
            <p style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.6; margin-bottom: 1rem;">
                Met steun van
            </p>
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <a href="https://www.vlaanderen.be/" target="_blank" rel="noopener">
                    <img src="{{ asset('images/logo-vlaanderen.svg') }}" alt="Vlaanderen" style="height: 3.75rem; width: auto;">
                </a>
                <a href="https://regiefonciere.bruxelles.be/nl" target="_blank" rel="noopener">
                    <img src="{{ asset('images/logo-nbrussel.png') }}" alt="Brussel" style="height: 3.125rem; width: auto;">
                </a>
                <a href="https://regiefonciere.bruxelles.be/nl" target="_blank" rel="noopener">
                    <img src="{{ asset('images/logo-grondregie.png') }}" alt="Grondregie Brussel" style="height: 2.8rem; width: auto;">
                </a>
                <a href="https://be.brussels/" target="_blank" rel="noopener">
                    <img src="{{ asset('images/logo-bhg.svg') }}" alt="Brussels Hoofdstedelijk Gewest" style="height: 2.5rem; width: auto; filter: brightness(0) invert(1); opacity: 0.85;">
                </a>
            </div>
        </div>

        {{-- Right: social --}}
        <div>
            <p style="font-size: 1rem; opacity: 0.8; margin-bottom: 1rem;">
                Volg De Harmonie op Facebook
            </p>
            <a href="https://www.facebook.com/deharmoniebrussel/" target="_blank" rel="noopener">
                <img src="{{ asset('images/logo-facebook.png') }}" alt="Facebook" style="width: 2.5rem; height: 2.5rem; opacity: 0.9;">
            </a>
        </div>

    </div>
    <div style="border-top: 1px solid rgba(255,255,255,0.25); text-align: center; padding: 0.75rem; opacity: 0.8;">
        &copy; {{ date('Y') }} VZW Buurtwerk Noordwijk
    </div>
</footer>
