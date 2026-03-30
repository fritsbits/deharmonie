@extends('layouts.app')
@section('title', 'Contact')
@section('content')

<x-page-hero
    :eyebrow="__('pages.contact_eyebrow')"
    eyebrow-color="blue"
    :heading="__('pages.contact_heading')"
    :lead="__('pages.contact_lead')"
    bg="white"
/>

<div style="background: #eef2f8;">
    <div class="max-w-5xl mx-auto px-6 py-10">
        <div class="grid md:grid-cols-2 gap-10">
            <div class="space-y-6">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-widest mb-2" style="color: var(--color-brand-muted)">
                        {{ __('common.address') }}
                    </h2>
                    <p style="color: var(--color-brand-dark)">
                        Antwerpsesteenweg 24<br>1000 Brussel
                    </p>
                </div>
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-widest mb-2" style="color: var(--color-brand-muted)">
                        {{ __('common.opening_hours') }}
                    </h2>
                    <div class="space-y-1 text-sm" style="color: var(--color-brand-dark)">
                        <p>{{ __('common.mon_fri') }}: 10:00–16:30</p>
                        <p>{{ __('common.sat') }}: 10:00–14:00</p>
                    </div>
                </div>
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-widest mb-2" style="color: var(--color-brand-muted)">
                        {{ __('common.phone') }}
                    </h2>
                    <a href="tel:0220328048" class="font-semibold hover:underline" style="color: var(--color-brand-blue)">02/203.28.48</a>
                </div>
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-widest mb-2" style="color: var(--color-brand-muted)">Email</h2>
                    <p class="space-y-1 text-sm">
                        <a href="mailto:info@deharmonie.be" class="block hover:underline" style="color: var(--color-brand-blue)">info@deharmonie.be</a>
                        <a href="mailto:animatie@deharmonie.be" class="block hover:underline text-xs" style="color: var(--color-brand-muted)">animatie@deharmonie.be</a>
                        <a href="mailto:diensten@deharmonie.be" class="block hover:underline text-xs" style="color: var(--color-brand-muted)">diensten@deharmonie.be</a>
                    </p>
                </div>
            </div>
            <div class="rounded-lg overflow-hidden" style="min-height: 300px; border: 1px solid var(--color-brand-gray)">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2519.5!2d4.3520!3d50.8520!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c3c38f0b0b0b0b%3A0x0!2zQW50d2VycHNlc3RlZW53ZWcgMjQ!5e0!3m2!1snl!2sbe!4v1234567890"
                    class="w-full h-full border-0 min-h-64"
                    allowfullscreen loading="lazy"
                    title="{{ __('common.map') }}">
                </iframe>
            </div>
        </div>
    </div>
</div>

@endsection
