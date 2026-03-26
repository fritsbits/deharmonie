@extends('layouts.app')

@section('title', __('pages.home_title'))

@section('content')

{{-- HERO --}}
<section style="background-color: white; overflow: hidden;">
    <div class="hero-inner" style="display: flex; align-items: stretch; min-height: 400px;">

        {{-- Copy --}}
        <div class="hero-copy" style="flex: 1; display: flex; align-items: center;">
            <div style="max-width: 72rem; width: 100%; margin: 0 auto; padding: 3rem 1.5rem;">
                <x-eyebrow mb="1rem">{{ __('pages.home_hero_eyebrow') }}</x-eyebrow>
                <h1 style="font-family: var(--font-sans); font-size: 4rem; font-weight: 900; line-height: 1.05; color: var(--color-brand-dark); margin-bottom: 1rem;">
                    {{ __('pages.home_hero_heading_line1') }}<br>
                    {{ __('pages.home_hero_heading_line2') }}<br>
                    {{ __('pages.home_hero_heading_line3') }}
                </h1>
                <p style="font-size: 1.125rem; line-height: 1.6; color: var(--color-brand-muted); margin-bottom: 1.75rem; max-width: 38rem;">
                    {{ __('pages.home_hero_subheading') }}
                </p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
                       style="background: var(--color-brand-orange); color: white; padding: 0.75rem 1.5rem; border-radius: 6px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none;">
                        {{ __('pages.home_hero_cta_activities') }}
                    </a>
                    <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
                       style="background: transparent; color: var(--color-brand-blue); padding: 0.75rem 1.5rem; border-radius: 6px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none; border: 2px solid var(--color-brand-blue);">
                        {{ __('pages.home_hero_cta_menu') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Right photo --}}
        <div class="hero-col-image" style="flex: 0 0 42%; overflow: hidden;">
            <img src="{{ asset('images/photo-restaurant-vol.webp') }}" alt=""
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
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

