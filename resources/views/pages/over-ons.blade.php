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

{{-- ONS VERHAAL + STATS --}}
<section style="background: var(--color-brand-bg); padding: 4rem 0;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">
        <div class="over-ons-verhaal-layout">

            {{-- Left: story --}}
            <div class="over-ons-verhaal-col">
                <x-eyebrow size="sm" color="blue" mb="0.75rem">{{ __('pages.over_ons_verhaal_eyebrow') }}</x-eyebrow>

                <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 1.5rem;">
                    {{ __('pages.over_ons_verhaal_heading') }}
                </h2>

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

            {{-- Right: stats card --}}
            <div class="over-ons-stats-sidebar">
                <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 16px rgba(44,40,38,0.09);">
                    <div style="height: 4px; background: var(--color-brand-blue);"></div>
                    <div style="padding: 1.5rem;">

                        <div style="margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px dashed #e4dbd3;">
                            <x-eyebrow size="sm" color="blue" mb="0.35rem">{{ __('pages.over_ons_impact_1_label') }}</x-eyebrow>
                            <div style="font-family: var(--font-sans); font-size: 2.75rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1; letter-spacing: -0.02em;">{{ $content->impact_1_aantal }}</div>
                            <p style="font-size: 0.9375rem; color: var(--color-brand-muted); margin: 0.25rem 0 0; line-height: 1.4;">{{ $content->impactOmschrijving(1) }}</p>
                        </div>

                        <div style="margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px dashed #e4dbd3;">
                            <x-eyebrow size="sm" color="blue" mb="0.35rem">{{ __('pages.over_ons_impact_2_label') }}</x-eyebrow>
                            <div style="font-family: var(--font-sans); font-size: 2.75rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1; letter-spacing: -0.02em;">{{ $content->impact_2_aantal }}</div>
                            <p style="font-size: 0.9375rem; color: var(--color-brand-muted); margin: 0.25rem 0 0; line-height: 1.4;">{{ $content->impactOmschrijving(2) }}</p>
                        </div>

                        <div>
                            <x-eyebrow size="sm" color="blue" mb="0.35rem">{{ __('pages.over_ons_impact_3_label') }}</x-eyebrow>
                            <div style="font-family: var(--font-sans); font-size: 2.75rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1; letter-spacing: -0.02em;">{{ $content->impact_3_aantal }}</div>
                            <p style="font-size: 0.9375rem; color: var(--color-brand-muted); margin: 0.25rem 0 0; line-height: 1.4;">{{ $content->impactOmschrijving(3) }}</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ONZE VISIE --}}
<section style="background: var(--color-brand-bg); border-top: 1px solid #e8e5e2; padding: 4rem 0;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">
        <div class="over-ons-visie-layout">

            {{-- Left: mission + values --}}
            <div class="over-ons-visie-main">
                <x-eyebrow size="sm" color="blue" mb="0.75rem">{{ __('pages.over_ons_visie_eyebrow') }}</x-eyebrow>

                <p style="font-size: 1.125rem; line-height: 1.75; color: var(--color-brand-dark); margin-bottom: 1.25rem;">
                    {{ __('pages.over_ons_visie_mission') }}
                </p>

                <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.75rem;">
                    <li style="font-size: 1.125rem; line-height: 1.75; color: var(--color-brand-dark); padding-left: 1.125rem; position: relative;">
                        <span style="position: absolute; left: 0; top: 0.55em; width: 5px; height: 5px; border-radius: 50%; background: var(--color-brand-blue); display: block;"></span>
                        {{ __('pages.over_ons_visie_waarde_1') }}
                    </li>
                    <li style="font-size: 1.125rem; line-height: 1.75; color: var(--color-brand-dark); padding-left: 1.125rem; position: relative;">
                        <span style="position: absolute; left: 0; top: 0.55em; width: 5px; height: 5px; border-radius: 50%; background: var(--color-brand-blue); display: block;"></span>
                        {{ __('pages.over_ons_visie_waarde_2') }}
                    </li>
                </ul>
            </div>

            {{-- Right: jaarverslag --}}
            <div class="over-ons-visie-aside">
                <a href="{{ asset('docs/jaarverslag-2025.pdf') }}" target="_blank" rel="noopener noreferrer" style="display: flex; align-items: center; gap: 1rem; text-decoration: none; background: white; border-radius: 10px; padding: 1.25rem 1.5rem; box-shadow: 0 2px 12px rgba(44,40,38,0.07);" class="over-ons-jaarverslag-link">
                    <div style="flex-shrink: 0; width: 44px; height: 44px; border-radius: 8px; background: #eef2f8; display: flex; align-items: center; justify-content: center; color: var(--color-brand-blue);">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <div>
                        <div class="over-ons-jaarverslag-title" style="font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.3;">{{ __('pages.over_ons_visie_jaarverslag_link') }}</div>
                        <div style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-top: 0.2rem;">{{ __('pages.over_ons_visie_jaarverslag_size') }}</div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

