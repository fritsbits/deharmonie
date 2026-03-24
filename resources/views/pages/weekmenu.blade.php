@extends('layouts.app')
@section('title', __('nav.menu'))
@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="font-sans font-extrabold text-3xl mb-6" style="color: var(--color-brand-dark)">
        {{ __('nav.menu') }}
    </h1>
    @if (config('app.weekly_menu_url'))
        <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="height: 800px;">
            <iframe src="{{ config('app.weekly_menu_url') }}"
                    class="w-full h-full border-0"
                    title="{{ __('nav.menu') }}">
            </iframe>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-500">
            <p>
                {{ app()->getLocale() === 'fr'
                    ? 'Le menu de la semaine n\'est pas encore disponible.'
                    : 'Het weekmenu is nog niet beschikbaar.' }}
            </p>
        </div>
    @endif
</div>
@endsection
