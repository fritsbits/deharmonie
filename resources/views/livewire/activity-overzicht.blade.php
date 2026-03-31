<div>
    {{-- Week navigation bar --}}
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; background: #d4e8df; border-bottom: 1px solid #bcd6ca;">
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

        @if ($this->hasNext)
            <button wire:click="nextWeek"
                    aria-label="{{ __('activities.next_week') }}"
                    style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-green); background: none; border: none; cursor: pointer; padding: 0;">
                {{ __('activities.next_week') }} →
            </button>
        @else
            <span></span>
        @endif
    </div>

    {{-- Activity list --}}
    <div style="padding: 0.5rem 1.5rem 1.5rem;">
        @php $locale = app()->getLocale(); @endphp

        @if ($this->activiteiten->isEmpty())
            <p style="padding: 2rem 0; color: var(--color-brand-muted); font-size: 1.0625rem;">
                {{ __('activities.no_upcoming') }}
            </p>
        @else
            @foreach ($this->activiteiten as $date => $dayActiviteiten)
                @foreach ($dayActiviteiten as $activiteit)
                    @php
                        $cancelled  = $activiteit->status->value === 'geannuleerd';
                        $dateColor  = $cancelled ? '#9e9690' : 'var(--color-brand-dark)';
                        $monthColor = $cancelled ? '#b8b0ac' : 'var(--color-brand-muted)';
                        $divColor   = $cancelled ? '#d8d0cc' : '#bcd6ca';
                        $titleColor = $cancelled ? '#9e9690' : 'var(--color-brand-dark)';
                        $metaColor  = $cancelled ? '#b8b0ac' : 'var(--color-brand-muted)';
                        $monthAbbr  = rtrim($activiteit->datum->locale($locale)->isoFormat('MMM'), '.');
                    @endphp

                    @if (! $loop->parent->first || ! $loop->first)
                        <div style="height: 1px; background: rgba(160,195,180,0.35); margin-left: calc(52px + 0.875rem + 2px + 0.875rem);"></div>
                    @endif

                    <a href="{{ route($locale . '.activiteiten.show', $activiteit->slug) }}"
                       class="activity-overzicht-row"
                       style="display: flex; align-items: flex-start; gap: 0; padding: 0.75rem 0.5rem 0.75rem 0; text-decoration: none; border-radius: 6px; border-left: 3px solid transparent;">

                        {{-- Date column --}}
                        <div style="width: 52px; flex-shrink: 0; text-align: right; padding-right: 0.875rem; border-right: 2px solid {{ $divColor }}; margin-right: 0.875rem;">
                            <span style="font-family: var(--font-sans); font-size: 1.875rem; font-weight: 900; line-height: 1; color: {{ $dateColor }}; display: block;">{{ $activiteit->datum->format('j') }}</span>
                            <span style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: block; color: {{ $monthColor }}; margin-top: 1px;">{{ $monthAbbr }}</span>
                        </div>

                        {{-- Content --}}
                        <div style="flex: 1; min-width: 0; padding-top: 0.2rem;">
                            <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                <p style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 700; color: {{ $titleColor }}; line-height: 1.25; margin: 0;">
                                    {{ $activiteit->titel }}
                                </p>
                                @if ($cancelled)
                                    <x-badge type="geannuleerd" />
                                @endif
                            </div>
                            <p style="font-size: 0.9rem; color: {{ $metaColor }}; margin: 0.15rem 0 0; line-height: 1.4;">
                                {{ ucfirst($activiteit->datum->locale($locale)->isoFormat('dddd')) }}
                                · {{ substr($activiteit->startuur, 0, 5) }}
                                @if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }} @endif
                                · {{ $activiteit->locatie }}
                            </p>
                        </div>
                    </a>
                @endforeach
            @endforeach
        @endif
    </div>

    <style>
    .activity-overzicht-row:hover {
        background: rgba(255, 255, 255, 0.7) !important;
        border-left-color: var(--color-brand-green) !important;
    }
    </style>
</div>
