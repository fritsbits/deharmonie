@extends('layouts.app')

@section('title', __('pages.home_title'))

@section('content')

{{-- HERO --}}
<section style="background-color: white; overflow: hidden; position: relative;">

    {{-- Right illustration: full height, flush to viewport right edge --}}
    <div class="hero-col-image" style="position: absolute; top: 0; right: 0; height: 100%; z-index: 0; overflow: hidden;">
        <img src="{{ asset('images/illustration-header.webp') }}" alt=""
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
                   class="press-scale"
                   style="background: var(--color-brand-blue); color: white; padding: 0.75rem 1.5rem; border-radius: 6px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none;">
                    {{ __('pages.home_hero_cta_menu') }}
                </a>
                <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
                   class="press-scale"
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

{{-- MENU PREVIEW --}}
@if ($menuVandaag || $menuMorgen)
<section style="background-color: var(--color-brand-orange-tint); padding: 4rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="orange" mb="0.75rem">{{ __('pages.home_menu_label') }}</x-eyebrow>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <x-section-heading>{{ __('pages.home_menu_preview_heading') }}</x-section-heading>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               class="press-scale"
               style="background: var(--color-brand-orange); color: white; padding: 0.5rem 1.25rem; border-radius: 999px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none; white-space: nowrap; flex-shrink: 0;">
                {{ __('pages.home_menu_link') }}
            </a>
        </div>
        <div class="menu-cards" style="display: flex; gap: 1rem;">
            @php
                $priceTag = fn($price) => '<span class="tabular-nums" style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 900; color: var(--color-brand-muted); line-height: 1; white-space: nowrap; flex-shrink: 0;"><span style="font-size: 0.65em; vertical-align: baseline; margin-right: 1px;">€</span>' . $price . '</span>';
            @endphp
            {{-- Today --}}
            @if ($menuVandaag)
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.5rem 1.75rem; box-shadow: 0 1px 2px rgba(44,40,38,.04), 0 2px 10px rgba(44,40,38,.06), inset 0 0 0 1px rgba(44,40,38,.04);">
                <p style="font-size: 0.875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-orange); margin: 0 0 0.4rem;">{{ __('activities.date_today') }}</p>
                @if ($menuVandaag->special_event)
                    <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <p style="font-size: 1.5rem; font-weight: 700; color: var(--color-brand-dark); margin: 0;">{{ $menuVandaag->event_label }}</p>
                            <span style="display: inline-block; background: var(--color-brand-orange); color: white; font-family: var(--font-sans); font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; padding: 1px 7px; border-radius: 999px;">{{ __('weekmenu.special_badge') }}</span>
                        </div>
                        {!! $priceTag($menuVandaag->price) !!}
                    </div>
                    <p style="font-size: 1.125rem; color: var(--color-brand-muted); margin: 0; line-height: 1.6;">{!! implode('<span style="color: var(--color-brand-orange)"> · </span>', array_map('e', $menuVandaag->courses_for_locale)) !!}</p>
                @else
                    <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; margin-bottom: 0.25rem;">
                        <p style="font-size: 1.5rem; font-weight: 700; color: var(--color-brand-dark); margin: 0;">{{ $menuVandaag->main }}</p>
                        {!! $priceTag($menuVandaag->price) !!}
                    </div>
                    <p style="font-size: 1.125rem; color: var(--color-brand-muted); margin: 0;">{{ __('pages.home_menu_soup_included') }}</p>
                @endif
            </div>
            @endif
            {{-- Tomorrow --}}
            @if ($menuMorgen)
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.5rem 1.75rem; box-shadow: 0 1px 2px rgba(44,40,38,.04), 0 2px 10px rgba(44,40,38,.06), inset 0 0 0 1px rgba(44,40,38,.04);">
                <p style="font-size: 0.875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-orange); margin: 0 0 0.4rem;">{{ __('activities.date_tomorrow') }}</p>
                @if ($menuMorgen->special_event)
                    <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <p style="font-size: 1.5rem; font-weight: 700; color: var(--color-brand-dark); margin: 0;">{{ $menuMorgen->event_label }}</p>
                            <span style="display: inline-block; background: var(--color-brand-orange); color: white; font-family: var(--font-sans); font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; padding: 1px 7px; border-radius: 999px;">{{ __('weekmenu.special_badge') }}</span>
                        </div>
                        {!! $priceTag($menuMorgen->price) !!}
                    </div>
                    <p style="font-size: 1.125rem; color: var(--color-brand-muted); margin: 0; line-height: 1.6;">{!! implode('<span style="color: var(--color-brand-orange)"> · </span>', array_map('e', $menuMorgen->courses_for_locale)) !!}</p>
                @else
                    <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; margin-bottom: 0.25rem;">
                        <p style="font-size: 1.5rem; font-weight: 700; color: var(--color-brand-dark); margin: 0;">{{ $menuMorgen->main }}</p>
                        {!! $priceTag($menuMorgen->price) !!}
                    </div>
                    <p style="font-size: 1.125rem; color: var(--color-brand-muted); margin: 0;">{{ __('pages.home_menu_soup_included') }}</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- UPCOMING ACTIVITIES --}}
