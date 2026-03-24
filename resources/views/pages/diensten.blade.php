@extends('layouts.app')
@section('title', __('nav.services'))
@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="font-sans font-extrabold text-3xl mb-8" style="color: var(--color-brand-dark)">
        {{ __('nav.services') }}
    </h1>
    <div class="grid md:grid-cols-2 gap-6">
        @foreach ([
            [
                'icon' => '🤝',
                'nl' => 'Sociale begeleiding',
                'fr' => 'Accompagnement social',
                'desc_nl' => 'Navigatie in het Brussels socioculturele landschap. Integratie in het eerstelijnsnetwerk.',
                'desc_fr' => 'Navigation dans le paysage socioculturel bruxellois. Intégration dans le réseau de première ligne.',
            ],
            [
                'icon' => '🍽️',
                'nl' => 'Sociaal restaurant',
                'fr' => 'Restaurant social',
                'desc_nl' => 'Dagschotel aan verminderd tarief voor senioren. Afhaal en thuisbezorging mogelijk.',
                'desc_fr' => 'Plat du jour à tarif réduit pour seniors. Emporter et livraison à domicile possibles.',
            ],
            [
                'icon' => '🧹',
                'nl' => 'Praktische hulp',
                'fr' => 'Aide pratique',
                'desc_nl' => 'Boodschappen, vervoer, poetshulp, kleine herstellingen en kledingwinkel.',
                'desc_fr' => 'Courses, transport, aide ménagère, petites réparations et magasin de vêtements.',
            ],
            [
                'icon' => '🏠',
                'nl' => 'Zaalverhuur & catering',
                'fr' => 'Location de salle & traiteur',
                'desc_nl' => 'Voor buurtbewoners en lokale organisaties.',
                'desc_fr' => 'Pour les riverains et les organisations locales.',
            ],
        ] as $service)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="text-3xl mb-3">{{ $service['icon'] }}</div>
                <h2 class="font-sans font-bold text-lg mb-2">
                    {{ app()->getLocale() === 'fr' ? $service['fr'] : $service['nl'] }}
                </h2>
                <p class="text-sm text-gray-600">
                    {{ app()->getLocale() === 'fr' ? $service['desc_fr'] : $service['desc_nl'] }}
                </p>
            </div>
        @endforeach
    </div>
    <div class="mt-8 text-white rounded-xl p-6" style="background-color: var(--color-brand-green)">
        <h2 class="font-sans font-bold text-xl mb-2">
            {{ app()->getLocale() === 'fr' ? 'Grand Nettoyage (Grote Kuis)' : 'Grote Kuis' }}
        </h2>
        <p class="text-sm">
            {{ app()->getLocale() === 'fr'
                ? 'Service combiné de nettoyage, petites réparations et aide administrative. Exemples : nettoyage du four, robinetterie, vitres, tapis et peinture.'
                : 'Gecombineerde dienst: poetsen, kleine herstellingen en administratieve hulp. Voorbeelden: oven reinigen, kranen, ramen, tapijten en schilderwerk.' }}
        </p>
        <p class="mt-3 text-sm font-semibold">
            <a href="mailto:diensten@deharmonie.be" class="underline">diensten@deharmonie.be</a>
            · 02 203 28 48
        </p>
    </div>
</div>
@endsection
