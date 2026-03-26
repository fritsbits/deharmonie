@extends('layouts.app')
@section('title', 'Agenda — ' . __('activities.all'))

@section('content')

<x-page-hero
    :eyebrow="__('nav.activities')"
    eyebrow-color="green"
    :heading="__('activities.overview_heading')"
    :lead="__('activities.overview_tagline')"
    bg="white"
/>

<div style="background: #eef5f1;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem 4rem;">
        <livewire:activity-overzicht />
    </div>
</div>

@endsection
