<div>
    @php $locale = app()->getLocale(); @endphp

    {{-- Print header (hidden on screen, visible on print) --}}
    <div class="agenda-print-header" style="display: none; padding: 1rem 3.25rem 0; font-family: var(--font-sans);">
        <span style="font-size: 1rem; font-weight: 700; color: var(--color-brand-dark);">{{ $this->weekHeading }}</span>
        <span style="font-size: 0.875rem; color: var(--color-brand-muted); margin-left: 1rem;">Antwerpsesteenweg 24 · 02 203 28 48</span>
    </div>

    {{-- Dark green card header --}}
    <div class="agenda-card-header" style="background: #3a6b52; padding: 2.25rem 3.25rem;">
        {{-- Print button top-right --}}
        <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
            <button onclick="window.print()" class="agenda-print-btn"
                    style="display: inline-flex; align-items: center; gap: 0.4rem; background: white; color: #3a6b52; border: none; border-radius: 6px; font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; padding: 0.6rem 1.25rem; cursor: pointer; white-space: nowrap;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                {{ __('activities.print') }}
            </button>
        </div>
        {{-- Eyebrow --}}
        <div style="font-family: var(--font-sans); font-size: 0.6rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.65); margin-bottom: 0.3rem;">Agenda</div>
        {{-- Week heading + nav buttons --}}
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
            <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 900; color: white; margin: 0; line-height: 1.1;">{{ $this->weekHeading }}</h2>
            <div style="display: flex; gap: 0.6rem; align-items: center;">
                @if ($this->hasPrev)
                    <button wire:click="prevWeek" aria-label="{{ __('activities.previous_week') }}"
                            style="background: transparent; color: white; border: 1.5px solid rgba(255,255,255,0.55); font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; padding: 0.6rem 1.25rem; border-radius: 999px; cursor: pointer; white-space: nowrap;">
                        ← {{ __('activities.previous_week') }}
                    </button>
                @endif
                @if ($this->hasNext)
                    <button wire:click="nextWeek" aria-label="{{ __('activities.next_week') }}"
                            style="background: transparent; color: white; border: 1.5px solid rgba(255,255,255,0.55); font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; padding: 0.6rem 1.25rem; border-radius: 999px; cursor: pointer; white-space: nowrap;">
                        {{ __('activities.next_week') }} →
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Day sections --}}
    <div class="agenda-body" style="padding: 0 3.25rem 2.5rem;">
        @php $hasAnyActivity = false; $dayIndex = 0; @endphp

        @for ($i = 0; $i < 7; $i++)
            @php
                $day           = $this->activeWeekStart->copy()->addDays($i);
                $dateKey       = $day->toDateString();
                $dayActivities = $this->activiteiten->get($dateKey, collect());
                $isPast        = $day->isPast() && ! $day->isToday();
                $monthAbbr     = mb_strtoupper(rtrim($day->locale($locale)->isoFormat('MMM'), '.'));
                $dateNumColor  = $isPast ? 'var(--color-brand-muted)' : 'var(--color-brand-dark)';
                $dividerColor  = $isPast ? '#dde8e3' : '#bcd6ca';
                $headingColor  = $isPast ? 'var(--color-brand-muted)' : 'var(--color-brand-dark)';
            @endphp

            @if ($dayActivities->isEmpty())
                @continue
            @endif

            @php $hasAnyActivity = true; @endphp

            {{-- Divider between days --}}
            @if ($dayIndex > 0)
                <div style="height: 1px; background: rgba(160,195,180,0.4);"></div>
            @endif
            @php $dayIndex++; @endphp

            {{-- Day group --}}
            <div class="agenda-day-group" style="display: flex; align-items: flex-start; padding: 1.75rem 0;">

                {{-- Date lockup --}}
                <div style="width: 108px; flex-shrink: 0; text-align: right; padding-right: 1rem; border-right: 2px solid {{ $dividerColor }}; margin-right: 1.5rem;">
                    <span class="agenda-date-label" style="display: block; font-family: var(--font-sans); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: {{ $headingColor }}; margin-bottom: 0.15rem;">{{ mb_strtoupper($day->locale($locale)->isoFormat('dddd')) }}</span>
                    <span class="agenda-date-num" style="display: block; font-family: var(--font-sans); font-size: 2.25rem; font-weight: 900; line-height: 0.95; color: {{ $dateNumColor }};">{{ $day->day }}</span>
                    <span class="agenda-date-label" style="display: block; font-family: var(--font-sans); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-brand-muted); margin-top: 0.2rem;">{{ $monthAbbr }}</span>
                </div>

                {{-- Activities --}}
                <div style="flex: 1; min-width: 0;">
                    @foreach ($dayActivities as $activiteit)
                        @php
                            $cancelled  = $activiteit->status->value === 'geannuleerd';
                            $titleColor = $cancelled ? '#9e9690' : 'var(--color-brand-dark)';
                            $metaColor  = $cancelled ? '#c8c0bc' : 'var(--color-brand-muted)';

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
                        @endphp

                        <div class="agenda-activity" style="{{ $loop->first ? '' : 'margin-top: 1.25rem;' }}">
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span class="agenda-activity-title" style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 700; color: {{ $titleColor }}; line-height: 1.3;">{{ $activiteit->titel }}</span>
                                @if ($cancelled)
                                    <x-badge type="geannuleerd" />
                                @endif
                            </div>
                            <p class="agenda-activity-meta" style="font-size: 1.0625rem; color: {{ $metaColor }}; margin: 0.25rem 0 0; font-family: var(--font-body);">{{ $metaStr }}</p>
                        </div>
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
