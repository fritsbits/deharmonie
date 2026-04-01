@extends('layouts.app')

@section('title', app()->getLocale() === 'fr' ? 'Page non trouvée' : 'Pagina niet gevonden')

@section('content')

{{-- Hero: uses same component as every interior page — left-aligned, on-brand --}}
<x-page-hero
    eyebrow="{{ app()->getLocale() === 'fr' ? 'Oups' : 'Oeps' }}"
    eyebrow-color="orange"
    heading="{{ app()->getLocale() === 'fr' ? 'Cette page n\'existe pas' : 'Deze pagina bestaat niet' }}"
    lead="{{ app()->getLocale() === 'fr' ? 'Désolé, la page que vous cherchez n\'existe plus ou a été déplacée.' : 'Sorry, de pagina die je zoekt bestaat niet meer of is verplaatst.' }}"
    bg="white"
    pb="2.5rem"
/>

{{-- Actions --}}
<div style="background: var(--color-brand-bg); padding: 2.5rem 0 3rem;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 3rem;">

        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 1rem 2.5rem;">
            @if (app()->getLocale() === 'fr')
                <a href="{{ route('fr.home') }}"
                   style="display: inline-block; background: var(--color-brand-blue); color: white; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; padding: 0.875rem 2rem; border-radius: 0.5rem; text-decoration: none;">
                    Retour à l'accueil
                </a>
                <span style="color: var(--color-brand-muted); font-size: 1rem;">
                    Besoin d'aide&nbsp;?
                    <a href="tel:0220328048"
                       style="color: var(--color-brand-dark); font-family: var(--font-sans); font-weight: 700; text-decoration: none; white-space: nowrap;">
                        02/203.28.48
                    </a>
                </span>
            @else
                <a href="{{ route('nl.home') }}"
                   style="display: inline-block; background: var(--color-brand-blue); color: white; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; padding: 0.875rem 2rem; border-radius: 0.5rem; text-decoration: none;">
                    Startpagina
                </a>
                <span style="color: var(--color-brand-muted); font-size: 1rem;">
                    Hulp nodig?
                    <a href="tel:0220328048"
                       style="color: var(--color-brand-dark); font-family: var(--font-sans); font-weight: 700; text-decoration: none; white-space: nowrap;">
                        02/203.28.48
                    </a>
                </span>
            @endif
        </div>

        {{-- Error code demoted to a small muted label --}}
        <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--color-brand-gray-dark); margin: 2.5rem 0 0;">
            Foutcode 404
        </p>

    </div>
</div>

@endsection
