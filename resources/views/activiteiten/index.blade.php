@extends('layouts.app')

@section('title', __('pages.home_title'))

@section('content')

{{-- HERO --}}
<section style="background-color: white; overflow: hidden;">
    <div class="hero-inner" style="display: flex; align-items: stretch; height: 468px;">

        {{-- Copy — left aligned, flush with nav --}}
        <div class="hero-copy" style="flex: 1; display: flex; align-items: center;">
            <div style="max-width: 72rem; width: 100%; margin: 0 auto; padding: 3rem 1.5rem;">
                <x-eyebrow mb="1rem">Noordwijk · Brussel</x-eyebrow>
                <h1 style="font-family: var(--font-sans); font-size: 4.5rem; font-weight: 900; line-height: 1.05; color: var(--color-brand-dark); margin-bottom: 0.6rem;">
                    Dienstencentrum<br>Restaurant Social
                </h1>
                <h2 style="font-family: var(--font-sans); font-size: 2.5rem; font-weight: 900; color: var(--color-brand-green); line-height: 1.2;">
                    Quartier Noordwijk
                </h2>
            </div>
        </div>

        {{-- Right illustration --}}
        <div class="hero-col-image" style="flex: 0 0 auto; overflow: hidden;">
            <img src="{{ asset('images/header-illustration.png') }}" alt=""
                 style="height: 100%; width: auto; display: block;">
        </div>

    </div>
</section>

{{-- COMMUNITY PHOTO STRIP --}}
<section style="padding: 0;">
    <div style="display: flex; height: 320px; overflow: hidden;">
        <div style="flex: 1; overflow: hidden;">
            <img src="{{ asset('images/photo-groep-tafel.webp') }}" alt=""
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>
        <div style="flex: 1; overflow: hidden;">
            <img src="{{ asset('images/photo-party.webp') }}" alt=""
                 style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center bottom;">
        </div>
        <div style="flex: 1; overflow: hidden;">
            <img src="{{ asset('images/photo-groep-actief.webp') }}" alt=""
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>
    </div>
</section>

{{-- SECTION 1: Restaurant — text left, photo right --}}
<section class="section-pad" style="background-color: #f2f6fb; padding: 7rem 1.5rem;">
    <div class="section-flex" style="max-width: 72rem; margin: 0 auto; display: flex; align-items: center; gap: 4rem;">

        <div style="flex: 1;">
            <x-eyebrow color="orange" mb="0.75rem">{{ __('pages.home_restaurant_label') }}</x-eyebrow>
            <h2 style="font-family: var(--font-sans); font-size: 1.875rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 0.75rem;">
                {{ __('pages.home_restaurant_heading') }}
            </h2>
            <p style="font-size: 1.125rem; line-height: 1.6; color: var(--color-brand-dark); margin-bottom: 1.5rem;">
                {!! __('pages.home_restaurant_body') !!}
            </p>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               style="color: var(--color-brand-blue); font-family: var(--font-sans); font-weight: 700; font-size: 1.0625rem; text-decoration: underline;">
                {{ __('pages.home_restaurant_cta') }}
            </a>
        </div>

        <div class="section-image" style="flex: 0 0 44%; overflow: hidden; aspect-ratio: 4/3;">
            <img src="{{ asset('images/photo-restaurant-vol.webp') }}" alt="{{ __('pages.home_restaurant_label') }}"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>

    </div>
</section>

{{-- SECTION 2: Activities — photo left, text right --}}
<section class="section-pad" style="background-color: white; padding: 7rem 1.5rem;">
    <div class="section-flex" style="max-width: 72rem; margin: 0 auto; display: flex; align-items: center; gap: 4rem;">

        <div class="section-image" style="flex: 0 0 44%; overflow: hidden; aspect-ratio: 4/3;">
            <img src="{{ asset('images/photo-thumbsup.webp') }}" alt="{{ __('nav.activities') }}"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>

        <div style="flex: 1;">
            <x-eyebrow mb="0.75rem">{{ __('nav.activities') }}</x-eyebrow>
            <h2 style="font-family: var(--font-sans); font-size: 1.875rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 0.75rem;">
                {{ __('pages.home_activities_heading') }}
            </h2>
            <p style="font-size: 1.125rem; line-height: 1.6; color: var(--color-brand-dark); margin-bottom: 1.5rem;">
                {!! __('pages.home_activities_body') !!}
            </p>

            {{-- Upcoming activities --}}
            <div style="margin-bottom: 1.5rem;">
                @forelse ($activiteiten as $activiteit)
                    <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                       style="display: block; padding: 0.75rem 0; text-decoration: none; {{ !$loop->last ? 'border-bottom: 1px solid rgba(216,211,210,0.7);' : '' }}">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 700; font-size: 1.25rem; line-height: 1.2; color: var(--color-brand-blue); font-family: var(--font-sans);">
                                {{ $activiteit->titel }}
                            </span>
                            @if ($activiteit->status->value === 'geannuleerd')
                                <x-badge type="geannuleerd">&times;</x-badge>
                            @endif
                        </div>
                        <p style="font-size: 0.9375rem; margin: 0.2rem 0 0; color: var(--color-brand-muted);">
                            {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('dddd')) }}
                            {{ $activiteit->datum->format('j/n') }}
                            {{ __('activities.at') }} {{ substr($activiteit->startuur, 0, 5) }}
                            @if ($activiteit->einduur)
                                &ndash; {{ substr($activiteit->einduur, 0, 5) }}
                            @endif
                            <span style="color: var(--color-brand-gray-dark);">&middot;</span> {{ $activiteit->locatie }}
                        </p>
                    </a>
                @empty
                    <p style="padding: 1.5rem 0; color: var(--color-brand-muted);">
                        {{ __('activities.no_upcoming') }}
                    </p>
                @endforelse
            </div>

            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               style="color: var(--color-brand-green); font-family: var(--font-sans); font-weight: 700; font-size: 1.0625rem; text-decoration: underline;">
                {{ __('activities.all') }} →
            </a>
        </div>

    </div>
