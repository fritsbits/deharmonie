@extends('layouts.app')
@section('title', __('activities.overview_heading') . ' — De Harmonie')

@section('content')

{{-- HERO --}}
<x-page-hero
    :eyebrow="__('activities.overview_hero_eyebrow')"
    eyebrow-color="green"
    :heading="__('activities.overview_heading')"
    :lead="__('activities.overview_tagline')"
    bg="white"
/>

{{-- THEMATIC CARDS --}}
<section style="background: var(--color-brand-green-tint); padding: 3.5rem 1.5rem 3rem;">
    <div style="max-width: 72rem; margin: 0 auto;">

        @php
            $isFr = app()->getLocale() === 'fr';

            $sections = [
                [
                    'key'        => 'beweeg',
                    'name'       => $isFr ? 'Bougez avec nous' : 'Beweeg mee',
                    'tagline'    => $isFr ? 'À votre rythme — pas besoin d\'être sportif.' : 'Op je tempo — je hoeft geen sportman te zijn.',
                    'color'      => 'var(--color-brand-orange)',
                    'photo'      => 'photo-petanque.webp',
                    'rotate'     => '-2deg',
                    'margin_top' => '0.75rem',
                ],
                [
                    'key'        => 'maak_leer',
                    'name'       => $isFr ? 'Créez & apprenez avec nous' : 'Maak & leer mee',
                    'tagline'    => $isFr ? 'Avec les mains ou la tête — tranquillement, ensemble.' : 'Met de handen of het hoofd — rustig, samen.',
                    'color'      => 'var(--color-brand-green)',
                    'photo'      => 'photo-handwerk.webp',
                    'rotate'     => '1.8deg',
                    'margin_top' => '0',
                ],
                [
                    'key'        => 'ontmoet_beleef',
                    'name'       => $isFr ? 'Rencontrez & vivez avec nous' : 'Ontmoet & beleef mee',
                    'tagline'    => $isFr ? 'À table, en sortie, en spectacle — toujours en compagnie.' : 'Aan tafel, op uitstap, op voorstelling — altijd gezelschap.',
                    'color'      => 'var(--color-brand-blue)',
                    'photo'      => 'photo-samen.webp',
                    'rotate'     => '-1deg',
                    'margin_top' => '1.25rem',
                ],
            ];
        @endphp

        <div class="theme-cards" style="display: flex; gap: 2rem; align-items: flex-start; margin-top: 1.5rem; padding-bottom: 2rem;">
            @foreach ($sections as $section)
                @php
                    $activiteiten = $vasteAanbod->get($section['key'], collect());
                    $total = $activiteiten->count();
                    $shown = $activiteiten->take(5);
                @endphp
                <div class="theme-card-outer" style="flex: 1; transform: rotate({{ $section['rotate'] }}); margin-top: {{ $section['margin_top'] }};">
                <div class="theme-card" style="background: white; border-radius: 2px; position: relative;">
                    {{-- color band --}}
                    <div style="height: 8px; background: {{ $section['color'] }};"></div>

                    {{-- photo --}}
                    <div class="theme-card-photo" style="height: 140px; overflow: hidden;">
                        <img src="{{ asset('images/' . $section['photo']) }}" alt="{{ $section['name'] }}"
                             loading="lazy"
                             style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    </div>

                    {{-- card body --}}
                    <div style="padding: 1.25rem 1.5rem 1.75rem;">
                        <p style="font-family: var(--font-sans); font-size: 1.625rem; font-weight: 900; color: var(--color-brand-dark); margin: 0 0 0.25rem;">
                            {{ $section['name'] }}
                        </p>
                        <p style="font-size: 0.9rem; color: var(--color-brand-muted); line-height: 1.5; margin-bottom: 1rem; font-style: italic;">
                            {{ $section['tagline'] }}
                        </p>
                        <ul style="list-style: none; padding: 0; border-top: 1px dashed #e4dbd3; padding-top: 0.75rem;">
                            @foreach ($shown as $activiteit)
                                @php
                                    $titel = $isFr ? ($activiteit->titel_fr ?? $activiteit->titel_nl) : $activiteit->titel_nl;
                                @endphp
                                <li style="{{ $loop->last && $total <= 5 ? '' : 'border-bottom: 1px solid rgba(44,40,38,0.05);' }}">
                                    <span style="display: block; font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); padding: 0.35rem 0;">
                                        {{ $titel }}
                                    </span>
                                </li>
                            @endforeach

                            @if ($total > 5)
                                <li>
                                    <a href="{{ route(app()->getLocale() . '.activiteiten.agenda') }}"
                                       style="display: block; font-size: 0.95rem; font-weight: 700; color: var(--color-brand-green); text-decoration: none; padding: 0.5rem 0 0.25rem;">
                                        {{ $isFr ? 'et plus →' : 'en meer →' }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                </div>
            @endforeach
        </div>

        {{-- CTA --}}
        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route(app()->getLocale() . '.activiteiten.agenda') }}"
               style="display: inline-block; border: 2px solid var(--color-brand-green); color: var(--color-brand-green); padding: 0.875rem 2.25rem; border-radius: 6px; font-family: var(--font-sans); font-weight: 700; font-size: 1.0625rem; text-decoration: none;">
                {{ __('activities.agenda_link') }} →
            </a>
        </div>
    </div>
