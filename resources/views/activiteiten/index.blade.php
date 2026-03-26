@extends('layouts.app')

@section('title', __('pages.home_title'))

@section('content')

{{-- HERO --}}
<section style="background-color: white; overflow: hidden; position: relative;">

    {{-- Right illustration: full height, flush to viewport right edge --}}
    <div class="hero-col-image" style="position: absolute; top: 0; right: 0; height: 100%; z-index: 0; overflow: hidden;">
        <img src="{{ asset('images/illustration-header.png') }}" alt=""
             style="height: 100%; width: auto; display: block;">
    </div>

    {{-- Copy: aligned with nav and all other sections --}}
    <div class="hero-inner" style="max-width: 72rem; margin: 0 auto; min-height: 400px; display: flex; align-items: center; position: relative; z-index: 1;">
        <div class="hero-copy" style="padding: 3rem 1.5rem; width: 58%;">
            <x-eyebrow color="blue" mb="1rem">{{ __('pages.home_hero_eyebrow') }}</x-eyebrow>
            <h1 style="font-family: var(--font-sans); font-size: clamp(2.5rem, 5.5vw, 4rem); font-weight: 900; line-height: 1.05; color: var(--color-brand-dark); margin-bottom: 1rem;">
                {{ __('pages.home_hero_heading_line1') }}<br>
                {{ __('pages.home_hero_heading_line2') }}<br>
                {{ __('pages.home_hero_heading_line3') }}
            </h1>
            <p style="font-size: 1.375rem; line-height: 1.6; color: var(--color-brand-dark); font-weight: 400; margin-bottom: 1.75rem; max-width: 38rem;">
                {{ __('pages.home_hero_subheading') }}
            </p>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
                   style="background: var(--color-brand-blue); color: white; padding: 0.75rem 1.5rem; border-radius: 6px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none;">
                    {{ __('pages.home_hero_cta_menu') }}
                </a>
                <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
                   style="background: transparent; color: var(--color-brand-blue); padding: 0.75rem 1.5rem; border-radius: 6px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none; border: 2px solid var(--color-brand-blue);">
                    {{ __('pages.home_hero_cta_activities') }}
                </a>
            </div>
        </div>
    </div>

</section>

{{-- COMMUNITY PHOTO STRIP --}}
<section style="padding: 0;">
    <div style="display: flex; height: 320px; overflow: hidden;">
        <div style="flex: 1; overflow: hidden;">
            <img src="{{ asset('images/photo-groep-tafel.webp') }}" alt="{{ __('pages.home_photo_groep_tafel_alt') }}"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>
        <div style="flex: 1; overflow: hidden;">
            <img src="{{ asset('images/photo-party.webp') }}" alt="{{ __('pages.home_photo_party_alt') }}"
                 style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center bottom;">
        </div>
        <div style="flex: 1; overflow: hidden;">
            <img src="{{ asset('images/photo-groep-actief.webp') }}" alt="{{ __('pages.home_photo_groep_actief_alt') }}"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>
    </div>
</section>

