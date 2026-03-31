@extends('layouts.app')

@section('title', $activiteit->titel)
@section('description', strip_tags($activiteit->beschrijving ?? ''))

@section('content')

<div>

{{-- Header band --}}
<div style="background: white; padding: 2rem 1.5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">

        <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
           class="font-semibold hover:underline inline-flex items-center gap-1"
           style="color: var(--color-brand-blue); font-size: 0.9rem; padding: 0.5rem 0; display: inline-block;">
            {{ __('activities.back') }}
        </a>

        {{-- Cancellation banner --}}
        @if ($activiteit->status->value === 'geannuleerd')
            <div class="rounded-lg p-4 mt-4 font-semibold"
                 style="background-color: rgba(235,102,67,0.1); color: var(--color-brand-orange); border: 1px solid rgba(235,102,67,0.3); font-size: 0.875rem;">
                &times; {{ $activiteit->notice ?? __('activities.cancellation_notice') }}
            </div>
        @endif

        <div style="margin-top: 1.25rem;">
            <x-eyebrow color="green">{{ __('activities.label') }}</x-eyebrow>
            <h1 style="font-family: var(--font-sans); font-size: clamp(1.75rem, 3.5vw, 2.5rem); font-weight: 900; line-height: 1.1; color: var(--color-brand-dark); margin: 0 0 0.25rem; text-transform: uppercase;">
                {{ $activiteit->titel }}
            </h1>
            {{-- "Ingeschreven" badge — client-side via localStorage --}}
            <span
                x-data="{ booked: false }"
                x-init="
                    const ids = JSON.parse(localStorage.getItem('bookedActivities') || '[]');
                    booked = ids.includes({{ $activiteit->id }});
                "
                x-show="booked"
                style="display: none; background: var(--color-brand-green); color: white; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 4px; font-family: var(--font-sans); letter-spacing: 0.04em; text-transform: uppercase; vertical-align: middle; margin-left: 0.5rem;">
                {{ __('activities.booked') }}
            </span>
        </div>

        {{-- Logistics strip --}}
        @php $diff = now()->startOfDay()->diffInDays($activiteit->datum->startOfDay(), false); @endphp
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem 2.5rem; margin-top: 1.5rem;">

            {{-- Date --}}
            <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="width: 52px; height: 52px; background: rgba(129,181,156,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#81b59c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div>
                    <p style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-green); margin: 0 0 0.2rem; font-family: var(--font-sans);">{{ __('activities.date') }}</p>
                    <p style="font-weight: 700; font-size: 1.2rem; color: var(--color-brand-dark); margin: 0; line-height: 1.2;">
                        @if ($diff === 0){{ __('activities.date_today') }} &middot; @elseif ($diff === 1){{ __('activities.date_tomorrow') }} &middot; @endif
                        {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY')) }}
                    </p>
                    <p style="font-size: 1rem; color: var(--color-brand-muted); margin: 0;">
                        {{ substr($activiteit->startuur, 0, 5) }}@if ($activiteit->einduur) &ndash; {{ substr($activiteit->einduur, 0, 5) }}@endif
                    </p>
                </div>
            </div>

            {{-- Price --}}
            <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="width: 52px; height: 52px; background: rgba(129,181,156,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#81b59c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                </div>
                <div>
                    <p style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-green); margin: 0 0 0.2rem; font-family: var(--font-sans);">{{ __('activities.price') }}</p>
                    @if (!$activiteit->prijs || $activiteit->prijs == 0)
                        <p style="font-weight: 700; font-size: 1.2rem; color: var(--color-brand-dark); margin: 0; line-height: 1.2;">{{ __('activities.badge_gratis') }}</p>
                    @else
                        <span style="font-weight: 700; font-size: 1.2rem; color: var(--color-brand-dark);">
                            {{ $activiteit->getPrijsLabel(app()->getLocale()) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Location --}}
            <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="width: 52px; height: 52px; background: rgba(129,181,156,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#81b59c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div>
                    <p style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-green); margin: 0 0 0.2rem; font-family: var(--font-sans);">{{ __('activities.location') }}</p>
                    <p style="font-weight: 700; font-size: 1.2rem; color: var(--color-brand-dark); margin: 0; line-height: 1.2;">{{ $activiteit->locatie }}</p>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Main content --}}
<div style="background: #eef5f1;">
<div style="max-width: 72rem; margin: 0 auto; padding: 2.5rem 1.5rem 4rem;">

    {{-- Description --}}
    @if ($activiteit->beschrijving)
        <div class="mb-8" style="color: var(--color-brand-muted); font-size: 1.3rem; line-height: 1.65; max-width: 60ch;">
            {!! $activiteit->beschrijving !!}
        </div>
    @endif

    {{-- Registration form disabled — people register by phone/email --}}
    @if ($activiteit->status->value === 'gepubliceerd')
        <hr style="border: none; border-top: 1px solid var(--color-brand-border-green); margin: 2rem 0;">
        <h3 style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 900; color: var(--color-brand-dark); margin: 0 0 0.5rem; line-height: 1.2;">
            {{ __('activities.questions') }}
        </h3>
        <p style="font-size: 1.1rem; color: var(--color-brand-dark); line-height: 1.65; max-width: 60ch; margin: 0;">
            {{ __('activities.register_cta_heading') }}
            <a href="tel:0220328048" class="font-semibold hover:underline" style="color: var(--color-brand-blue); white-space: nowrap;">02&nbsp;203&nbsp;28&nbsp;48</a>,
            <a href="mailto:info@deharmonie.be" class="hover:underline" style="color: var(--color-brand-blue);">info@deharmonie.be</a>.
        </p>
    @elseif ($activiteit->status->value === 'geannuleerd')
        <p class="text-sm italic" style="color: var(--color-brand-muted)">
            {{ __('activities.registration_closed') }}
        </p>
    @endif

</div>
</div>

{{-- Mobile sticky CTA removed — registration form is disabled --}}

</div>

@endsection
