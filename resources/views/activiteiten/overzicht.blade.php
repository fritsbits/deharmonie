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
<section style="background: #fff8f5; padding: 3.5rem 1.5rem 6rem;">
    <div style="max-width: 72rem; margin: 0 auto;">

        @php
            $isFr = app()->getLocale() === 'fr';

            $themes = [
                [
                    'name'       => $isFr ? 'Bougez avec nous' : 'Beweeg mee',
                    'tagline'    => $isFr ? 'À votre rythme — pas besoin d\'être sportif.' : 'Op eigen tempo — je hoeft geen sportman te zijn.',
                    'color'      => '#eb6643',
                    'photo'      => 'photo-groep-actief.webp',
                    'tape'       => null,
                    'rotate'     => '-2deg',
                    'margin_top' => '1.5rem',
                    'ids'        => [5, 7, 11, 15],
                ],
                [
                    'name'       => $isFr ? 'Créez ensemble' : 'Maak iets',
                    'tagline'    => $isFr ? 'Les mains à l\'ouvrage — calme, convivial, ensemble.' : 'Met de handen bezig — rustig, gezellig, samen.',
                    'color'      => '#81b59c',
                    'photo'      => 'photo-visitors-2.webp',
                    'tape'       => 'rgba(129,181,156,0.2)',
                    'rotate'     => '1.8deg',
                    'margin_top' => '0',
                    'ids'        => [10, 12, 13],
                ],
                [
                    'name'       => $isFr ? 'Parlez & apprenez' : 'Praat & leer',
                    'tagline'    => $isFr ? 'Quatre langues, la mémoire, le numérique.' : 'Vier talen, het geheugen oefenen, digitaal leren.',
                    'color'      => '#4679bc',
                    'photo'      => 'photo-samen.webp',
                    'tape'       => 'rgba(70,121,188,0.15)',
                    'rotate'     => '-1deg',
                    'margin_top' => '2.5rem',
                    'ids'        => [1, 2, 3, 4, 6, 8],
                ],
                [
                    'name'       => $isFr ? 'Fêtez avec nous' : 'Vier mee',
                    'tagline'    => $isFr ? 'Bingo, sorties, anniversaires — toujours de la compagnie.' : 'Bingo, uitstappen, verjaardagen — altijd gezelschap.',
                    'color'      => '#d4956a',
                    'photo'      => 'photo-party.webp',
                    'tape'       => 'rgba(235,102,67,0.18)',
                    'rotate'     => '2.2deg',
                    'margin_top' => '0.5rem',
                    'ids'        => [9, 14, 16, 17, 18],
                ],
            ];
        @endphp

        <div class="theme-cards" style="display: flex; gap: 2rem; align-items: flex-start; margin-top: 3rem; padding-bottom: 2rem;">
            @foreach ($themes as $theme)
                @php
                    $templates = $reeksen->only($theme['ids'])->values();
                @endphp
                <div class="theme-card" style="flex: 1; background: white; border-radius: 2px; border: 1px solid rgba(44,40,38,0.07); position: relative; transform: rotate({{ $theme['rotate'] }}); margin-top: {{ $theme['margin_top'] }};">
                    {{-- tape decoration --}}
                    @if ($theme['tape'])
                        <div style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%) rotate(-1.5deg); width: 55px; height: 20px; background: {{ $theme['tape'] }}; border-radius: 2px; z-index: 10;"></div>
                    @endif

                    {{-- color band --}}
                    <div style="height: 4px; background: {{ $theme['color'] }};"></div>

                    {{-- photo --}}
                    <div style="height: 140px; overflow: hidden;">
                        <img src="{{ asset('images/' . $theme['photo']) }}" alt=""
                             style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    </div>

                    {{-- card body --}}
                    <div style="padding: 1.25rem 1.5rem 1.75rem;">
                        <p style="font-family: var(--font-sans); font-size: 1.25rem; font-weight: 900; color: var(--color-brand-dark); margin: 0 0 0.25rem;">
                            {{ $theme['name'] }}
                        </p>
                        <p style="font-size: 0.9rem; color: var(--color-brand-muted); line-height: 1.5; margin-bottom: 1rem; font-style: italic;">
                            {{ $theme['tagline'] }}
                        </p>
                        <ul style="list-style: none; padding: 0; border-top: 1px dashed #e4dbd3; padding-top: 0.75rem;">
                            @foreach ($templates as $t)
                                @php
                                    $titel = $isFr ? ($t->titel_fr ?? $t->titel_nl) : $t->titel_nl;
                                    $nextActiviteit = $nextActiviteiten->get($t->id);
                                @endphp
                                <li style="border-bottom: 1px solid rgba(44,40,38,0.05);">
                                    @if ($nextActiviteit)
                                        <a href="{{ route(app()->getLocale() . '.activiteiten.show', $nextActiviteit->slug) }}"
                                           style="display: block; font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); padding: 0.35rem 0; text-decoration: none;">
                                            {{ $titel }}
                                        </a>
                                    @else
                                        <span style="display: block; font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); padding: 0.35rem 0;">
                                            {{ $titel }}
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- CTA --}}
        <div style="text-align: center; margin-top: 2.5rem;">
            <a href="{{ route(app()->getLocale() . '.activiteiten.agenda') }}"
               style="display: inline-block; border: 2px solid var(--color-brand-dark); color: var(--color-brand-dark); padding: 0.875rem 2.25rem; border-radius: 6px; font-family: var(--font-sans); font-weight: 700; font-size: 1.0625rem; text-decoration: none;">
                {{ __('activities.agenda_link') }} →
            </a>
        </div>
    </div>
