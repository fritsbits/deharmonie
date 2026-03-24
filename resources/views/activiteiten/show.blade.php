@extends('layouts.app')

@section('title', $activiteit->titel)
@section('description', strip_tags($activiteit->beschrijving ?? ''))
@section('og_title', $activiteit->titel)
@section('og_description', strip_tags($activiteit->beschrijving ?? ''))

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
       class="text-sm font-semibold hover:underline"
       style="color: var(--color-brand-green)">
        {{ __('activities.back') }}
    </a>

    <div class="mt-6 bg-white rounded-xl shadow-sm overflow-hidden">
        @if ($activiteit->getFirstMediaUrl('afbeelding'))
            <img src="{{ $activiteit->getFirstMediaUrl('afbeelding') }}"
                 alt="{{ $activiteit->titel }}"
                 class="w-full h-64 object-cover">
        @endif

        <div class="p-6 md:p-8">
            {{-- Cancellation banner --}}
            @if ($activiteit->status === 'geannuleerd')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="font-semibold text-red-700">
                        {{ $activiteit->notice ?? __('activities.cancellation_notice') }}
                    </p>
                </div>
            @endif

            <div class="flex items-start justify-between gap-4 flex-wrap">
                <h1 class="font-sans font-extrabold text-3xl" style="color: var(--color-brand-dark)">
                    {{ $activiteit->titel }}
                </h1>
                <a href="{{ route(app()->getLocale() . '.activiteiten.print', $activiteit->slug) }}"
                   class="text-sm border px-3 py-1 rounded hover:text-white transition-colors"
                   style="border-color: var(--color-brand-green); color: var(--color-brand-green)">
                    {{ __('activities.print') }}
                </a>
            </div>

            {{-- Meta info --}}
            <dl class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <dt class="font-semibold text-gray-500 uppercase tracking-wide text-xs">{{ __('activities.date') }}</dt>
                    <dd class="mt-1 font-semibold">
                        {{ $activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY') }}
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-500 uppercase tracking-wide text-xs">{{ __('activities.time') }}</dt>
                    <dd class="mt-1 font-semibold">
                        {{ substr($activiteit->startuur, 0, 5) }}
                        @if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }} @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-500 uppercase tracking-wide text-xs">{{ __('activities.location') }}</dt>
                    <dd class="mt-1 font-semibold">{{ $activiteit->locatie }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-500 uppercase tracking-wide text-xs">{{ __('activities.price') }}</dt>
                    <dd class="mt-1 font-semibold" style="color: var(--color-brand-green)">
                        {{ $activiteit->getPrijsLabel(app()->getLocale()) }}
                    </dd>
                </div>
            </dl>

            {{-- Description --}}
            @if ($activiteit->beschrijving)
                <div class="mt-6 prose max-w-none">
                    {!! $activiteit->beschrijving !!}
                </div>
            @endif

            {{-- Registration section --}}
            @if ($activiteit->status === 'gepubliceerd')
                <div class="mt-8 border-t pt-8">
                    <livewire:registration-form :activiteit="$activiteit" />
                </div>
            @elseif ($activiteit->status === 'geannuleerd')
                <p class="mt-6 text-sm text-gray-500 italic">{{ __('activities.registration_closed') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
