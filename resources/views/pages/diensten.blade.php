@extends('layouts.app')
@section('title', app()->getLocale() === 'fr' ? 'Services' : 'Diensten')
@section('content')

{{-- INTRO: lead text left, photo right --}}
<div style="max-width: 72rem; margin: 0 auto; padding: 5rem 1.5rem 4rem;">
    <div class="diensten-intro" style="display: flex; align-items: center; gap: 4rem;">

        <div style="flex: 1;">
            <x-eyebrow color="orange" mb="0.75rem">{{ app()->getLocale() === 'fr' ? 'SERVICES' : 'DIENSTEN' }}</x-eyebrow>
            <h1 style="font-family: var(--font-sans); font-size: 3rem; font-weight: 900; line-height: 1.1; color: var(--color-brand-dark); margin-bottom: 1.25rem;">
                {{ app()->getLocale() === 'fr' ? 'De Harmonie est là pour vous' : 'De Harmonie is er voor jou' }}
            </h1>
            <p style="font-size: 2rem; font-weight: 300; line-height: 1.35; color: var(--color-brand-muted); max-width: 38rem;">
                {{ app()->getLocale() === 'fr'
                    ? 'De Harmonie aide les seniors du quartier Noordwijk dans leur vie quotidienne. Nous organisons des activités et des services dans notre propre centre, dans le quartier, mais aussi chez les personnes à domicile.'
                    : 'De Harmonie helpt senioren uit de Noordwijk in het dagelijks leven. We organiseren activiteiten en diensten in ons eigen centrum, in de buurt, maar ook bij mensen thuis.' }}
            </p>
        </div>

        <div class="diensten-intro-photo" style="flex: 0 0 44%; overflow: hidden; aspect-ratio: 4/3;">
            <img src="{{ asset('images/photo-begeleiding-klas.webp') }}"
                 alt="{{ app()->getLocale() === 'fr' ? 'Accompagnement personnalisé' : 'Persoonlijke begeleiding' }}"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>

    </div>
</div>

{{-- SERVICE CLUSTERS --}}
<div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem 5rem;">

    @php
    $clusters = app()->getLocale() === 'fr' ? [
        [
            'label' => 'Repas & activités',
            'items' => [
                'Restaurant social, plats à emporter et livraison à domicile',
                'Restauration et location pour les habitants et les organisations locales',
                'Services, activités et sorties pour les seniors — Créatif · Détente · Culturel · Formateur · Informatif · Sportif',
            ],
        ],
        [
            'label' => 'Accompagnement & soutien',
            'items' => [
                'Parcours dans la vie socioculturelle de Bruxelles — Service social',
                'Partenaire du réseau de soins primaires du quartier Nord',
                'Boutique de vêtements d\'occasion et retouches',
            ],
        ],
        [
            'label' => 'À domicile & dans le quartier',
            'items' => [
                'Service de courses et de transport',
                'Service de nettoyage et de bricolage',
                'Aide au Grand Nettoyage',
            ],
        ],
    ] : [
        [
            'label' => 'Eten & activiteiten',
            'items' => [
                'Sociaal restaurant, afhaal en levering aan huis',
                'Catering & Verhuur voor buurtbewoners & -organisaties',
                'Diensten, Activiteiten en Uitstappen voor Senioren — Creatief · Ontspannend · Cultureel · Vormend · Informatief · Sportief',
            ],
        ],
        [
            'label' => 'Begeleiding & ondersteuning',
            'items' => [
                'Wegwijs in socio-cultureel Brussel — Sociale dienst',
                'Partner in het eerstelijnszorgnetwerk in de Noordwijk',
                'Tweedehands Klerenwinkel & Retouches',
            ],
        ],
        [
            'label' => 'Thuis & in de buurt',
            'items' => [
                'Boodschappendienst & Vervoersdienst',
                'Klusjesdienst & Poetsdienst',
                'Hulp bij de Grote Kuis',
            ],
        ],
    ];
    @endphp

    <div style="display: flex; gap: 3rem; flex-wrap: wrap;">
        @foreach ($clusters as $cluster)
            <div style="flex: 1; min-width: 220px;">
                <p style="font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-blue); margin-bottom: 0.5rem;">
                    {{ $cluster['label'] }}
                </p>
                <ul style="border-top: 1px solid var(--color-brand-gray); margin: 0; padding: 0;">
                    @foreach ($cluster['items'] as $item)
                        <li style="display: flex; align-items: baseline; gap: 0.75rem; padding: 0.85rem 0; border-bottom: 1px solid var(--color-brand-gray); list-style: none;">
                            <span style="flex-shrink: 0; color: var(--color-brand-orange); font-weight: 700; font-size: 1.1rem; line-height: 1;">&#10003;</span>
                            <span style="font-size: 1.0625rem; color: var(--color-brand-dark); line-height: 1.5;">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

