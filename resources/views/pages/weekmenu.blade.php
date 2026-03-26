@extends('layouts.app')
@section('title', __('nav.restaurant_menu'))
@section('content')

{{-- HERO --}}
<div style="position: relative; overflow: hidden;">
    <img src="{{ asset('images/photo-restaurant-vol.webp') }}" alt=""
         style="width: 100%; height: 280px; object-fit: cover; display: block; object-position: center center;">
    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(44,40,38,0.65) 0%, transparent 55%);"></div>
    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem 1.5rem;">
        <div style="max-width: 72rem; margin: 0 auto;">
            <x-eyebrow color="orange" mb="0.5rem">{{ __('weekmenu.eyebrow') }}</x-eyebrow>
            <h1 style="font-family: var(--font-sans); font-size: clamp(1.75rem, 4vw, 2.75rem); font-weight: 900; color: white; margin: 0; line-height: 1.1;">
                {{ __('nav.restaurant_menu') }}
            </h1>
            <p style="color: rgba(255,255,255,0.85); font-size: 1.125rem; margin-top: 0.35rem; margin-bottom: 0;">
                {{ __('weekmenu.tagline') }}
            </p>
        </div>
    </div>
</div>

{{-- PRACTICAL INFO --}}
<div style="background: white; border-bottom: 1px solid #e8e0d8;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 1.75rem 1.5rem;">
        <div class="practical-grid" style="display: flex; gap: 2rem;">
            <div style="flex: 1;">
                <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.25rem; margin-top: 0;">{{ __('weekmenu.hours_label') }}</p>
                <p style="font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.5; margin: 0;">{!! nl2br(e(__('weekmenu.hours_value'))) !!}</p>
            </div>
            <div style="flex: 1;">
                <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.25rem; margin-top: 0;">{{ __('weekmenu.price_label') }}</p>
                <p style="font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.5; margin: 0;">
                    {{ __('weekmenu.price_value') }}<br>
                    <span style="font-weight: 400; font-size: 0.875rem; color: var(--color-brand-muted);">{{ __('weekmenu.price_sub') }}</span>
                </p>
            </div>
            <div style="flex: 1;">
                <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.25rem; margin-top: 0;">{{ __('weekmenu.walkin_label') }}</p>
                <p style="font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); margin: 0;">{{ __('weekmenu.walkin_value') }}</p>
            </div>
            <div style="flex: 1;">
                <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.25rem; margin-top: 0;">{{ __('weekmenu.address_label') }}</p>
                <p style="font-size: 1rem; line-height: 1.5; margin: 0;">
                    <a href="tel:0220328048" style="font-weight: 700; color: var(--color-brand-blue); text-decoration: none; display: block;">02 203 28 48</a>
                    <span style="font-weight: 600; color: var(--color-brand-dark);">Antwerpsesteenweg 24</span>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- WEEKLY MENU --}}
