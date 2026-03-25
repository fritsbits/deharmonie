@extends('layouts.app')

@section('title', $activiteit->titel)
@section('description', strip_tags($activiteit->beschrijving ?? ''))
@section('og_title', $activiteit->titel)
@section('og_description', strip_tags($activiteit->beschrijving ?? ''))

@section('content')
<div class="max-w-3xl mx-auto px-6 py-10">
    <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
       class="text-sm font-semibold hover:underline"
       style="color: var(--color-brand-blue)">
        &larr; {{ app()->getLocale() === 'fr' ? 'Retour aux activités' : 'Terug naar alle activiteiten' }}
    </a>

    <div class="mt-6">
        {{-- Status badge --}}
        @if ($activiteit->status === 'geannuleerd')
            <div class="rounded-lg p-4 mb-6 text-sm font-semibold" style="background-color: #fde8e3; color: #c0392b; border: 1px solid #f5c6b8;">
                &times; {{ $activiteit->notice ?? __('activities.cancellation_notice') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: var(--color-brand-orange)">
                    {{ $activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY') }}
                </p>
                <h1 style="font-family: var(--font-sans); font-size: 2rem; font-weight: 800; color: var(--color-brand-dark); line-height: 1.2;">
                    {{ $activiteit->titel }}
                </h1>
            </div>
            <a href="{{ route(app()->getLocale() . '.activiteiten.print', $activiteit->slug) }}"
               class="flex-shrink-0 text-xs font-semibold px-3 py-1.5 rounded"
               style="border: 1px solid var(--color-brand-gray); color: var(--color-brand-muted)">
                &#9113; {{ __('activities.print') }}
            </a>
        </div>

        {{-- Meta --}}
        <div class="flex flex-wrap gap-6 py-4 text-sm mb-6"
             style="border-top: 1px solid var(--color-brand-gray); border-bottom: 1px solid var(--color-brand-gray)">
            <div>
                <span class="block text-xs uppercase font-bold mb-0.5" style="color: var(--color-brand-muted)">
                    {{ app()->getLocale() === 'fr' ? 'Heure' : 'Uur' }}
                </span>
                <span style="color: var(--color-brand-dark)">
                    {{ substr($activiteit->startuur, 0, 5) }}
                    @if ($activiteit->einduur) &ndash; {{ substr($activiteit->einduur, 0, 5) }} @endif
                </span>
            </div>
            <div>
                <span class="block text-xs uppercase font-bold mb-0.5" style="color: var(--color-brand-muted)">
                    {{ __('activities.location') }}
                </span>
                <span style="color: var(--color-brand-dark)">{{ $activiteit->locatie }}</span>
            </div>
            <div>
                <span class="block text-xs uppercase font-bold mb-0.5" style="color: var(--color-brand-muted)">
                    {{ __('activities.price') }}
                </span>
                <span style="color: var(--color-brand-dark)">{{ $activiteit->getPrijsLabel(app()->getLocale()) }}</span>
            </div>
        </div>

        {{-- Image --}}
        @if ($activiteit->getFirstMediaUrl('afbeelding'))
            <img src="{{ $activiteit->getFirstMediaUrl('afbeelding') }}"
                 alt="{{ $activiteit->titel }}"
                 class="w-full h-64 object-cover rounded-lg mb-6">
        @endif

        {{-- Description --}}
        @if ($activiteit->beschrijving)
            <div class="text-sm leading-relaxed mb-8" style="color: var(--color-brand-dark)">
                {!! $activiteit->beschrijving !!}
            </div>
        @endif

        {{-- Registration --}}
        @if ($activiteit->status === 'gepubliceerd')
            <div class="mt-8 pt-8" style="border-top: 1px solid var(--color-brand-gray)">
                <livewire:registration-form :activiteit="$activiteit" />
            </div>
        @elseif ($activiteit->status === 'geannuleerd')
            <p class="text-sm italic" style="color: var(--color-brand-muted)">
                {{ __('activities.registration_closed') }}
            </p>
        @endif
    </div>
</div>
@endsection