{{-- MENU PREVIEW (static — to be wired to Weekmenu model in future) --}}
<section style="background-color: #fff8f5; padding: 4rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="orange" mb="0.75rem">{{ __('pages.home_menu_label') }}</x-eyebrow>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <x-section-heading>{{ __('pages.home_menu_preview_heading') }}</x-section-heading>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               style="background: var(--color-brand-orange); color: white; padding: 0.5rem 1.25rem; border-radius: 999px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none; white-space: nowrap; flex-shrink: 0;">
                {{ __('pages.home_menu_link') }}
            </a>
        </div>
        {{-- TODO: Replace static content with dynamic Weekmenu model query --}}
        <div class="menu-cards" style="display: flex; gap: 1rem;">
            {{-- Today --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.5rem 1.75rem; border: 1px solid #e8e0d8;">
                <p style="font-size: 0.875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-orange); margin-bottom: 0.4rem;">{{ __('activities.date_today') }}</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Kalf blanket met bulgur</p>
                <p style="font-size: 1rem; color: var(--color-brand-muted); margin-bottom: 0.75rem;">{{ __('pages.home_menu_soup_included') }}</p>
                <p style="font-size: 1.25rem; font-weight: 900; color: var(--color-brand-orange); font-family: var(--font-sans);">€ 10</p>
            </div>
            {{-- Tomorrow --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.5rem 1.75rem; border: 1px solid #e8e0d8;">
                <p style="font-size: 0.875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-orange); margin-bottom: 0.4rem;">{{ __('activities.date_tomorrow') }}</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Varkensgebraad met gestoofd witloof</p>
                <p style="font-size: 1rem; color: var(--color-brand-muted); margin-bottom: 0.75rem;">{{ __('pages.home_menu_soup_included') }}</p>
                <p style="font-size: 1.25rem; font-weight: 900; color: var(--color-brand-orange); font-family: var(--font-sans);">€ 9</p>
            </div>
        </div>
    </div>
</section>

{{-- UPCOMING ACTIVITIES --}}
<section style="background-color: white; padding: 5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <x-eyebrow mb="0.75rem">{{ __('nav.activities') }}</x-eyebrow>
                <x-section-heading>{{ __('pages.home_activities_heading') }}</x-section-heading>
            </div>
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               style="background: var(--color-brand-green); color: white; padding: 0.5rem 1.25rem; border-radius: 999px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none; white-space: nowrap; flex-shrink: 0;">
                {{ __('activities.all') }} →
            </a>
        </div>
        <div class="activity-cards-grid" style="display: flex; gap: 1rem;">
            @forelse ($activiteiten as $activiteit)
                @php
                    $colors = ['#f3dbd5','#d4e8df','#d5e0f0'];
                    $bg = $colors[$loop->index % count($colors)];
                    $imgUrl = $activiteit->getFirstMediaUrl('afbeelding');
                @endphp
                <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                   style="flex: 1; display: block; text-decoration: none; border-radius: 10px; overflow: hidden; border: 1px solid #e8e0d8; {{ $activiteit->status->value === 'geannuleerd' ? 'opacity: 0.7;' : '' }}">
                    {{-- Photo or date band --}}
                    <div style="height: 160px; background: {{ $bg }}; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center;">
                        @if ($imgUrl)
                            <img src="{{ $imgUrl }}" alt="" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block;">
                        @else
                            <div style="text-align: center; color: rgba(44,40,38,0.2);">
                                <p style="font-family: var(--font-sans); font-size: 4rem; font-weight: 900; line-height: 1; margin: 0;">{{ \Carbon\Carbon::parse($activiteit->datum)->format('d') }}</p>
                                <p style="font-family: var(--font-sans); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; margin: 0.25rem 0 0;">{{ strtoupper(\Carbon\Carbon::parse($activiteit->datum)->locale(app()->getLocale())->isoFormat('MMM')) }}</p>
                            </div>
                        @endif
                    </div>
                    {{-- Card body --}}
                    <div style="padding: 1rem 1.25rem 1.5rem; background: var(--color-brand-bg);">
                        <p style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-green); margin: 0 0 0.3rem;">
                            <x-relative-date :datum="$activiteit->datum" />
                        </p>
                        <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--color-brand-dark); line-height: 1.2; margin: 0 0 0.25rem;">
                            {{ $activiteit->titel }}
                            @if ($activiteit->status->value === 'geannuleerd')
                                <x-badge type="geannuleerd" />
                            @endif
                        </h3>
                        <p style="font-size: 0.9rem; color: var(--color-brand-muted); margin: 0;">
                            {{ substr($activiteit->startuur, 0, 5) }} · {{ $activiteit->locatie }}
                        </p>
                    </div>
                </a>
            @empty
                <p style="color: var(--color-brand-muted); padding: 1rem 0;">{{ __('activities.no_upcoming') }}</p>
            @endforelse
        </div>
    </div>
</section>

{{-- SOCIAL PROOF PHOTO STRIP --}}
<div class="social-proof-strip" style="display: flex; height: 260px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-thumbsup.webp') }}" alt="{{ __('pages.home_photo_thumbsup_alt') }}"
             style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center top;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-samen.webp') }}" alt="{{ __('pages.home_photo_samen_alt') }}"
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-feest-2.webp') }}" alt="{{ __('pages.home_photo_feest_alt') }}"
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
</div>

{{-- SERVICES — home visit focus --}}
<section style="background-color: #eef2f8; padding: 5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="blue" mb="0.75rem">{{ __('nav.services') }}</x-eyebrow>
        <x-section-heading mb="1.25rem">{{ __('pages.home_services_section_heading') }}</x-section-heading>
        <p style="font-size: 1.125rem; line-height: 1.75; color: var(--color-brand-muted); max-width: 44rem; margin-bottom: 1.75rem;">
            {{ __('pages.home_services_intro') }}
        </p>
        <a href="{{ route(app()->getLocale() . '.diensten') }}"
           style="font-size: 1rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
            {{ __('pages.home_services_cta') }}
        </a>
    </div>
</section>

{{-- PRACTICAL INFO --}}
<section id="contact" style="background-color: var(--color-brand-bg); padding: 5rem 1.5rem 6rem;">
    <div style="max-width: 72rem; margin: 0 auto; display: flex; gap: 4rem; align-items: center;">

        {{-- Building photo --}}
        <div class="contact-photo" style="flex: 0 0 300px; height: 260px; overflow: hidden; border-radius: 12px;">
            <img src="{{ asset('images/photo-gebouw.webp') }}" alt="Het gebouw van De Harmonie"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>

        {{-- Info columns --}}
        <div class="practical-grid" style="flex: 1; display: flex; gap: 5rem; align-items: start;">
            <div>
                <p style="font-size: 0.875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.35rem;">
                    {{ __('pages.home_practical_address_label') }}
                </p>
                {{-- address and contact are locale-invariant factual data --}}
                <p style="font-size: 1.125rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.5;">
                    Antwerpsesteenweg 24<br>1000 Brussel
                </p>
            </div>
            <div>
                <p style="font-size: 0.875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.35rem;">
                    {{ __('pages.home_practical_hours_label') }}
                </p>
                <p style="font-size: 1.125rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.5;">
                    {{ __('pages.home_hours_weekdays') }}<br>{{ __('pages.home_hours_saturday') }}
                </p>
            </div>
            <div>
                <p style="font-size: 0.875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.35rem;">
                    {{ __('pages.home_practical_contact_label') }}
                </p>
                <p style="font-size: 1.125rem; line-height: 1.6;">
                    <a href="tel:0220328048" style="font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">02/203.28.48</a><br>
                    <a href="mailto:info@deharmonie.be" style="color: var(--color-brand-blue); text-decoration: underline;">info@deharmonie.be</a>
                </p>
            </div>
        </div>

    </div>
</section>

<style>
/* sm — mobile */
@media (max-width: 767px) {
    .hero-inner { min-height: auto !important; }
    .hero-col-image { display: none; }
    .hero-copy { width: 100% !important; padding: 2.5rem 1.25rem !important; }
    .menu-cards { flex-direction: column !important; }
    .activity-cards-grid { flex-direction: column !important; }
.practical-grid { flex-direction: column !important; gap: 1.5rem !important; }
    .social-proof-strip { display: none; }
    .contact-photo { display: none; }
}
</style>

@endsection
