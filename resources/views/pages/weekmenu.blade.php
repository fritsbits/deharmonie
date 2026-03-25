@extends('layouts.app')
@section('title', 'Weekmenu de la Semaine')
@section('content')
<div class="max-w-5xl mx-auto px-6 py-10">
    <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: var(--color-brand-orange)">WEEKMENU</p>
    <h1 class="font-bold text-3xl mb-6" style="font-family: var(--font-sans); color: var(--color-brand-dark)">
        Weekmenu de la Semaine
    </h1>
    <div class="rounded-lg overflow-hidden" style="height: 900px; border: 1px solid var(--color-brand-gray)">
        <iframe
            src="https://docs.google.com/document/d/1QW8cVxFS-ew1TWO5Czk3WXGn567ryRC92C1oluGWX4c/preview"
            class="w-full h-full border-0"
            title="Weekmenu de la Semaine">
        </iframe>
    </div>
</div>
@endsection