{{-- MENU PREVIEW (static — to be wired to Weekmenu model in future) --}}
<section style="background-color: #fff8f5; border-top: 3px solid var(--color-brand-orange); padding: 2.5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="orange" mb="0.5rem">{{ __('pages.home_menu_label') }}</x-eyebrow>
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.25rem;">
            <h2 style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 900; color: var(--color-brand-dark);">
                {{ __('pages.home_menu_preview_heading') }}
            </h2>
        </div>
        {{-- TODO: Replace static content with dynamic Weekmenu model query --}}
        <div class="menu-cards" style="display: flex; gap: 1rem;">
            {{-- Today --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.25rem 1.5rem; border: 1px solid #e8e0d8; position: relative;">
                <span style="position: absolute; top: -10px; left: 1rem; background: var(--color-brand-orange); color: white; font-size: 0.6875rem; font-weight: 800; padding: 2px 10px; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.06em;">
                    {{ __('pages.home_menu_today_badge') }}
                </span>
                <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.4rem;">Maandag 30/03</p>
                <p style="font-size: 1rem; font-weight: 700; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Kalf blanket met bulgur</p>
                <p style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-bottom: 0.75rem;">{{ __('pages.home_menu_soup_included') }}</p>
                <p style="font-size: 1.25rem; font-weight: 900; color: var(--color-brand-orange); font-family: var(--font-sans);">€ 10</p>
            </div>
            {{-- Tomorrow --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.25rem 1.5rem; border: 1px solid #e8e0d8;">
                <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.4rem;">Dinsdag 31/03</p>
                <p style="font-size: 1rem; font-weight: 700; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Varkensgebraad met gestoofd witloof</p>
                <p style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-bottom: 0.75rem;">{{ __('pages.home_menu_soup_included') }}</p>
                <p style="font-size: 1.25rem; font-weight: 900; color: var(--color-brand-orange); font-family: var(--font-sans);">€ 9</p>
            </div>
        </div>
        <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
           style="display: inline-block; margin-top: 1rem; font-size: 0.9375rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
            {{ __('pages.home_menu_link') }}
        </a>
    </div>
</section>

{{-- UPCOMING ACTIVITIES --}}
<section style="background-color: white; padding: 3.5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.25rem;">
            <div>
                <x-eyebrow mb="0.4rem">{{ __('nav.activities') }}</x-eyebrow>
                <h2 style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 900; color: var(--color-brand-dark);">
                    {{ __('pages.home_activities_heading') }}
                </h2>
            </div>
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               style="font-family: var(--font-sans); font-weight: 700; font-size: 0.9375rem; color: var(--color-brand-green); text-decoration: underline; white-space: nowrap;">
                {{ __('activities.all') }} →
            </a>
        </div>
        <div class="activity-cards-grid" style="display: flex; gap: 1rem;">
            @forelse ($activiteiten as $activiteit)
                <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                   style="flex: 1; display: block; background: var(--color-brand-bg); border: 1px solid #e8e0d8; border-radius: 8px; padding: 1.25rem 1.25rem; text-decoration: none; {{ $activiteit->status->value === 'geannuleerd' ? 'opacity: 0.6;' : '' }}">
                    <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-orange); margin-bottom: 0.35rem;">
                        {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('ddd')) }}
                        {{ $activiteit->datum->format('j/n') }}
                    </p>
                    <p style="font-size: 1rem; font-weight: 800; color: var(--color-brand-dark); line-height: 1.2; margin-bottom: 0.35rem;">
                        {{ $activiteit->titel }}
                        @if ($activiteit->status->value === 'geannuleerd')
                            <x-badge type="geannuleerd" />
                        @endif
                    </p>
                    <p style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-bottom: 0.75rem;">
                        {{ substr($activiteit->startuur, 0, 5) }} · {{ $activiteit->locatie }}
                    </p>
                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
                        {{ __('activities.register') }} →
                    </span>
                </a>
            @empty
                <p style="color: var(--color-brand-muted); padding: 1rem 0;">{{ __('activities.no_upcoming') }}</p>
            @endforelse
        </div>
    </div>
</section>

{{-- WHAT WE DO — SERVICE CARDS --}}
<section style="background-color: #f2f6fb; padding: 3.5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow mb="0.4rem">{{ __('nav.services') }}</x-eyebrow>
        <h2 style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 900; color: var(--color-brand-dark); margin-bottom: 1.5rem;">
            {{ __('pages.home_services_section_heading') }}
        </h2>
        <div class="service-cards-grid" style="display: flex; gap: 1rem; align-items: stretch;">
            {{-- Restaurant --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.5rem; border-bottom: 3px solid var(--color-brand-orange);">
                <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-dark); margin-bottom: 0.5rem;">
                    {{ __('pages.home_service_restaurant_title') }}
                </p>
                <p style="font-size: 0.9375rem; color: var(--color-brand-muted); line-height: 1.5; margin-bottom: 0.75rem;">
                    {{ __('pages.home_service_restaurant_body') }}
                </p>
                <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-orange); margin-bottom: 0.75rem;">
                    {{ __('pages.home_service_restaurant_price') }}
                </p>
                <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
                   style="font-size: 0.875rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
                    {{ __('pages.home_service_restaurant_link') }}
                </a>
            </div>
            {{-- Activities --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.5rem; border-bottom: 3px solid var(--color-brand-green);">
                <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-dark); margin-bottom: 0.5rem;">
                    {{ __('pages.home_service_activities_title') }}
                </p>
                <p style="font-size: 0.9375rem; color: var(--color-brand-muted); line-height: 1.5; margin-bottom: 0.75rem;">
                    {{ __('pages.home_service_activities_body') }}
                </p>
                <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
                   style="font-size: 0.875rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
                    {{ __('pages.home_service_activities_link') }}
                </a>
            </div>
            {{-- Home services --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.5rem; border-bottom: 3px solid var(--color-brand-blue);">
                <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-dark); margin-bottom: 0.5rem;">
                    {{ __('pages.home_service_home_title') }}
                </p>
                <p style="font-size: 0.9375rem; color: var(--color-brand-muted); line-height: 1.5; margin-bottom: 0.75rem;">
                    {{ __('pages.home_service_home_body') }}
                </p>
                <a href="{{ route(app()->getLocale() . '.diensten') }}"
                   style="font-size: 0.875rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
                    {{ __('pages.home_service_home_link') }}
                </a>
            </div>
        </div>
    </div>
</section>

{{-- PRACTICAL INFO --}}
<section id="contact" style="background-color: var(--color-brand-bg); border-top: 1px solid #e8e0d8; padding: 2rem 1.5rem;">
    <div class="practical-grid" style="max-width: 72rem; margin: 0 auto; display: flex; gap: 3rem; align-items: start;">
        <div>
            <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-muted); margin-bottom: 0.35rem;">
                {{ __('pages.home_practical_address_label') }}
            </p>
            {{-- address and contact are locale-invariant factual data --}}
            <p style="font-size: 0.9375rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.5;">
                Antwerpsesteenweg 24<br>1000 Brussel
            </p>
        </div>
        <div>
            <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-muted); margin-bottom: 0.35rem;">
                {{ __('pages.home_practical_hours_label') }}
            </p>
            <p style="font-size: 0.9375rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.5;">
                {{ __('pages.home_hours_weekdays') }}<br>{{ __('pages.home_hours_saturday') }}
            </p>
        </div>
        <div>
            <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-muted); margin-bottom: 0.35rem;">
                {{ __('pages.home_practical_contact_label') }}
            </p>
            <p style="font-size: 0.9375rem; line-height: 1.6;">
                <a href="tel:0220328048" style="font-weight: 700; color: var(--color-brand-blue); text-decoration: none;">02/203.28.48</a><br>
                <a href="mailto:info@deharmonie.be" style="color: var(--color-brand-blue); text-decoration: none;">info@deharmonie.be</a>
            </p>
        </div>
    </div>
</section>

<style>
/* sm — mobile */
@media (max-width: 767px) {
    .hero-inner { flex-direction: column !important; min-height: auto !important; }
    .hero-col-image { display: none; }
    .hero-copy div { padding: 2.5rem 1.25rem !important; }
    .hero-copy h1 { font-size: 2.75rem !important; }
    .menu-cards { flex-direction: column !important; }
    .activity-cards-grid { flex-direction: column !important; }
    .service-cards-grid { flex-direction: column !important; }
    .practical-grid { flex-direction: column !important; gap: 1.5rem !important; }
}
/* md — tablet */
@media (min-width: 768px) and (max-width: 1023px) {
    .hero-copy h1 { font-size: 3rem !important; }
}
</style>

@endsection
