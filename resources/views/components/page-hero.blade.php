@props([
    'heading',
    'eyebrow',
    'eyebrowColor' => 'green',
    'lead' => null,
    'bg' => 'white',
    'pb' => '2rem',
])

<section style="background: {{ $bg }}; padding: 2.5rem 0 {{ $pb }};">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">
        <x-eyebrow :color="$eyebrowColor" mb="0.5rem">{{ $eyebrow }}</x-eyebrow>
        <h1 style="font-family: var(--font-sans); font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight: 900; line-height: 1.1; color: var(--color-brand-dark); margin: 0 0 0.625rem;">
            {{ $heading }}
        </h1>
        @if ($lead)
            <p style="font-size: 1.125rem; line-height: 1.6; color: var(--color-brand-muted); margin: 0; max-width: 38rem;">
                {{ $lead }}
            </p>
        @endif
        @if ($slot->isNotEmpty())
            <div style="margin-top: 1rem;">{{ $slot }}</div>
        @endif
    </div>
</section>
