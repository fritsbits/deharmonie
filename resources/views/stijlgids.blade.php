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
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Kleurenpalet</h2>
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
            ['--color-brand-bg-tint',   '#e8eef7', 'Brand achtergrond blauw'],
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
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Typografie</h2>

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

        {{-- Eyebrow / label --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Eyebrow / label</p>
            <x-eyebrow>AGENDA</x-eyebrow>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">&lt;x-eyebrow&gt;AGENDA&lt;/x-eyebrow&gt; · color="orange|green|blue" · mb="0.15rem"</code>
        </div>

        {{-- Lead tekst --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Lead tekst / intro</p>
            <p style="font-size: 2rem; font-weight: 300; line-height: 1.35; color: var(--color-brand-muted); max-width: 42rem; margin: 0;">De Harmonie helpt senioren uit de Noordwijk in het dagelijks leven. We organiseren activiteiten en diensten in ons eigen centrum, in de buurt, maar ook bij mensen thuis.</p>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-body · 2rem · weight 300 · line-height 1.35 · brand-muted · max-width 42rem</code>
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
            <p style="font-size: 0.8rem; font-weight: 600; color: var(--color-brand-muted); margin: 0;">Snel naar</p>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">font-body · 0.8rem · weight 600 · brand-muted</code>
        </div>

    </div>
</section>
        <section id="knoppen" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Knoppen & links</h2>

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

        {{-- Tekstlink --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.75rem;">Tekstlink</p>
            <a href="#" style="color: var(--color-brand-blue); text-decoration: underline; text-decoration-color: var(--color-brand-gray); font-size: 1.125rem; font-weight: 700;">02 203 28 48</a>
            &nbsp;&nbsp;
            <a href="#" style="color: var(--color-brand-blue); text-decoration: underline; text-decoration-color: var(--color-brand-gray); font-size: 1rem;">info@deharmonie.be</a>
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">color: brand-blue · text-decoration: underline · text-decoration-color: brand-gray</code>
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
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Formulierelementen</h2>

    <div style="max-width: 420px; display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- Label + tekstveld --}}
        <div>
            <label style="display: block; font-size: 1.125rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Je naam *</label>
            <input type="text" placeholder="Marie Dupont"
                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 1.125rem; box-sizing: border-box;">
            <code style="font-size: 0.7rem; color: var(--color-brand-muted);">label: 1.125rem brand-dark · input: border brand-gray · bg white · rounded 4px</code>
        </div>

        {{-- Telefoonveld --}}
        <div>
            <label style="display: block; font-size: 1.125rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Je telefoonnummer *</label>
            <input type="tel" placeholder="02 203 28 48"
                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 1.125rem; box-sizing: border-box;">
        </div>

        {{-- Tekstvak --}}
        <div>
            <label style="display: block; font-size: 1.125rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Bericht *</label>
            <textarea rows="3" placeholder="Ik schrijf me in..."
                      style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 1.125rem; box-sizing: border-box;"></textarea>
        </div>

        {{-- Foutstatus --}}
        <div>
            <label style="display: block; font-size: 1.125rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Veld met fout</label>
            <input type="text" value=""
                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-orange); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 1.125rem; box-sizing: border-box; outline: 1px solid var(--color-brand-orange);">
            <p style="color: var(--color-brand-orange); font-size: 1rem; margin: 0.25rem 0 0;">Dit veld is verplicht.</p>
            <code style="font-size: 0.7rem; color: var(--color-brand-muted);">outline: 1px solid brand-orange · error: 1rem brand-orange</code>
        </div>

        {{-- Successtatus --}}
        <div style="border-radius: 8px; padding: 1.5rem; text-align: center; background-color: rgba(129,181,156,0.12); border: 1px solid var(--color-brand-green);">
            <svg style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.75rem;" fill="none" stroke="var(--color-brand-green)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p style="font-weight: 600; color: var(--color-brand-green); margin: 0;">Je inschrijving is ontvangen.</p>
            <code style="font-size: 0.7rem; color: var(--color-brand-muted);">bg rgba(brand-green, 0.12) · border brand-green · color brand-green</code>
        </div>

    </div>
</section>
        <section id="badges" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Badges & statussen</h2>

    <div style="display: flex; flex-direction: column; gap: 2rem;">

        {{-- Gratis badge --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Gratis badge</p>
            <x-badge type="gratis" />
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">&lt;x-badge type="gratis" /&gt;</code>
        </div>

        {{-- Geannuleerd badge --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Geannuleerd badge (inline)</p>
            <x-badge type="geannuleerd" />
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">&lt;x-badge type="geannuleerd" /&gt;</code>
        </div>

        {{-- Volzet melding --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Volzet melding</p>
            <div style="display: inline-block; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background-color: rgba(70,121,188,0.12); color: var(--color-brand-blue); border: 1px solid rgba(70,121,188,0.3);">Volzet</div>
            <br><code style="font-size: 0.75rem; color: var(--color-brand-muted);">bg: rgba(brand-blue, 0.12) · color: brand-blue · border: rgba(brand-blue, 0.3) · text-sm · weight 600 · rounded 8px</code>
        </div>

        {{-- Annuleringsbanner --}}
        <div>
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.5rem;">Annuleringsbanner (volledige breedte)</p>
            <div style="border-radius: 8px; padding: 1rem; font-size: 0.875rem; font-weight: 600; background-color: rgba(235,102,67,0.1); color: var(--color-brand-orange); border: 1px solid rgba(235,102,67,0.3);">
                &times; Deze activiteit is geannuleerd.
            </div>
            <code style="font-size: 0.75rem; color: var(--color-brand-muted);">bg: rgba(brand-orange, 0.1) · color: brand-orange · border: rgba(brand-orange, 0.3) · rounded 8px · text-sm · weight 600</code>
        </div>

    </div>
</section>
        <section id="navigatie" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Navigatiebalk</h2>

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
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Hero sectie</h2>

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
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Activiteitenlijst item</h2>

    <div style="max-width: 560px;">
        {{-- Normaal item --}}
        <a href="#" style="display: flex; align-items: center; gap: 1.25rem; padding: 1rem 0; text-decoration: none; border-bottom: 1px solid #e5e2de;">
            <div style="flex-shrink: 0; width: 80px; height: 80px; border-radius: 8px; overflow: hidden; background-color: var(--color-brand-gray);">
                <img src="{{ asset('images/interesses/activiteiten.png') }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div>
                <p style="font-weight: 700; font-size: 1.625rem; line-height: 1.2; color: var(--color-brand-blue); font-family: var(--font-sans); margin: 0;">Yoga voor senioren</p>
                <p style="font-size: 1.125rem; margin: 0.35rem 0 0; color: var(--color-brand-muted);">Donderdag 27/3 om 10:00 &ndash; 11:30 <span style="color: var(--color-brand-gray-dark);">&middot;</span> Zaal De Harmonie</p>
            </div>
        </a>
        <code style="font-size: 0.7rem; color: var(--color-brand-muted);">thumbnail: 80×80px rounded-8px · title: font-sans 1.625rem weight 700 brand-blue · meta: 1rem brand-muted</code>

        {{-- Geannuleerd item --}}
        <a href="#" style="display: flex; align-items: center; gap: 1.25rem; padding: 1rem 0; text-decoration: none; border-bottom: 1px solid #e5e2de; opacity: 0.85;">
            <div style="flex-shrink: 0; width: 80px; height: 80px; border-radius: 8px; overflow: hidden; background-color: var(--color-brand-gray);">
                <img src="{{ asset('images/interesses/uitstappen_en_vakanties.png') }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <p style="font-weight: 700; font-size: 1.625rem; line-height: 1.2; color: var(--color-brand-blue); font-family: var(--font-sans); margin: 0;">Uitstap Brugge</p>
                    <x-badge type="geannuleerd" />
                </div>
                <p style="font-size: 1.125rem; margin: 0.35rem 0 0; color: var(--color-brand-muted);">Vrijdag 28/3 om 08:00 <span style="color: var(--color-brand-gray-dark);">&middot;</span> Vertrek aan de deur</p>
            </div>
        </a>
        <code style="font-size: 0.7rem; color: var(--color-brand-muted);">geannuleerd: opacity 0.5 + badge inline</code>
    </div>
</section>
        <section id="activiteit-detail" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Activiteit detail sidebar</h2>

    <div style="max-width: 280px; border-radius: 8px; padding: 1.25rem; border: 1px solid var(--color-brand-gray); background: white;">

        <div style="margin-bottom: 1rem;">
            <p style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--color-brand-green); margin: 0 0 0.25rem;">Datum</p>
            <p style="font-weight: 700; color: var(--color-brand-dark); font-size: 1.25rem; margin: 0;">Donderdag 27 maart 2026</p>
            <p style="font-weight: 600; color: var(--color-brand-muted); font-size: 1.125rem; margin: 0.1rem 0 0;">10:00 &ndash; 11:30</p>
        </div>

        <div style="border-top: 1px solid var(--color-brand-gray); margin: 1rem 0;"></div>

        <div style="margin-bottom: 1rem;">
            <p style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--color-brand-green); margin: 0 0 0.25rem;">Prijs</p>
            <span style="font-weight: 700; color: var(--color-brand-dark); font-size: 1.25rem;">€ 5,00</span>
            &nbsp;
            <x-badge type="gratis" />
        </div>

        <div style="border-top: 1px solid var(--color-brand-gray); margin: 1rem 0;"></div>

        <div style="margin-bottom: 1rem;">
            <p style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--color-brand-green); margin: 0 0 0.25rem;">Locatie</p>
            <p style="font-weight: 600; color: var(--color-brand-dark); font-size: 1.125rem; margin: 0;">Zaal De Harmonie</p>
        </div>

        <div style="border-top: 1px solid var(--color-brand-gray); margin: 1rem 0;"></div>

        <div>
            <p style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--color-brand-green); margin: 0 0 0.25rem;">Contact</p>
            <p style="font-weight: 700; color: var(--color-brand-dark); font-size: 1.125rem; margin: 0 0 0.25rem;">De Harmonie</p>
            <p style="font-size: 1.125rem; margin: 0 0 0.25rem;">
                <a href="#" style="color: var(--color-brand-blue); text-decoration: none; font-weight: 700;">02 203 28 48</a>
            </p>
            <p style="font-size: 1rem; margin: 0;">
                <a href="#" style="color: var(--color-brand-blue); text-decoration: none;">info@deharmonie.be</a>
            </p>
        </div>

        <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--color-brand-gray);">
            <a href="#" style="display: inline-block; font-size: 0.9rem; font-weight: 700; padding: 0.5rem 1rem; border: 2px solid var(--color-brand-blue); color: var(--color-brand-blue); border-radius: 4px; text-decoration: none; font-family: var(--font-sans);">&#9113; Afdrukken</a>
        </div>

    </div>
    <code style="font-size: 0.75rem; color: var(--color-brand-muted);">card: border brand-gray · bg white · rounded 8px · rows separated by 1px brand-gray divider</code>
</section>
        <section id="registratieformulier" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Registratieformulier</h2>

    <div style="max-width: 480px; border-radius: 8px; padding: 1.5rem; border: 1px solid var(--color-brand-gray); background: white;">
        <h3 style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 800; color: var(--color-brand-dark); margin: 0 0 0.75rem;">Inschrijven</h3>
        <p style="font-weight: 700; font-size: 1rem; color: var(--color-brand-dark); margin: 0 0 0.25rem; font-family: var(--font-sans);">
            Vul dit formulier in en we bellen je op om te bevestigen.
        </p>
        <p style="color: var(--color-brand-muted); font-size: 1rem; margin: 0 0 1.25rem;">
            Remplissez le formulaire et nous vous appellerons pour confirmer.
        </p>
        <form style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
                <label style="display: block; font-size: 1.125rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Je naam *</label>
                <input type="text" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 1.125rem; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; font-size: 1.125rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Je telefoonnummer *</label>
                <input type="tel" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 1.125rem; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; font-size: 1.125rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Je email</label>
                <input type="email" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 1.125rem; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; font-size: 1.125rem; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Bericht *</label>
                <textarea rows="3" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-brand-gray); border-radius: 4px; background: white; color: var(--color-brand-dark); font-size: 1.125rem; box-sizing: border-box;"></textarea>
            </div>
            <button type="button" style="font-size: 1rem; font-weight: 600; padding: 0.625rem 1.25rem; border-radius: 4px; background-color: var(--color-brand-dark); color: white; font-family: var(--font-sans); border: none; cursor: pointer; align-self: flex-start;">
                Verzenden
            </button>
        </form>
    </div>
</section>
        <section id="diensten" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Diensten sectie</h2>

    {{-- Services list --}}
    <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin-bottom: 0.75rem;">Diensten lijst</p>
    <ul style="max-width: 560px; margin: 0 0 2.5rem; border-top: 1px solid var(--color-brand-gray);">
        @foreach (['Sociale dienst & Wegwijs in socio-cultureel Brussel', 'Sociaal restaurant, afhaal en levering aan huis', 'Boodschappendienst & Vervoersdienst', 'Klusjesdienst & Poetsdienst'] as $dienst)
        <li style="display: flex; align-items: baseline; gap: 0.75rem; padding: 0.85rem 0; border-bottom: 1px solid var(--color-brand-gray); list-style: none;">
            <span style="flex-shrink: 0; color: var(--color-brand-orange); font-weight: 700; font-size: 1.1rem; line-height: 1;">&#10003;</span>
            <span style="font-size: 1.125rem; color: var(--color-brand-dark); line-height: 1.5;">{{ $dienst }}</span>
        </li>
        @endforeach
    </ul>
    <code style="font-size: 0.75rem; color: var(--color-brand-muted);">border-top + border-bottom brand-gray · bullet: 6px circle brand-orange · text 1rem brand-dark</code>

    {{-- Service card --}}
    <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-muted); margin: 2rem 0 0.75rem;">Diensten kaart</p>
    <div style="max-width: 560px; background: white; border: 1px solid var(--color-brand-gray); border-radius: 0.75rem; padding: 2rem;">
        <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-orange); margin: 0 0 0.15rem; font-family: var(--font-sans);">PROJECT</p>
        <h2 style="font-family: var(--font-sans); font-size: 1.625rem; font-weight: 900; color: var(--color-brand-dark); margin: 0 0 0.75rem;">Hulp bij de Grote Kuis</h2>
        <p style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-dark); margin: 0 0 1rem;">Met dit project willen we je helpen met de 'Grote Kuis'. Samen met onze poetsers en klussers nemen we je woning onder handen.</p>
        <a href="#" style="font-size: 1rem; font-weight: 600; color: var(--color-brand-blue); text-decoration: none;">diensten@deharmonie.be</a>
    </div>
    <code style="font-size: 0.75rem; color: var(--color-brand-muted);">card: bg white · border brand-gray · rounded-xl · padding 2rem · eyebrow orange</code>
</section>
        <section id="voettekst" style="padding: 3rem 0;">
    <x-eyebrow>Stijlgids</x-eyebrow>
    <h2 style="font-family: var(--font-sans); font-size: 2.1rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.5rem;">Voettekst</h2>

    <div style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.1);">
        <footer style="background-color: var(--color-brand-blue); color: white;">
            <div style="max-width: 64rem; margin: 0 auto; padding: 3rem 1.5rem; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2.5rem;">
                <div>
                    <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" style="height: 2rem; width: auto; margin-bottom: 1.25rem; filter: brightness(0) invert(1);">
                    <p style="font-size: 1rem; line-height: 1.6; opacity: 0.8; margin-bottom: 0.5rem;">VZW Buurtwerk Noordwijk<br>Antwerpsesteenweg 24<br>1000 Brussel</p>
                    <p style="font-size: 1rem; opacity: 0.8; margin-bottom: 0.25rem;">
                        <a href="#" style="color: white; text-decoration: none;">02 203 28 48</a>
                    </p>
                    <p style="font-size: 1rem; opacity: 0.8; margin-bottom: 1.5rem;">
                        <a href="#" style="color: white; text-decoration: none;">info@deharmonie.be</a>
                    </p>
                    <p style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.6; margin-bottom: 0.5rem;">Snel naar</p>
                    <p style="font-size: 1rem; opacity: 0.75; margin-bottom: 0.2rem;"><a href="#" style="color: white; text-decoration: none;">Diensten</a></p>
                    <p style="font-size: 1rem; opacity: 0.75;"><a href="#" style="color: white; text-decoration: none;">Wie is wie</a></p>
                </div>
                <div>
                    <p style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.6; margin-bottom: 1rem;">Met steun van</p>
                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        <img src="{{ asset('images/logo-vlaanderen.svg') }}" alt="Vlaanderen" style="height: 3rem; width: auto; filter: brightness(0) invert(1); opacity: 0.85;">
                        <img src="{{ asset('images/logo-bhg.svg') }}" alt="Brussels Hoofdstedelijk Gewest" style="height: 2rem; width: auto; filter: brightness(0) invert(1); opacity: 0.85;">
                    </div>
                </div>
                <div>
                    <p style="font-size: 1rem; opacity: 0.8; margin-bottom: 1rem;">Volg De Harmonie op Facebook</p>
                    <img src="{{ asset('images/logo-facebook.png') }}" alt="Facebook" style="width: 2.5rem; height: 2.5rem; opacity: 0.9;">
                </div>
            </div>
            <div style="border-top: 1px solid rgba(255,255,255,0.15); text-align: center; padding: 0.75rem; font-size: 0.85rem; opacity: 0.4;">
                &copy; {{ date('Y') }} VZW Buurtwerk Noordwijk
            </div>
        </footer>
    </div>
</section>

    </main>
</div>
@endsection
