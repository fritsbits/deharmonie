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
    <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-green); margin-bottom: 0.25rem; font-family: var(--font-sans);">Stijlgids</p>
    <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Knoppen & links</h2>

    <div style="display: flex; flex-direction: column; gap: 2rem;">

        {{-- Primaire knop --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.75rem;">Primaire knop</p>
            <a href="#" style="display: inline-block; font-size: 1rem; font-weight: 700; padding: 0.75rem 1.75rem; background-color: var(--color-brand-blue); color: white; border-radius: 4px; text-decoration: none; font-family: var(--font-sans);">Weekmenu de la Semaine</a>
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">background: brand-blue · color: white · font-sans · weight 700 · padding 0.75rem 1.75rem · border-radius 4px</code>
        </div>

        {{-- Secundaire knop --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.75rem;">Secundaire knop (outline)</p>
            <a href="#" style="display: inline-block; font-size: 1rem; font-weight: 600; padding: 0.5rem 1.25rem; border-radius: 4px; text-decoration: none; font-family: var(--font-sans); background-color: transparent; color: var(--color-brand-blue); border: 1.5px solid var(--color-brand-blue);">Alle activiteiten</a>
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">background: transparent · color: brand-blue · border: 1.5px solid brand-blue · font-sans · weight 600 · padding 0.5rem 1.25rem</code>
        </div>

        {{-- Donkere knop --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.75rem;">Donkere knop (formulier)</p>
            <button type="button" style="font-size: 0.875rem; font-weight: 600; padding: 0.625rem 1.25rem; border-radius: 4px; background-color: var(--color-brand-dark); color: white; font-family: var(--font-sans); border: none; cursor: pointer;">Verzenden</button>
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">background: brand-dark · color: white · font-sans · weight 600 · text-sm · padding 0.625rem 1.25rem</code>
        </div>

        {{-- Tekstlink --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.75rem;">Tekstlink</p>
            <a href="#" style="color: var(--color-brand-blue); text-decoration: none; font-size: 1.125rem; font-weight: 700;">02 203 28 48</a>
            &nbsp;&nbsp;
            <a href="#" style="color: var(--color-brand-blue); text-decoration: none; font-size: 1rem;">info@deharmonie.be</a>
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">color: brand-blue · text-decoration: none · hover: underline</code>
        </div>

        {{-- Teruglink --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.75rem;">Teruglink (navigatie)</p>
            <a href="#" style="color: var(--color-brand-blue); text-decoration: none; font-size: 0.875rem; font-weight: 600;">&larr; Alle activiteiten</a>
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">color: brand-blue · text-sm · weight 600 · arrow prefix</code>
        </div>

    </div>
</section>
        <section id="formulieren" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-green); margin-bottom: 0.25rem; font-family: var(--font-sans);">Stijlgids</p>
    <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Formulierelementen</h2>

    <div style="max-width: 420px; display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- Label + tekstveld --}}
        <div>
            <label style="display: block; font-size: 0.875rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Je naam *</label>
            <input type="text" placeholder="Marie Dupont"
                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 0.875rem; box-sizing: border-box;">
            <code style="font-size: 0.7rem; color: var(--color-brand-muted);">label: text-sm brand-dark · input: border brand-gray · bg white · rounded 4px</code>
        </div>

        {{-- Telefoonveld --}}
        <div>
            <label style="display: block; font-size: 0.875rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Je telefoonnummer *</label>
            <input type="tel" placeholder="02 203 28 48"
                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 0.875rem; box-sizing: border-box;">
        </div>

        {{-- Tekstvak --}}
        <div>
            <label style="display: block; font-size: 0.875rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Bericht *</label>
            <textarea rows="3" placeholder="Ik schrijf me in..."
                      style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 0.875rem; box-sizing: border-box;"></textarea>
        </div>

        {{-- Foutstatus --}}
        <div>
            <label style="display: block; font-size: 0.875rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Veld met fout</label>
            <input type="text" value=""
                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 0.875rem; box-sizing: border-box; outline: 1px solid #f87171;">
            <p style="color: #dc2626; font-size: 0.75rem; margin: 0.25rem 0 0;">Dit veld is verplicht.</p>
            <code style="font-size: 0.7rem; color: var(--color-brand-muted);">outline: 1px solid red-400 · error: text-xs text-red-600</code>
        </div>

        {{-- Successtatus --}}
        <div style="border-radius: 8px; padding: 1.5rem; text-align: center; background-color: #edf7f1; border: 1px solid #a8d5b8;">
            <svg style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.75rem;" fill="none" stroke="#2e7d52" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p style="font-weight: 600; color: #2e7d52; margin: 0;">Je inschrijving is ontvangen.</p>
            <code style="font-size: 0.7rem; color: var(--color-brand-muted);">bg #edf7f1 · border #a8d5b8 · color #2e7d52</code>
        </div>

    </div>
</section>
        <section id="badges" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-green); margin-bottom: 0.25rem; font-family: var(--font-sans);">Stijlgids</p>
    <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Badges & statussen</h2>

    <div style="display: flex; flex-direction: column; gap: 2rem;">

        {{-- Gratis badge --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Gratis badge</p>
            <span style="font-size: 1rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 4px; background-color: var(--color-brand-orange); color: white;">Gratis</span>
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">bg: brand-orange · color: white · weight 700 · px 0.6rem py 0.2rem · rounded 4px</code>
        </div>

        {{-- Geannuleerd badge --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Geannuleerd badge (inline)</p>
            <span style="font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 4px; background-color: #fde8e3; color: #c0392b;">Geannuleerd</span>
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">bg: #fde8e3 · color: #c0392b · text-xs · weight 700 · rounded 4px</code>
        </div>

        {{-- Volzet melding --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Volzet melding</p>
            <div style="display: inline-block; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background-color: #fff3e0; color: #e65100; border: 1px solid #ffccbc;">Volzet</div>
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">bg: #fff3e0 · color: #e65100 · border: #ffccbc · text-sm · weight 600 · rounded 8px</code>
        </div>

        {{-- Annuleringsbanner --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Annuleringsbanner (volledige breedte)</p>
            <div style="border-radius: 8px; padding: 1rem; font-size: 0.875rem; font-weight: 600; background-color: #fde8e3; color: #c0392b; border: 1px solid #f5c6b8;">
                &times; Deze activiteit is geannuleerd.
            </div>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">bg: #fde8e3 · color: #c0392b · border: #f5c6b8 · rounded 8px · text-sm · weight 600</code>
        </div>

    </div>
</section>
        <section id="navigatie" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-green); margin-bottom: 0.25rem; font-family: var(--font-sans);">Stijlgids</p>
    <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Navigatiebalk</h2>

    <div style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.1);">
        <header style="background-color: var(--color-brand-blue);">
            <div style="max-width: 64rem; margin: 0 auto; padding: 1.25rem 1.5rem; display: flex; align-items: center;">
                <a href="#" style="display: flex; align-items: center;">
                    <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" style="height: 2rem; width: auto; filter: brightness(0) invert(1);">
                </a>
                <nav style="margin-left: auto; display: flex; align-items: center; gap: 2rem; font-family: var(--font-sans);">
                    <a href="#" style="color: white; font-size: 1.125rem; font-weight: 600; text-decoration: none;">Activiteiten</a>
                    <a href="#" style="color: white; font-size: 1.125rem; font-weight: 600; text-decoration: none;">Diensten</a>
                    <a href="#" style="color: white; font-size: 1.125rem; font-weight: 600; text-decoration: none;">Weekmenu de la Semaine</a>
                    <a href="#" style="color: white; font-size: 1.125rem; font-weight: 600; text-decoration: none;">Contact</a>
                </nav>
            </div>
        </header>
    </div>
    <code style="font-size: 0.75rem; color: var(--color-brand-muted);">bg: brand-blue · links: white · font-sans · weight 600 · 1.125rem</code>
</section>
        <section id="hero" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-green); margin-bottom: 0.25rem; font-family: var(--font-sans);">Stijlgids</p>
    <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Hero sectie</h2>

    <div style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.1); background: white; padding: 4rem 3rem;">
        <h1 style="font-family: var(--font-sans); font-size: 3.7rem; font-weight: 900; line-height: 1.1; color: var(--color-brand-dark); margin: 0 0 0.5rem;">
            Dienstencentrum<br>Restaurant Social
        </h1>
        <h2 style="font-family: var(--font-sans); font-size: 2.25rem; font-weight: 900; color: var(--color-brand-green); line-height: 1.2; margin: 0 0 2rem;">
            Quartier Noordwijk
        </h2>

        <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <img src="{{ asset('images/icon-check.svg') }}" alt="" style="width: 1.5rem; height: 1.5rem; margin-top: 2px; flex-shrink: 0;">
                <p style="font-size: 1.125rem; line-height: 1.5; color: var(--color-brand-dark); margin: 0;">
                    <strong>Activiteiten &amp; diensten</strong> in ons centrum en bij u thuis.<br>
                    <span style="color: var(--color-brand-muted);">Services &amp; activités chez nous et chez vous.</span>
                </p>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <img src="{{ asset('images/icon-check.svg') }}" alt="" style="width: 1.5rem; height: 1.5rem; margin-top: 2px; flex-shrink: 0;">
                <p style="font-size: 1.125rem; line-height: 1.5; color: var(--color-brand-dark); margin: 0;">
                    <strong>Dagschotels</strong> aan verminderd tarief voor senioren.
                </p>
            </div>
        </div>

        <a href="#" style="display: inline-block; font-size: 1rem; font-weight: 700; padding: 0.75rem 1.75rem; background-color: var(--color-brand-blue); color: white; border-radius: 4px; text-decoration: none; font-family: var(--font-sans);">
            Weekmenu de la Semaine
        </a>
        <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">H1: font-sans 3.7rem weight 900 brand-dark · H2: brand-green weight 900 · checklist: icon-check.svg 1.5rem · knop: brand-blue</code>
    </div>
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
