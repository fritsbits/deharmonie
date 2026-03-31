@extends('layouts.app')
@section('title', __('pages.over_ons_title'))
@section('content')

{{-- HERO --}}
<x-page-hero
    :eyebrow="__('pages.over_ons_eyebrow')"
    :heading="__('pages.over_ons_heading')"
    :lead="__('pages.over_ons_lead')"
/>

{{-- MISSION STORY --}}
<section style="background: var(--color-brand-bg); padding: 4rem 0;">
    <div style="max-width: 900px; margin: 0 auto; padding: 0 2rem;">

        <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-green); margin-bottom: 0.5rem;">
            {{ __('pages.over_ons_verhaal_eyebrow') }}
        </p>

        <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2.25rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 2rem;">
            {{ __('pages.over_ons_verhaal_heading') }}
        </h2>

        <div class="over-ons-mission-grid" style="display: flex; gap: 3rem; align-items: flex-start;">

            <div style="flex: 0 0 60%;">
                <p style="font-size: 1.125rem; line-height: 1.75; color: var(--color-brand-dark); margin-bottom: 1.25rem;">
                    {{ __('pages.over_ons_verhaal_p1') }}
                </p>
                <p style="font-size: 1.125rem; line-height: 1.75; color: var(--color-brand-dark); margin-bottom: 1.25rem;">
                    {{ __('pages.over_ons_verhaal_p2') }}
                </p>
                <p style="font-size: 1.125rem; line-height: 1.75; color: var(--color-brand-dark); margin-bottom: 0;">
                    {{ __('pages.over_ons_verhaal_p3') }}
                </p>
            </div>

            <div class="over-ons-mission-photo" style="flex: 0 0 37%; aspect-ratio: 3/4; border-radius: 3px; overflow: hidden;">
                <img src="{{ asset('images/photo-groep-tafel.webp') }}" alt="Bezoekers aan tafel" style="width:100%;height:100%;object-fit:cover;display:block;">
            </div>

        </div>
    </div>
</section>

{{-- PHOTO STRIP --}}
<div style="display: flex; height: 280px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-samen.webp') }}" alt="{{ __('pages.over_ons_photo_samen_alt') }}" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-buiten-event.webp') }}" alt="{{ __('pages.over_ons_photo_buiten_event_alt') }}" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
    <div class="over-ons-photo-strip-third" style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-groep-actief.webp') }}" alt="{{ __('pages.over_ons_photo_groep_actief_alt') }}" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
</div>

{{-- VISITOR VOICES --}}
<section style="background: #eef2f8; padding: 4rem 0;">
    <div style="max-width: 900px; margin: 0 auto; padding: 0 2rem;">

        <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-blue); margin-bottom: 2rem;">
            {{ __('pages.over_ons_quotes_eyebrow') }}
        </p>

        <div class="over-ons-quotes-grid" style="display: flex; gap: 1.5rem;">

            <div style="flex: 1; background: white; border-radius: 4px; padding: 1.5rem; border: 1px solid rgba(70,121,188,0.12);">
                <div style="color: var(--color-brand-orange); font-size: 0.9rem; margin-bottom: 1rem;">★★★★★</div>
                <p style="font-size:1rem;line-height:1.65;font-style:italic;margin-bottom:1rem;">"Hier wordt met veel moed en inzet elke dag gewerkt. Ook met allerlei activiteiten kunnen mensen zich amuseren of iets bijleren."</p>
                <p style="font-family:var(--font-sans);font-size:0.8rem;font-weight:700;color:var(--color-brand-muted);text-transform:uppercase;letter-spacing:0.06em;">Josiane C.</p>
            </div>

            <div style="flex: 1; background: white; border-radius: 4px; padding: 1.5rem; border: 1px solid rgba(70,121,188,0.12);">
                <div style="color: var(--color-brand-orange); font-size: 0.9rem; margin-bottom: 1rem;">★★★★★</div>
                <p style="font-size:1rem;line-height:1.65;font-style:italic;margin-bottom:1rem;">"Un accueil hors du commun. Ils sont des piliers du quartier."</p>
                <p style="font-family:var(--font-sans);font-size:0.8rem;font-weight:700;color:var(--color-brand-muted);text-transform:uppercase;letter-spacing:0.06em;">Marc P.</p>
            </div>

            <div style="flex: 1; background: white; border-radius: 4px; padding: 1.5rem; border: 1px solid rgba(70,121,188,0.12);">
                <div style="color: var(--color-brand-orange); font-size: 0.9rem; margin-bottom: 1rem;">★★★★★</div>
                <p style="font-size:1rem;line-height:1.65;font-style:italic;margin-bottom:1rem;">"Comme d'habitude accueil super chaleureux. On s'y sent bien."</p>
                <p style="font-family:var(--font-sans);font-size:0.8rem;font-weight:700;color:var(--color-brand-muted);text-transform:uppercase;letter-spacing:0.06em;">Hélène-Christine A.</p>
            </div>

        </div>
    </div>
