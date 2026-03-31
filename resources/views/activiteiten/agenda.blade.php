@extends('layouts.app')
@section('title', __('activities.agenda_page_heading') . ' — ' . __('activities.all'))

@section('content')

<x-page-hero
    eyebrow="Agenda"
    eyebrow-color="green"
    :heading="__('activities.agenda_page_heading')"
    bg="white"
/>

<div style="background: #eef5f1;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 2rem 1.5rem 4rem;">
        <div class="agenda-paper-outer">
            <div class="agenda-paper">
                <livewire:activity-overzicht />
            </div>
        </div>
    </div>
</div>

<style>
.agenda-paper-outer {
    transform: rotate(-1.5deg);
    max-width: 680px;
    margin: 0 auto;
}
.agenda-paper {
    position: relative;
    background: white;
    border: 1px solid rgba(44,40,38,0.08);
    border-radius: 2px;
    overflow: hidden;
    box-shadow: 20px 20px 30px rgba(44,40,38,0.10);
}
.agenda-paper::before,
.agenda-paper::after {
    content: '';
    position: absolute;
    bottom: -2.6px;
    width: 42%; height: 45%;
    background: transparent;
    pointer-events: none;
    z-index: -1;
}
.agenda-paper::before {
    left: 5%;
    box-shadow: -10px 13px 17px rgba(44,40,38,0.23);
    transform: rotate(-3.1deg);
    transform-origin: bottom left;
}
.agenda-paper::after {
    right: 5%;
    box-shadow: 10px 13px 17px rgba(44,40,38,0.23);
    transform: rotate(3.1deg);
    transform-origin: bottom right;
}

@media (max-width: 767px) {
    .agenda-paper-outer { transform: none !important; }
}

@media print {
    .agenda-paper-outer { transform: none !important; box-shadow: none !important; max-width: none !important; }
    .agenda-paper { border: none !important; box-shadow: none !important; }
    nav, footer, .agenda-print-btn { display: none !important; }
    .agenda-print-header { display: block !important; }
    body { background: white !important; }
}
</style>

@endsection
