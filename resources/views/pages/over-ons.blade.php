@extends('layouts.app')
@section('title', __('pages.over_ons_title'))
@section('content')

{{-- HERO --}}
<x-page-hero
    :eyebrow="__('pages.over_ons_eyebrow')"
    eyebrow-color="blue"
    :heading="__('pages.over_ons_heading')"
    :lead="__('pages.over_ons_lead')"
/>

{{-- MISSION STORY --}}
<section style="background: var(--color-brand-blue-tint); padding: 4rem 0;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">

        <x-eyebrow size="sm" color="blue" mb="0.5rem">{{ __('pages.over_ons_verhaal_eyebrow') }}</x-eyebrow>

        <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2.25rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 2rem;">
            {{ __('pages.over_ons_verhaal_heading') }}
        </h2>

        <div style="max-width: 48rem;">
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
    </div>
</section>

{{-- PHOTO STRIP --}}
<div style="display: flex; height: 280px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-samen.webp') }}" alt="{{ __('pages.over_ons_photo_samen_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-buiten-event.webp') }}" alt="{{ __('pages.over_ons_photo_buiten_event_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
    <div class="over-ons-photo-strip-third" style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-groep-actief.webp') }}" alt="{{ __('pages.over_ons_photo_groep_actief_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
</div>

{{-- VISITOR VOICES --}}
<section style="background: white; padding: 5rem 0;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">

        <div style="text-align: center; margin-bottom: 3rem;">
            <x-eyebrow size="sm" color="blue" mb="0.5rem">{{ __('pages.over_ons_quotes_eyebrow') }}</x-eyebrow>
            <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2.25rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15;">
                {{ __('pages.over_ons_quotes_heading') }}
            </h2>
        </div>

        <div class="over-ons-quotes-grid" style="display: flex; gap: 1.5rem;">

            {{-- Card 1: green --}}
            <div class="over-ons-quote-card" style="flex: 1; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(44,40,38,.09), 0 8px 28px rgba(44,40,38,.11); background: white;">
                <div style="position: relative; background: var(--color-brand-green); padding: 1.5rem 1.75rem 1.25rem;">
                    <div style="color: white; font-size: 1.5rem; letter-spacing: 0.1em; position: relative; z-index: 1;">★★★★★</div>
                    <span aria-hidden="true" style="font-family: Georgia, serif; font-size: 8rem; line-height: 1; color: #5a8a74; position: absolute; bottom: -2.75rem; right: 1.25rem; pointer-events: none; user-select: none; font-weight: 900; z-index: 2;">&rdquo;</span>
                </div>
                <div style="background: white; padding: 3.5rem 1.75rem 1.75rem;">
                    <p style="font-size: 1.0625rem; line-height: 1.75; font-style: italic; color: var(--color-brand-dark); margin-bottom: 1.25rem;">"Hier wordt met veel moed en inzet elke dag gewerkt. Ook met allerlei activiteiten kunnen mensen zich amuseren of iets bijleren."</p>
                    <p style="font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; color: var(--color-brand-green); text-transform: uppercase; letter-spacing: 0.08em;">— Josiane C.</p>
                </div>
            </div>

            {{-- Card 2: blue --}}
            <div class="over-ons-quote-card" style="flex: 1; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(44,40,38,.09), 0 8px 28px rgba(44,40,38,.11); background: white;">
                <div style="position: relative; background: var(--color-brand-blue); padding: 1.5rem 1.75rem 1.25rem;">
                    <div style="color: white; font-size: 1.5rem; letter-spacing: 0.1em; position: relative; z-index: 1;">★★★★★</div>
                    <span aria-hidden="true" style="font-family: Georgia, serif; font-size: 8rem; line-height: 1; color: #2f5490; position: absolute; bottom: -2.75rem; right: 1.25rem; pointer-events: none; user-select: none; font-weight: 900; z-index: 2;">&rdquo;</span>
                </div>
                <div style="background: white; padding: 3.5rem 1.75rem 1.75rem;">
                    <p style="font-size: 1.0625rem; line-height: 1.75; font-style: italic; color: var(--color-brand-dark); margin-bottom: 1.25rem;">"Un accueil hors du commun. Ils sont des piliers du quartier."</p>
                    <p style="font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; color: var(--color-brand-blue); text-transform: uppercase; letter-spacing: 0.08em;">— Marc P.</p>
                </div>
            </div>

            {{-- Card 3: orange --}}
            <div class="over-ons-quote-card" style="flex: 1; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(44,40,38,.09), 0 8px 28px rgba(44,40,38,.11); background: white;">
                <div style="position: relative; background: var(--color-brand-orange); padding: 1.5rem 1.75rem 1.25rem;">
                    <div style="color: white; font-size: 1.5rem; letter-spacing: 0.1em; position: relative; z-index: 1;">★★★★★</div>
                    <span aria-hidden="true" style="font-family: Georgia, serif; font-size: 8rem; line-height: 1; color: #b34a2d; position: absolute; bottom: -2.75rem; right: 1.25rem; pointer-events: none; user-select: none; font-weight: 900; z-index: 2;">&rdquo;</span>
                </div>
                <div style="background: white; padding: 3.5rem 1.75rem 1.75rem;">
                    <p style="font-size: 1.0625rem; line-height: 1.75; font-style: italic; color: var(--color-brand-dark); margin-bottom: 1.25rem;">"Comme d'habitude accueil super chaleureux. On s'y sent bien."</p>
                    <p style="font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; color: var(--color-brand-orange); text-transform: uppercase; letter-spacing: 0.08em;">— Hélène-Christine A.</p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- TEAM REFERENCE --}}
<section style="background: white; border-top: 1px solid #e8e5e2; padding: 3.5rem 0;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">

        <x-eyebrow size="sm" color="blue" mb="0.5rem">{{ __('pages.over_ons_team_eyebrow') }}</x-eyebrow>

        <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2.25rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 1rem;">
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
<section style="background: var(--color-brand-blue-tint); border-top: 1px solid #e8e5e2; padding: 3.5rem 2rem; text-align: center;">
    <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2.25rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 0.75rem;">
        {{ __('pages.over_ons_cta_heading') }}
    </h2>
    <p style="font-size: 1.125rem; line-height: 1.6; color: var(--color-brand-muted); margin-bottom: 2rem; max-width: 480px; margin-left: auto; margin-right: auto;">
        {{ __('pages.over_ons_cta_lead') }}
    </p>
    <a href="{{ route(app()->getLocale() . '.contact') }}" style="display:inline-block;background:var(--color-brand-blue);color:white;font-family:var(--font-sans);font-weight:800;font-size:0.9rem;padding:0.85rem 2rem;border-radius:3px;text-decoration:none;letter-spacing:0.04em;text-transform:uppercase;">
        {{ __('pages.over_ons_cta_btn') }}
    </a>
</section>

<style>
.over-ons-quote-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.over-ons-quote-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(70,121,188,0.16) !important; }

@media (max-width: 767px) {
    .over-ons-photo-strip-third { display: none !important; }
    .over-ons-quotes-grid { flex-direction: column !important; }
    .over-ons-team-lead { max-width: 100% !important; }
    .over-ons-team-link { width: 100%; text-align: center; display: block !important; }
}
</style>

@endsection
