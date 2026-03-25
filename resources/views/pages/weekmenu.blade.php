@extends('layouts.app')
@section('title', 'Weekmenu de la Semaine')
@section('content')

{{-- Kitchen photo strip — full width, top --}}
<div style="display: flex; height: 300px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-restaurant-bord.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-restaurant-bediening.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-chef-taart.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
</div>

<div style="max-width: 72rem; margin: 0 auto; padding: 4rem 1.5rem 4rem;">
    <div style="margin-bottom: 2.5rem;">
        <x-eyebrow color="orange" mb="0.75rem">SOCIAAL RESTAURANT · RESTAURANT SOCIAL</x-eyebrow>
        <h1 style="font-family: var(--font-sans); font-size: 2.75rem; font-weight: 900; line-height: 1.1; color: var(--color-brand-dark); margin: 0;">
            Weekmenu de la Semaine
        </h1>
    </div>

    @php
        $dag = ucfirst(now()->locale(app()->getLocale())->isoFormat('dddd'));
    @endphp

    {{-- Two-column: menu left (8), info right (4) --}}
    <div class="weekmenu-cols" style="display: flex; align-items: flex-start; gap: 3rem;">

        {{-- Left: iframe --}}
        <div style="flex: 2; min-width: 0;">
            <div style="overflow: hidden; box-shadow: 0 2px 16px rgba(44,40,38,0.08); height: 860px;">
                <iframe
                    src="https://docs.google.com/document/d/1QW8cVxFS-ew1TWO5Czk3WXGn567ryRC92C1oluGWX4c/preview"
                    style="width: 100%; height: 100%; border: 0; display: block;"
                    title="Weekmenu de la Semaine">
                </iframe>
            </div>
            <p style="margin-top: 0.75rem; font-size: 0.9rem; color: var(--color-brand-muted);">
                <a href="https://docs.google.com/document/d/1QW8cVxFS-ew1TWO5Czk3WXGn567ryRC92C1oluGWX4c/edit?tab=t.0"
                   target="_blank"
                   rel="noopener"
                   style="color: var(--color-brand-blue); text-decoration: underline; text-decoration-color: var(--color-brand-gray);">
                    {{ app()->getLocale() === 'fr' ? 'Ouvrir le menu dans un nouvel onglet →' : 'Menu openen in nieuw venster →' }}
                </a>
            </p>
        </div>

        {{-- Right: today + ordering + hours --}}
        <div style="flex: 1; min-width: 220px; padding-top: 0.25rem;">

            {{-- Today --}}
            <div style="margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--color-brand-gray);">
                <p style="font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.35rem;">
                    {{ app()->getLocale() === 'fr' ? "Aujourd'hui" : 'Vandaag' }}
                </p>
                <p style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 900; color: var(--color-brand-dark); margin: 0;">
                    {{ $dag }}
                </p>
            </div>

            {{-- Ordering --}}
            <div style="margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--color-brand-gray);">
                <p style="font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.75rem;">
                    {{ app()->getLocale() === 'fr' ? 'Emporter & livraison' : 'Afhaal & levering' }}
                </p>
                <p style="font-size: 1rem; line-height: 1.6; color: var(--color-brand-dark); margin-bottom: 1rem;">
                    {{ app()->getLocale() === 'fr'
                        ? 'Emportez votre repas ou faites-le livrer à domicile. Appelez avant midi.'
                        : 'Afhalen of aan huis laten leveren. Bel ons voor de middag.' }}
                </p>
                <a href="tel:0220328048"
                   style="font-family: var(--font-sans); font-size: 1.25rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: none; display: block; margin-bottom: 0.3rem;">
                    02 203 28 48
                </a>
                <a href="mailto:info@deharmonie.be"
                   style="font-size: 0.9375rem; font-weight: 600; color: var(--color-brand-blue); text-decoration: underline; text-decoration-color: var(--color-brand-gray);">
                    info@deharmonie.be
                </a>
            </div>

            {{-- Hours --}}
            <div>
                <p style="font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.75rem;">
                    {{ app()->getLocale() === 'fr' ? 'Heures d\'ouverture' : 'Openingsuren' }}
                </p>
                <p style="font-size: 1rem; line-height: 1.9; color: var(--color-brand-dark); margin: 0;">
                    {{ app()->getLocale() === 'fr' ? 'Lundi – vendredi' : 'Maandag – vrijdag' }}<br>
                    <span style="color: var(--color-brand-muted);">10u – 16u30</span><br>
                    {{ app()->getLocale() === 'fr' ? 'Samedi' : 'Zaterdag' }}<br>
                    <span style="color: var(--color-brand-muted);">10u – 14u</span>
                </p>
            </div>

        </div>
    </div>

</div>


<style>
@media (max-width: 767px) {
    .weekmenu-cols { flex-direction: column !important; }
}
</style>

@endsection
