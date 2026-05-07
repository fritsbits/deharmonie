@extends('layouts.app')
@section('title', __('pages.vrijwilligers_title'))
@section('content')

{{-- HERO --}}
<x-page-hero
    :eyebrow="__('pages.vrijwilligers_eyebrow')"
    eyebrow-color="orange"
    :heading="__('pages.vrijwilligers_heading')"
    :lead="__('pages.vrijwilligers_lead')"
    bg="white"
/>

{{-- PHOTO STRIP --}}
<div style="display: flex; height: 260px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-gemeenschap.webp') }}" alt="{{ __('pages.vrijwilligers_photo_gemeenschap_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-handwerk.webp') }}" alt="{{ __('pages.vrijwilligers_photo_handwerk_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;object-position:center top;">
    </div>
    <div class="vrijwilligers-photo-third" style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-muzikanten.webp') }}" alt="{{ __('pages.vrijwilligers_photo_muzikanten_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
</div>

{{-- WHY VOLUNTEER — activity card pattern --}}
<section style="background: var(--color-brand-bg); padding: 4rem 0;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">
        <h2 style="font-family: var(--font-sans); font-size: clamp(1.375rem, 2.5vw, 2rem); font-weight: 900; color: var(--color-brand-dark); margin: 0 0 2rem; text-align: center;">
            {{ __('pages.vrijwilligers_why_heading') }}
        </h2>
        @php
            $whyCards = [
                [
                    'bg'       => 'var(--color-brand-green)',
                    'dark'     => '#5a8a74',
                    'title'    => __('pages.vrijwilligers_why_1_title'),
                    'body'     => __('pages.vrijwilligers_why_1_body'),
                    'icon'     => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
                ],
                [
                    'bg'       => 'var(--color-brand-blue)',
                    'dark'     => '#2f5490',
                    'title'    => __('pages.vrijwilligers_why_2_title'),
                    'body'     => __('pages.vrijwilligers_why_2_body'),
                    'icon'     => 'M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z',
                ],
                [
                    'bg'       => 'var(--color-brand-orange)',
                    'dark'     => '#b34a2d',
                    'title'    => __('pages.vrijwilligers_why_3_title'),
                    'body'     => __('pages.vrijwilligers_why_3_body'),
                    'icon'     => 'M19.952 1.651a.75.75 0 0 1 .298.599V16.303a3 3 0 0 1-2.176 2.884l-1.32.377a2.553 2.553 0 1 1-1.403-4.909l2.311-.66a1.5 1.5 0 0 0 1.088-1.442V6.994l-9 2.572v9.737a3 3 0 0 1-2.176 2.884l-1.32.377a2.553 2.553 0 1 1-1.402-4.909l2.31-.66a1.5 1.5 0 0 0 1.088-1.442V5.25a.75.75 0 0 1 .544-.721l10.5-3a.75.75 0 0 1 .658.122Z',
                ],
            ];
        @endphp
        <div class="vrijwilligers-why-cols" style="display: flex; gap: 1rem; align-items: stretch;">
            @foreach ($whyCards as $card)
                <div style="flex: 1; display: flex; flex-direction: column; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(44,40,38,.09), 0 8px 28px rgba(44,40,38,.11);">
                    {{-- Colored header with background icon --}}
                    <div style="position: relative; height: 170px; background: {{ $card['bg'] }}; overflow: hidden;">
                        <svg style="position: absolute; width: 170px; height: 170px; bottom: -22px; right: -12px; transform: rotate(12deg); pointer-events: none;"
                             viewBox="0 0 24 24" fill="{{ $card['dark'] }}" stroke="none" aria-hidden="true">
                            <path d="{{ $card['icon'] }}"/>
                        </svg>
                        <div style="position: absolute; bottom: 1.1rem; left: 1.25rem; right: 1.25rem; z-index: 2;">
                            <h3 style="font-family: var(--font-sans); font-size: clamp(1.25rem, 2vw, 1.625rem); font-weight: 900; color: white; line-height: 1.15; margin: 0;">{{ $card['title'] }}</h3>
                        </div>
                    </div>
                    {{-- White card body --}}
                    <div style="padding: 1rem 1.25rem 1.4rem; background: white; flex: 1;">
                        <p style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-muted); margin: 0;">{{ $card['body'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- WHAT YOU CAN DO --}}
<section style="background: white; padding: 4rem 0; border-top: 1px solid #e8e5e2;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">
        <div class="vrijwilligers-what-grid" style="display: flex; gap: 4rem; align-items: start;">

            {{-- Left: CTA --}}
            <div style="flex: 1; min-width: 0;">
                <h2 style="font-family: var(--font-sans); font-size: clamp(1.375rem, 2vw, 1.875rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin: 0 0 1rem;">
                    {{ __('pages.vrijwilligers_contact_heading') }}
                </h2>
                <p style="font-size: 1.0625rem; line-height: 1.75; color: var(--color-brand-muted); margin: 0 0 1.75rem;">
                    {{ __('pages.vrijwilligers_contact_lead') }}
                </p>
                @php
                    $subject = app()->getLocale() === 'fr'
                        ? 'B%C3%A9n%C3%A9vole+%C3%A0+De+Harmonie'
                        : 'Vrijwilliger+bij+De+Harmonie';
                @endphp
                <a href="mailto:info@deharmonie.be?subject={{ $subject }}"
                   style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--color-brand-orange); color: white; font-family: var(--font-sans); font-size: 1rem; font-weight: 700; text-decoration: none; padding: 0.875rem 2rem; border-radius: 999px; transition: opacity 0.15s;"
                   onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    {{ __('pages.vrijwilligers_contact_cta') }}
                </a>
            </div>

            {{-- Right: unified role list --}}
            <div style="flex: 1; min-width: 0;">
                <p class="ui-label" style="color: var(--color-brand-orange); margin: 0 0 0.875rem;">
                    {{ __('pages.vrijwilligers_what_lead_label') }}
                </p>
                <ul style="list-style: none; margin: 0 0 2rem; padding: 0; display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach (app()->getLocale() === 'fr'
                        ? ['Ciné-Club', 'Table de conversation', 'Danse & mouvement', 'Atelier créatif', 'Journées d\'activités', __('pages.vrijwilligers_what_new')]
                        : ['Ciné-Club', 'Conversatietafel', 'Dans & bewegen', 'Creatief atelier', 'Activiteitsdagen', __('pages.vrijwilligers_what_new')]
                    as $item)
                        <li style="display: flex; align-items: center; gap: 0.625rem; font-size: 1.125rem; color: var(--color-brand-dark); line-height: 1.5;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--color-brand-orange); flex-shrink: 0; display: block;"></span>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
</section>


<style>
@media (max-width: 767px) {
    .vrijwilligers-photo-third { display: none !important; }
    .vrijwilligers-why-cols { flex-direction: column !important; gap: 2rem !important; }
    .vrijwilligers-what-grid { flex-direction: column !important; gap: 2rem !important; }
}
</style>

@endsection
