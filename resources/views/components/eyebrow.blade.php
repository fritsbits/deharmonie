@props(['color' => 'green', 'mb' => '0', 'size' => 'lg'])

<p {{ $attributes }} style="font-family: var(--font-sans); {{ $size === 'sm' ? 'font-size: 0.875rem; font-weight: 700; letter-spacing: 0.06em;' : 'font-size: 1.3125rem; font-weight: 800; letter-spacing: 0.08em;' }} text-transform: uppercase; color: var(--color-brand-{{ $color }}); margin-bottom: {{ $mb }};">{{ $slot }}</p>
