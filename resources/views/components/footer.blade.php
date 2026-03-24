<footer class="text-white mt-12" style="background-color: var(--color-brand-green)">
    <div class="max-w-5xl mx-auto px-4 py-8 grid md:grid-cols-3 gap-6 text-sm">
        <div>
            <h3 class="font-sans font-bold text-lg mb-2">De Harmonie</h3>
            <p>VZW Buurtwerk Noordwijk</p>
            <p>Antwerpsesteenweg 24</p>
            <p>1000 Brussel</p>
        </div>
        <div>
            <h3 class="font-sans font-bold text-lg mb-2">
                {{ app()->getLocale() === 'fr' ? 'Heures d\'ouverture' : 'Openingsuren' }}
            </h3>
            <p>{{ app()->getLocale() === 'fr' ? 'Lun–Ven' : 'Ma–Vr' }}: 9:30–16:30</p>
            <p>{{ app()->getLocale() === 'fr' ? 'Sam' : 'Za' }}: 10:00–14:00</p>
        </div>
        <div>
            <h3 class="font-sans font-bold text-lg mb-2">Contact</h3>
            <p><a href="tel:0220328048" class="underline">02 203 28 48</a></p>
            <p><a href="mailto:info@deharmonie.be" class="underline">info@deharmonie.be</a></p>
        </div>
    </div>
    <div class="border-t border-white/20 text-center py-3 text-xs opacity-70">
        © {{ date('Y') }} VZW Buurtwerk Noordwijk
    </div>
</footer>
