<div>
    @php $locale = app()->getLocale(); @endphp

    {{-- Print header (hidden on screen, visible on print) --}}
    <div class="agenda-print-header" style="display: none; margin-bottom: 1.5rem; font-family: var(--font-sans);">
        <strong style="font-size: 1.125rem; font-weight: 900; color: var(--color-brand-dark);">
            De Harmonie — Weekplanning {{ $this->weekHeading }}
        </strong><br>
        <span style="font-size: 0.875rem; color: var(--color-brand-muted);">Antwerpsesteenweg 24 · 02 203 28 48</span>
    </div>

    {{-- Week navigation bar --}}
    <div class="agenda-week-nav" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; background: #d4e8df; border-bottom: 1px solid #bcd6ca;">
        @if ($this->hasPrev)
            <button wire:click="prevWeek"
                    aria-label="{{ __('activities.previous_week') }}"
                    style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-green); background: none; border: none; cursor: pointer; padding: 0;">
                ← {{ __('activities.previous_week') }}
            </button>
        @else
            <span></span>
        @endif

        <span style="font-family: var(--font-sans); font-size: 1.125rem; font-weight: 900; color: var(--color-brand-dark);">
            {{ $this->weekHeading }}
        </span>

        <div style="display: flex; align-items: center; gap: 1.25rem;">
            @if ($this->hasNext)
                <button wire:click="nextWeek"
                        aria-label="{{ __('activities.next_week') }}"
                        style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-green); background: none; border: none; cursor: pointer; padding: 0;">
                    {{ __('activities.next_week') }} →
                </button>
            @else
                <span></span>
            @endif

            <button class="agenda-print-btn"
                    onclick="window.print()"
                    aria-label="{{ __('activities.print') }}"
                    style="font-family: var(--font-sans); font-size: 0.8125rem; font-weight: 700; color: var(--color-brand-muted); background: none; border: 1px solid var(--color-brand-gray-dark); border-radius: 4px; cursor: pointer; padding: 0.25rem 0.625rem;">
                🖨 {{ __('activities.print') }}
            </button>
        </div>
    </div>

    {{-- Day sections --}}
    <div style="padding: 0 1.5rem 2rem;">
        @for ($i = 0; $i < 7; $i++)
            @php
                $day = $this->activeWeekStart->copy()->addDays($i);
                $dateKey = $day->toDateString();
                $dayActivities = $this->activiteiten->get($dateKey, collect());
                $isSatOrSun = in_array($day->dayOfWeek, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY]);
                $isToday = $day->isToday();
                $isPast = $day->isPast() && ! $isToday;

                $headingColor = $isToday
                    ? 'var(--color-brand-green)'
                    : ($isPast ? 'var(--color-brand-muted)' : 'var(--color-brand-dark)');
            @endphp

            @if ($isSatOrSun && $dayActivities->isEmpty())
                @continue
            @endif

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
            @if ($dayActivities->isEmpty())
                <p style="padding: 0.5rem 0 0; font-size: 0.875rem; color: var(--color-brand-muted); font-family: var(--font-body);">
                    {{ __('activities.no_activities_this_day') }}
                </p>
            @else
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
            @endif
        @endfor
    </div>
</div>