</div>

{{-- CTA beneath services --}}
<div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem 5rem;">
    <div style="border-top: 1px solid var(--color-brand-gray); padding-top: 2.5rem; display: flex; align-items: center; gap: 3rem; flex-wrap: wrap;">
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
            <x-eyebrow color="orange" mb="0.75rem">{{ app()->getLocale() === 'fr' ? 'PROJECT' : 'PROJECT' }}</x-eyebrow>
            <h2 style="font-family: var(--font-sans); font-size: 2.25rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.1; margin-bottom: 1rem;">
                {{ app()->getLocale() === 'fr' ? 'Aide au Grand Nettoyage' : 'Hulp bij de Grote Kuis' }}
            </h2>
            <p style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-dark); margin-bottom: 1.25rem;">
                {{ app()->getLocale() === 'fr'
                    ? 'Avec ce projet, nous voulons vous aider avec le \'Grand Nettoyage\'. Avec nos agents de nettoyage et bricoleurs, nous prenons en charge votre domicile. Nous pouvons effectuer de petits travaux ou réparations, donner un nettoyage complet à toutes vos affaires et vous aider avec votre administration.'
                    : 'Met dit project willen we je helpen met de \'Grote Kuis\'. Samen met onze poetsers en klussers nemen we je woning onder handen. We kunnen kleine werken of herstellingen doen, we geven alle spullen een grondige poetsbeurt en we kunnen je ook helpen met je administratie.' }}
            </p>

            <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.75rem; font-family: var(--font-sans);">
                {{ app()->getLocale() === 'fr' ? 'Exemples' : 'Waarbij kan je bijvoorbeeld hulp krijgen?' }}
            </p>
            <ul style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem 2rem; margin-bottom: 2rem; padding: 0; list-style: none;">
                @php
                $examples = app()->getLocale() === 'fr'
                    ? ['Nettoyer le four', 'Réparer un robinet', 'Laver les vitres', 'Nettoyer le tapis', 'Installer une hotte', 'Laver les rideaux', 'Peindre les toilettes']
                    : ['Oven kuisen', 'Kraantje repareren', 'Ruiten wassen', 'Tapijt kuisen', 'Dampkap installeren', 'Gordijnen wassen', 'Toilet schilderen'];
                @endphp
                @foreach ($examples as $example)
                    <li style="font-size: 1rem; color: var(--color-brand-dark); display: flex; align-items: center; gap: 0.5rem;">
                        <span style="flex-shrink: 0; width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-brand-orange);"></span>
                        {{ $example }}
                    </li>
                @endforeach
            </ul>

            <p style="font-size: 1.0625rem; line-height: 1.7; color: var(--color-brand-dark); margin-bottom: 2rem;">
                {{ app()->getLocale() === 'fr'
                    ? 'Vous êtes intéressé(e) ou vous connaissez quelqu\'un qui pourrait l\'être ? Faites-le nous savoir !'
                    : 'Heb je interesse of ken je iemand die hiervoor interesse heeft? Laat het ons zeker weten!' }}
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
                 alt="Project Grote Kuis"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>

    </div>
</section>

<style>
@media (max-width: 767px) {
    .diensten-intro { flex-direction: column !important; gap: 2rem !important; }
    .diensten-intro-photo { width: 100% !important; flex: none !important; aspect-ratio: 16/9 !important; }
}
</style>

@endsection
