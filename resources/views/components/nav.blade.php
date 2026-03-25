<header style="background-color: white; border-bottom: 1px solid var(--color-brand-gray); position: relative;">
    <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" class="h-10 w-auto">
        </a>
        <nav class="hidden md:flex items-center gap-8" style="font-family: var(--font-sans);">
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               class="text-sm font-semibold hover:underline"
               style="color: var(--color-brand-dark)">
               {{ app()->getLocale() === 'fr' ? 'Activités' : 'Activiteiten' }}
            </a>
            <a href="{{ route(app()->getLocale() . '.diensten') }}"
               class="text-sm font-semibold hover:underline"
               style="color: var(--color-brand-dark)">
               {{ app()->getLocale() === 'fr' ? 'Services' : 'Diensten' }}
            </a>
            <a href="https://docs.google.com/document/d/1QW8cVxFS-ew1TWO5Czk3WXGn567ryRC92C1oluGWX4c/preview"
               target="_blank"
               class="text-sm font-semibold hover:underline"
               style="color: var(--color-brand-dark)">
               Weekmenu de la Semaine
            </a>
            <a href="{{ route(app()->getLocale() . '.contact') }}"
               class="text-sm font-semibold hover:underline"
               style="color: var(--color-brand-dark)">
               Contact
            </a>
            @php
                $switchLocale = app()->getLocale() === 'nl' ? 'fr' : 'nl';
                $switchRoute = $switchLocale . '.activiteiten.index';
            @endphp
            <a href="{{ route($switchRoute) }}"
               class="text-xs font-bold px-3 py-1 rounded"
               style="background-color: var(--color-brand-gray); color: var(--color-brand-dark)">
               {{ app()->getLocale() === 'nl' ? 'FR' : 'NL' }}
            </a>
        </nav>
        <!-- Mobile toggle -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="md:hidden p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div x-show="open" class="absolute top-full left-0 right-0 bg-white border-t px-6 py-4 space-y-3 md:hidden z-50">
                <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="block text-sm font-semibold">{{ app()->getLocale() === 'fr' ? 'Activités' : 'Activiteiten' }}</a>
                <a href="{{ route(app()->getLocale() . '.diensten') }}" class="block text-sm font-semibold">{{ app()->getLocale() === 'fr' ? 'Services' : 'Diensten' }}</a>
                <a href="https://docs.google.com/document/d/1QW8cVxFS-ew1TWO5Czk3WXGn567ryRC92C1oluGWX4c/preview" target="_blank" class="block text-sm font-semibold">Weekmenu de la Semaine</a>
                <a href="{{ route(app()->getLocale() . '.contact') }}" class="block text-sm font-semibold">Contact</a>
            </div>
        </div>
    </div>
</header>
