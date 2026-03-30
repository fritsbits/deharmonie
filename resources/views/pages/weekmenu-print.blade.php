<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('weekmenu.menu_label') }} — {{ $weekLabel }}</title>
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--color-brand-bg);
            font-family: var(--font-body);
            color: var(--color-brand-dark);
        }

        /* Fixed top bar — hidden when printing */
        .print-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: var(--color-brand-bg);
            border-bottom: 3px solid var(--color-brand-orange);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .print-bar-label {
            font-family: var(--font-sans);
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--color-brand-muted);
            letter-spacing: 0.03em;
        }

        .print-bar-button {
            background: var(--color-brand-orange);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            font-family: var(--font-sans);
            font-size: 0.875rem;
            font-weight: 800;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
            letter-spacing: 0.01em;
        }

        .print-bar-button:hover {
            opacity: 0.9;
        }

        /* A4 content area */
        .print-content {
            max-width: 640px;
            margin: 0 auto;
            padding: 6rem 3rem 3rem; /* top padding clears fixed bar */
            background: white;
            min-height: 100vh;
        }

        /* Document header */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--color-brand-orange);
        }

        .doc-eyebrow {
            font-family: var(--font-sans);
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--color-brand-orange);
            margin: 0 0 0.25rem;
        }

        .doc-heading {
            font-family: var(--font-sans);
            font-size: 1.75rem;
            font-weight: 900;
            color: var(--color-brand-dark);
            margin: 0;
            line-height: 1.1;
        }

        .doc-logo {
            width: 72px;
            flex-shrink: 0;
        }

        /* Day rows */
        .days-list {
            display: flex;
            flex-direction: column;
            gap: 0.875rem;
        }

        .day-row {
            display: flex;
            gap: 0;
            align-items: flex-start;
        }

        .day-row.closed {
            opacity: 0.45;
        }

        .day-date {
            width: 52px;
            flex-shrink: 0;
            text-align: right;
            padding-right: 0.875rem;
            margin-right: 0.875rem;
            border-right: 2px solid #e8e0d8;
        }

        .day-date-num {
            font-family: var(--font-sans);
            font-size: 1.5rem;
            font-weight: 900;
            line-height: 1;
            display: block;
            color: var(--color-brand-dark);
        }

        .day-date-month {
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            display: block;
            color: var(--color-brand-muted);
        }

        .day-body {
            flex: 1;
            min-width: 0;
        }

        .day-label {
            font-family: var(--font-sans);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-brand-muted);
            margin: 0 0 0.05rem;
        }

        .day-soup {
            font-size: 0.875rem;
            color: var(--color-brand-muted);
            margin: 0.1rem 0;
        }

        .day-main-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 1rem;
        }

        .day-main {
            font-family: var(--font-body);
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--color-brand-dark);
            margin: 0;
            line-height: 1.3;
        }

        .day-price {
            font-family: var(--font-sans);
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--color-brand-muted);
            flex-shrink: 0;
            font-variant-numeric: tabular-nums;
            margin: 0;
        }

        .day-closed-label {
            font-family: var(--font-body);
            font-size: 1rem;
            font-style: italic;
            color: var(--color-brand-muted);
            margin: 0;
        }

        /* Special event */
        .day-row.special {
            margin-left: -1px;
            padding-left: 1rem;
            border-left: 3px solid var(--color-brand-orange);
        }

        .day-row.special .day-date {
            border-right-color: var(--color-brand-orange);
        }

        .day-special-badge {
            display: inline-block;
            background: var(--color-brand-orange);
            color: white;
            font-family: var(--font-sans);
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            padding: 1px 7px;
            border-radius: 999px;
            margin-bottom: 0.2rem;
        }

        .day-event-label {
            font-family: var(--font-body);
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-brand-dark);
            margin: 0;
            line-height: 1.3;
        }

        .day-courses {
            list-style: none;
            padding: 0;
            margin: 0.4rem 0 0;
            border-top: 1px solid #e8e0d8;
            padding-top: 0.3rem;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .day-courses li {
            font-size: 1rem;
            color: var(--color-brand-dark);
            padding-left: 0.75rem;
            position: relative;
        }

        .day-courses li::before {
            content: '·';
            position: absolute;
            left: 0;
            color: var(--color-brand-orange);
            font-weight: 700;
        }

        /* Allergen note */
        .allergen-note {
            margin-top: 1.5rem;
            padding-top: 0.875rem;
            border-top: 1px solid #e8e0d8;
        }

        .allergen-note p {
            font-size: 0.875rem;
            color: var(--color-brand-muted);
            margin: 0;
            line-height: 1.5;
        }

        /* Print styles */
        @media print {
            .print-bar { display: none !important; }

            body { background: white; }

            .print-content {
                padding-top: 2rem;
                min-height: unset;
                box-shadow: none;
            }

            @page {
                size: A4;
                margin: 1.5cm;
            }
        }

        @media (max-width: 640px) {
            .print-content { padding: 5rem 1.25rem 2rem; }
        }
    </style>
</head>
<body>

{{-- Fixed top bar --}}
<div class="print-bar">
    <span class="print-bar-label">{{ __('weekmenu.menu_label') }} — {{ $weekLabel }}</span>
    <button class="print-bar-button" onclick="window.print()">
        🖨 {{ __('weekmenu.print_link') }}
    </button>
</div>

{{-- A4 content --}}
<div class="print-content">

    {{-- Document header --}}
    <div class="doc-header">
        <div>
            <p class="doc-eyebrow">{{ __('weekmenu.menu_label') }}</p>
            <h1 class="doc-heading">{{ $weekLabel }}</h1>
        </div>
        <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" class="doc-logo">
    </div>

    {{-- Day rows --}}
    <div class="days-list">
        @forelse ($days as $day)
            @php
                $carbon = \Carbon\Carbon::parse($day['date'])->locale($locale);
                $dateNum = $carbon->day;
                $monthAbbr = rtrim($carbon->isoFormat('MMM'), '.');
                $weekdayLabel = $carbon->isoFormat('dddd');
            @endphp

            @if ($day['closed'])
                <div class="day-row closed">
                    <div class="day-date">
                        <span class="day-date-num">{{ $dateNum }}</span>
                        <span class="day-date-month">{{ $monthAbbr }}</span>
                    </div>
                    <div class="day-body">
                        <p class="day-label">{{ $weekdayLabel }}</p>
                        <p class="day-closed-label">{{ $day['closed_label_' . $locale] ?? __('weekmenu.closed') }}</p>
                    </div>
                </div>

            @elseif ($day['special_event'])
                <div class="day-row special">
                    <div class="day-date">
                        <span class="day-date-num">{{ $dateNum }}</span>
                        <span class="day-date-month">{{ $monthAbbr }}</span>
                    </div>
                    <div class="day-body">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                            <div>
                                <span class="day-special-badge">{{ __('weekmenu.special_badge') }}</span>
                                <p class="day-event-label">{{ $day[$locale]['event_label'] }}</p>
                            </div>
                            <p class="day-price">€ {{ $day['price'] }}</p>
                        </div>
                        <ul class="day-courses">
                            @foreach ($day[$locale]['courses'] as $course)
                                <li>{{ $course }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            @else
                <div class="day-row">
                    <div class="day-date">
                        <span class="day-date-num">{{ $dateNum }}</span>
                        <span class="day-date-month">{{ $monthAbbr }}</span>
                    </div>
                    <div class="day-body">
                        <p class="day-label">{{ $weekdayLabel }}</p>
                        @if ($day[$locale]['soup'])
                            <p class="day-soup">{{ $day[$locale]['soup'] }}</p>
                        @endif
                        <div class="day-main-row">
                            <p class="day-main">{{ $day[$locale]['main'] }}</p>
                            <p class="day-price">€&thinsp;{{ $day['price'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

        @empty
            <p style="color: var(--color-brand-muted); font-size: 0.9rem;">{{ __('weekmenu.no_days') }}</p>
        @endforelse
    </div>

    {{-- Allergen note --}}
    <div class="allergen-note">
        <p>{{ __('weekmenu.allergen_note') }}</p>
    </div>

</div>

</body>
</html>
