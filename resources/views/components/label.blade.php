@props(['color' => 'muted', 'mb' => '0'])

<p {{ $attributes->merge(['class' => 'ui-label']) }} style="color: var(--color-brand-{{ $color }}); margin-bottom: {{ $mb }};">{{ $slot }}</p>
