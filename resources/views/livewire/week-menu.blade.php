<div>
{{-- ORANGE HEADER --}}
<div class="weekmenu-header" style="background: var(--color-brand-orange);">
    <div class="ui-label" style="color: rgba(255,255,255,0.8); margin-bottom: 0.3rem;">{{ __('weekmenu.menu_label') }}</div>
    <div class="weekmenu-header-row" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 900; color: white; margin: 0; line-height: 1.1;">{{ $this->weekHeading }}</h2>
        <div class="weekmenu-header-actions" style="display: flex; gap: 0.6rem; flex-shrink: 0; align-items: center; flex-wrap: wrap;">
            @if ($this->hasPrev)
                <button wire:click="prevWeek" aria-label="{{ __('weekmenu.prev_week') }}"
                        style="background: transparent; color: white; border: 1.5px solid rgba(255,255,255,0.55); font-family: var(--font-sans); font-size: 1rem; font-weight: 700; padding: 0.3rem 0.9rem; border-radius: 999px; cursor: pointer; white-space: nowrap;">
                    ← {{ __('weekmenu.prev_week') }}
                </button>
            @endif
            @if ($this->hasNext)
                <button wire:click="nextWeek" aria-label="{{ __('weekmenu.next_week') }}"
                        style="background: transparent; color: white; border: 1.5px solid rgba(255,255,255,0.55); font-family: var(--font-sans); font-size: 1rem; font-weight: 700; padding: 0.3rem 0.9rem; border-radius: 999px; cursor: pointer; white-space: nowrap;">
                    {{ __('weekmenu.next_week') }} →
                </button>
            @endif
            <a href="{{ route(app()->getLocale() . '.weekmenu.print', ['week' => $this->weekOffset]) }}"
               target="_blank"
               aria-label="{{ __('weekmenu.print_link') }}"
               style="display: inline-flex; align-items: center; gap: 0.35rem; background: transparent; color: white; border: 1.5px solid rgba(255,255,255,0.55); font-family: var(--font-sans); font-size: 1rem; font-weight: 700; padding: 0.3rem 0.9rem; border-radius: 999px; text-decoration: none; white-space: nowrap;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                {{ __('weekmenu.print_link') }}
            </a>
        </div>
    </div>
</div>

