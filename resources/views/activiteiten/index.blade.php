@extends('layouts.app')

@section('title', __('pages.home_title'))

@section('content')

{{-- HERO: text only --}}
<section style="background-color: white; border-bottom: 1px solid #ebe8e5;">
    <div style="max-width: 64rem; margin: 0 auto; padding: 3rem 1.5rem 2.5rem;">
        <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-green); margin-bottom: 0.4rem;">
            Noordwijk · Brussel
        </p>
        <h1 style="font-family: var(--font-sans); font-size: 2.8rem; font-weight: 900; line-height: 1.05; color: var(--color-brand-dark); margin-bottom: 0.35rem;">
            Dienstencentrum<br>Restaurant Social
        </h1>
        <h2 style="font-family: var(--font-sans); font-size: 1.6rem; font-weight: 900; color: var(--color-brand-green); line-height: 1.2;">
            Quartier Noordwijk
        </h2>
    </div>
</section>

{{-- SECTION 1: Restaurant — photo left, text right --}}
<section style="border-top: 1px solid rgba(216,211,210,0.5);">
    <div style="display: flex; min-height: 320px;">
        <div style="flex: 0 0 42%; overflow: hidden; position: relative;">
            <img src="{{ asset('images/photo-restaurant.jpg') }}" alt="Sociaal restaurant"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>
        <div style="flex: 1; padding: 2rem; display: flex; flex-direction: column; background: white;">
            <p style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-orange); margin-bottom: 0.5rem;">
                Sociaal restaurant · Restaurant social
            </p>
            <h2 style="font-family: var(--font-sans); font-size: 1.35rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.2; margin-bottom: 0.6rem;">
                Elke dag samen aan tafel
            </h2>
            <p style="font-size: 0.95rem; line-height: 1.55; color: var(--color-brand-dark); margin-bottom: 0.35rem;">
                <strong>Dagschotels</strong> aan verminderd tarief voor senioren. Afhaal en levering aan huis mogelijk.
            </p>
            <p style="font-size: 0.88rem; line-height: 1.5; color: var(--color-brand-muted); font-style: italic; margin-bottom: 1.25rem;">
                Plat du jour à un tarif réduit pour les seniors. Emporter et livraison à domicile.
            </p>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               style="display: inline-flex; align-items: center; gap: 0.4rem; background: var(--color-brand-blue); color: white; font-family: var(--font-sans); font-weight: 700; font-size: 0.85rem; padding: 0.5rem 1.1rem; border-radius: 5px; text-decoration: none; align-self: flex-start; margin-top: auto;">
                Weekmenu de la Semaine →
            </a>
        </div>
    </div>
</section>

{{-- SECTION 2: Activities — carousel right, content+list left --}}
<section style="border-top: 1px solid rgba(216,211,210,0.5);">
    <div style="display: flex; flex-direction: row-reverse; min-height: 320px;">

        {{-- Photo carousel (right) --}}
        @php $carouselPhotos = ['photo-party.jpg', 'photo-cake.jpg', 'photo-thumbsup.jpg']; @endphp
        <div x-data="{ current: 0 }"
             style="flex: 0 0 42%; position: relative; overflow: hidden; min-height: 320px;">
            @foreach ($carouselPhotos as $idx => $photo)
                <img src="{{ asset('images/' . $photo) }}"
                     alt="Activiteiten"
                     x-show="current === {{ $idx }}"
                     style="width: 100%; height: 100%; object-fit: cover; display: block; position: absolute; inset: 0;">
            @endforeach

            {{-- Prev arrow --}}
            <button @click="current = (current - 1 + {{ count($carouselPhotos) }}) % {{ count($carouselPhotos) }}"
                    style="position: absolute; left: 0.6rem; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.8); color: var(--color-brand-dark); width: 28px; height: 28px; border-radius: 50%; border: none; cursor: pointer; font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; justify-content: center; z-index: 2;">
                ‹
            </button>
            {{-- Next arrow --}}
            <button @click="current = (current + 1) % {{ count($carouselPhotos) }}"
                    style="position: absolute; right: 0.6rem; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.8); color: var(--color-brand-dark); width: 28px; height: 28px; border-radius: 50%; border: none; cursor: pointer; font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; justify-content: center; z-index: 2;">
                ›
            </button>
            {{-- Dots --}}
            <div style="position: absolute; bottom: 0.75rem; left: 0; right: 0; display: flex; justify-content: center; gap: 0.4rem; z-index: 2;">
                @foreach ($carouselPhotos as $idx => $photo)
                    <span @click="current = {{ $idx }}"
                          :style="current === {{ $idx }} ? 'opacity:1' : 'opacity:0.5'"
                          style="width: 7px; height: 7px; border-radius: 50%; background: white; display: block; cursor: pointer;"></span>
                @endforeach
            </div>
        </div>

        {{-- Header + live activity list (left) --}}
        <div style="flex: 1; display: flex; flex-direction: column; background: #f5f2ef;">
            <div style="padding: 2rem 2rem 1rem;">
                <p style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-green); margin-bottom: 0.5rem;">
                    Activiteiten · Activités
                </p>
                <h2 style="font-family: var(--font-sans); font-size: 1.35rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.2; margin-bottom: 0.5rem;">
                    Creatief, cultureel en sportief
                </h2>
                <p style="font-size: 0.95rem; line-height: 1.55; color: var(--color-brand-dark); margin-bottom: 0.2rem;">
                    <strong>Activiteiten &amp; diensten</strong> in ons centrum en bij u thuis.
                </p>
                <p style="font-size: 0.88rem; line-height: 1.5; color: var(--color-brand-muted); font-style: italic;">
                    Des activités dans notre centre et chez vous. Créatif, culturel, formateur.
                </p>
            </div>
            <div style="padding: 0 2rem; flex: 1;">
                @livewire('activity-filter')
            </div>
        </div>

    </div>
