@extends('layouts.app')
@section('title', 'Contact')
@section('content')

<x-page-hero
    :eyebrow="__('pages.contact_eyebrow')"
    eyebrow-color="blue"
    :heading="__('pages.contact_heading')"
    :lead="__('pages.contact_lead')"
    bg="white"
/>

{{-- PHOTO STRIP --}}
<div style="display: flex; height: 260px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-contact-gebouw.webp') }}" alt="{{ __('common.building_exterior_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-contact-onthaal.webp') }}" alt="{{ __('common.reception_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;object-position:center top;">
    </div>
    <div class="contact-photo-third" style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-contact-terras.webp') }}" alt="{{ __('common.terrace_alt') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
</div>

{{-- CONTACT INFO --}}
<div style="background: #eef2f8;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 3.5rem 1.5rem;">
        <div class="contact-grid" style="display: flex; gap: 3rem; align-items: start;">

            {{-- LEFT: details --}}
            <div style="flex: 1; min-width: 0;">
                <div style="background: white; border-radius: 10px; padding: 0 1.25rem;">

                    {{-- Openingsuren --}}
                    <div class="contact-field-row">
                        <div class="contact-icon-tile">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4679bc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <p class="contact-field-label">{{ __('common.opening_hours') }}</p>
                            <p style="line-height: 1.75; color: var(--color-brand-dark);">{{ __('common.mon_fri') }}: 10:00–16:30<br>{{ __('common.sat') }}: 10:00–14:00</p>
                        </div>
                    </div>

                    {{-- Telefoon --}}
                    <div class="contact-field-row">
                        <div class="contact-icon-tile">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4679bc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.71 3.39 2 2 0 0 1 3.68 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.64a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div>
                            <p class="contact-field-label">{{ __('common.phone') }}</p>
                            <a href="tel:0220328048" style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 900; color: var(--color-brand-dark); text-decoration: none; line-height: 1.1;">02/203.28.48</a>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="contact-field-row" style="border-bottom: none;">
                        <div class="contact-icon-tile">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4679bc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div>
                            <p class="contact-field-label">Email</p>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <div>
                                    <p style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-bottom: 0.05rem;">{{ __('common.email_general') }}</p>
                                    <a href="mailto:info@deharmonie.be" class="contact-email-link" style="font-size: 1rem; color: var(--color-brand-blue); text-decoration: none;">info@deharmonie.be</a>
                                </div>
                                <div>
                                    <p style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-bottom: 0.05rem;">{{ __('common.email_activities') }}</p>
                                    <a href="mailto:animatie@deharmonie.be" class="contact-email-link" style="font-size: 1rem; color: var(--color-brand-blue); text-decoration: none;">animatie@deharmonie.be</a>
                                </div>
                                <div>
                                    <p style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-bottom: 0.05rem;">{{ __('common.email_services') }}</p>
                                    <a href="mailto:diensten@deharmonie.be" class="contact-email-link" style="font-size: 1rem; color: var(--color-brand-blue); text-decoration: none;">diensten@deharmonie.be</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- RIGHT: OpenStreetMap embed (GDPR-safe, no cookies, no tracking) --}}
            <div style="flex: 1.4; min-width: 0; border-radius: 8px; overflow: hidden; border: 1px solid var(--color-brand-gray); display: flex; flex-direction: column;">
                <iframe
                    src="https://www.openstreetmap.org/export/embed.html?bbox=4.3515%2C50.8568%2C4.3555%2C50.8588&layer=humanitarian&marker=50.8578%2C4.3535"
                    style="flex: 1; width: 100%; min-height: 320px; border: 0; display: block;"
                    loading="lazy"
                    title="{{ __('common.map') }}">
                </iframe>
                {{-- Address row — matches left column styling --}}
                <div class="contact-field-row" style="border-bottom: none; padding: 1.25rem 1.25rem; background: white; border-top: 1px solid var(--color-brand-gray);">
                    <div class="contact-icon-tile">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4679bc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p class="contact-field-label">{{ __('common.address') }}</p>
                        <p style="line-height: 1.6; color: var(--color-brand-dark);">Antwerpsesteenweg 24<br>1000 Brussel</p>
                    </div>
                    <a
                        href="https://www.openstreetmap.org/?mlat=50.8578&mlon=4.3535#map=17/50.8578/4.3535"
                        target="_blank"
                        rel="noopener noreferrer"
                        style="display: inline-flex; align-items: center; gap: 0.4rem; font-family: var(--font-sans); font-size: 0.8125rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: none; white-space: nowrap; align-self: center; min-height: 44px; padding: 0 0.25rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        {{ __('common.open_in_maps') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.contact-field-row {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem 0;
    border-bottom: 1px solid var(--color-brand-gray);
}
.contact-icon-tile {
    width: 48px; height: 48px;
    background: rgba(70, 121, 188, 0.12);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.contact-field-label {
    font-family: var(--font-sans);
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--color-brand-blue);
    margin-bottom: 0.25rem;
}
.contact-email-link:hover { text-decoration: underline; }

@media (max-width: 767px) {
    .contact-photo-third { display: none !important; }
    .contact-grid { flex-direction: column !important; }
    .contact-grid > div:last-child { min-height: 280px !important; }
}
</style>

@endsection