</section>

{{-- BIJZONDERE MOMENTEN --}}
<section style="background: white; padding: 5rem 1.5rem 5.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="green" mb="0.75rem">{{ __('activities.special_moments_eyebrow') }}</x-eyebrow>
        <x-section-heading mb="0.75rem">{{ __('activities.special_moments_heading') }}</x-section-heading>
        <p style="font-size: 1.1rem; color: var(--color-brand-muted); line-height: 1.6; max-width: 600px; margin-bottom: 0;">
            {{ __('activities.special_moments_intro') }}
        </p>

        {{-- 3-col grid: big photo | 2 stacked photos | upcoming events --}}
        <div class="moments-layout" style="display: grid; grid-template-columns: 2fr 1fr 2fr; grid-template-rows: 240px 220px; gap: 0.875rem; margin-top: 2rem;">

            {{-- Big photo — spans both rows --}}
            <div style="grid-row: 1 / 3; overflow: hidden; position: relative;">
                <img src="{{ asset('images/photo-uitstap.webp') }}" alt="{{ __('pages.overzicht_photo_uitstap_alt') }}"
                     loading="lazy"
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>

            {{-- Middle top photo --}}
            <div style="overflow: hidden; position: relative;">
                <img src="{{ asset('images/photo-muzikanten.webp') }}" alt="{{ __('pages.overzicht_photo_muzikanten_alt') }}"
                     loading="lazy"
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>

            {{-- Upcoming special events — homepage-style cards --}}
            <div style="grid-row: 1 / 3; grid-column: 3; display: flex; flex-direction: column; gap: 0.875rem; padding: 4px;">
                @php
                    $specialCardColors = [
                        ['bg' => 'var(--color-brand-green)',  'dark_tint' => '#5a8a74', 'accent' => 'var(--color-brand-green)'],
                        ['bg' => 'var(--color-brand-blue)',   'dark_tint' => '#2f5490', 'accent' => 'var(--color-brand-blue)'],
                        ['bg' => 'var(--color-brand-orange)', 'dark_tint' => '#b34a2d', 'accent' => 'var(--color-brand-orange)'],
                    ];
                    $iconChat  = '<path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0 1 12 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 0 1-3.476.383.39.39 0 0 0-.297.17l-2.755 4.133a.75.75 0 0 1-1.248 0l-2.755-4.133a.39.39 0 0 0-.297-.17 48.9 48.9 0 0 1-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97Z" clip-rule="evenodd"/>';
                    $iconMusic = '<path fill-rule="evenodd" d="M19.952 1.651a.75.75 0 0 1 .298.599V16.303a3 3 0 0 1-2.176 2.884l-1.32.377a2.553 2.553 0 1 1-1.403-4.909l2.311-.66a1.5 1.5 0 0 0 1.088-1.442V6.994l-9 2.572v9.737a3 3 0 0 1-2.176 2.884l-1.32.377a2.553 2.553 0 1 1-1.402-4.909l2.31-.66a1.5 1.5 0 0 0 1.088-1.442V5.25a.75.75 0 0 1 .544-.721l10.5-3a.75.75 0 0 1 .658.122Z" clip-rule="evenodd"/>';
                    $iconStar  = '<path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd"/>';
                    $iconBolt  = '<path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.268a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .895-.143Z" clip-rule="evenodd"/>';
                @endphp

                @forelse ($bijzondereActiviteiten as $activiteit)
                    @php
                        $scc = $specialCardColors[$loop->index % 3];
                        $t   = strtolower($activiteit->titel_nl ?? $activiteit->titel);
                        if (str_contains($t, 'conversat') || str_contains($t, 'tafel') || str_contains($t, 'praat')) {
                            $icon = $iconChat;
                        } elseif (str_contains($t, 'zumba') || str_contains($t, 'dans') || str_contains($t, 'muziek') || str_contains($t, 'concert')) {
                            $icon = $iconMusic;
                        } elseif (str_contains($t, 'voorstelling') || str_contains($t, 'theater') || str_contains($t, 'théâtre') || str_contains($t, 'film')) {
                            $icon = $iconStar;
                        } elseif (str_contains($t, 'yoga') || str_contains($t, 'sport') || str_contains($t, 'fitness') || str_contains($t, 'bewegen') || str_contains($t, 'gym')) {
                            $icon = $iconBolt;
                        } else {
                            $icon = [$iconChat, $iconMusic, $iconStar][abs(crc32($activiteit->slug)) % 3];
                        }
                        $date  = \Carbon\Carbon::parse($activiteit->datum);
                        $titel = $isFr ? ($activiteit->titel_fr ?? $activiteit->titel_nl) : $activiteit->titel_nl;
                        $uur   = substr($activiteit->startuur, 0, 5);
                    @endphp
                    <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                       class="special-activity-card"
                       style="display: flex; flex-direction: column; text-decoration: none; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(44,40,38,.09), 0 8px 28px rgba(44,40,38,.11); flex: 1;">
                        {{-- Colored header --}}
                        <div style="position: relative; height: 130px; background: {{ $scc['bg'] }}; overflow: hidden; flex-shrink: 0;">
                            <svg style="position: absolute; width: 155px; height: 155px; bottom: -22px; right: -12px; transform: rotate(12deg); pointer-events: none;"
                                 viewBox="0 0 24 24" fill="{{ $scc['dark_tint'] }}" stroke="none">
                                {!! $icon !!}
                            </svg>
                            <div style="position: absolute; bottom: 0.9rem; left: 1.25rem; z-index: 2;">
                                <span style="font-family: var(--font-sans); font-weight: 900; font-size: 3.25rem; line-height: 1; color: white; display: block;">{{ $date->format('d') }}</span>
                                <span style="font-family: var(--font-sans); font-weight: 800; font-size: 0.7rem; letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.7); display: block; margin-top: 1px;">{{ strtoupper($date->locale(app()->getLocale())->isoFormat('MMMM')) }}</span>
                            </div>
                        </div>
                        {{-- Card body --}}
                        <div style="padding: 0.875rem 1.25rem 1rem; background: white; flex: 1; display: flex; flex-direction: column;">
                            <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: {{ $scc['accent'] }}; margin: 0 0 0.3rem;">
                                {{ __('activities.coming_soon') }}
                            </p>
                            <h3 style="font-family: var(--font-sans); font-size: 1.25rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.2; margin: 0 0 0.3rem;">
                                {{ $titel }}
                            </h3>
                            <p style="font-size: 0.95rem; color: var(--color-brand-muted); margin: 0; font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
                                {{ $uur }}<span style="display: inline-block; width: 4px; height: 4px; border-radius: 50%; background: {{ $scc['accent'] }}; flex-shrink: 0;"></span>{{ $activiteit->locatie }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div style="background: white; border-radius: 12px; padding: 1.5rem; color: var(--color-brand-muted); font-size: 0.875rem; border: 1px solid #d8ece5;">
                        {{ __('activities.no_upcoming') }}
                    </div>
                @endforelse
            </div>

            {{-- Middle bottom photo --}}
            <div style="overflow: hidden; position: relative;">
                <img src="{{ asset('images/photo-verjaardag.webp') }}" alt="{{ __('pages.overzicht_photo_verjaardag_alt') }}"
                     loading="lazy"
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>

        </div>

        {{-- Facebook follow block --}}
        <div style="margin-top: 1.5rem; display: flex; align-items: center; gap: 1.25rem; background: white; border-radius: 10px; padding: 1.25rem 1.5rem; border: 1px solid #d8ece5;">
            <div style="width: 40px; height: 40px; background: #1877f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg viewBox="0 0 24 24" style="fill: white; width: 22px; height: 22px;" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.235 2.686.235v2.97h-1.514c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                </svg>
            </div>
            <div style="flex: 1;">
                <p style="font-family: var(--font-sans); font-weight: 700; font-size: 1rem; color: var(--color-brand-dark); margin: 0 0 0.1rem;">
                    {{ __('activities.facebook_follow_heading') }}
                </p>
                <p style="font-size: 0.875rem; color: var(--color-brand-muted); margin: 0;">
                    {{ __('activities.facebook_follow_body') }}
                </p>
            </div>
            <a href="https://www.facebook.com/deharmoniebrussel/" target="_blank" rel="noopener"
               style="font-family: var(--font-sans); font-weight: 700; font-size: 0.875rem; color: #1877f2; text-decoration: none; white-space: nowrap; flex-shrink: 0;">
                facebook.com/deharmoniebrussel →
            </a>
        </div>
    </div>
</section>

<style>
.theme-card {
    box-shadow:
        0 1px 3px rgba(44,40,38,0.08),
        0 6px 18px rgba(44,40,38,0.11),
        0 18px 40px rgba(44,40,38,0.08);
    overflow: hidden;
}

/* Tablet: 2×2 grid */
@media (min-width: 640px) and (max-width: 1023px) {
    .theme-cards { flex-wrap: wrap !important; gap: 2rem 1.5rem !important; padding-bottom: 1rem !important; }
    .theme-card-outer { flex: 0 0 calc(50% - 0.75rem) !important; margin-top: 0 !important; }
    .theme-card-outer:nth-child(odd)  { transform: rotate(-1deg) !important; }
    .theme-card-outer:nth-child(even) { transform: rotate(1.2deg) !important; }
}

/* Mobile: single column */
@media (max-width: 639px) {
    .theme-cards { flex-direction: column !important; gap: 2.5rem !important; padding-bottom: 1rem !important; }
    .theme-card-outer { margin-top: 0 !important; transform: none !important; }
    .theme-card-photo { height: 200px !important; }
    .moments-layout { grid-template-columns: 1fr !important; grid-template-rows: 260px auto auto !important; }
    .moments-layout > div[style*="grid-row: 1 / 3"] { grid-row: auto !important; }
}
</style>

@endsection
