@php
    $activiteit = $getRecord();
    $section = $activiteit->categorie->section();
    $bg = match ($section) {
        'beweeg' => 'var(--color-brand-orange)',
        'maak_leer' => 'var(--color-brand-green)',
        'ontmoet_beleef' => 'var(--color-brand-blue)',
        default => '#9ca3af',
    };
@endphp

<div style="display: flex; align-items: center; gap: 0.625rem; min-height: 32px;">
    <span style="display: inline-block; width: 30px; height: 30px; border-radius: 6px; background: {{ $bg }}; flex-shrink: 0; position: relative; overflow: hidden;">
        <svg viewBox="0 0 24 24" fill="white" stroke="none" width="20" height="20" style="position: absolute; top: 5px; left: 5px;">
            {!! $activiteit->categorie->icon() !!}
        </svg>
    </span>
    <span style="display: flex; flex-direction: column; min-width: 0;">
        <span style="display: flex; align-items: center; gap: 0.4rem; font-weight: 600; line-height: 1.3;">
            <span title="{{ $activiteit->titel_nl }}">{{ \Illuminate\Support\Str::limit($activiteit->titel_nl, 35) }}</span>
            @if ($activiteit->soort->value === 'speciaal')
                <span style="font-size: 0.65rem; background: #efc56a; color: #5a4419; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">speciaal</span>
            @endif
            @if ($activiteit->status->value === 'geannuleerd')
                <span style="font-size: 0.65rem; background: #c43; color: white; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">geannuleerd</span>
            @endif
            @if ($activiteit->status->value === 'concept')
                <span style="font-size: 0.65rem; background: #ddd; color: #444; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">concept</span>
            @endif
        </span>
        <span style="font-size: 0.8rem; color: #706662;">{{ $activiteit->categorie->getLabel() }}</span>
    </span>
</div>
