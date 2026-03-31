@extends('layouts.app')
@section('title', __('pages.wie_is_wie_title'))
@section('content')

<x-page-hero
    :eyebrow="__('pages.team_eyebrow')"
    eyebrow-color="blue"
    :heading="__('pages.team_heading')"
    :lead="__('pages.team_lead')"
    bg="white"
/>

<div style="background: #eef2f8;">
<div class="max-w-5xl mx-auto px-6 py-10">

    <div style="border-top: 1px solid var(--color-brand-gray); margin-bottom: 3.5rem;">
        @foreach ($categorieen as $categorie)
            <div style="display: flex; gap: 2rem; padding: 1.1rem 0; border-bottom: 1px solid var(--color-brand-gray); align-items: baseline;">
                <p style="flex: 0 0 38%; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); font-family: var(--font-sans); margin: 0; padding-top: 0.15rem;">
                    {{ $categorie->naam }}
                </p>
                <div style="flex: 1;">
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

@endsection
