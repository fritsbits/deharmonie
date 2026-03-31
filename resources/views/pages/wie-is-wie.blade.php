@extends('layouts.app')
@section('title', __('pages.wie_is_wie_title'))
@section('content')

<style>
.wie-rij { display: flex; gap: 3rem; align-items: flex-start; }
.wie-label { width: 220px; flex-shrink: 0; }
.wie-namen { flex: 1; max-width: 680px; display: grid; grid-template-columns: 1fr 1fr; column-gap: 2.5rem; }
.bestuur-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; }
@media (max-width: 640px) {
    .wie-rij { flex-direction: column; gap: 0.35rem; }
    .wie-label { width: auto; }
    .wie-namen { grid-template-columns: 1fr; }
    .bestuur-grid { grid-template-columns: 1fr; gap: 2rem; }
}
</style>

<x-page-hero
    :eyebrow="__('pages.team_eyebrow')"
    eyebrow-color="blue"
    :heading="__('pages.team_heading')"
    :lead="__('pages.team_lead')"
    bg="white"
/>

@php
    $staf = $categorieen->where('volgorde', '<=', 8)->values();
    $governance = $categorieen->where('volgorde', '>', 8)->values();
@endphp

{{-- Sectie 1: Medewerkers --}}
<div style="background: var(--color-brand-blue-tint);">
<div class="max-w-7xl mx-auto px-6 py-10">

    <p style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-blue); font-family: var(--font-sans); margin: 0 0 1.5rem;">
        {{ __('pages.team_staff') }}
    </p>

    <div style="border-top: 1px solid var(--color-brand-border-blue); margin-bottom: 0;">
        @foreach ($staf as $categorie)
            <div class="wie-rij" style="padding: 1.25rem 0; border-bottom: 1px solid var(--color-brand-border-blue);">
                <p class="wie-label" style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-blue); font-family: var(--font-sans); margin: 0; padding-top: 0.2rem;">
                    {{ $categorie->naam }}
                </p>
                <div class="wie-namen">
                    @foreach ($categorie->leden as $lid)
                        <p style="font-size: 1rem; color: var(--color-brand-dark); line-height: 1.7; margin: 0;">
                            {{ $lid->naam }}@if ($lid->titel)<span style="color: var(--color-brand-muted); font-size: 0.875rem;"> — {{ $lid->titel }}</span>@endif
                        </p>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</div>
</div>

{{-- Sectie 2+3: Governance naast elkaar --}}
<div style="background: var(--color-brand-green-tint);">
<div class="max-w-7xl mx-auto px-6 py-10">

    <p style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-green-dark); font-family: var(--font-sans); margin: 0 0 1.5rem;">
        {{ __('pages.governance') }}
    </p>

    <div class="bestuur-grid">
        @foreach ($governance as $categorie)
            <div>
                <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--color-brand-dark); font-family: var(--font-sans); margin: 0 0 1rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--color-brand-border-green); line-height: 1.2;">
                    {{ $categorie->naam }}
                </h2>
                @foreach ($categorie->leden as $lid)
                    <p style="font-size: 1rem; color: var(--color-brand-dark); line-height: 1.7; margin: 0;">
                        {{ $lid->naam }}@if ($lid->titel)<span style="color: var(--color-brand-muted); font-size: 0.875rem;"> — {{ $lid->titel }}</span>@endif
                    </p>
                @endforeach
            </div>
        @endforeach
    </div>

</div>
</div>

@endsection
