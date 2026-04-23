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
                    <div style="font-family: var(--font-sans); font-size: 0.8rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.65); margin-bottom: 0.4rem;">Agenda</div>
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
                        $agendaColorIndex = 0;
                        $agendaColors = [
                            ['bg' => 'var(--color-brand-orange)', 'icon' => '#b34a2d'],
                            ['bg' => 'var(--color-brand-blue)',   'icon' => '#2f5490'],
                            ['bg' => 'var(--color-brand-green)',  'icon' => '#5a8a74'],
                        ];
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
                            <div style="width: 140px; flex-shrink: 0; display: flex; align-items: center; min-height: calc(1.375rem * 1.3);">
                                <span class="agenda-date-label" style="display: inline-block; background: var(--color-brand-green-tint); color: var(--color-brand-green-dark); font-family: var(--font-sans); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 0.25rem 0.75rem; border-radius: 999px; white-space: nowrap; opacity: {{ $isPast ? '0.55' : '1' }};">{{ $dayLabel }}</span>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                @foreach ($dayActivities as $activiteit)
                                    @php
                                        $ac = $agendaColors[$agendaColorIndex % 3];
                                        $agendaColorIndex++;
                                        $cancelled  = $activiteit->status->value === 'geannuleerd';
                                        $titleColor = $isPast || $cancelled ? 'var(--color-brand-muted)' : 'var(--color-brand-green-dark)';
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

                                        // Theme-based icon silhouette — same solid-fill treatment as the homepage cards.
                                        // Solid Heroicons for the four primary themes; closed-path outlines fill fine as silhouettes for the rest.
                                        $t = strtolower($activiteit->titel);
                                        $iconChat     = '<path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0 1 12 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 0 1-3.476.383.39.39 0 0 0-.297.17l-2.755 4.133a.75.75 0 0 1-1.248 0l-2.755-4.133a.39.39 0 0 0-.297-.17 48.9 48.9 0 0 1-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97Z" clip-rule="evenodd"/>';
                                        $iconMusic    = '<path fill-rule="evenodd" d="M19.952 1.651a.75.75 0 0 1 .298.599V16.303a3 3 0 0 1-2.176 2.884l-1.32.377a2.553 2.553 0 1 1-1.403-4.909l2.311-.66a1.5 1.5 0 0 0 1.088-1.442V6.994l-9 2.572v9.737a3 3 0 0 1-2.176 2.884l-1.32.377a2.553 2.553 0 1 1-1.402-4.909l2.31-.66a1.5 1.5 0 0 0 1.088-1.442V5.25a.75.75 0 0 1 .544-.721l10.5-3a.75.75 0 0 1 .658.122Z" clip-rule="evenodd"/>';
                                        $iconStar     = '<path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd"/>';
                                        $iconBolt     = '<path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.268a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .895-.143Z" clip-rule="evenodd"/>';
                                        $iconFood     = '<path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z"/>';
                                        $iconGame     = '<path d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>';
                                        $iconInfo     = '<path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 0 1-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 0 1-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 0 1-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.83-.727.83-1.857 0-2.584ZM12 18a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>';
                                        $iconWorkshop = '<path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd"/>';
                                        $iconArt      = '<path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd"/>';

                                        // Order matters: specific keywords before broader ones.
                                        if (str_contains($t, 'gouter') || str_contains($t, 'goûter') || str_contains($t, 'ontbijt') || str_contains($t, 'brunch') || str_contains($t, 'lunch') || str_contains($t, 'dîner') || str_contains($t, 'diner') || str_contains($t, 'souper') || str_contains($t, 'buffet') || str_contains($t, 'aperitief') || str_contains($t, 'apéro') || str_contains($t, 'apero') || str_contains($t, 'koffie') || str_contains($t, 'café') || str_contains($t, 'cafe')) {
                                            $icon = $iconFood;
                                        } elseif (str_contains($t, 'jeu') || str_contains($t, 'spel') || str_contains($t, 'domino') || str_contains($t, 'scrabble') || str_contains($t, 'bingo') || str_contains($t, 'schaak') || str_contains($t, 'quiz') || str_contains($t, 'kaart')) {
                                            $icon = $iconGame;
                                        } elseif (str_contains($t, 'infopunt') || str_contains($t, 'infopoint') || str_contains($t, 'spreekuur') || str_contains($t, 'permanentie') || str_contains($t, 'loket') || str_contains($t, 'adviseur')) {
                                            $icon = $iconInfo;
                                        } elseif (str_contains($t, 'expo') || str_contains($t, 'tentoon') || str_contains($t, 'museum') || str_contains($t, 'kunst')) {
                                            $icon = $iconArt;
                                        } elseif (str_contains($t, 'atelier') || str_contains($t, 'workshop') || str_contains($t, 'cursus') || str_contains($t, 'geheugen') || str_contains($t, 'mémoire') || str_contains($t, 'memoire')) {
                                            $icon = $iconWorkshop;
                                        } elseif (str_contains($t, 'conversat') || str_contains($t, 'tafel') || str_contains($t, 'praat')) {
                                            $icon = $iconChat;
                                        } elseif (str_contains($t, 'zumba') || str_contains($t, 'dans') || str_contains($t, 'muziek') || str_contains($t, 'concert')) {
                                            $icon = $iconMusic;
                                        } elseif (str_contains($t, 'voorstelling') || str_contains($t, 'theater') || str_contains($t, 'théâtre') || str_contains($t, 'film')) {
                                            $icon = $iconStar;
                                        } elseif (str_contains($t, 'yoga') || str_contains($t, 'sport') || str_contains($t, 'fitness') || str_contains($t, 'bewegen') || str_contains($t, 'gym') || str_contains($t, 'wandel') || str_contains($t, 'marche')) {
                                            $icon = $iconBolt;
                                        } else {
                                            $fallbacks = [$iconChat, $iconMusic, $iconStar];
                                            $icon = $fallbacks[abs(crc32($activiteit->slug)) % 3];
                                        }
                                    @endphp

                                    <a class="agenda-activity{{ $cancelled ? ' agenda-activity--cancelled' : '' }}"
                                       href="{{ route($locale . '.activiteiten.show', $activiteit->slug) }}"
                                       style="{{ $loop->first ? '' : 'margin-top: 0.625rem;' }}">
                                        <span class="agenda-activity-icon" aria-hidden="true" style="background: {{ $ac['bg'] }};">
                                            <svg viewBox="0 0 24 24" fill="{{ $ac['icon'] }}" stroke="none" width="56" height="56" style="position: absolute; bottom: -8px; right: -7px; transform: rotate(12deg); pointer-events: none;">
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
    width: 3.5rem;
    height: 2.5rem;
    border-radius: 8px;
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
