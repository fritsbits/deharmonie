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
            <p style="color: var(--color-brand-muted);">Kleurenpalet — nog in te vullen</p>
        </section>
        <section id="typografie" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">
            <p style="color: var(--color-brand-muted);">Typografie — nog in te vullen</p>
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
