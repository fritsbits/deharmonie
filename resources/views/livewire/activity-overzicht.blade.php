<div>
    @php $locale = app()->getLocale(); @endphp

    {{-- Print header (hidden on screen, visible on print) --}}
    <div class="agenda-print-header" style="display: none; padding: 1.5rem 3.25rem 0; font-family: var(--font-sans);">
        <strong style="font-size: 1.125rem; font-weight: 900; color: var(--color-brand-dark);">
            De Harmonie — Weekplanning {{ $this->weekHeading }}
        </strong><br>
        <span style="font-size: 0.875rem; color: var(--color-brand-muted);">Antwerpsesteenweg 24 · 02 203 28 48</span>
    </div>

    {{-- Dark green card header --}}
    <div style="background: #3a6b52; padding: 2.25rem 3.25rem;">
        <div style="font-family: var(--font-sans); font-size: 0.6rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.65); margin-bottom: 0.3rem;">Agenda</div>
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
            <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 900; color: white; margin: 0; line-height: 1.1;">{{ $this->weekHeading }}</h2>
            <div style="display: flex; gap: 0.6rem; flex-shrink: 0; align-items: center; flex-wrap: wrap;">
                @if ($this->hasPrev)
                    <button wire:click="prevWeek" aria-label="{{ __('activities.previous_week') }}"
                            style="background: transparent; color: white; border: 1.5px solid rgba(255,255,255,0.55); font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; padding: 0.3rem 0.9rem; border-radius: 999px; cursor: pointer; white-space: nowrap;">
                        ← {{ __('activities.previous_week') }}
                    </button>
                @endif
                @if ($this->hasNext)
                    <button wire:click="nextWeek" aria-label="{{ __('activities.next_week') }}"
                            style="background: transparent; color: white; border: 1.5px solid rgba(255,255,255,0.55); font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; padding: 0.3rem 0.9rem; border-radius: 999px; cursor: pointer; white-space: nowrap;">
                        {{ __('activities.next_week') }} →
                    </button>
                @endif
                <button onclick="window.print()" class="agenda-print-btn"
                        style="display: inline-flex; align-items: center; gap: 0.35rem; background: transparent; color: white; border: 1.5px solid rgba(255,255,255,0.55); font-family: var(--font-sans); font-size: 0.8rem; font-weight: 700; padding: 0.3rem 0.9rem; border-radius: 999px; cursor: pointer; white-space: nowrap;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    {{ __('activities.print') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Day sections --}}
    <div style="padding: 0 3.25rem 2.5rem;">
        @php $hasAnyActivity = false; @endphp

        @for ($i = 0; $i < 7; $i++)
            @php
                $day = $this->activeWeekStart->copy()->addDays($i);
                $dateKey = $day->toDateString();
                $dayActivities = $this->activiteiten->get($dateKey, collect());
                $isToday = $day->isToday();
                $isPast = $day->isPast() && ! $isToday;

                $headingColor = $isToday
                    ? 'var(--color-brand-green)'
                    : ($isPast ? 'var(--color-brand-muted)' : 'var(--color-brand-dark)');
            @endphp

            @if ($dayActivities->isEmpty())
                @continue
            @endif

            @php $hasAnyActivity = true; @endphp

            {{-- Day heading --}}
            <div style="margin-top: 1.75rem;">
                <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <span style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 900; letter-spacing: 0.08em; text-transform: uppercase; color: {{ $headingColor }};">
                        {{ mb_strtoupper($day->locale($locale)->isoFormat('dddd')) }}
                    </span>
                    <span style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--color-brand-muted);">
                        {{ mb_strtoupper($day->locale($locale)->isoFormat('D MMMM')) }}
                    </span>
                    @if ($isToday)
                        <span style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; background: var(--color-brand-green); color: white; padding: 0.1rem 0.4rem; border-radius: 3px;">
                            {{ __('activities.date_today') }}
                        </span>
                    @endif
                </div>
                <div style="height: 1px; background: #bcd6ca;"></div>
            </div>

            {{-- Activities for this day --}}
            @foreach ($dayActivities as $activiteit)
                @php
                    $cancelled = $activiteit->status->value === 'geannuleerd';
                    $timeColor = $cancelled ? '#b8b0ac' : 'var(--color-brand-muted)';
                    $titleColor = $cancelled ? '#9e9690' : 'var(--color-brand-dark)';
                    $metaColor = $cancelled ? '#c8c0bc' : 'var(--color-brand-muted)';
                @endphp

                @if (! $loop->first)
                    <div style="height: 1px; background: rgba(160,195,180,0.3); margin-left: 88px;"></div>
                @endif

                <div style="display: flex; align-items: baseline; gap: 0; padding: 0.55rem 0.25rem;">
                    {{-- Time column --}}
                    <div style="width: 88px; flex-shrink: 0; text-align: right; padding-right: 1rem;">
                        <span style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: {{ $timeColor }}; white-space: nowrap;">
                            {{ substr($activiteit->startuur, 0, 5) }}@if($activiteit->einduur)–{{ substr($activiteit->einduur, 0, 5) }}@endif
                        </span>
                    </div>

                    {{-- Title + meta --}}
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                            <span style="font-family: var(--font-sans); font-size: 1.0625rem; font-weight: 700; color: {{ $titleColor }}; line-height: 1.3;">
                                {{ $activiteit->titel }}
                            </span>
                            @if ($cancelled)
                                <x-badge type="geannuleerd" />
                            @endif
                        </div>
                        <p style="font-size: 0.8125rem; color: {{ $metaColor }}; margin: 0.1rem 0 0; font-family: var(--font-body);">
                            {{ $activiteit->locatie }}@if($activiteit->prijs !== null) · {{ $activiteit->getPrijsLabel($locale) }}@endif
                        </p>
                    </div>
                </div>
            @endforeach
        @endfor

        @if (! $hasAnyActivity)
            <p style="padding: 2rem 0; color: var(--color-brand-muted); font-size: 1.0625rem; font-family: var(--font-body);">
                {{ __('activities.no_activities_this_week') }}
            </p>
        @endif
    </div>
</div>