{{-- SERVICE CARDS --}}
<section style="background: white; border-top: 1px solid #e8e5e2; padding: 4rem 0;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">

        @php
        $clusters = app()->getLocale() === 'fr' ? [
            [
                'label_top'  => 'Restaurant',
                'label_main' => 'Social',
                'color'      => 'var(--color-brand-orange)',
                'icon'       => '<path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM9 7.5A.75.75 0 0 0 8.25 8v1.5a2.25 2.25 0 0 0 1.5 2.122v3.628a.75.75 0 0 0 1.5 0v-3.628A2.25 2.25 0 0 0 12.75 9.5V8A.75.75 0 0 0 12 7.5H9ZM15 7.5a.75.75 0 0 0-.75.75v7.5a.75.75 0 0 0 1.5 0V12.5h.75a.75.75 0 0 0 .75-.75V9a1.5 1.5 0 0 0-1.5-1.5H15Z" clip-rule="evenodd"/>',
                'items' => [
                    'À emporter et livraison',
                    'Sur place à tarif social',
                    'Traiteur & location',
                ],
            ],
            [
                'label_top'  => 'Point d\'info',
                'label_main' => 'Social',
                'color'      => 'var(--color-brand-green)',
                'icon'       => '<path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd"/>',
                'items' => [
                    'Orientation',
                    'Soutien à domicile',
                    'Accompagnement sur demande',
                ],
            ],
            [
                'label_top'  => 'Nos',
                'label_main' => 'Activités',
                'color'      => 'var(--color-brand-blue)',
                'icon'       => '<path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a3.375 3.375 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a3.375 3.375 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd"/>',
                'items' => [
                    'Activités récréatives',
                    'Bien-être',
                    'Séances d\'information',
                ],
            ],
        ] : [
            [
                'label_top'  => 'Restaurant',
                'label_main' => '& catering',
                'color'      => 'var(--color-brand-orange)',
                'icon'       => '<path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM9 7.5A.75.75 0 0 0 8.25 8v1.5a2.25 2.25 0 0 0 1.5 2.122v3.628a.75.75 0 0 0 1.5 0v-3.628A2.25 2.25 0 0 0 12.75 9.5V8A.75.75 0 0 0 12 7.5H9ZM15 7.5a.75.75 0 0 0-.75.75v7.5a.75.75 0 0 0 1.5 0V12.5h.75a.75.75 0 0 0 .75-.75V9a1.5 1.5 0 0 0-1.5-1.5H15Z" clip-rule="evenodd"/>',
                'items' => [
                    'Afhaal en leveringen',
                    'Ter plaatse aan een sociaal tarief',
                    'Catering & verhuur',
                ],
            ],
            [
                'label_top'  => 'Info',
                'label_main' => 'punt',
                'color'      => 'var(--color-brand-green)',
                'icon'       => '<path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd"/>',
                'items' => [
                    'Doorverwijzen',
                    'Ondersteuning in thuissituatie',
                    'Begeleiding bij vragen',
                ],
            ],
            [
                'label_top'  => 'Onze',
                'label_main' => 'Activiteiten',
                'color'      => 'var(--color-brand-blue)',
                'icon'       => '<path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a3.375 3.375 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a3.375 3.375 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd"/>',
                'items' => [
                    'Recreatieve activiteiten',
                    'Welzijn',
                    'Info sessies',
                ],
            ],
        ];
        @endphp

        <div class="over-ons-service-cards">
            @foreach ($clusters as $cluster)
                <div style="flex: 1; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(44,40,38,.09), 0 8px 28px rgba(44,40,38,.10);">

                    <div style="background: {{ $cluster['color'] }}; padding: 1.25rem 1.5rem 1.4rem; position: relative; overflow: hidden; min-height: 76px;">
                        <svg style="position: absolute; right: -14px; bottom: -18px; width: 110px; height: 110px; opacity: 0.22; transform: rotate(12deg); pointer-events: none;"
                             viewBox="0 0 24 24" fill="white" stroke="none">
                            {!! $cluster['icon'] !!}
                        </svg>
                        <p style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 900; color: white; margin: 0; position: relative; z-index: 1; line-height: 1.25;">
                            <span style="display: block;">{{ $cluster['label_top'] }}</span>
                            <span style="display: block;">{{ $cluster['label_main'] }}</span>
                        </p>
                    </div>

                    <div style="padding: 1.25rem 1.5rem 1.5rem;">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            @foreach ($cluster['items'] as $item)
                                <li style="display: flex; gap: 0.75rem; align-items: flex-start; padding: 0.65rem 0; {{ !$loop->last ? 'border-bottom: 1px solid rgba(44,40,38,.07);' : '' }}">
                                    <span style="flex-shrink: 0; width: 5px; height: 5px; border-radius: 50%; background: {{ $cluster['color'] }}; display: block; margin-top: 0.55em;"></span>
                                    <span style="font-size: 1.0625rem; color: var(--color-brand-dark); line-height: 1.45;">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- PHOTO STRIP --}}
