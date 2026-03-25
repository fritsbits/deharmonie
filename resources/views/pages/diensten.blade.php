@extends('layouts.app')
@section('title', __('pages.diensten_title'))
@section('content')
<div class="max-w-5xl mx-auto px-6 py-10">
    <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: var(--color-brand-orange)">
        {{ __('pages.diensten_eyebrow') }}
    </p>
    <h1 class="font-bold text-3xl mb-4" style="font-family: var(--font-sans); color: var(--color-brand-dark)">
        {{ __('pages.diensten_heading') }}
    </h1>
    <p class="mb-8" style="color: var(--color-brand-muted)">
        {{ __('pages.diensten_intro') }}
    </p>
    <h2 class="font-bold text-xl mb-4" style="font-family: var(--font-sans); color: var(--color-brand-dark)">
        {{ __('pages.diensten_services_heading') }}
    </h2>
    @php $services = trans('pages.diensten_services'); @endphp
    <ul class="mb-8 space-y-2">
        @foreach ($services as $service)
            <li class="flex items-start gap-2" style="color: var(--color-brand-muted)">
                <span class="mt-1 shrink-0" style="color: var(--color-brand-orange)">&#8226;</span>
                <span>{{ $service }}</span>
            </li>
        @endforeach
    </ul>
    <div class="rounded-xl p-6 text-white" style="background-color: var(--color-brand-orange)">
        <p class="text-xs font-bold uppercase tracking-widest mb-1 opacity-80">
            {{ __('pages.grote_kuis_eyebrow') }}
        </p>
        <h2 class="font-bold text-xl mb-2" style="font-family: var(--font-sans)">
            {{ __('pages.grote_kuis_title') }}
        </h2>
        <p class="text-sm opacity-90 mb-3">
            {{ __('pages.grote_kuis_description') }}
        </p>
        <p class="text-sm font-semibold mb-2">
            {{ __('pages.grote_kuis_examples_label') }}
        </p>
        @php $examples = trans('pages.grote_kuis_examples'); @endphp
        <ul class="text-sm opacity-90 space-y-1 mb-3">
            @foreach ($examples as $example)
                <li>&#8226; {{ $example }}</li>
            @endforeach
        </ul>
        <p class="text-sm opacity-90">
            {{ __('pages.grote_kuis_cta') }}
        </p>
    </div>
</div>
@endsection
