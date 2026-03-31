@extends('layouts.app')
@section('title', __('activities.agenda_page_heading') . ' — ' . __('activities.all'))

@section('content')

<x-page-hero
    eyebrow="Agenda"
    eyebrow-color="green"
    :heading="__('activities.agenda_page_heading')"
    bg="white"
    pb="1.25rem"
>
    <button onclick="window.print()"
            class="agenda-print-btn"
            style="font-family: var(--font-sans); font-size: 0.8125rem; font-weight: 700; color: var(--color-brand-muted); background: none; border: 1px solid var(--color-brand-gray-dark); border-radius: 4px; cursor: pointer; padding: 0.3rem 0.75rem;">
        {{ __('activities.print') }}
    </button>
</x-page-hero>

<div style="background: #eef5f1;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem 4rem;">
        <livewire:activity-overzicht />
    </div>
</div>

@endsection
