@extends('layouts.app')

@section('title', 'Agenda — ' . __('activities.all'))

@section('content')

{{-- Photo strip — full width, top --}}
<div style="display: flex; height: 300px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-visitors-1.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center 40%;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-visitors-2.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-samen.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
</div>

<div style="max-width: 72rem; margin: 0 auto; padding: 4rem 1.5rem 4rem;">

    {{-- Page header --}}
    <div style="margin-bottom: 2.5rem;">
        <x-eyebrow>AGENDA</x-eyebrow>
        <h1 style="font-family: var(--font-sans); font-size: 2.75rem; font-weight: 900; color: var(--color-brand-dark); margin: 0 0 0.5rem; line-height: 1.1;">
            {{ __('activities.all') }}
        </h1>
        <p style="font-size: 1.0625rem; color: var(--color-brand-muted); margin: 0;">
            {{ __('activities.overview_tagline') }}
        </p>
    </div>

    {{-- Activity list grouped by month --}}
    @php
        $grouped = $activiteiten->groupBy(fn($a) => $a->datum->translatedFormat('F Y'));
    @endphp

    @forelse ($grouped as $maand => $items)
        {{-- Month header --}}
        <div style="margin-top: 2rem; margin-bottom: 0.75rem; padding-bottom: 0.4rem; border-bottom: 2px solid var(--color-brand-orange);">
            <h2 style="font-family: var(--font-sans); font-size: 1rem; font-weight: 700; color: var(--color-brand-dark); margin: 0; text-transform: uppercase; letter-spacing: 0.04em;">
                {{ ucfirst($maand) }}
            </h2>
        </div>

        {{-- Activities in this month --}}
        @foreach ($items as $activiteit)
            <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
               class="activity-row {{ $activiteit->status === 'geannuleerd' ? 'activity-row--cancelled' : '' }}">

                {{-- Date block --}}
                <div style="flex-shrink: 0; width: 48px; text-align: center;">
                    <div class="activity-row__day">{{ $activiteit->datum->format('j') }}</div>
                </div>

                {{-- Content --}}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <p style="font-weight: 700; font-size: 1.0625rem; line-height: 1.3; color: var(--color-brand-dark); font-family: var(--font-sans); margin: 0;">
                            {{ $activiteit->titel }}
                        </p>
                        @if ($activiteit->status === 'geannuleerd')
                            <x-badge type="geannuleerd" />
                        @endif
                    </div>
                    <p style="font-size: 0.9375rem; margin: 0.2rem 0 0; color: var(--color-brand-muted);">
                        {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('dddd')) }},
                        {{ substr($activiteit->startuur, 0, 5) }}
                        @if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }} @endif
                        &middot; {{ $activiteit->locatie }}
                    </p>
                </div>
            </a>
        @endforeach

    @empty
        <div style="padding: 4rem 0;">
            <p style="color: var(--color-brand-muted); font-size: 1.0625rem;">
                {{ __('activities.no_upcoming') }}
            </p>
        </div>
    @endforelse

</div>

<style>
.activity-row {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 0.9rem 0.75rem 0.9rem 1rem;
    text-decoration: none;
    border-radius: 6px;
    border-left: 3px solid transparent;
    transition: background-color 0.15s ease, border-left-color 0.15s ease;
}
.activity-row:hover {
    background-color: var(--color-brand-bg);
    border-left-color: var(--color-brand-green);
}
.activity-row--cancelled {
    opacity: 0.7;
}
.activity-row__day {
    font-family: var(--font-sans);
    font-size: 1.75rem;
    font-weight: 900;
    line-height: 1;
    color: var(--color-brand-dark);
    transition: color 0.15s ease;
}
.activity-row:hover .activity-row__day {
    color: var(--color-brand-green);
}
</style>

@endsection