<section style="background-color: white; padding: 5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <x-eyebrow mb="0.75rem">{{ __('nav.activities') }}</x-eyebrow>
                <x-section-heading>{{ __('pages.home_activities_heading') }}</x-section-heading>
            </div>
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               class="press-scale"
               style="background: var(--color-brand-green); color: white; padding: 0.5rem 1.25rem; border-radius: 999px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none; white-space: nowrap; flex-shrink: 0;">
                {{ __('activities.all') }} →
            </a>
        </div>
        @php
            $cardColors = [
                ['bg' => 'var(--color-brand-green)',  'tint' => 'var(--color-brand-green-tint)',  'dark_tint' => '#5a8a74', 'accent' => 'var(--color-brand-green)'],
                ['bg' => 'var(--color-brand-blue)',   'tint' => 'var(--color-brand-blue-tint)',   'dark_tint' => '#2f5490', 'accent' => 'var(--color-brand-blue)'],
                ['bg' => 'var(--color-brand-orange)', 'tint' => 'var(--color-brand-orange-tint)', 'dark_tint' => '#b34a2d', 'accent' => 'var(--color-brand-orange)'],
            ];
        @endphp
        <div class="activity-cards-grid" style="display: flex; gap: 1rem;">
            @forelse ($activiteiten as $activiteit)
                @php
                    $cc = $cardColors[$loop->index % 3];
                    $t = strtolower($activiteit->titel);
                    // Filled icon paths (Heroicons solid 24x24)
                    $iconChat   = '<path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0 1 12 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 0 1-3.476.383.39.39 0 0 0-.297.17l-2.755 4.133a.75.75 0 0 1-1.248 0l-2.755-4.133a.39.39 0 0 0-.297-.17 48.9 48.9 0 0 1-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97Z" clip-rule="evenodd"/>';
                    $iconMusic  = '<path fill-rule="evenodd" d="M19.952 1.651a.75.75 0 0 1 .298.599V16.303a3 3 0 0 1-2.176 2.884l-1.32.377a2.553 2.553 0 1 1-1.403-4.909l2.311-.66a1.5 1.5 0 0 0 1.088-1.442V6.994l-9 2.572v9.737a3 3 0 0 1-2.176 2.884l-1.32.377a2.553 2.553 0 1 1-1.402-4.909l2.31-.66a1.5 1.5 0 0 0 1.088-1.442V5.25a.75.75 0 0 1 .544-.721l10.5-3a.75.75 0 0 1 .658.122Z" clip-rule="evenodd"/>';
                    $iconStar   = '<path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd"/>';
                    $iconBolt   = '<path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.268a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .895-.143Z" clip-rule="evenodd"/>';

                    if (str_contains($t, 'conversat') || str_contains($t, 'tafel') || str_contains($t, 'praat')) {
                        $icon = $iconChat;
                    } elseif (str_contains($t, 'zumba') || str_contains($t, 'dans') || str_contains($t, 'muziek') || str_contains($t, 'concert')) {
                        $icon = $iconMusic;
                    } elseif (str_contains($t, 'voorstelling') || str_contains($t, 'theater') || str_contains($t, 'théâtre') || str_contains($t, 'film')) {
                        $icon = $iconStar;
                    } elseif (str_contains($t, 'yoga') || str_contains($t, 'sport') || str_contains($t, 'fitness') || str_contains($t, 'bewegen') || str_contains($t, 'gym')) {
                        $icon = $iconBolt;
                    } else {
                        $fallbacks = [$iconChat, $iconMusic, $iconStar];
                        $icon = $fallbacks[abs(crc32($activiteit->slug)) % 3];
                    }
                @endphp
                <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                   class="activity-card"
                   style="flex: 1; display: flex; flex-direction: column; text-decoration: none; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(44,40,38,.09), 0 8px 28px rgba(44,40,38,.11); {{ $activiteit->status->value === 'geannuleerd' ? 'opacity: 0.7;' : '' }}">
                    {{-- Colored header with big date + clipped icon --}}
                    <div style="position: relative; height: 160px; background: {{ $cc['bg'] }}; overflow: hidden;">
                        <svg style="position: absolute; width: 170px; height: 170px; bottom: -22px; right: -12px; transform: rotate(12deg); pointer-events: none;"
                             viewBox="0 0 24 24" fill="{{ $cc['dark_tint'] }}" stroke="none">
                            {!! $icon !!}
                        </svg>
                        <div style="position: absolute; bottom: 1.1rem; left: 1.25rem; z-index: 2;">
                            <span class="tabular-nums" style="font-family: var(--font-sans); font-weight: 900; font-size: 3.75rem; line-height: 1; color: white; display: block;">{{ \Carbon\Carbon::parse($activiteit->datum)->format('d') }}</span>
                            <span style="font-family: var(--font-sans); font-weight: 800; font-size: 0.75rem; letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.7); display: block; margin-top: 1px;">{{ strtoupper(\Carbon\Carbon::parse($activiteit->datum)->locale(app()->getLocale())->isoFormat('MMMM')) }}</span>
                        </div>
                    </div>
                    {{-- White card body --}}
                    <div style="padding: 1rem 1.25rem 1.4rem; background: white; flex: 1; display: flex; flex-direction: column;">
                        <p style="font-family: var(--font-sans); font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: {{ $cc['accent'] }}; margin: 0 0 0.3rem;">
                            <x-relative-date :datum="$activiteit->datum" />
                        </p>
                        <h3 style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.2; margin: 0 0 0.35rem; flex: 1;">
                            {{ $activiteit->titel }}
                            @if ($activiteit->status->value === 'geannuleerd')
                                <x-badge type="geannuleerd" />
                            @endif
                        </h3>
                        <p style="font-size: 1.05rem; color: var(--color-brand-muted); margin: 0; font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
                            <span class="tabular-nums">{{ substr($activiteit->startuur, 0, 5) }}</span><span style="display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: {{ $cc['accent'] }}; flex-shrink: 0;"></span>{{ $activiteit->locatie }}
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
             loading="lazy"
             style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center top;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-samen.webp') }}" alt="{{ __('pages.home_photo_samen_alt') }}"
             loading="lazy"
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-visitors-2.webp') }}" alt="{{ __('pages.home_photo_feest_alt') }}"
             loading="lazy"
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
</div>

