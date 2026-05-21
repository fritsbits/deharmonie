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

{{-- PHOTO STRIP --}}
<div class="wie-photo-strip" style="display: flex; height: 320px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-contact-onthaal.webp') }}"
             alt="{{ app()->getLocale() === 'fr' ? 'L\'équipe d\'accueil de De Harmonie' : 'Onthaalteam van De Harmonie' }}"
             loading="lazy"
             style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center 30%;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-keukenteam.webp') }}"
             alt="{{ app()->getLocale() === 'fr' ? 'L\'équipe de cuisine' : 'Het keukenteam' }}"
             loading="lazy"
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-restaurant-bediening.webp') }}"
             alt="{{ app()->getLocale() === 'fr' ? 'Un membre de l\'équipe sert les visiteurs au restaurant' : 'Medewerker bedient bezoekers in het restaurant' }}"
             loading="lazy"
             style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center 40%;">
    </div>
</div>

@php
    $staf = $categorieen->where('volgorde', '<=', 8)->values();
    $governance = $categorieen->where('volgorde', '>', 8)->values();
@endphp

<div style="background: var(--color-brand-blue-tint);">
<div style="max-width: 72rem; margin: 0 auto; padding: 3.5rem 1.5rem;">

    {{-- Medewerkers --}}
    <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2.25rem); font-weight: 900; color: var(--color-brand-dark); margin: 0 0 1.5rem; line-height: 1.1;">{{ __('pages.team_staff') }}</h2>

    <div style="border-top: 1px solid var(--color-brand-border-blue); margin-bottom: 3.5rem;">
        @foreach ($staf as $categorie)
            <div class="wie-rij" style="padding: 1.25rem 0; border-bottom: 1px solid var(--color-brand-border-blue);">
                <h4 class="wie-label ui-label" style="color: var(--color-brand-blue); margin: 0; padding-top: 0.2rem;">
                    {{ $categorie->naam }}
                </h4>
                <div class="wie-namen">
                    @foreach ($categorie->leden as $lid)
                        <p style="font-size: 1rem; color: var(--color-brand-dark); line-height: 1.7; margin: 0;">
                            {{ $lid->naam }}@if ($lid->titel)<span style="color: var(--color-brand-muted); font-size: 1rem;"> — {{ $lid->titel }}</span>@endif
                        </p>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Bestuur --}}
    <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2.25rem); font-weight: 900; color: var(--color-brand-dark); margin: 0 0 1.5rem; line-height: 1.1;">{{ __('pages.governance') }}</h2>

    <div class="bestuur-grid">
        @foreach ($governance as $categorie)
            <div>
                <h4 class="ui-label" style="color: var(--color-brand-blue); margin: 0 0 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--color-brand-border-blue);">
                    {{ $categorie->naam }}
                </h4>
                @foreach ($categorie->leden as $lid)
                    <p style="font-size: 1rem; color: var(--color-brand-dark); line-height: 1.7; margin: 0;">
                        {{ $lid->naam }}@if ($lid->titel)<span style="color: var(--color-brand-muted); font-size: 1rem;"> — {{ $lid->titel }}</span>@endif
                    </p>
                @endforeach
            </div>
        @endforeach
    </div>

</div>
</div>

<style>
.wie-rij { display: flex; gap: 3rem; align-items: flex-start; }
.wie-label { width: 220px; flex-shrink: 0; }
.wie-namen { flex: 1; max-width: 680px; display: grid; grid-template-columns: 1fr 1fr; column-gap: 2.5rem; }
.bestuur-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; padding-bottom: 1rem; }
@media (max-width: 767px) {
    .wie-photo-strip {
        flex-direction: column !important;
        height: auto !important;
    }
    .wie-photo-strip > div { aspect-ratio: 3 / 2; }
}
@media (max-width: 640px) {
    .wie-rij { flex-direction: column; gap: 0.35rem; }
    .wie-label { width: auto; }
    .wie-namen { grid-template-columns: 1fr; }
    .bestuur-grid { grid-template-columns: 1fr; gap: 2rem; }
}
</style>

@endsection
