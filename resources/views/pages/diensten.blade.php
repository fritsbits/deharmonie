@extends('layouts.app')
@section('title', __('pages.diensten_title'))
@section('content')

<x-page-hero
    :eyebrow="__('pages.diensten_eyebrow')"
    eyebrow-color="blue"
    :heading="__('pages.diensten_heading')"
    :lead="__('pages.diensten_lead')"
    bg="white"
    pb="1rem"
/>

{{-- SERVICE CLUSTERS --}}
<div style="background: #eef2f8;">
<div style="max-width: 72rem; margin: 0 auto; padding: 3.5rem 1.5rem 5rem;">

    @php
    $clusters = app()->getLocale() === 'fr' ? [
        [
            'label_top'  => 'Repas &',
            'label_main' => 'Activités',
            'color'      => '#eb6643',
            'icon'       => '<path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM9 7.5A.75.75 0 0 0 8.25 8v1.5a2.25 2.25 0 0 0 1.5 2.122v3.628a.75.75 0 0 0 1.5 0v-3.628A2.25 2.25 0 0 0 12.75 9.5V8A.75.75 0 0 0 12 7.5H9ZM15 7.5a.75.75 0 0 0-.75.75v7.5a.75.75 0 0 0 1.5 0V12.5h.75a.75.75 0 0 0 .75-.75V9a1.5 1.5 0 0 0-1.5-1.5H15Z" clip-rule="evenodd"/>',
            'items' => [
                'Restaurant social, plats à emporter et livraison à domicile',
                'Restauration et location pour les habitants et les organisations locales',
                'Services, activités et sorties pour les seniors — Créatif · Détente · Culturel · Formateur · Informatif · Sportif',
            ],
        ],
        [
            'label_top'  => 'Accompagnement &',
            'label_main' => 'Soutien',
            'color'      => '#81b59c',
            'icon'       => '<path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd"/>',
            'items' => [
                'Parcours dans la vie socioculturelle de Bruxelles — Service social',
                'Partenaire du réseau de soins primaires du quartier Nord',
                'Boutique de vêtements d\'occasion et retouches',
            ],
        ],
        [
            'label_top'  => 'À domicile &',
            'label_main' => 'Dans le quartier',
            'color'      => '#4679bc',
            'icon'       => '<path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z"/><path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z"/>',
            'items' => [
                'Service de courses et de transport',
                'Service de nettoyage et de bricolage',
                'Aide au Grand Nettoyage',
            ],
        ],
    ] : [
        [
            'label_top'  => 'Eten &',
            'label_main' => 'Activiteiten',
            'color'      => '#eb6643',
            'icon'       => '<path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM9 7.5A.75.75 0 0 0 8.25 8v1.5a2.25 2.25 0 0 0 1.5 2.122v3.628a.75.75 0 0 0 1.5 0v-3.628A2.25 2.25 0 0 0 12.75 9.5V8A.75.75 0 0 0 12 7.5H9ZM15 7.5a.75.75 0 0 0-.75.75v7.5a.75.75 0 0 0 1.5 0V12.5h.75a.75.75 0 0 0 .75-.75V9a1.5 1.5 0 0 0-1.5-1.5H15Z" clip-rule="evenodd"/>',
            'items' => [
                'Sociaal restaurant, afhaal en levering aan huis',
                'Catering & Verhuur voor buurtbewoners & -organisaties',
                'Diensten, Activiteiten en Uitstappen voor Senioren — Creatief · Ontspannend · Cultureel · Vormend · Informatief · Sportief',
            ],
        ],
        [
            'label_top'  => 'Begeleiding &',
            'label_main' => 'Ondersteuning',
            'color'      => '#81b59c',
            'icon'       => '<path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd"/>',
            'items' => [
                'Wegwijs in socio-cultureel Brussel — Sociale dienst',
                'Partner in het eerstelijnszorgnetwerk in de Noordwijk',
                'Tweedehands Klerenwinkel & Retouches',
            ],
        ],
        [
            'label_top'  => 'Thuis &',
            'label_main' => 'In de buurt',
            'color'      => '#4679bc',
            'icon'       => '<path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z"/><path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z"/>',
            'items' => [
                'Boodschappendienst & Vervoersdienst',
                'Klusjesdienst & Poetsdienst',
                'Hulp bij de Grote Kuis',
            ],
        ],
    ];
    @endphp

    <div class="service-cards" style="display: flex; gap: 1.5rem;">
        @foreach ($clusters as $cluster)
            <div style="flex: 1; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(44,40,38,.09), 0 8px 28px rgba(44,40,38,.10);">

                {{-- Coloured header --}}
                <div style="background: {{ $cluster['color'] }}; padding: 1.25rem 1.5rem 1.4rem; position: relative; overflow: hidden; min-height: 76px;">
                    {{-- icon is hardcoded SVG path data from this file's @php block — never user input --}}
                    <svg style="position: absolute; right: -14px; bottom: -18px; width: 110px; height: 110px; opacity: 0.22; transform: rotate(12deg); pointer-events: none;"
                         viewBox="0 0 24 24" fill="white" stroke="none">
                        {!! $cluster['icon'] !!}
                    </svg>
                    <p style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 900; color: white; margin: 0; position: relative; z-index: 1; line-height: 1.25;">
                        <span style="display: block;">{{ $cluster['label_top'] }}</span>
                        <span style="display: block;">{{ $cluster['label_main'] }}</span>
                    </p>
                </div>

                {{-- Card body --}}
                <div style="padding: 1.25rem 1.5rem 1.5rem;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach ($cluster['items'] as $item)
                            <li style="display: flex; gap: 0.6rem; align-items: baseline; padding: 0.65rem 0; {{ !$loop->last ? 'border-bottom: 1px solid rgba(44,40,38,.07);' : '' }}">
                                <span style="flex-shrink: 0; color: {{ $cluster['color'] }}; font-weight: 700;">&#10003;</span>
                                <span style="font-size: 1.0625rem; color: var(--color-brand-dark); line-height: 1.45;">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        @endforeach
    </div>

</div>

{{-- close blue-tint wrapper --}}
</div>

<div style="background: #eef2f8;">
{{-- CTA beneath services --}}
<div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem 5rem;">
    <div style="border-top: 1px solid #c8d4e4; padding-top: 2.5rem; display: flex; align-items: center; gap: 3rem; flex-wrap: wrap;">
        <p style="font-family: var(--font-sans); font-size: 1.25rem; font-weight: 700; color: var(--color-brand-dark); margin: 0; flex-shrink: 0;">
            {{ app()->getLocale() === 'fr' ? 'Des questions sur nos services ?' : 'Vragen over onze diensten?' }}
        </p>
        <a href="tel:0220328048"
           style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: none; flex-shrink: 0;">
            02 203 28 48
        </a>
        <a href="mailto:info@deharmonie.be"
           style="font-size: 1.0625rem; font-weight: 600; color: var(--color-brand-blue); text-decoration: underline; text-decoration-color: var(--color-brand-gray);">
            info@deharmonie.be
        </a>
    </div>
</div>

{{-- close blue-tint wrapper --}}
</div>

{{-- PHOTO STRIP --}}
<div style="display: flex; height: 380px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-vervoer.webp') }}"
             alt="{{ app()->getLocale() === 'fr' ? 'Service de transport' : 'Vervoersdienst' }}"
             style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center 40%;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-onthaal.webp') }}"
             alt="{{ app()->getLocale() === 'fr' ? 'Boutique de vêtements' : 'Tweedehands Klerenwinkel' }}"
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-keuken-chefs.webp') }}"
             alt="{{ app()->getLocale() === 'fr' ? 'Restaurant social' : 'Sociaal restaurant' }}"
             style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center center;">
    </div>
</div>

{{-- PROJECT GROTE KUIS --}}
<section style="background-color: var(--color-brand-bg-tint); padding: 5rem 1.5rem;">
    <div class="diensten-intro" style="max-width: 72rem; margin: 0 auto; display: flex; align-items: center; gap: 4rem;">

        <div style="flex: 1;">
            <x-eyebrow color="orange" mb="0.75rem">{{ __('pages.grote_kuis_eyebrow') }}</x-eyebrow>
            <h2 style="font-family: var(--font-sans); font-size: 2.25rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.1; margin-bottom: 1rem;">
                {{ __('pages.grote_kuis_title') }}
            </h2>
            <p style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-dark); margin-bottom: 1.25rem;">
                {{ __('pages.grote_kuis_description') }}
            </p>

            <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.75rem; font-family: var(--font-sans);">
                {{ __('pages.grote_kuis_examples_label') }}
            </p>
            <ul style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem 2rem; margin-bottom: 2rem; padding: 0; list-style: none;">
                @foreach (trans('pages.grote_kuis_examples') as $example)
                    <li style="font-size: 1rem; color: var(--color-brand-dark); display: flex; align-items: center; gap: 0.5rem;">
                        <span style="flex-shrink: 0; width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-brand-orange);"></span>
                        {{ $example }}
                    </li>
                @endforeach
            </ul>

            <p style="font-size: 1.0625rem; line-height: 1.7; color: var(--color-brand-dark); margin-bottom: 2rem;">
                {{ __('pages.grote_kuis_cta') }}
            </p>

            <div style="border-top: 1px solid var(--color-brand-gray-dark); padding-top: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="tel:0220328048"
                   style="font-size: 1.375rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: none; font-family: var(--font-sans);">
                    02 203 28 48
                </a>
                <a href="mailto:diensten@deharmonie.be"
                   style="font-size: 1.0625rem; font-weight: 600; color: var(--color-brand-blue); text-decoration: underline; text-decoration-color: var(--color-brand-gray);">
                    diensten@deharmonie.be
                </a>
            </div>
        </div>

        <div class="diensten-intro-photo" style="flex: 0 0 44%; overflow: hidden; aspect-ratio: 3/4;">
            <img src="{{ asset('images/grote-kuis.jpg') }}"
                 alt="{{ __('pages.grote_kuis_title') }}"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>

    </div>
</section>

<style>
.service-cards { align-items: stretch; }

@media (max-width: 767px) {
    .service-cards { flex-direction: column !important; }
    .diensten-intro { flex-direction: column !important; gap: 2rem !important; }
    .diensten-intro-photo { width: 100% !important; flex: none !important; aspect-ratio: 16/9 !important; }
}
</style>

@endsection
