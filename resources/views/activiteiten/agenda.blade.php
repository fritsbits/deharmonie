@extends('layouts.app')
@section('title', __('activities.agenda_page_heading') . ' — ' . __('activities.all'))

@section('content')

<div class="agenda-screen-only">
    <x-page-hero
        eyebrow="Agenda"
        eyebrow-color="green"
        heading="Weekplanning De Harmonie"
        bg="white"
    />
</div>

<div class="agenda-bg-wrapper" style="background: #eef5f1;">
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
    @page { size: A4 portrait; margin: 12mm 14mm; }

    /* Hide all screen chrome */
    header, footer,
    .agenda-card-header,
    .agenda-print-btn { display: none !important; }

    /* Strip card decoration */
    .agenda-bg-wrapper { background: white !important; }
    .agenda-paper-outer { transform: none !important; }
    .agenda-paper {
        border: none !important;
        box-shadow: none !important;
        overflow: visible !important;
    }
    .agenda-paper::before,
    .agenda-paper::after { display: none !important; }

    /* Compact body padding */
    .agenda-body { padding: 0 0 0.5rem !important; }

    /* Compact day groups */
    .agenda-day-group { padding: 0.875rem 0 !important; }

    /* Slightly larger type for paper — seniors hold paper further away */
    .agenda-activity-title { font-size: 1.25rem !important; }
    .agenda-activity-meta  { font-size: 1rem !important; }
    .agenda-date-num       { font-size: 1.75rem !important; }
    .agenda-date-label     { font-size: 0.8rem !important; letter-spacing: 0.02em !important; }
}
</style>

@endsection
