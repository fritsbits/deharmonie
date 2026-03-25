@extends('layouts.app')
@section('title', app()->getLocale() === 'fr' ? 'Services' : 'Diensten')
@section('content')
<div class="max-w-5xl mx-auto px-6 py-10">
    <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: var(--color-brand-orange)">
        {{ app()->getLocale() === 'fr' ? 'SERVICES' : 'DIENSTEN' }}
    </p>
    <h1 class="font-bold text-3xl mb-8" style="font-family: var(--font-sans); color: var(--color-brand-dark)">
        {{ app()->getLocale() === 'fr' ? 'Services & activités' : 'Diensten & activiteiten' }}
    </h1>
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        @foreach ([
            ['icon' => '🤝', 'nl' => 'Sociale begeleiding', 'fr' => 'Accompagnement social',
             'desc_nl' => 'Navigatie in het Brussels socioculturele landschap. Integratie in het eerstelijnsnetwerk.',
             'desc_fr' => 'Navigation dans le paysage socioculturel bruxellois. Intégration dans le réseau de première ligne.'],
            ['icon' => '🍽️', 'nl' => 'Sociaal restaurant', 'fr' => 'Restaurant social',
             'desc_nl' => 'Dagschotel aan verminderd tarief voor senioren. Afhaal en thuisbezorging mogelijk.',
             'desc_fr' => 'Plat du jour à tarif réduit pour seniors. Emporter et livraison à domicile possibles.'],
            ['icon' => '🧹', 'nl' => 'Praktische hulp', 'fr' => 'Aide pratique',
             'desc_nl' => 'Boodschappen, vervoer, poetshulp, kleine herstellingen en kledingwinkel.',
             'desc_fr' => 'Courses, transport, aide ménagère, petites réparations et magasin de vêtements.'],
            ['icon' => '🏠', 'nl' => 'Zaalverhuur & catering', 'fr' => 'Location de salle & traiteur',
             'desc_nl' => 'Voor buurtbewoners en lokale organisaties.',
             'desc_fr' => 'Pour les riverains et les organisations locales.'],
        ] as $service)
            <div class="rounded-xl p-6" style="background: white; border: 1px solid var(--color-brand-gray)">
                <div class="text-3xl mb-3">{{ $service['icon'] }}</div>
                <h2 class="font-bold text-lg mb-2" style="font-family: var(--font-sans); color: var(--color-brand-dark)">
                    {{ app()->getLocale() === 'fr' ? $service['fr'] : $service['nl'] }}
                </h2>
                <p class="text-sm" style="color: var(--color-brand-muted)">
                    {{ app()->getLocale() === 'fr' ? $service['desc_fr'] : $service['desc_nl'] }}
                </p>
            </div>
        @endforeach
    </div>
    <div class="rounded-xl p-6 text-white" style="background-color: var(--color-brand-orange)">
        <h2 class="font-bold text-xl mb-2" style="font-family: var(--font-sans)">
            {{ app()->getLocale() === 'fr' ? 'Grand Nettoyage (Grote Kuis)' : 'Grote Kuis' }}
        </h2>
        <p class="text-sm opacity-90">
            {{ app()->getLocale() === 'fr'
                ? 'Service combiné de nettoyage, petites réparations et aide administrative.'
                : 'Gecombineerde dienst: poetsen, kleine herstellingen en administratieve hulp.' }}
        </p>
        <a href="mailto:diensten@deharmonie.be" class="block mt-3 text-sm font-semibold underline">diensten@deharmonie.be</a>
    </div>
</div>
@endsection
