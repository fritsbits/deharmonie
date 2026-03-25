@extends('layouts.stijlgids')

@section('content')
<div style="display: flex; min-height: 100vh;">

    {{-- Sticky sidebar --}}
    <nav style="width: 220px; flex-shrink: 0; padding: 2rem 1.5rem; position: sticky; top: 0; height: 100vh; overflow-y: auto; border-right: 1px solid var(--color-brand-gray); background: white;">
        <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 1rem; font-family: var(--font-sans);">
            Stijlgids
        </p>
        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.25rem;">
            @foreach ([
                ['kleurenpalet', 'Kleurenpalet'],
                ['typografie', 'Typografie'],
                ['knoppen', 'Knoppen & links'],
                ['formulieren', 'Formulierelementen'],
                ['badges', 'Badges & statussen'],
                ['navigatie', 'Navigatiebalk'],
                ['hero', 'Hero sectie'],
                ['activiteitenlijst', 'Activiteitenlijst'],
                ['activiteit-detail', 'Detail sidebar'],
                ['registratieformulier', 'Registratieformulier'],
                ['diensten', 'Diensten sectie'],
                ['voettekst', 'Voettekst'],
            ] as [$anchor, $label])
            <li>
                <a href="#{{ $anchor }}"
                   style="font-size: 0.9rem; color: var(--color-brand-muted); text-decoration: none; display: block; padding: 0.25rem 0;">
                    {{ $label }}
                </a>
            </li>
            @endforeach
        </ul>
    </nav>

    {{-- Main content --}}
    <main style="flex: 1; padding: 3rem 4rem; max-width: 860px;">

        <h1 style="font-family: var(--font-sans); font-size: 2.5rem; font-weight: 900; color: var(--color-brand-dark); margin-bottom: 0.5rem;">
            Stijlgids
        </h1>
        <p style="font-size: 1.05rem; color: var(--color-brand-muted); margin-bottom: 4rem;">
            Interne referentiepagina. Niet publiek gelinkt.
        </p>

        {{-- Section placeholders (replaced task by task) --}}
        <section id="kleurenpalet" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-green); margin-bottom: 0.25rem; font-family: var(--font-sans);">Stijlgids</p>
    <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Kleurenpalet</h2>
    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
        @foreach ([
            ['--color-brand-blue',      '#4679bc', 'Brand blauw'],
            ['--color-brand-green',     '#81b59c', 'Brand groen'],
            ['--color-brand-orange',    '#eb6643', 'Brand oranje'],
            ['--color-brand-dark',      '#2c2826', 'Brand donker'],
            ['--color-brand-muted',     '#706662', 'Brand gedempd'],
            ['--color-brand-bg',        '#fbfaf9', 'Brand achtergrond'],
            ['--color-brand-gray',      '#d8d3d2', 'Brand grijs'],
            ['--color-brand-gray-dark', '#c0bbb9', 'Brand grijs donker'],
            ['--color-brand-medium',    '#4e4543', 'Brand medium'],
        ] as [$token, $hex, $name])
        <div style="width: 140px;">
            <div style="width: 100%; height: 64px; border-radius: 8px; background-color: {{ $hex }}; border: 1px solid rgba(0,0,0,0.08); margin-bottom: 0.5rem;"></div>
            <p style="font-size: 0.85rem; font-weight: 700; color: var(--color-brand-dark); margin: 0;">{{ $name }}</p>
            <p style="font-size: 0.75rem; color: var(--color-brand-muted); margin: 0.1rem 0 0;">{{ $hex }}</p>
            <code style="font-size: 0.7rem; color: var(--color-brand-muted);">{{ $token }}</code>
        </div>
        @endforeach
    </div>