{{-- SERVICES — home visit focus --}}
<section style="background-color: var(--color-brand-blue-tint); padding: 5rem 1.5rem;">
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
        <div class="contact-photo img-outline" style="flex: 0 0 300px; height: 260px; overflow: hidden; border-radius: 12px; position: relative;">
            <img src="{{ asset('images/photo-gebouw.webp') }}" alt="Het gebouw van De Harmonie"
                 loading="lazy"
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
                    <a href="tel:0220328048" class="tabular-nums" style="font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">02/203.28.48</a><br>
                    <a href="mailto:info@deharmonie.be" style="color: var(--color-brand-blue); text-decoration: underline;">info@deharmonie.be</a>
                </p>
            </div>
        </div>

    </div>
</section>

<style>
.activity-card { transition: transform .14s ease, box-shadow .14s ease; }
.activity-card:hover { transform: translateY(-3px); box-shadow: 0 4px 14px rgba(44,40,38,.13), 0 16px 40px rgba(44,40,38,.14); }

/* sm — mobile */
@media (max-width: 767px) {
    .hero-inner { min-height: auto !important; }
    .hero-col-image { display: none; }
    .hero-copy { width: 100% !important; padding: 2.5rem 1.25rem !important; }
    .menu-cards { flex-direction: column !important; }
    .activity-cards-grid { flex-direction: column !important; }
.practical-grid { flex-direction: column !important; gap: 1.5rem !important; }
    .social-proof-strip { height: 200px; }
    .social-proof-strip > div:nth-child(2),
    .social-proof-strip > div:nth-child(3) { display: none; }
    .contact-photo { display: none; }
}
</style>

@endsection
