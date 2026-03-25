<div>
    {{-- Month navigation --}}
    <div class="flex items-center justify-between mb-5">
        <button wire:click="previousMonth"
                class="text-sm font-semibold hover:underline flex items-center gap-1"
                style="color: var(--color-brand-blue); font-family: var(--font-sans)">
            &larr; {{ app()->getLocale() === 'fr' ? 'Mois précédent' : 'Vorige maand' }}
        </button>
        <span class="text-sm font-bold capitalize" style="color: var(--color-brand-muted); font-family: var(--font-sans)">
            {{ $this->monthLabel }}
        </span>
        <button wire:click="nextMonth"
                class="text-sm font-semibold hover:underline flex items-center gap-1"
                style="color: var(--color-brand-blue); font-family: var(--font-sans)">
            {{ app()->getLocale() === 'fr' ? 'Mois suivant' : 'Volgende maand' }} &rarr;
        </button>
    </div>

    {{-- Activity list --}}
    <div class="divide-y" style="border-top: 1px solid var(--color-brand-gray); border-bottom: 1px solid var(--color-brand-gray)">
        @forelse ($this->activiteiten as $activiteit)
            <div class="py-4 flex items-start gap-4 {{ $activiteit->status === 'geannuleerd' ? 'opacity-60' : '' }}">
                {{-- Left: date block --}}
                <div class="flex-shrink-0 text-center w-14">
                    <div class="text-xs font-bold uppercase" style="color: var(--color-brand-muted); font-family: var(--font-sans)">
                        {{ $activiteit->datum->locale(app()->getLocale())->isoFormat('ddd') }}
                    </div>
                    <div class="text-2xl font-extrabold leading-none" style="color: var(--color-brand-dark); font-family: var(--font-sans)">
                        {{ $activiteit->datum->format('d') }}
                    </div>
                    <div class="text-xs" style="color: var(--color-brand-muted)">
                        {{ $activiteit->datum->locale(app()->getLocale())->isoFormat('MMM') }}
                    </div>
                </div>

                {{-- Divider --}}
                <div class="w-px self-stretch flex-shrink-0" style="background-color: var(--color-brand-gray)"></div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                               class="font-bold text-sm uppercase hover:underline"
                               style="color: var(--color-brand-dark); font-family: var(--font-sans); letter-spacing: 0.02em">
                                {{ $activiteit->titel }}
                            </a>
                            <p class="text-xs mt-1" style="color: var(--color-brand-muted)">
                                {{ substr($activiteit->startuur, 0, 5) }}
                                @if ($activiteit->einduur) &ndash; {{ substr($activiteit->einduur, 0, 5) }} @endif
                                &middot; {{ $activiteit->locatie }}
                            </p>
                        </div>
                        @if ($activiteit->status === 'geannuleerd')
                            <span class="flex-shrink-0 text-xs font-bold px-2 py-0.5 rounded" style="background-color: #fde8e3; color: #c0392b">
                                &times; {{ app()->getLocale() === 'fr' ? 'Annulé' : 'Geannuleerd' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-sm" style="color: var(--color-brand-muted)">
                {{ app()->getLocale() === 'fr'
                    ? 'Pas d\'activités en ' . $this->monthLabel
                    : 'Geen activiteiten in ' . $this->monthLabel }}
            </div>
        @endforelse
    </div>
</div>
