@extends('layouts.app')

@section('title', __('activities.upcoming'))

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10">
        <h1 class="font-sans font-extrabold text-3xl mb-8" style="color: var(--color-brand-dark)">
            {{ __('activities.upcoming') }}
        </h1>
        <livewire:activity-filter />
    </div>
@endsection