</section>
        <section id="typografie" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-green); margin-bottom: 0.25rem; font-family: var(--font-sans);">Stijlgids</p>
    <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Typografie</h2>

    <div style="display: flex; flex-direction: column; gap: 2.5rem;">

        {{-- H1 Hero --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">H1 — Hoofdtitel (hero)</p>
            <h1 style="font-family: var(--font-sans); font-size: 3.7rem; font-weight: 900; line-height: 1.1; color: var(--color-brand-dark); margin: 0;">Dienstencentrum</h1>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-sans · 3.7rem · weight 900 · line-height 1.1 · brand-dark</code>
        </div>

        {{-- H2 Sectie --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">H2 — Sectietitel</p>
            <h2 style="font-family: var(--font-sans); font-size: 2.25rem; font-weight: 800; color: var(--color-brand-dark); margin: 0;">Volgende activiteiten</h2>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-sans · 2.25rem · weight 800 · brand-dark</code>
        </div>

        {{-- H2 Subtitle (green) --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">H2 — Ondertitel (groen)</p>
            <h2 style="font-family: var(--font-sans); font-size: 2.25rem; font-weight: 900; color: var(--color-brand-green); line-height: 1.2; margin: 0;">Quartier Noordwijk</h2>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-sans · 2.25rem · weight 900 · line-height 1.2 · brand-green</code>
        </div>

        {{-- H2 Card --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">H2 — Kaarttitel</p>
            <h2 style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 900; color: var(--color-brand-dark); margin: 0;">Hulp bij de Grote Kuis</h2>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-sans · 1.5rem · weight 900 · brand-dark</code>
        </div>

        {{-- H1 Paginatitel --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">H1 — Paginatitel (detail)</p>
            <h1 style="font-family: var(--font-sans); font-size: 2rem; font-weight: 800; text-transform: uppercase; color: var(--color-brand-dark); margin: 0;">Activiteitsnaam</h1>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-sans · 2rem · weight 800 · uppercase · brand-dark</code>
        </div>

        {{-- Eyebrow / label --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Eyebrow / label</p>
            <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-orange); margin: 0; font-family: var(--font-sans);">DIENSTEN</p>
            <p style="font-size: 1.1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-green); margin: 0.5rem 0 0; font-family: var(--font-sans);">AGENDA</p>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-sans · 0.85–1.1rem · weight 700 · uppercase · letter-spacing 0.06–0.08em · orange of green</code>
        </div>

        {{-- Lead tekst --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Lead tekst / intro</p>
            <p style="font-size: 1.05rem; line-height: 1.7; color: var(--color-brand-muted); max-width: 42rem; margin: 0;">De Harmonie helpt senioren uit de Noordwijk in het dagelijks leven. We organiseren activiteiten en diensten in ons eigen centrum, in de buurt, maar ook bij mensen thuis.</p>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-body · 1.05rem · line-height 1.7 · brand-muted · max-width 42rem</code>
        </div>

        {{-- Body tekst --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Body tekst</p>
            <p style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-dark); margin: 0;">Kom voor een lekker maaltijd of voor de activiteiten en uitstappen. We geven je graag ook meer info over diensten zoals vervoer, poetsdienst en maaltijden aan huis.</p>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-body · 1.125rem · line-height 1.7 · brand-dark</code>
        </div>

        {{-- Klein / meta --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Klein / meta tekst</p>
            <p style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-brand-muted); margin: 0;">Snel naar</p>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-body · 0.8rem · weight 600 · uppercase · brand-muted</code>
        </div>

    </div>
</section>
        <section id="knoppen" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
            <p style="color: var(--color-brand-muted);">Knoppen & links — nog in te vullen</p>
        </section>
        <section id="formulieren" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
            <p style="color: var(--color-brand-muted);">Formulierelementen — nog in te vullen</p>
        </section>
        <section id="badges" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
            <p style="color: var(--color-brand-muted);">Badges & statussen — nog in te vullen</p>
        </section>
        <section id="navigatie" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
            <p style="color: var(--color-brand-muted);">Navigatiebalk — nog in te vullen</p>
        </section>
        <section id="hero" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
            <p style="color: var(--color-brand-muted);">Hero sectie — nog in te vullen</p>
        </section>
        <section id="activiteitenlijst" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
            <p style="color: var(--color-brand-muted);">Activiteitenlijst — nog in te vullen</p>
        </section>
        <section id="activiteit-detail" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
            <p style="color: var(--color-brand-muted);">Detail sidebar — nog in te vullen</p>
        </section>
        <section id="registratieformulier" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
            <p style="color: var(--color-brand-muted);">Registratieformulier — nog in te vullen</p>
        </section>
        <section id="diensten" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
            <p style="color: var(--color-brand-muted);">Diensten sectie — nog in te vullen</p>
        </section>
        <section id="voettekst" style="padding: 3rem 0;">
            <p style="color: var(--color-brand-muted);">Voettekst — nog in te vullen</p>
        </section>

    </main>
</div>
@endsection
