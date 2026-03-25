<footer style="background-color: #1a3a5c; color: white;">
    <div class="max-w-5xl mx-auto px-6 py-12 grid md:grid-cols-3 gap-8">
        <div>
            <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" class="h-10 w-auto mb-4 brightness-0 invert">
            <p class="text-sm opacity-80 leading-relaxed">
                VZW Buurtwerk Noordwijk<br>
                Antwerpsesteenweg 24<br>
                1000 Brussel
            </p>
            <div class="mt-4 space-y-1 text-sm opacity-80">
                <p><a href="tel:0220328048" class="hover:underline">02/203.28.48</a></p>
                <p><a href="mailto:info@deharmonie.be" class="hover:underline">info@deharmonie.be</a></p>
            </div>
        </div>
        <div>
            <h3 style="font-family: var(--font-sans);" class="font-bold text-lg mb-4">
                {{ __('common.opening_hours') }}
            </h3>
            <div class="text-sm opacity-80 space-y-1">
                <p>{{ __('common.mon_fri') }}: 10:00–16:30</p>
                <p>{{ __('common.sat') }}: 10:00–14:00</p>
            </div>
            <div class="mt-6">
                <h3 style="font-family: var(--font-sans);" class="font-bold text-lg mb-3">Links</h3>
                <div class="text-sm opacity-80 space-y-1">
                    <p><a href="{{ route(app()->getLocale() . '.diensten') }}" class="hover:underline">{{ __('nav.services') }}</a></p>
                    <p><a href="{{ route(app()->getLocale() . '.contact') }}" class="hover:underline">Contact</a></p>
                </div>
            </div>
        </div>
        <div>
            <h3 style="font-family: var(--font-sans);" class="font-bold text-lg mb-4">
                {{ __('common.follow_us') }}
            </h3>
            <a href="https://www.facebook.com/DeHarmonieBrussel" target="_blank" class="flex items-center gap-2 text-sm opacity-80 hover:opacity-100">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                {{ __('common.follow_facebook') }}
            </a>
            <div class="mt-8">
                <p class="text-xs opacity-50 mb-3">{{ __('common.supported_by') }}</p>
                <div class="flex flex-wrap gap-4 items-center opacity-60">
                    <span class="text-xs font-bold">Vlaanderen</span>
                    <span class="text-xs font-bold">VGC</span>
                    <span class="text-xs font-bold">Brussels</span>
                </div>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10 text-center py-4 text-xs opacity-40">
        &copy; {{ date('Y') }} VZW Buurtwerk Noordwijk
    </div>
</footer>