<div style="display: flex; height: 280px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-samen.webp') }}" alt="{{ __('pages.over_ons_photo_samen_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-buiten-activiteit.webp') }}" alt="{{ __('pages.over_ons_photo_buiten_event_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
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
            <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15;">
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
                    <p class="ui-label" style="color: var(--color-brand-green);">— Josiane C.</p>
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
                    <p class="ui-label" style="color: var(--color-brand-blue);">— Marc P.</p>
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
                    <p class="ui-label" style="color: var(--color-brand-orange);">— Hélène-Christine A.</p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- VOLUNTEER CTA --}}
<section style="background: var(--color-brand-bg); border-top: 1px solid #e8e5e2; padding: 3.5rem 0;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">
        <div class="over-ons-vrijwilligers-layout" style="display: flex; gap: 3rem; align-items: center;">

            {{-- Left: photo --}}
            <div class="over-ons-vrijwilligers-img img-outline" style="flex: 0 0 300px; height: 260px; overflow: hidden; border-radius: 12px;">
                <img src="{{ asset('images/photo-handwerk.webp') }}"
                     alt="{{ __('pages.over_ons_photo_vrijwilligers_alt') }}"
                     loading="lazy"
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>

            {{-- Right: text --}}
            <div style="flex: 1; min-width: 0;">
                <x-eyebrow size="sm" color="orange" mb="0.5rem">{{ __('pages.over_ons_vrijwilligers_eyebrow') }}</x-eyebrow>
                <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 1rem;">
                    {{ __('pages.over_ons_vrijwilligers_heading') }}
                </h2>
                <p style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-muted); margin-bottom: 1.75rem;">
                    {{ __('pages.over_ons_vrijwilligers_lead') }}
                </p>
                <a href="{{ route(app()->getLocale() . '.vrijwilligers') }}"
                   class="over-ons-team-link"
                   style="display: inline-block; font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-orange); border: 1.5px solid var(--color-brand-orange); padding: 0.6rem 1.25rem; border-radius: 3px; text-decoration: none; letter-spacing: 0.03em;">
                    {{ __('pages.over_ons_vrijwilligers_cta') }} →
                </a>
            </div>

        </div>
    </div>
</section>

