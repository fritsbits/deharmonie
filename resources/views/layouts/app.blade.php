<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'De Harmonie') — {{ __('common.site_tagline') }}</title>
    <meta name="description" content="@yield('description', 'Lokaal dienstencentrum en sociaal restaurant in de Noordwijk, Brussel.')">
    {{-- Open Graph --}}
    @php
        $ogTitle = $__env->yieldContent('og_title') ?: ($__env->yieldContent('title', 'De Harmonie') . ' — ' . __('common.site_tagline'));
        $ogLocale = app()->getLocale() === 'nl' ? 'nl_BE' : 'fr_BE';
        $ogLocaleAlt = app()->getLocale() === 'nl' ? 'fr_BE' : 'nl_BE';
        $otherLocale = app()->getLocale() === 'nl' ? 'fr' : 'nl';
        try {
            $currentRouteName = request()->route()?->getName() ?? '';
            $altRouteName = $currentRouteName ? ($otherLocale . substr($currentRouteName, 2)) : null;
            $alternateUrl = $altRouteName ? route($altRouteName, request()->route()->parameters()) : null;
        } catch (\Throwable $e) {
            $alternateUrl = null;
        }
    @endphp
    <link rel="canonical" href="{{ url()->current() }}">
    @if ($alternateUrl)
        <link rel="alternate" hreflang="{{ app()->getLocale() === 'nl' ? 'nl' : 'fr' }}" href="{{ url()->current() }}">
        <link rel="alternate" hreflang="{{ $otherLocale }}" href="{{ $alternateUrl }}">
        <link rel="alternate" hreflang="x-default" href="{{ app()->getLocale() === 'nl' ? url()->current() : $alternateUrl }}">
    @endif
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="De Harmonie">
    <meta property="og:locale" content="{{ $ogLocale }}">
    <meta property="og:locale:alternate" content="{{ $ogLocaleAlt }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="@yield('og_description', __('common.og_default_description'))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.webp'))">
    <meta property="og:image:width" content="700">
    <meta property="og:image:height" content="933">
    <meta property="og:image:alt" content="De Harmonie — {{ __('common.site_tagline') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    {{-- Twitter / X Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="@yield('og_description', __('common.og_default_description'))">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-image.webp'))">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,300;6..12,400;6..12,600;6..12,700;6..12,800&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body style="background-color: var(--color-brand-bg); color: var(--color-brand-dark); font-family: var(--font-body); font-size: 18px;" class="min-h-screen flex flex-col">
    <x-nav />
    <main class="flex-1">
        @yield('content')
    </main>
    <x-footer />
    @livewireScripts
</body>
</html>