{{-- MENU BODY --}}
<div class="weekmenu-body">

    @php $locale = app()->getLocale(); @endphp
    <div style="display: flex; flex-direction: column; gap: 1.875rem;">
        @forelse ($this->days as $day)
            @php
                $carbon        = $day->date->locale($locale);
                $isPast        = $carbon->lt(\Carbon\Carbon::today());
                $isHighlighted = $this->highlightedDate && $day->date->toDateString() === $this->highlightedDate;
                $dateNum       = $carbon->day;
                $monthAbbr     = $carbon->isoFormat('MMM');

                // Replace weekday name with contextual label when highlighted
                if ($isHighlighted && $this->highlightedIsToday) {
                    $label      = __('weekmenu.today');
                    $labelColor = 'var(--color-brand-orange)';
                } elseif ($isHighlighted && $this->highlightedIsTomorrow) {
                    $label      = __('weekmenu.tomorrow');
                    $labelColor = 'var(--color-brand-orange)';
                } elseif ($isHighlighted) {
                    $label      = __('weekmenu.next_meal');
                    $labelColor = 'var(--color-brand-orange)';
                } else {
                    $label      = $carbon->isoFormat('dddd');
                    $labelColor = $isPast ? '#9e9690' : 'var(--color-brand-muted)';
                }

                $textColor    = $isPast ? '#9e9690' : 'var(--color-brand-dark)';
                $mutedColor   = $isPast ? '#9e9690' : 'var(--color-brand-muted)';
                $dateNumColor = $isPast ? '#9e9690' : ($isHighlighted ? 'var(--color-brand-orange)' : 'var(--color-brand-dark)');
                $dividerColor = ($isHighlighted && !$isPast) ? 'var(--color-brand-orange)' : '#e8e0d8';
                $monthAbbr    = rtrim($monthAbbr, '.');

                // Highlighted row extends to paper left edge — see CSS in pages/weekmenu.blade.php
                $rowClass = $isHighlighted ? 'weekmenu-row weekmenu-row--highlighted' : 'weekmenu-row';
            @endphp

            @if ($day->special_event)
                {{-- SPECIAL EVENT — extends to paper edge like highlighted --}}
                <div class="weekmenu-row weekmenu-row--highlighted" style="display: flex; align-items: flex-start; gap: 0; {{ $isPast ? 'opacity: 0.45;' : '' }}">
                    {{-- Date column --}}
                    <div style="width: 52px; flex-shrink: 0; text-align: right; padding-right: 1rem; border-right: 2px solid var(--color-brand-orange); margin-right: 1rem;">
                        <span style="font-family: var(--font-sans); font-size: 1.875rem; font-weight: 900; line-height: 1.0; display: block; color: var(--color-brand-orange);">{{ $dateNum }}</span>
                        <span style="font-size: 0.8125rem; font-weight: 800; text-transform: uppercase; display: block; color: {{ $mutedColor }};">{{ $monthAbbr }}</span>
                    </div>
                    {{-- Content --}}
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                            <div>
                                <span style="display: inline-block; background: var(--color-brand-orange); color: white; font-family: var(--font-sans); font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; padding: 2px 7px; border-radius: 999px; margin-bottom: 0.2rem;">{{ __('weekmenu.special_badge') }}</span>
                                <p style="font-family: var(--font-body); font-size: 1.5rem; font-weight: 700; color: var(--color-brand-dark); margin: 0; line-height: 1.3;">{{ $day->event_label }}</p>
                            </div>
                            <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 800; color: {{ $mutedColor }}; margin: 0; flex-shrink: 0;">€ {{ $day->price }}</p>
                        </div>
                        <ul style="list-style: none; padding: 0; margin: 0.5rem 0 0; border-top: 1px solid #e8e0d8; padding-top: 0.4rem; display: flex; flex-direction: column; gap: 0.2rem;">
                            @foreach ($day->coursesForLocale as $course)
                                <li style="font-size: 1.15rem; color: var(--color-brand-dark); padding-left: 0.75rem; position: relative;">
                                    <span style="position: absolute; left: 0; color: var(--color-brand-orange); font-weight: 700;" aria-hidden="true">·</span>
                                    {{ $course }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            @else
                {{-- STANDARD DAY --}}
                <div class="{{ $rowClass }}" style="display: flex; align-items: flex-start; gap: 0;">
                    {{-- Date column --}}
                    <div style="width: 52px; flex-shrink: 0; text-align: right; padding-right: 1rem; border-right: 2px solid {{ $dividerColor }}; margin-right: 1rem;">
                        <span style="font-family: var(--font-sans); font-size: 1.875rem; font-weight: 900; line-height: 1.0; display: block; color: {{ $dateNumColor }};">{{ $dateNum }}</span>
                        <span style="font-size: 0.8125rem; font-weight: 800; text-transform: uppercase; display: block; color: {{ $mutedColor }};">{{ $monthAbbr }}</span>
                    </div>
                    {{-- Content --}}
                    <div style="flex: 1; min-width: 0;">
                        <p class="ui-label" style="color: {{ $labelColor }}; margin: 0 0 0.05rem;">{{ $label }}</p>
                        <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 1rem;">
                            <p style="font-family: var(--font-body); font-size: 1.5rem; font-weight: 700; color: {{ $textColor }}; margin: 0; line-height: 1.3;">{{ $day->main }}</p>
                            <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 700; color: {{ $mutedColor }}; margin: 0; flex-shrink: 0; font-variant-numeric: tabular-nums;">€&thinsp;{{ $day->price }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <p class="ui-meta">{{ __('weekmenu.no_days') }}</p>
        @endforelse
    </div>

    <div style="border-top: 1px solid #e8e0d8; padding-top: 0.875rem; margin-top: 1.5rem;">
        <p style="font-size: 1rem; color: var(--color-brand-muted); margin: 0;">{{ __('weekmenu.allergen_note') }}</p>
    </div>

</div>
</div>