</section>

{{-- SECTION 3: Services — text left, photo right --}}
<section class="section-pad" style="background-color: white; padding: 7rem 1.5rem;">
    <div class="section-flex" style="max-width: 72rem; margin: 0 auto; display: flex; align-items: center; gap: 4rem;">

        <div style="flex: 1;">
            <x-eyebrow color="blue" mb="0.75rem">{{ __('nav.services') }}</x-eyebrow>
            <h2 style="font-family: var(--font-sans); font-size: 1.875rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 0.75rem;">
                {{ __('pages.home_services_heading') }}
            </h2>
            <p style="font-size: 1.125rem; line-height: 1.6; color: var(--color-brand-dark); margin-bottom: 1.5rem;">
                {!! __('pages.home_services_body') !!}
            </p>
            <a href="{{ route(app()->getLocale() . '.diensten') }}"
               style="color: var(--color-brand-dark); font-family: var(--font-sans); font-weight: 700; font-size: 1.0625rem; text-decoration: underline;">
                {{ __('pages.home_services_cta') }}
            </a>
        </div>

        <div class="section-image" style="flex: 0 0 44%; overflow: hidden; aspect-ratio: 4/3;">
            <img src="{{ asset('images/photo-samen.webp') }}" alt="{{ __('nav.services') }}"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>

    </div>
</section>

{{-- CONTACT / OPENING HOURS --}}
<section id="contact" class="section-pad" style="background-color: var(--color-brand-bg-tint); padding: 7rem 1.5rem;">
    <div class="section-flex" style="max-width: 72rem; margin: 0 auto; display: flex; align-items: center; gap: 4rem;">

        <div class="section-image" style="flex: 0 0 44%; overflow: hidden; aspect-ratio: 4/3;">
            <img src="{{ asset('images/photo-gebouw.webp') }}"
                 alt="De Harmonie — Antwerpsesteenweg"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>

        <div style="flex: 1;">
            <x-eyebrow mb="0.15rem">CONTACT &amp; {{ __('pages.home_hours_label') }}</x-eyebrow>
            <h2 style="font-family: var(--font-sans); font-size: 2.25rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.75rem;">
                {{ __('activities.visit_us') }}
            </h2>
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 1rem; font-size: 1.125rem; color: var(--color-brand-dark); font-weight: 600;">
                    <img src="{{ asset('images/icon-clock.svg') }}" alt="" style="width: 26px; height: 26px; flex-shrink: 0;">
                    {{ __('pages.home_hours_weekdays') }}
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; font-size: 1.125rem; color: var(--color-brand-dark); font-weight: 600;">
                    <img src="{{ asset('images/icon-clock.svg') }}" alt="" style="width: 26px; height: 26px; flex-shrink: 0;">
                    {{ __('pages.home_hours_saturday') }}
                </div>
            </div>
            <p style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-muted); margin-bottom: 2rem;">
                {{ __('pages.home_hours_body') }}
            </p>
            <p style="font-size: 1.125rem; color: var(--color-brand-muted); margin-bottom: 1.25rem; line-height: 1.6;">
                VZW Buurtwerk Noordwijk<br>
                Antwerpsesteenweg 24 — 1000 Brussel
            </p>
            <p style="margin-bottom: 0.4rem;">
                <a href="tel:0220328048" style="font-size: 1.25rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: none;">
                    02/203.28.48
                </a>
            </p>
            <p style="font-size: 1.125rem; color: var(--color-brand-blue);">
                <a href="mailto:info@deharmonie.be" style="text-decoration: none; color: inherit;">info@deharmonie.be</a>
            </p>
        </div>

    </div>
</section>

<style>
/* sm — mobile */
@media (max-width: 767px) {
    .hero-inner { height: auto !important; }
    .hero-col-image { display: none; }
    .hero-copy { padding: 3rem 1.5rem !important; }
    .hero-copy h1 { font-size: 2.75rem !important; }
    .hero-copy h2 { font-size: 1.625rem !important; }
    .section-flex { flex-direction: column !important; gap: 2rem !important; }
    .section-image { width: 100% !important; aspect-ratio: 16/9 !important; flex: none !important; }
    .section-pad { padding: 3.5rem 1.25rem !important; }
}
/* md — tablet */
@media (min-width: 768px) and (max-width: 1023px) {
    .hero-inner { height: 340px !important; }
    .hero-copy h1 { font-size: 3.25rem !important; }
    .hero-copy h2 { font-size: 1.875rem !important; }
}
/* lg — small desktop */
@media (min-width: 1024px) and (max-width: 1279px) {
    .hero-inner { height: 410px !important; }
    .hero-copy h1 { font-size: 4rem !important; }
    .hero-copy h2 { font-size: 2.2rem !important; }
}
/* xl — large desktop (default inline styles apply: 468px / 4.5rem / 2.5rem) */
</style>

@endsection
