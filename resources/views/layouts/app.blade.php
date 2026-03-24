<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'De Harmonie') — De Harmonie</title>
    <meta name="description" content="@yield('description', 'Lokaal dienstencentrum en sociaal restaurant in de Noordwijk, Brussel.')">
    @if(View::hasSection('og_title'))
    <meta property="og:title" content="@yield('og_title')">
    <meta property="og:description" content="@yield('og_description')">
    <meta property="og:type" content="website">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,400;6..12,600;6..12,700;6..12,800&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[--color-brand-cream] min-h-screen flex flex-col">
    <x-nav />
    <main class="flex-1">
        @yield('content')
    </main>
    <x-footer />
    @livewireScripts
</body>
</html>
