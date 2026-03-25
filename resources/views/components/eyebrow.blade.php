@props(['color' => 'green', 'mb' => '0'])

<p {{ $attributes }} style="font-family: var(--font-sans); font-size: 1.1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-{{ $color }}); margin-bottom: {{ $mb }};">{{ $slot }}</p>
