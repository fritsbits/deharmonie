<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Activiteit') — De Harmonie</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { font-size: 12pt; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="p-8">
    <div class="no-print mb-4 flex gap-4">
        <button onclick="window.print()" class="px-4 py-2 rounded text-white text-sm" style="background-color: var(--color-brand-green)">
            {{ __('activities.print') }}
        </button>
        <a href="javascript:history.back()" class="text-sm underline">{{ __('activities.back') }}</a>
    </div>
    @yield('content')
</body>
</html>
