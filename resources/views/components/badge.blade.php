@props(['type'])

@php
$styles = match($type) {
    'gratis'      => 'font-size: 1rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 4px; background-color: var(--color-brand-green); color: white;',
    'geannuleerd' => 'font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 4px; background-color: rgba(112,102,98,0.1); color: var(--color-brand-muted);',
    'volzet'      => 'display: inline-block; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; background-color: rgba(70,121,188,0.1); color: var(--color-brand-blue);',
    default       => '',
};

$defaultLabel = match($type) {
    'gratis'      => __('activities.badge_gratis'),
    'geannuleerd' => __('activities.badge_cancelled'),
    'volzet'      => __('activities.badge_full'),
    default       => '',
};
@endphp

<span style="{{ $styles }}">{{ $slot->isEmpty() ? $defaultLabel : $slot }}</span>
