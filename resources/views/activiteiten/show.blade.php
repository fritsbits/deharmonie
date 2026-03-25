@extends('layouts.app')

@section('title', $activiteit->titel)
@section('description', strip_tags($activiteit->beschrijving ?? ''))

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">

    <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
       class="text-sm font-semibold hover:underline inline-flex items-center gap-1"
       style="color: var(--color-brand-blue)">
        {{ __('activities.back') }}
    </a>

    <div class="mt-6 grid md:grid-cols-3 gap-10">

        {{-- Left: main content --}}
        <div class="md:col-span-2">

            {{-- Cancellation banner --}}
            @if ($activiteit->status->value === 'geannuleerd')
                <div class="rounded-lg p-4 mb-6 font-semibold"
                     style="background-color: rgba(235,102,67,0.1); color: var(--color-brand-orange); border: 1px solid rgba(235,102,67,0.3); font-size: 0.875rem;">
                    &times; {{ $activiteit->notice ?? __('activities.cancellation_notice') }}
                </div>
            @endif

            {{-- Label --}}
            <x-eyebrow>{{ __('activities.label') }}</x-eyebrow>

            {{-- Title --}}
            <h1 class="font-extrabold uppercase leading-tight mb-1"
                style="font-family: var(--font-sans); font-size: 2rem; color: var(--color-brand-dark)">
                {{ $activiteit->titel }}
            </h1>

            {{-- Alternate language title --}}
            @if (app()->getLocale() === 'nl' && $activiteit->titel_fr)
                <p class="text-base mb-6" style="color: var(--color-brand-muted)">
                    {{ $activiteit->titel_fr }}
                </p>
            @elseif (app()->getLocale() === 'fr' && $activiteit->titel_nl)
                <p class="text-base mb-6" style="color: var(--color-brand-muted)">
                    {{ $activiteit->titel_nl }}
                </p>
            @else
                <div class="mb-6"></div>
            @endif

            {{-- Image --}}
            @if ($activiteit->getFirstMediaUrl('afbeelding'))
                <img src="{{ $activiteit->getFirstMediaUrl('afbeelding') }}"
                     alt="{{ $activiteit->titel }}"
                     class="w-full h-64 object-cover rounded-lg mb-6">
            @endif

            {{-- Description --}}
            @if ($activiteit->beschrijving)
                <div class="leading-relaxed mb-8" style="color: var(--color-brand-dark); font-size: 1.125rem;">
                    {!! $activiteit->beschrijving !!}
                </div>
            @endif

            {{-- Registration form --}}
            @if ($activiteit->status->value === 'gepubliceerd')
                <div class="rounded-lg p-6" style="border: 1px solid var(--color-brand-gray); background: white">
                    <livewire:registration-form :activiteit="$activiteit" />
                </div>
            @elseif ($activiteit->status->value === 'geannuleerd')
                <p class="text-sm italic" style="color: var(--color-brand-muted)">
                    {{ __('activities.registration_closed') }}
                </p>
            @endif

        </div>

        {{-- Right: sidebar --}}
        <div class="md:col-span-1">
            <div class="rounded-lg p-5 sticky top-6" style="border: 1px solid var(--color-brand-gray); background: white">

                {{-- Date --}}
                <div class="mb-4">
                    <p class="text-xs uppercase font-bold mb-1" style="color: var(--color-brand-green)">
                        {{ __('activities.date') }}
                    </p>
                    <p class="font-bold" style="color: var(--color-brand-dark); font-size: 1.25rem;">
                        {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY')) }}
                    </p>
                    <p class="font-semibold" style="color: var(--color-brand-muted); font-size: 1.125rem;">
                        {{ substr($activiteit->startuur, 0, 5) }}
                        @if ($activiteit->einduur)
                            &ndash; {{ substr($activiteit->einduur, 0, 5) }}
                        @endif
                    </p>
                </div>

                <div class="my-4" style="border-top: 1px solid var(--color-brand-gray)"></div>

                {{-- Price --}}
                <div class="mb-4">
                    <p class="text-xs uppercase font-bold mb-1" style="color: var(--color-brand-green)">
                        {{ __('activities.price') }}
                    </p>
                    <span class="font-bold" style="color: var(--color-brand-dark); font-size: 1.25rem;">
                        {{ $activiteit->getPrijsLabel(app()->getLocale()) }}
                    </span>
                    @if (!$activiteit->prijs || $activiteit->prijs == 0)
                        <x-badge type="gratis" />
                    @endif
                </div>

                <div class="my-4" style="border-top: 1px solid var(--color-brand-gray)"></div>

                {{-- Location --}}
                <div class="mb-4">
                    <p class="text-xs uppercase font-bold mb-1" style="color: var(--color-brand-green)">
                        {{ __('activities.location') }}
                    </p>
                    <p class="font-semibold" style="color: var(--color-brand-dark); font-size: 1.125rem;">{{ $activiteit->locatie }}</p>
                </div>

                <div class="my-4" style="border-top: 1px solid var(--color-brand-gray)"></div>

                {{-- Contact --}}
                <div>
                    <p class="text-xs uppercase font-bold mb-1" style="color: var(--color-brand-green)">
                        Contact
                    </p>
                    <p class="font-bold mb-1" style="color: var(--color-brand-dark); font-size: 1.125rem;">De Harmonie</p>
                    <p class="mb-0.5" style="font-size: 1.125rem;">
                        <a href="tel:0220328048" class="hover:underline font-bold" style="color: var(--color-brand-blue)">
                            02 203 28 48
                        </a>
                    </p>
                    <p style="font-size: 1rem;">
                        <a href="mailto:info@deharmonie.be" class="hover:underline" style="color: var(--color-brand-blue)">
                            info@deharmonie.be
                        </a>
                    </p>
                </div>

                {{-- Print --}}
                <div class="mt-5 pt-4" style="border-top: 1px solid var(--color-brand-gray)">
                    <a href="{{ route(app()->getLocale() . '.activiteiten.print', $activiteit->slug) }}"
                       style="display: inline-block; font-size: 0.9rem; font-weight: 700; padding: 0.5rem 1rem; border: 2px solid var(--color-brand-blue); color: var(--color-brand-blue); border-radius: 4px; text-decoration: none; font-family: var(--font-sans);">
                        &#9113; {{ __('activities.print') }}
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