{{-- TEAM REFERENCE --}}
<section style="background: var(--color-brand-bg); border-top: 1px solid #e8e5e2; padding: 3.5rem 0;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">
        <div class="over-ons-team-layout">

            <div class="over-ons-team-text">
                <x-eyebrow size="sm" color="blue" mb="0.5rem">{{ __('pages.over_ons_team_eyebrow') }}</x-eyebrow>

                <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 1rem;">
                    {{ __('pages.over_ons_team_heading') }}
                </h2>

                <p class="over-ons-team-lead" style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-muted); margin-bottom: 1.75rem;">
                    {{ __('pages.over_ons_team_lead') }}
                </p>

                <a href="{{ route(app()->getLocale() . '.wie-is-wie') }}" class="over-ons-team-link" style="display:inline-block;font-family:var(--font-sans);font-size:0.875rem;font-weight:700;color:var(--color-brand-blue);border:1.5px solid var(--color-brand-blue);padding:0.6rem 1.25rem;border-radius:3px;text-decoration:none;letter-spacing:0.03em;">
                    {{ __('pages.over_ons_team_cta') }} →
                </a>
            </div>

            <div class="over-ons-team-photo">
                <img src="{{ asset('images/photo-keukenteam.webp') }}" alt="{{ __('pages.over_ons_photo_keukenteam_alt') }}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 10px;">
            </div>

        </div>
    </div>
</section>

{{-- CTA BAND --}}
<section style="background: var(--color-brand-blue); border-top: 1px solid #e8e5e2; border-bottom: 1px solid rgba(0,0,0,0.22); padding: 3.5rem 2rem; text-align: center;">
    <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 900; color: white; line-height: 1.15; margin-bottom: 0.75rem;">
        {{ __('pages.over_ons_cta_heading') }}
    </h2>
    <p style="font-size: 1.125rem; line-height: 1.6; color: rgba(255,255,255,0.8); margin-bottom: 2rem; max-width: 480px; margin-left: auto; margin-right: auto;">
        {{ __('pages.over_ons_cta_lead') }}
    </p>
    <a href="{{ route(app()->getLocale() . '.contact') }}" style="display:inline-block;background:white;color:var(--color-brand-blue);font-family:var(--font-sans);font-weight:800;font-size:0.875rem;padding:0.85rem 2rem;border-radius:3px;text-decoration:none;letter-spacing:0.04em;text-transform:uppercase;">
        {{ __('pages.over_ons_cta_btn') }}
    </a>
</section>

<style>
.over-ons-quote-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.over-ons-quote-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(70,121,188,0.16) !important; }
.over-ons-jaarverslag-link:hover .over-ons-jaarverslag-title { text-decoration: underline; }

.over-ons-service-cards { display: flex; gap: 1.5rem; align-items: stretch; }

.over-ons-team-layout { display: flex; align-items: center; gap: 3rem; }
.over-ons-team-text { flex: 1; min-width: 0; }
.over-ons-team-photo { flex: 0 0 380px; height: 280px; overflow: hidden; border-radius: 10px; }

.over-ons-visie-layout { display: flex; align-items: flex-start; gap: 3rem; }
.over-ons-visie-main { flex: 1; min-width: 0; }
.over-ons-visie-aside { flex: 0 0 300px; }

.over-ons-verhaal-layout { display: flex; align-items: flex-start; gap: 3rem; }
.over-ons-verhaal-col { flex: 1; min-width: 0; }
.over-ons-stats-sidebar { flex: 0 0 300px; position: sticky; top: 2rem; }

@media (max-width: 767px) {
    .over-ons-photo-strip-third { display: none !important; }
    .over-ons-quotes-grid { flex-direction: column !important; }
    .over-ons-team-lead { max-width: 100% !important; }
    .over-ons-team-link { width: 100%; text-align: center; display: block !important; }
    .over-ons-service-cards { flex-direction: column; }
    .over-ons-team-layout { flex-direction: column; }
    .over-ons-team-photo { flex: none; width: 100%; height: 220px; }
    .over-ons-visie-layout { flex-direction: column; gap: 2rem; }
    .over-ons-visie-aside { flex: none; width: 100%; }
    .over-ons-verhaal-layout { flex-direction: column; gap: 2rem; }
    .over-ons-stats-sidebar { flex: none; width: 100%; position: static; }
    .over-ons-vrijwilligers-layout { flex-direction: column !important; gap: 2rem !important; }
    .over-ons-vrijwilligers-img { flex: none !important; width: 100% !important; height: auto !important; }
}
</style>

@endsection
