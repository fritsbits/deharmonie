@extends('layouts.app')
@section('title', __('nav.restaurant_menu'))
@section('content')

<x-page-hero
    :eyebrow="__('weekmenu.eyebrow')"
    eyebrow-color="orange"
    :heading="__('weekmenu.heading')"
    :lead="__('weekmenu.tagline')"
    bg="white"
/>

{{-- TWO-COLUMN: MENU + PRACTICAL INFO --}}
<div style="background: var(--color-brand-orange-tint);">
    <div style="max-width: 72rem; margin: 0 auto; padding: 3rem 1.5rem;">
        <div class="menu-layout" style="display: flex; gap: 3rem; align-items: flex-start;">

            {{-- LEFT: Weekly menu on paper --}}
            <div style="flex: 2; min-width: 0;">
                <div class="menu-paper" style="position: relative; background: white; border: 1px solid rgba(44,40,38,0.08); border-radius: 2px; overflow: hidden;">
                    <livewire:week-menu />
                </div>
            </div>

            {{-- RIGHT: Practical info sidebar --}}
            <div class="practical-sidebar" style="flex: 1; min-width: 260px; position: sticky; top: 2rem;">
                <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 16px rgba(44,40,38,0.09);">
                    <div style="height: 4px; background: var(--color-brand-orange);"></div>
                    <div style="padding: 1.5rem;">

                        {{-- Hours + walk-in hint --}}
                        <div style="margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px dashed #e4dbd3;">
                            <x-eyebrow size="sm" color="orange" mb="0.35rem">{{ __('weekmenu.hours_label') }}</x-eyebrow>
                            <p class="sidebar-value">{!! nl2br(e(__('weekmenu.hours_value'))) !!}</p>
                            <p style="font-size: 1rem; color: var(--color-brand-muted); font-style: italic; margin: 0.35rem 0 0; line-height: 1.4;">{{ __('weekmenu.walkin_value') }}</p>
                        </div>

                        {{-- Price --}}
                        <div style="margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px dashed #e4dbd3;">
                            <x-eyebrow size="sm" color="orange" mb="0.35rem">{{ __('weekmenu.price_label') }}</x-eyebrow>
                            <div style="display: flex; align-items: flex-start; line-height: 1; margin: 0.1rem 0 0.2rem; gap: 0;">
                                <span style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 900; color: var(--color-brand-dark); margin-top: 0.3rem; letter-spacing: -0.01em;">€</span>
                                <span style="font-family: var(--font-sans); font-size: 3.25rem; font-weight: 900; color: var(--color-brand-dark); letter-spacing: -0.04em; line-height: 0.9;">{{ __('weekmenu.price_value') }}</span>
                            </div>
                            <p class="ui-meta" style="margin: 0;">{{ __('weekmenu.price_prefix') }} — {{ __('weekmenu.price_sub') }}</p>
                        </div>

                        {{-- Takeaway & Delivery --}}
                        <div style="margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px dashed #e4dbd3;">
                            <x-eyebrow size="sm" color="orange" mb="0.35rem">{{ __('weekmenu.order_label') }}</x-eyebrow>
                            <p style="font-size: 1rem; color: var(--color-brand-muted); margin: 0 0 0.875rem; line-height: 1.5;">{{ __('weekmenu.order_value') }}</p>
                            <a href="tel:0220328048" class="sidebar-link" style="margin-bottom: 0.4rem;">02 203 28 48</a>
                            <a href="mailto:info@deharmonie.be?subject={{ rawurlencode(__('weekmenu.order_subject')) }}&body={{ rawurlencode(__('weekmenu.order_body')) }}" class="sidebar-link">info@deharmonie.be</a>
                        </div>

                        {{-- Address → contact page --}}
                        <a href="{{ route(app()->getLocale() . '.contact') }}"
                           style="display: flex; align-items: center; justify-content: space-between; padding: 0.8rem 1rem; border: 1.5px solid #e4dbd3; border-radius: 6px; text-decoration: none; color: var(--color-brand-dark); font-size: 1rem; font-weight: 600; gap: 0.75rem;">
                            {{ __('weekmenu.address_cta') }}
                            <span style="color: var(--color-brand-orange); font-weight: 700; flex-shrink: 0;">→</span>
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- PHOTO STRIP --}}
<div style="display: flex; height: 380px; overflow: hidden;">
    <div style="flex: 2; overflow: hidden;">
        <img src="{{ asset('images/photo-chef-taart-2.webp') }}" alt="{{ __('pages.weekmenu_photo_chef_taart_alt') }}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-restaurant-bord.webp') }}" alt="{{ __('pages.weekmenu_photo_restaurant_bord_alt') }}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-feest-2.webp') }}" alt="{{ __('pages.weekmenu_photo_feest_alt') }}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
</div>

<style>
.sidebar-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--color-brand-dark);
    line-height: 1.5;
    margin: 0;
}
.sidebar-link {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--color-brand-dark);
    text-decoration: underline;
    text-underline-offset: 3px;
    text-decoration-thickness: 1px;
    display: block;
}
.menu-paper {
    box-shadow: 20px 20px 30px rgba(44,40,38,0.10);
}
.menu-paper::before,
.menu-paper::after {
    content: '';
    position: absolute;
    bottom: -2.6px;
    width: 42%; height: 45%;
    background: transparent;
    pointer-events: none;
    z-index: -1;
}
.menu-paper::before {
    left: 5%;
    box-shadow: -10px 13px 17px rgba(44,40,38,0.23);
    transform: rotate(-3.1deg);
    transform-origin: bottom left;
}
.menu-paper::after {
    right: 5%;
    box-shadow: 10px 13px 17px rgba(44,40,38,0.23);
    transform: rotate(3.1deg);
    transform-origin: bottom right;
}

/* Weekmenu paper padding — generous on desktop, compact on mobile */
.weekmenu-header { padding: 2.25rem 3.25rem; }
.weekmenu-body { padding: 3.25rem; }
.weekmenu-row--highlighted {
    margin-left: -3.25rem;
    padding-left: calc(3.25rem - 3px);
    border-left: 3px solid var(--color-brand-orange);
}
/* Prevent long single words (meal names) from forcing horizontal overflow */
.weekmenu-body p { overflow-wrap: anywhere; }

@media (max-width: 767px) {
    .menu-layout { flex-direction: column !important; gap: 2rem !important; }
    .practical-sidebar { position: static !important; min-width: 0 !important; order: -1; }
    .weekmenu-header { padding: 1.5rem 1.25rem !important; }
    .weekmenu-body { padding: 1.25rem !important; }
    .weekmenu-row--highlighted {
        margin-left: -1.25rem !important;
        padding-left: calc(1.25rem - 3px) !important;
    }
}
</style>

@endsection