<div style="max-width: 72rem; margin: 0 auto; padding: 2.5rem 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;">
        <h2 style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 900; color: var(--color-brand-dark); margin: 0;">
            {{ __('weekmenu.section_title') }}
        </h2>
        <span style="font-size: 0.9rem; color: var(--color-brand-muted); font-weight: 600;">
            {{ $week[app()->getLocale()] }}
        </span>
    </div>

    @php $locale = app()->getLocale(); @endphp
    <div style="display: flex; flex-direction: column; gap: 0.625rem; max-width: 640px;">
        @foreach($days as $day)
            @php
                $isHighlighted = $highlightedDate && $day['date'] === $highlightedDate;
                $dayLabel = \Carbon\Carbon::parse($day['date'])->locale($locale)->isoFormat('dddd D/MM');
            @endphp

            @if($day['closed'])

                {{-- CLOSED DAY --}}
                <div style="background: #f5f3f1; border: 1px solid #e0d9d4; border-radius: 10px; padding: 1rem 1.25rem; opacity: 0.6;">
                    <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.15rem; margin-top: 0;">{{ $dayLabel }}</p>
                    <p style="font-family: var(--font-sans); font-size: 0.95rem; font-weight: 700; color: var(--color-brand-muted); margin: 0;">
                        {{ $day['closed_label_' . $locale] ?? __('weekmenu.closed') }}
                    </p>
                </div>

            @elseif($day['special_event'])

                {{-- SPECIAL EVENT --}}
                <div style="background: #fff8f0; border: 2px solid var(--color-brand-orange); border-radius: 10px; padding: 1rem 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.6rem; gap: 1rem;">
                        <div>
                            <span style="display: inline-block; background: var(--color-brand-orange); color: white; font-family: var(--font-sans); font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; padding: 2px 8px; border-radius: 999px; margin-bottom: 0.25rem;">{{ __('weekmenu.special_badge') }}</span>
                            <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.1rem; margin-top: 0;">{{ $dayLabel }}</p>
                            <p style="font-family: var(--font-sans); font-size: 1.1rem; font-weight: 900; color: var(--color-brand-dark); margin: 0;">{{ $day[$locale]['event_label'] }}</p>
                        </div>
                        <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-orange); margin: 0; flex-shrink: 0;">€ {{ $day['price'] }}</p>
                    </div>
                    <ul style="list-style: none; padding: 0; margin: 0; border-top: 1px solid #e8e0d8; padding-top: 0.5rem; display: flex; flex-direction: column; gap: 0.25rem;">
                        @foreach($day[$locale]['courses'] as $course)
                            <li style="font-size: 0.9rem; color: var(--color-brand-dark); padding-left: 0.75rem; position: relative;">
                                <span style="position: absolute; left: 0; color: var(--color-brand-orange); font-weight: 700;" aria-hidden="true">·</span>
                                {{ $course }}
                            </li>
                        @endforeach
                    </ul>
                </div>

            @else

                {{-- STANDARD DAY --}}
                <div style="background: {{ $isHighlighted ? '#fff8f5' : 'white' }}; border: 1px solid #e8e0d8; {{ $isHighlighted ? 'border-left: 4px solid var(--color-brand-orange);' : '' }} border-radius: 10px; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                    <div style="flex: 1; min-width: 0;">
                        @if($isHighlighted)
                            <span style="display: inline-block; background: var(--color-brand-orange); color: white; font-family: var(--font-sans); font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; padding: 1px 7px; border-radius: 999px; margin-bottom: 0.3rem;">{{ $highlightedIsToday ? __('weekmenu.today') : __('weekmenu.tomorrow') }}</span>
                        @endif
                        <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.1rem; margin-top: 0;">{{ $dayLabel }}</p>
                        <p style="font-size: 0.85rem; color: var(--color-brand-muted); margin-bottom: 0.1rem; margin-top: 0;">{{ $day[$locale]['soup'] }}</p>
                        <p style="font-size: 1.25rem; font-weight: 700; color: var(--color-brand-dark); line-height: 1.3; margin: 0;">{{ $day[$locale]['main'] }}</p>
                    </div>
                    <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-orange); margin: 0; flex-shrink: 0; padding-top: 0.25rem;">€ {{ $day['price'] }}</p>
                </div>

            @endif
        @endforeach
    </div>

    <p style="font-size: 0.8rem; color: var(--color-brand-muted); font-style: italic; margin-top: 1rem;">{{ __('weekmenu.allergen_note') }}</p>
</div>

{{-- SFEER --}}
<div style="border-top: 1px solid #e8e0d8; background: white; padding: 2.5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="orange" mb="0.75rem">{{ __('weekmenu.sfeer_label') }}</x-eyebrow>
        <div class="sfeer-strip" style="display: flex; gap: 0.75rem; height: 220px; margin-bottom: 1rem; border-radius: 8px; overflow: hidden;">
            <div style="flex: 1; overflow: hidden;">
                <img src="{{ asset('images/photo-chef-taart.webp') }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
            <div style="flex: 1; overflow: hidden;">
                <img src="{{ asset('images/photo-groep-tafel.webp') }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
            <div style="flex: 1; overflow: hidden;">
                <img src="{{ asset('images/photo-feest-2.webp') }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
        </div>
        <p style="font-size: 1rem; color: var(--color-brand-muted); line-height: 1.6; max-width: 42rem; margin: 0;">{{ __('weekmenu.sfeer_caption') }}</p>
    </div>
</div>

<style>
@media (max-width: 767px) {
    .practical-grid { flex-direction: column !important; gap: 1.25rem !important; }
    .sfeer-strip { height: 140px !important; }
}
</style>

@endsection
