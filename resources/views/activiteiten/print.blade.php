@extends('layouts.print')

@section('title', $activiteit->titel)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="text-center mb-8">
        <p class="text-sm font-semibold text-gray-500 uppercase tracking-widest">De Harmonie</p>
        <h1 class="font-sans font-extrabold text-3xl mt-2">{{ $activiteit->titel }}</h1>
        @if ($activiteit->status->value === 'geannuleerd')
            <p class="mt-2 text-red-600 font-bold">{{ __('activities.cancelled') }}</p>
        @endif
    </div>

    <table class="w-full text-sm mb-6 border-collapse">
        <tr class="border-b">
            <th class="text-left py-2 pr-4 font-semibold w-32">{{ __('activities.date') }}</th>
            <td class="py-2">{{ $activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY') }}</td>
        </tr>
        <tr class="border-b">
            <th class="text-left py-2 pr-4 font-semibold">{{ __('activities.time') }}</th>
            <td class="py-2">
                {{ substr($activiteit->startuur, 0, 5) }}
                @if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }} @endif
            </td>
        </tr>
        <tr class="border-b">
            <th class="text-left py-2 pr-4 font-semibold">{{ __('activities.location') }}</th>
            <td class="py-2">{{ $activiteit->locatie }}</td>
        </tr>
        <tr>
            <th class="text-left py-2 pr-4 font-semibold">{{ __('activities.price') }}</th>
            <td class="py-2">{{ $activiteit->getPrijsLabel(app()->getLocale()) }}</td>
        </tr>
    </table>

    @if ($activiteit->beschrijving)
        <div class="prose text-sm">
            {!! $activiteit->beschrijving !!}
        </div>
    @endif

    @if ($activiteit->notice)
        <div class="mt-6 border border-red-300 rounded p-3 text-sm text-red-700">
            {{ $activiteit->notice }}
        </div>
    @endif

    <div class="mt-8 text-xs text-gray-400 border-t pt-4">
        De Harmonie · Antwerpsesteenweg 24, 1000 Brussel · 02 203 28 48 · info@deharmonie.be
    </div>
</div>
@endsection