</section>

{{-- BIJZONDERE MOMENTEN --}}
<section style="background: #eef5f1; padding: 5rem 1.5rem 5.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="green" mb="0.75rem">{{ __('activities.special_moments_eyebrow') }}</x-eyebrow>
        <x-section-heading mb="0.75rem">{{ __('activities.special_moments_heading') }}</x-section-heading>
        <p style="font-size: 1.1rem; color: var(--color-brand-muted); line-height: 1.6; max-width: 600px; margin-bottom: 0;">
            {{ __('activities.special_moments_intro') }}
        </p>

        {{-- 3-col grid: big photo | 2 stacked photos | upcoming events --}}
        <div class="moments-layout" style="display: grid; grid-template-columns: 2fr 1.5fr 1.5fr; grid-template-rows: 240px 220px; gap: 0.875rem; margin-top: 2rem;">

            {{-- Big photo — spans both rows --}}
            <div style="grid-row: 1 / 3; overflow: hidden; position: relative;">
                <img src="{{ asset('images/photo-feest-2.webp') }}" alt=""
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <div style="position: absolute; bottom: 0.75rem; left: 0.75rem;">
                    <span style="background: var(--color-brand-green); color: white; padding: 0.2rem 0.6rem; font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; display: inline-block;">
                        {{ $isFr ? 'Fête des 51 ans de De Harmonie' : 'Feest van 51 jaar De Harmonie' }}
                    </span>
                </div>
            </div>

            {{-- Middle top photo --}}
            <div style="overflow: hidden; position: relative;">
                <img src="{{ asset('images/photo-buiten-event.webp') }}" alt=""
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <div style="position: absolute; bottom: 0.75rem; left: 0.75rem;">
                    <span style="background: var(--color-brand-green); color: white; padding: 0.2rem 0.6rem; font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; display: inline-block;">
                        {{ $isFr ? 'Sortie culturelle' : 'Culturele uitstap' }}
                    </span>
                </div>
            </div>

            {{-- Upcoming events card — spans both rows --}}
            <div style="grid-row: 1 / 3; grid-column: 3; background: white; border: 1px solid #d8ece5; overflow: hidden; display: flex; flex-direction: column;">
                <div style="padding: 1rem 1.25rem 0.75rem; background: var(--color-brand-green);">
                    <p style="font-family: var(--font-sans); font-size: 0.9375rem; font-weight: 800; color: white; margin: 0 0 0.1rem;">
                        {{ __('activities.upcoming_activities_heading') }}
                    </p>
                    <p style="font-size: 0.75rem; color: rgba(255,255,255,0.8); margin: 0;">
                        {{ __('activities.upcoming_activities_subline') }}
                    </p>
                </div>

                @forelse ($bijzondereActiviteiten as $activiteit)
                    @php
                        $date = \Carbon\Carbon::parse($activiteit->datum);
                        $titel = app()->getLocale() === 'fr'
                            ? ($activiteit->titel_fr ?? $activiteit->titel_nl)
                            : $activiteit->titel_nl;
                        $uur = substr($activiteit->startuur, 0, 5);
                    @endphp
                    <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                       style="display: flex; align-items: flex-start; gap: 0.875rem; padding: 0.875rem 1.25rem; border-bottom: 1px solid #f0ebe6; text-decoration: none; color: inherit;">
                        <div style="flex-shrink: 0; width: 40px; text-align: center;">
                            <div style="font-family: var(--font-sans); font-size: 1.25rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1;">
                                {{ $date->format('d') }}
                            </div>
                            <div style="font-family: var(--font-sans); font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-top: 0.1rem;">
                                {{ rtrim($date->locale(app()->getLocale())->isoFormat('MMM'), '.') }}
                            </div>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-dark); margin: 0 0 0.2rem; line-height: 1.3;">
                                {{ $titel }}
                            </p>
                            <p style="font-size: 0.775rem; color: var(--color-brand-muted); margin: 0;">
                                {{ $uur }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div style="padding: 1.5rem 1.25rem; color: var(--color-brand-muted); font-size: 0.875rem;">
                        {{ __('activities.no_upcoming') }}
                    </div>
                @endforelse
            </div>

            {{-- Middle bottom photo --}}
            <div style="overflow: hidden; position: relative;">
                <img src="{{ asset('images/photo-cake.jpg') }}" alt=""
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <div style="position: absolute; bottom: 0.75rem; left: 0.75rem;">
                    <span style="background: var(--color-brand-green); color: white; padding: 0.2rem 0.6rem; font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; display: inline-block;">
                        {{ $isFr ? 'Fête d\'anniversaire' : 'Verjaardagsfeest' }}
                    </span>
                </div>
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
@media (max-width: 767px) {
    .theme-cards { flex-direction: column !important; gap: 3rem !important; padding-bottom: 1rem !important; }
    .theme-card { margin-top: 0 !important; transform: none !important; }
    .moments-layout { grid-template-columns: 1fr !important; grid-template-rows: 260px auto auto !important; }
    .moments-layout > div[style*="grid-row: 1 / 3"] { grid-row: auto !important; }
}
</style>

@endsection
