@php
    if (request()->is('fr') || request()->is('fr/*')) {
        app()->setLocale('fr');
    }
    $locale = app()->getLocale();
    $isFr = $locale === 'fr';

    try {
        $nextActiviteit = \App\Models\Activiteit::query()
            ->whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', now()->startOfDay())
            ->orderBy('datum')
            ->orderBy('startuur')
            ->first();
    } catch (\Throwable $e) {
        $nextActiviteit = null;
    }
@endphp

@extends('layouts.app')

@section('title', $isFr ? 'Page introuvable' : 'Pagina niet gevonden')

@section('content')

<x-page-hero
    eyebrow="{{ $isFr ? 'Page introuvable' : 'Pagina niet gevonden' }}"
    eyebrow-color="orange"
    heading="{{ $isFr ? 'On vous aide à retrouver votre chemin' : 'We helpen je verder' }}"
    lead="{{ $isFr ? 'Cette page n\'existe pas ou a été déplacée. Voici par où continuer.' : 'Deze pagina bestaat niet meer of is verplaatst. Misschien helpt een van deze verder.' }}"
    bg="white"
    pb="2.5rem"
/>

<section style="background: var(--color-brand-bg); padding: 0 0 4rem;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem;">

            <a href="{{ route($locale . '.activiteiten.index') }}"
               style="display: block; background: white; border-radius: 0.75rem; overflow: hidden; text-decoration: none; color: inherit; box-shadow: 0 1px 2px rgba(44,40,38,0.06); transition: transform 200ms ease, box-shadow 200ms ease;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 16px rgba(44,40,38,0.1)';"
               onmouseout="this.style.transform='';this.style.boxShadow='0 1px 2px rgba(44,40,38,0.06)';">
                <div style="aspect-ratio: 4 / 3; overflow: hidden; background: #f3dbd5;">
                    <img src="{{ asset('images/photo-groep-tafel.webp') }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
                <div style="padding: 1.25rem 1.5rem 1.5rem;">
                    <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--color-brand-orange); margin: 0 0 0.5rem;">
                        {{ $isFr ? 'Activités' : 'Activiteiten' }}
                    </p>
                    <h2 style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 800; line-height: 1.15; color: var(--color-brand-dark); margin: 0 0 0.5rem;">
                        {{ $isFr ? 'Voir toutes les activités' : 'Bekijk de agenda' }}
                    </h2>
                    @if ($nextActiviteit)
                        <p style="font-size: 0.9375rem; color: var(--color-brand-muted); line-height: 1.45; margin: 0;">
                            {{ $isFr ? 'Prochainement' : 'Eerstvolgende' }}:
                            <span style="color: var(--color-brand-dark); font-weight: 600;">{{ $nextActiviteit->titel }}</span>
                        </p>
                    @else
                        <p style="font-size: 0.9375rem; color: var(--color-brand-muted); line-height: 1.45; margin: 0;">
                            {{ $isFr ? 'Cours, ateliers et sorties' : 'Cursussen, workshops en uitstappen' }}
                        </p>
                    @endif
                </div>
            </a>

            <a href="{{ route($locale . '.weekmenu') }}"
               style="display: block; background: white; border-radius: 0.75rem; overflow: hidden; text-decoration: none; color: inherit; box-shadow: 0 1px 2px rgba(44,40,38,0.06); transition: transform 200ms ease, box-shadow 200ms ease;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 16px rgba(44,40,38,0.1)';"
               onmouseout="this.style.transform='';this.style.boxShadow='0 1px 2px rgba(44,40,38,0.06)';">
                <div style="aspect-ratio: 4 / 3; overflow: hidden; background: #d4e8df;">
                    <img src="{{ asset('images/photo-restaurant-bord.webp') }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
                <div style="padding: 1.25rem 1.5rem 1.5rem;">
                    <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--color-brand-green); margin: 0 0 0.5rem;">
                        Restaurant
                    </p>
                    <h2 style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 800; line-height: 1.15; color: var(--color-brand-dark); margin: 0 0 0.5rem;">
                        {{ $isFr ? 'Le menu de la semaine' : 'Het weekmenu' }}
                    </h2>
                    <p style="font-size: 0.9375rem; color: var(--color-brand-muted); line-height: 1.45; margin: 0;">
                        {{ $isFr ? 'Lunch chaque jour ouvrable' : 'Lunch op werkdagen' }}
                    </p>
                </div>
            </a>

            <a href="{{ route($locale . '.contact') }}"
               style="display: block; background: white; border-radius: 0.75rem; overflow: hidden; text-decoration: none; color: inherit; box-shadow: 0 1px 2px rgba(44,40,38,0.06); transition: transform 200ms ease, box-shadow 200ms ease;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 16px rgba(44,40,38,0.1)';"
               onmouseout="this.style.transform='';this.style.boxShadow='0 1px 2px rgba(44,40,38,0.06)';">
                <div style="aspect-ratio: 4 / 3; overflow: hidden; background: #d5e0f0;">
                    <img src="{{ asset('images/photo-contact-onthaal.webp') }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
                <div style="padding: 1.25rem 1.5rem 1.5rem;">
                    <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--color-brand-blue); margin: 0 0 0.5rem;">
                        Contact
                    </p>
                    <h2 style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 800; line-height: 1.15; color: var(--color-brand-dark); margin: 0 0 0.5rem;">
                        {{ $isFr ? 'Venez nous voir' : 'Kom eens langs' }}
                    </h2>
                    <p style="font-size: 0.9375rem; color: var(--color-brand-muted); line-height: 1.45; margin: 0;">
                        Antwerpselaan 26, 1000 Brussel
                    </p>
                </div>
            </a>

        </div>

        <div style="margin-top: 3rem; display: flex; flex-wrap: wrap; align-items: center; gap: 1rem 2rem;">
            <a href="{{ route($locale . '.home') }}"
               style="display: inline-block; background: var(--color-brand-blue); color: white; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; padding: 0.875rem 2rem; border-radius: 0.5rem; text-decoration: none;">
                {{ $isFr ? "Retour à l'accueil" : 'Startpagina' }}
            </a>
            <span style="color: var(--color-brand-muted); font-size: 1.0625rem;">
                {{ $isFr ? "Besoin d'aide\u{a0}?" : 'Hulp nodig?' }}
                <a href="tel:+3222032848"
                   style="color: var(--color-brand-dark); font-family: var(--font-sans); font-weight: 700; text-decoration: none; white-space: nowrap;">
                    02/203.28.48
                </a>
            </span>
        </div>

    </div>
</section>

@endsection