</section>

{{-- TEAM REFERENCE --}}
<section style="background: var(--color-brand-bg); border-top: 1px solid #e8e5e2; padding: 3.5rem 0;">
    <div style="max-width: 900px; margin: 0 auto; padding: 0 2rem;">

        <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-green); margin-bottom: 0.5rem;">
            {{ __('pages.over_ons_team_eyebrow') }}
        </p>

        <h2 style="font-family: var(--font-sans); font-size: clamp(1.375rem, 2.2vw, 2rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 1rem;">
            {{ __('pages.over_ons_team_heading') }}
        </h2>

        <p class="over-ons-team-lead" style="font-size: 1.0625rem; line-height: 1.7; color: var(--color-brand-muted); margin-bottom: 1.75rem; max-width: 480px;">
            {{ __('pages.over_ons_team_lead') }}
        </p>

        <a href="{{ route(app()->getLocale() . '.wie-is-wie') }}" class="over-ons-team-link" style="display:inline-block;font-family:var(--font-sans);font-size:0.875rem;font-weight:700;color:var(--color-brand-blue);border:1.5px solid var(--color-brand-blue);padding:0.6rem 1.25rem;border-radius:3px;text-decoration:none;letter-spacing:0.03em;">
            {{ __('pages.over_ons_team_cta') }} →
        </a>

    </div>
</section>

{{-- CTA BAND --}}
<section style="background: var(--color-brand-blue); padding: 3.5rem 2rem; text-align: center;">
    <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2.25rem); font-weight: 900; color: white; line-height: 1.15; margin-bottom: 0.75rem;">
        {{ __('pages.over_ons_cta_heading') }}
    </h2>
    <p style="font-size: 1.125rem; line-height: 1.6; color: rgba(255,255,255,0.85); margin-bottom: 2rem; max-width: 480px; margin-left: auto; margin-right: auto;">
        {{ __('pages.over_ons_cta_lead') }}
    </p>
    <a href="{{ route(app()->getLocale() . '.contact') }}" style="display:inline-block;background:var(--color-brand-orange);color:white;font-family:var(--font-sans);font-weight:800;font-size:0.9rem;padding:0.85rem 2rem;border-radius:3px;text-decoration:none;letter-spacing:0.04em;text-transform:uppercase;">
        {{ __('pages.over_ons_cta_btn') }}
    </a>
</section>

<style>
@media (max-width: 767px) {
    .over-ons-mission-grid { flex-direction: column !important; }
    .over-ons-mission-photo { aspect-ratio: 16/9 !important; width: 100% !important; flex: none !important; }
    .over-ons-photo-strip-third { display: none !important; }
    .over-ons-quotes-grid { flex-direction: column !important; }
    .over-ons-team-lead { max-width: 100% !important; }
    .over-ons-team-link { width: 100%; text-align: center; display: block !important; }
}
</style>

@endsection
