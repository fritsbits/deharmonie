@props(['mb' => '0'])

<h2 {{ $attributes }} style="font-family: var(--font-sans); font-size: 2.4rem; font-weight: 900; line-height: 1.1; color: var(--color-brand-dark); margin-bottom: {{ $mb }};">{{ $slot }}</h2>
