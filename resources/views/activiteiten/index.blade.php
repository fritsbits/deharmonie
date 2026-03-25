@extends('layouts.app')

@section('title', app()->getLocale() === 'fr' ? 'Activités' : 'Activiteiten')

@section('content')

{{-- HERO --}}
<section class="max-w-5xl mx-auto px-6 pt-12 pb-8">
    <div class="flex items-start justify-between gap-8">
        <div class="flex-1 max-w-xl">
            <h1 style="font-family: var(--font-sans); font-size: 3rem; font-weight: 800; line-height: 1.1; color: var(--color-brand-dark);" class="mb-2">
                Dienstencentrum<br>Restaurant Social
            </h1>
            <p class="text-lg font-semibold mb-6" style="color: var(--color-brand-orange)">Quartier Noordwijk</p>

            <div class="space-y-3 mb-8">
                <div class="flex items-start gap-3">
                    <img src="{{ asset('images/icon-check.svg') }}" alt="" class="w-6 h-6 mt-0.5 flex-shrink-0">
                    <p class="text-sm" style="color: var(--color-brand-dark)">
                        <strong>Activiteiten &amp; diensten</strong> in ons centrum en bij u thuis.<br>
                        <span style="color: var(--color-brand-muted)">Services &amp; activités chez nous et chez vous.</span>
                    </p>
                </div>
                <div class="flex items-start gap-3">
                    <img src="{{ asset('images/icon-check.svg') }}" alt="" class="w-6 h-6 mt-0.5 flex-shrink-0">
                    <p class="text-sm" style="color: var(--color-brand-dark)">
                        <strong>Dagschotels</strong> aan verminderd tarief voor senioren.<br>
                        <span style="color: var(--color-brand-muted)">Plat du jour à un tarif réduit pour les seniors.</span>
                    </p>
                </div>
                <div class="flex items-start gap-3">
                    <img src="{{ asset('images/icon-check.svg') }}" alt="" class="w-6 h-6 mt-0.5 flex-shrink-0">
                    <p class="text-sm" style="color: var(--color-brand-dark)">
                        <strong>Partner</strong> voor iedereen met een hart voor onze buurt.<br>
                        <span style="color: var(--color-brand-muted)">Partenaire pour tout le monde avec un cœur pour notre quartier.</span>
                    </p>
                </div>
            </div>

            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               class="inline-block text-sm font-bold px-6 py-3 rounded text-white"
               style="background-color: var(--color-brand-orange); font-family: var(--font-sans)">
               {{ app()->getLocale() === 'fr' ? 'Activités de la semaine' : 'Activiteiten de la Semaine' }}
            </a>
        </div>
        <div class="hidden lg:block flex-shrink-0">
            <img src="{{ asset('images/header-illustration.png') }}" alt="" class="w-72 h-auto">
        </div>
    </div>
</section>

{{-- AGENDA --}}
<section class="max-w-5xl mx-auto px-6 py-8">
    <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: var(--color-brand-orange)">
        AGENDA
    </p>
    <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 800; color: var(--color-brand-dark);" class="mb-6">
        {{ app()->getLocale() === 'fr' ? 'Activités à venir' : 'Volgende activiteiten' }}
    </h2>

    <livewire:activity-filter />

    <div class="mt-6 flex gap-3">
        <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
           class="text-sm font-semibold px-4 py-2 rounded"
           style="background-color: var(--color-brand-dark); color: white; font-family: var(--font-sans)">
           {{ app()->getLocale() === 'fr' ? 'Toutes les activités' : 'Alle activiteiten' }}
        </a>
        <a href="{{ route(app()->getLocale() . '.activiteiten.print', 'overzicht') }}"
           class="text-sm font-semibold px-4 py-2 rounded"
           style="border: 1px solid var(--color-brand-gray); color: var(--color-brand-dark); font-family: var(--font-sans)"
           onclick="window.print(); return false;">
           {{ app()->getLocale() === 'fr' ? 'Imprimer l\'aperçu' : 'Print overzicht' }}
        </a>
    </div>
</section>

{{-- PHOTOS --}}
<section class="max-w-5xl mx-auto px-6 py-4">
    <div class="grid md:grid-cols-2 gap-4">
        <img src="{{ asset('images/photo-visitors-1.jpg') }}" alt="Bezoekers aan De Harmonie" class="w-full h-56 object-cover rounded-lg">
        <img src="{{ asset('images/photo-visitors-2.jpg') }}" alt="Bezoekers aan De Harmonie" class="w-full h-56 object-cover rounded-lg">
    </div>
</section>

{{-- OPENING HOURS --}}
<section id="contact" class="max-w-5xl mx-auto px-6 py-10">
    <div class="grid md:grid-cols-2 gap-10">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: var(--color-brand-orange)">
                OPENINGSUREN
            </p>
            <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 800; color: var(--color-brand-dark);" class="mb-4">
                {{ app()->getLocale() === 'fr' ? 'Venez nous rendre visite' : 'Kom eens langs' }}
            </h2>
            <div class="space-y-2 text-sm mb-6" style="color: var(--color-brand-dark)">
                <div class="flex gap-3">
                    <span class="font-semibold w-16">Ma–Vr<br><span style="color: var(--color-brand-muted); font-weight: normal">Lun–Ven</span></span>
                    <span>10:00 – 16:30</span>
                </div>
                <div class="flex gap-3">
                    <span class="font-semibold w-16">Za<br><span style="color: var(--color-brand-muted); font-weight: normal">Sam</span></span>
                    <span>10:00 – 14:00</span>
                </div>
            </div>
            <p class="text-sm mb-1" style="color: var(--color-brand-muted)">{{ app()->getLocale() === 'fr' ? 'Vous pouvez nous rejoindre pour les activités et les repas. Nous proposons des plats du jour à tarif réduit pour les seniors.' : 'U kunt bij ons terecht voor activiteiten en maaltijden. We bieden dagschotels aan verminderd tarief voor senioren.' }}</p>
            <div class="mt-4 space-y-1">
                <p class="text-sm font-semibold" style="color: var(--color-brand-blue)">02/203.28.48</p>
                <p class="text-sm" style="color: var(--color-brand-blue)">
                    <a href="mailto:info@deharmonie.be" class="hover:underline">info@deharmonie.be</a>
                </p>
            </div>
        </div>
        <div>
            <div class="rounded-lg overflow-hidden h-64 md:h-full min-h-48" style="border: 1px solid var(--color-brand-gray)">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2519.5!2d4.3520!3d50.8520!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c3c38f0b0b0b0b%3A0x0!2zQW50d2VycHNlc3RlZW53ZWcgMjQ!5e0!3m2!1snl!2sbe!4v1234567890"
                    class="w-full h-full border-0"
                    allowfullscreen loading="lazy"
                    title="{{ app()->getLocale() === 'fr' ? 'Carte' : 'Kaart' }}">
                </iframe>
            </div>
        </div>
    </div>
</section>

@endsection
