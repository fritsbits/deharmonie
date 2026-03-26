@extends('layouts.app')
@section('title', __('activities.overview_heading') . ' — De Harmonie')

@section('content')

{{-- HERO --}}
<x-page-hero
    :eyebrow="__('activities.overview_hero_eyebrow')"
    eyebrow-color="green"
    :heading="__('activities.overview_heading')"
    :lead="__('activities.overview_tagline')"
    bg="white"
/>

{{-- PHOTO STRIP --}}
<div style="display: flex; height: 280px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-groep-tafel.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-buiten-activiteit.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-party.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center bottom;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-groep-actief.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
</div>

{{-- REEKSEN --}}
<section style="background: var(--color-brand-bg); padding: 5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="blue" mb="0.75rem">{{ __('activities.reeksen_eyebrow') }}</x-eyebrow>
        <x-section-heading mb="2.5rem">{{ __('activities.reeksen_heading') }}</x-section-heading>

        @php
            $iconMap = [
                1 => 'message-circle', 2 => 'message-circle', 3 => 'message-circle',
                4 => 'message-circle', 5 => 'music-2', 6 => 'brain', 7 => 'armchair',
                8 => 'monitor', 9 => 'circle-dot', 10 => 'palette', 11 => 'zap',
                12 => 'gem', 13 => 'scissors', 14 => 'shopping-bag', 15 => 'dumbbell',
                16 => 'info', 17 => 'cake', 18 => 'landmark',
            ];
            $bgColors = ['#f3dbd5','#d4e8df','#d5e0f0','#f5e8d3','#dde7d5','#e8d9ef','#d9e8f0'];
            $days = __('activities.days');
        @endphp

        <div class="reeksen-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            @foreach ($reeksen as $index => $reeks)
                @php
                    $icon = $iconMap[$reeks->id] ?? 'calendar';
                    $bg = $bgColors[$index % count($bgColors)];
                    $dag = $days[$reeks->dag_van_de_week] ?? '';
                    $uur = substr($reeks->startuur, 0, 5);
                    $beschrijving = app()->getLocale() === 'fr'
                        ? ($reeks->beschrijving_fr ?? $reeks->beschrijving_nl)
                        : ($reeks->beschrijving_nl ?? $reeks->beschrijving_fr);
                @endphp
                <div style="background: white; border: 1px solid #e8e0d8; border-radius: 10px; padding: 1.25rem; display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: {{ $bg }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <x-dynamic-component :component="'lucide-' . $icon" style="width: 20px; height: 20px; color: var(--color-brand-dark);" />
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-family: var(--font-sans); font-weight: 800; font-size: 1rem; color: var(--color-brand-dark); margin: 0 0 0.2rem;">
                            {{ app()->getLocale() === 'fr' ? ($reeks->titel_fr ?? $reeks->titel_nl) : $reeks->titel_nl }}
                        </p>
                        <p style="font-size: 0.875rem; color: var(--color-brand-muted); margin: 0 0 0.35rem;">
                            {{ __('activities.reeksen_day_prefix') }} {{ $dag }} · {{ $uur }}
                        </p>
                        @if ($beschrijving)
                            <p style="font-size: 0.875rem; color: var(--color-brand-muted); margin: 0; line-height: 1.5;">
                                {{ Str::limit(strip_tags($beschrijving), 80) }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SPECIAL MOMENTS --}}
<section style="background: #eef5f1; padding: 5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="green" mb="0.75rem">{{ __('activities.special_moments_eyebrow') }}</x-eyebrow>
        <x-section-heading mb="2rem">{{ __('activities.special_moments_heading') }}</x-section-heading>

        <div class="moments-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; height: 320px;">
            {{-- Large photo left --}}
            <div style="border-radius: 12px; overflow: hidden;">
                <img src="{{ asset('images/photo-feest-2.webp') }}" alt=""
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
            {{-- Two smaller right --}}
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="flex: 1; border-radius: 12px; overflow: hidden;">
                    <img src="{{ asset('images/photo-buiten-event.webp') }}" alt=""
                         style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
                <div style="flex: 1; border-radius: 12px; overflow: hidden;">
                    <img src="{{ asset('images/photo-cake.jpg') }}" alt=""
                         style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FULL AGENDA LINK --}}
<section style="background: white; padding: 3rem 1.5rem; text-align: center;">
    <a href="{{ route(app()->getLocale() . '.activiteiten.agenda') }}"
       style="font-family: var(--font-sans); font-size: 1rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
        {{ __('activities.agenda_link') }} →
    </a>
</section>

<style>
@media (max-width: 767px) {
    .reeksen-grid { grid-template-columns: 1fr !important; }
    .moments-grid { grid-template-columns: 1fr !important; height: auto !important; }
    .moments-grid > div:last-child { display: none; }
    .moments-grid > div:first-child { height: 220px; }
}
</style>

@endsection
