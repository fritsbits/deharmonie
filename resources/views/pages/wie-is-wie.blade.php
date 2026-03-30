@extends('layouts.app')
@section('title', __('pages.wie_is_wie_title'))
@section('content')

<x-page-hero
    :eyebrow="__('pages.team_eyebrow')"
    eyebrow-color="blue"
    :heading="__('pages.team_heading')"
    :lead="__('pages.team_lead')"
    bg="white"
/>

<div style="background: #eef2f8;">
<div class="max-w-5xl mx-auto px-6 py-10">

    {{-- Staff: flat rows, department label left / names right --}}
    @php
    $teams = [
        [
            'nl' => 'Onthaal & Animatie',
            'fr' => 'Accueil & Animation',
            'members' => ['Deborah Monfils', 'Arnaud Petit', 'Nicolas Van den Eede', 'Peter Kern'],
        ],
        [
            'nl' => 'Keuken – Chefs & Instructeurs',
            'fr' => 'Cuisine – Chefs & Instructeurs',
            'members' => ['Claude Muaka', 'Pernelle Mbawu'],
        ],
        [
            'nl' => 'Zaal – Instructeur',
            'fr' => 'Salle – Instructeur',
            'members' => ['Gonard Matondo'],
        ],
        [
            'nl' => 'Keuken- & Zaalassistenten',
            'fr' => 'Assistants Cuisine & Salle',
            'members' => [
                'Agnes Kalonda-Mbiye', 'Hassna Boumediane', 'Japhet Mawanda Nzukum',
                'Mohamed Dahmani', 'Mohammad Malikzai Lal', 'Rapten Tenzin',
                'Sahara Ahmed', 'Shafahat Mallakhel', 'Tarakhel Kefayatullah',
            ],
        ],
        [
            'nl' => 'Transport & Onderhoud',
            'fr' => 'Transport & Entretien',
            'members' => ['Omid Arabzai', 'Eduardo Manzoangani'],
        ],
        [
            'nl' => 'Poetsdienst',
            'fr' => 'Service de nettoyage',
            'members' => ['Nadine Abeng Evouna', 'John Saquee'],
        ],
        [
            'nl' => 'Boekhouding & Administratie',
            'fr' => 'Comptabilité & Administration',
            'members' => ['Nancy Jacobs'],
        ],
        [
            'nl' => 'Coördinatie',
            'fr' => 'Coordination',
            'members' => ['Cynthia Spijker'],
        ],
    ];
    @endphp

    <div style="border-top: 1px solid var(--color-brand-gray); margin-bottom: 3.5rem;">
        @foreach ($teams as $team)
            <div style="display: flex; gap: 2rem; padding: 1.1rem 0; border-bottom: 1px solid var(--color-brand-gray); align-items: baseline;">
                <p style="flex: 0 0 38%; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); font-family: var(--font-sans); margin: 0; padding-top: 0.15rem;">
                    {{ $team[app()->getLocale()] }}
                </p>
                <div style="flex: 1;">
                    @foreach ($team['members'] as $name)
                        <p style="font-size: 1rem; color: var(--color-brand-dark); line-height: 1.7; margin: 0;">{{ $name }}</p>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Governance --}}
    <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-green); margin-bottom: 1.25rem; font-family: var(--font-sans);">
        {{ __('pages.governance') }}
    </p>

    <div style="display: flex; gap: 3rem;">
        <div style="flex: 1;">
            <p style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); font-family: var(--font-sans); margin-bottom: 0.75rem;">
                {{ __('pages.board_organ') }}
            </p>
            @foreach (['Jan Vandekerckhove', 'Maarten Janssens', 'Sebastiano Cincinnato', 'Isabelle De Meyere', 'Relinde Raeymakers', 'Linda Struelens', 'Inge Verhaegen'] as $name)
                <p style="font-size: 1rem; color: var(--color-brand-dark); line-height: 1.7; margin: 0;">{{ $name }}</p>
            @endforeach
        </div>

        <div style="flex: 1;">
            <p style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); font-family: var(--font-sans); margin-bottom: 0.75rem;">
                {{ __('pages.neighborhood_council') }}
            </p>
            @foreach (['Jan Vandekerckhove', 'Maarten Janssens', 'Karen De Cooman', 'Mohamed El Morabit', 'Carine Haelemeersch', 'Bianca Laurino', 'Peter Vandenbempt', 'Léopold Vodak'] as $name)
                <p style="font-size: 1rem; color: var(--color-brand-dark); line-height: 1.7; margin: 0;">{{ $name }}</p>
            @endforeach
        </div>
    </div>

</div>
</div>

@endsection