</section>

{{-- SECTION 3: Services — photo left, text right --}}
<section style="border-top: 1px solid rgba(216,211,210,0.5);">
    <div style="display: flex; min-height: 320px;">
        <div style="flex: 0 0 42%; overflow: hidden; position: relative;">
            <img src="{{ asset('images/photo-samen.jpg') }}" alt="Diensten"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>
        <div style="flex: 1; padding: 2rem; display: flex; flex-direction: column; background: #f0efed;">
            <p style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-blue); margin-bottom: 0.5rem;">
                Diensten · Services
            </p>
            <h2 style="font-family: var(--font-sans); font-size: 1.35rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.2; margin-bottom: 0.6rem;">
                Ook hulp waar u het nodig heeft
            </h2>
            <p style="font-size: 0.95rem; line-height: 1.55; color: var(--color-brand-dark); margin-bottom: 0.35rem;">
                <strong>Partner</strong> voor iedereen met een hart voor onze buurt. Boodschappen, vervoer, poetswerk en meer.
            </p>
            <p style="font-size: 0.88rem; line-height: 1.5; color: var(--color-brand-muted); font-style: italic; margin-bottom: 1.25rem;">
                Partenaire pour tout le monde. Courses, transport, nettoyage et petites réparations.
            </p>
            <a href="{{ route(app()->getLocale() . '.diensten') }}"
               style="display: inline-flex; align-items: center; gap: 0.4rem; background: var(--color-brand-orange); color: white; font-family: var(--font-sans); font-weight: 700; font-size: 0.85rem; padding: 0.5rem 1.1rem; border-radius: 5px; text-decoration: none; align-self: flex-start; margin-top: auto;">
                Onze diensten →
            </a>
        </div>
    </div>
</section>

{{-- OPENING HOURS — unchanged --}}
<section id="contact" style="background-color: white; position: relative; overflow: hidden;">
    <img src="{{ asset('images/header-illustration.png') }}"
         id="opening-hours-illustration"
         alt=""
         style="position: absolute; right: 0; top: 0; height: 100%; width: auto; pointer-events: none; user-select: none;">
    <div class="max-w-5xl mx-auto" style="position: relative; z-index: 1; padding: 4rem 1.5rem;">
        <div style="max-width: 36rem;">
            <p style="color: var(--color-brand-green); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.15rem; font-family: var(--font-sans); letter-spacing: 0.06em; text-transform: uppercase;">
                OPENINGSUREN
            </p>
            <h2 style="font-family: var(--font-sans); font-size: 2.25rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.75rem;">
                {{ __('activities.visit_us') }}
            </h2>
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 1rem; font-size: 1.125rem; color: var(--color-brand-dark); font-weight: 600;">
                    <img src="{{ asset('images/icon-clock.svg') }}" alt="" style="width: 26px; height: 26px; flex-shrink: 0;">
                    10u – 16u30, maandag tot vrijdag
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; font-size: 1.125rem; color: var(--color-brand-dark); font-weight: 600;">
                    <img src="{{ asset('images/icon-clock.svg') }}" alt="" style="width: 26px; height: 26px; flex-shrink: 0;">
                    10u – 14u, zaterdag
                </div>
            </div>
            <p style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-muted); margin-bottom: 2rem;">
                Kom voor een lekker maaltijd of voor de activiteiten en uitstappen. We geven je graag ook meer info over diensten zoals vervoer, poetsdienst (ook ruilen wassen), boodschappen, kleine herstellingen, wassen en strijken en maaltijden aan huis.
            </p>
            <p style="margin-bottom: 0.4rem;">
                <a href="tel:0220328048" style="font-size: 1.25rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: none;">
                    02/203.28.48
                </a>
            </p>
            <p style="font-size: 1.125rem; color: var(--color-brand-blue);">
                <a href="mailto:info@deharmonie.be" style="text-decoration: none; color: inherit;">info@deharmonie.be</a>
            </p>
        </div>
    </div>
</section>

<style>
@media (max-width: 1023px) {
    #opening-hours-illustration { display: none; }
}
</style>

@endsection
