@extends('layouts.app')
@section('title', __('activities.agenda_page_heading') . ' — ' . __('activities.all'))

@section('content')

<div class="agenda-screen-only">
    <x-page-hero
        eyebrow="Agenda"
        eyebrow-color="green"
        :heading="__('activities.agenda_page_heading') . ' De Harmonie'"
        bg="white"
    />
</div>

<div class="agenda-bg-wrapper" style="background: var(--color-brand-green-tint);">
    <div style="max-width: 72rem; margin: 0 auto; padding: 2rem 1.5rem 4rem;">
        <div class="agenda-paper-outer">
            <div class="agenda-paper">
                @php $locale = app()->getLocale(); $agendaRoute = $locale . '.activiteiten.agenda'; @endphp

                {{-- Print header (hidden on screen, visible on print) --}}
                <div class="agenda-print-header" style="display: none; padding: 0 3.25rem 1.5rem; font-family: var(--font-sans);">
                    <span style="font-size: 1.25rem; font-weight: 700; color: var(--color-brand-muted);">{{ $weekHeading }}</span>
                </div>

                {{-- Dark green card header --}}
                <div class="agenda-card-header" style="background: #3a6b52; padding: 2.25rem 3.25rem;">
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
                        <button onclick="window.print()" class="agenda-print-btn press-scale"
                                style="display: inline-flex; align-items: center; gap: 0.4rem; background: white; color: #3a6b52; border: none; border-radius: 6px; font-family: var(--font-sans); font-size: 1rem; font-weight: 700; padding: 0.6rem 1.25rem; cursor: pointer; white-space: nowrap;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="6 9 6 2 18 2 18 9"/>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                <rect x="6" y="14" width="12" height="8"/>
                            </svg>
                            {{ __('activities.print') }}
                        </button>
                    </div>
                    <div style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.75); margin-bottom: 0.4rem;">Agenda</div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <h2 style="font-family: var(--font-sans); font-size: 2.25rem; font-weight: 900; color: white; margin: 0; line-height: 1.1;">{{ $weekHeading }}</h2>
                        <div style="display: flex; gap: 0.6rem; align-items: center;">
                            @if ($hasPrev)
                                <a href="{{ route($agendaRoute, ['week' => $prevWeek]) }}" aria-label="{{ __('activities.previous_week') }}"
                                        class="press-scale"
                                        style="display: inline-block; background: transparent; color: white; border: 1.5px solid rgba(255,255,255,0.55); font-family: var(--font-sans); font-size: 1rem; font-weight: 700; padding: 0.6rem 1.25rem; border-radius: 999px; cursor: pointer; white-space: nowrap; text-decoration: none;">
                                    ← {{ __('activities.previous_week') }}
                                </a>
                            @endif
                            @if ($hasNext)
                                <a href="{{ route($agendaRoute, ['week' => $nextWeek]) }}" aria-label="{{ __('activities.next_week') }}"
                                        class="press-scale"
                                        style="display: inline-block; background: transparent; color: white; border: 1.5px solid rgba(255,255,255,0.55); font-family: var(--font-sans); font-size: 1rem; font-weight: 700; padding: 0.6rem 1.25rem; border-radius: 999px; cursor: pointer; white-space: nowrap; text-decoration: none;">
                                    {{ __('activities.next_week') }} →
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Day sections --}}
                <div class="agenda-body" style="padding: 2.5rem 3.25rem 2.5rem;">
                    @php
                        $hasAnyActivity = false;
                        $dayIndex = 0;
                    @endphp

                    @for ($i = 0; $i < 7; $i++)
                        @php
                            $day           = $weekStart->copy()->addDays($i);
                            $dateKey       = $day->toDateString();
                            $dayActivities = $activiteiten->get($dateKey, collect());
                            $isPast        = $day->isPast() && ! $day->isToday();
                            $dayLabel      = mb_strtoupper($day->locale($locale)->isoFormat('dddd'));
                        @endphp

                        @if ($dayActivities->isEmpty())
                            @continue
                        @endif

                        @php $hasAnyActivity = true; @endphp

                        @if ($dayIndex > 0)
                            <div style="height: 1px; background: rgba(160,195,180,0.35);"></div>
                        @endif
                        @php $dayIndex++; @endphp

                        <div class="agenda-day-group" style="display: flex; align-items: flex-start; gap: 1.5rem; padding: 1.5rem 0;">
                            <div style="width: 140px; flex-shrink: 0; display: flex; align-items: flex-start; padding-top: 0.625rem;">
                                <span class="agenda-date-label" style="display: inline-block; background: var(--color-brand-green-tint); color: var(--color-brand-green-dark); font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.25rem 0.875rem; border-radius: 999px; white-space: nowrap; opacity: {{ $isPast ? '0.6' : '1' }};">{{ $dayLabel }}</span>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                @foreach ($dayActivities as $activiteit)
                                    @php
                                        $ac = match ($activiteit->categorie->section()) {
                                            'beweeg'         => ['bg' => 'var(--color-brand-orange)', 'icon' => '#b34a2d'],
                                            'maak_leer'      => ['bg' => 'var(--color-brand-green)',  'icon' => '#5a8a74'],
                                            'ontmoet_beleef' => ['bg' => 'var(--color-brand-blue)',   'icon' => '#2f5490'],
                                        };
                                        $cancelled  = $activiteit->status->value === 'geannuleerd';
                                        $titleColor = $isPast || $cancelled ? 'var(--color-brand-muted)' : 'var(--color-brand-dark)';
                                        $metaColor  = $isPast || $cancelled ? '#c8c0bc' : 'var(--color-brand-muted)';

                                        $timeStr = substr($activiteit->startuur, 0, 5);
                                        if ($activiteit->einduur) {
                                            $timeStr .= '–' . substr($activiteit->einduur, 0, 5);
                                        }
                                        $metaParts = [$timeStr];
                                        if ($activiteit->locatie) {
                                            $metaParts[] = $activiteit->locatie;
                                        }
                                        if ($activiteit->prijs !== null) {
                                            $metaParts[] = $activiteit->getPrijsLabel($locale);
                                        }
                                        $metaStr = implode(' · ', $metaParts);

                                        $icon = $activiteit->categorie->icon();
                                    @endphp

                                    <a class="agenda-activity{{ $cancelled ? ' agenda-activity--cancelled' : '' }}"
                                       href="{{ route($locale . '.activiteiten.show', $activiteit->slug) }}"
                                       style="{{ $loop->first ? '' : 'margin-top: 0.625rem;' }}">
                                        <span class="agenda-activity-icon" aria-hidden="true" style="background: {{ $ac['bg'] }};">
                                            <svg viewBox="0 0 24 24" fill="{{ $ac['icon'] }}" stroke="none" width="45" height="45" style="position: absolute; bottom: -4px; right: 4px; transform: rotate(12deg); pointer-events: none;">
                                                {!! $icon !!}
                                            </svg>
                                        </span>
                                        <span class="agenda-activity-body">
                                            <span style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                <span class="agenda-activity-title" style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 700; color: {{ $titleColor }}; line-height: 1.3;">{{ $activiteit->titel }}</span>
                                                @if ($cancelled)
                                                    <x-badge type="geannuleerd" />
                                                @endif
                                            </span>
                                            <span class="agenda-activity-meta tabular-nums" style="display: block; font-size: 1.0625rem; color: {{ $metaColor }}; margin: 0.25rem 0 0; font-family: var(--font-body);">{{ $metaStr }}</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endfor

                    @if (! $hasAnyActivity)
                        <p style="padding: 2rem 0; color: var(--color-brand-muted); font-size: 1.0625rem; font-family: var(--font-body);">
                            {{ __('activities.no_activities_this_week') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.agenda-paper-outer {
}
.agenda-paper {
    position: relative;
    background: white;
    border: 1px solid rgba(44,40,38,0.08);
    border-radius: 2px;
    overflow: hidden;
    box-shadow: 20px 20px 30px rgba(44,40,38,0.10);
}
.agenda-paper::before,
.agenda-paper::after {
    content: '';
    position: absolute;
    bottom: -2.6px;
    width: 42%; height: 45%;
    background: transparent;
    pointer-events: none;
    z-index: -1;
}
.agenda-paper::before {
    left: 5%;
    box-shadow: -10px 13px 17px rgba(44,40,38,0.23);
    transform: rotate(-3.1deg);
    transform-origin: bottom left;
}
.agenda-paper::after {
    right: 5%;
    box-shadow: 10px 13px 17px rgba(44,40,38,0.23);
    transform: rotate(3.1deg);
    transform-origin: bottom right;
}

@@media (max-width: 767px) {
    .agenda-paper-outer { transform: none !important; }
}

.agenda-activity {
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    text-decoration: none;
    padding: 0.625rem 2.25rem 0.625rem 0.75rem;
    margin-left: -0.75rem;
    margin-right: -0.75rem;
    border-radius: 6px;
    transition: background-color 160ms ease;
}
.agenda-activity-icon {
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
    width: 5rem;
    height: 3.5rem;
    border-radius: 10px;
}
.agenda-activity-body {
    flex: 1;
    min-width: 0;
    display: block;
}
.agenda-activity::after {
    content: '→';
    position: absolute;
    top: 50%;
    right: 0.75rem;
    transform: translateY(-50%) translateX(0);
    color: var(--color-brand-green);
    font-family: var(--font-sans);
    font-size: 1rem;
    font-weight: 500;
    transition: transform 160ms ease;
    pointer-events: none;
}
.agenda-activity:hover,
.agenda-activity:focus-visible {
    background-color: rgba(129, 181, 156, 0.10);
}
.agenda-activity:hover::after,
.agenda-activity:focus-visible::after {
    transform: translateY(-50%) translateX(4px);
}
.agenda-activity:focus-visible {
    outline: 2px solid var(--color-brand-green-dark);
    outline-offset: 2px;
}
.agenda-activity--cancelled {
    opacity: 0.6;
}

@@media print {
    @@page { size: A4 portrait; margin: 12mm 14mm; }

    /* Hide all screen chrome */
    header, footer,
    .agenda-card-header,
    .agenda-print-btn { display: none !important; }

    /* Show print-only date subtitle and left-align it flush with the heading */
    .agenda-print-header { display: block !important; padding: 0 0 0.25rem !important; margin-top: -0.5rem; }

    /* Strip card decoration and collapse spacing so date subtitle sits under h1 */
    .agenda-screen-only section { padding-bottom: 0 !important; }
    .agenda-bg-wrapper { background: white !important; padding-top: 0 !important; }
    .agenda-paper-outer { transform: none !important; }
    .agenda-paper {
        border: none !important;
        box-shadow: none !important;
        overflow: visible !important;
    }
    .agenda-paper::before,
    .agenda-paper::after { display: none !important; }

    /* Compact body padding */
    .agenda-body { padding: 0 0 0.5rem !important; }

    /* Compact day groups */
    .agenda-day-group { padding: 0.875rem 0 !important; }

    /* Slightly larger type for paper — seniors hold paper further away */
    .agenda-activity-title { font-size: 1.25rem !important; color: var(--color-brand-dark) !important; }
    .agenda-activity-meta  { font-size: 1rem !important; }
    .agenda-date-num       { font-size: 1.75rem !important; }
    .agenda-date-label     { font-size: 0.8rem !important; letter-spacing: 0.02em !important; }

    /* Strip link affordances so print matches the pre-link layout */
    .agenda-activity {
        padding: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        background: none !important;
        border-radius: 0 !important;
        opacity: 1 !important;
    }
    .agenda-activity::after {
        display: none !important;
    }
    .agenda-activity-icon {
        display: none !important;
    }
}
</style>

@endsection
