<div>
    {{-- Month navigation --}}
    <div class="flex items-center justify-between mb-6">
        <button wire:click="previousMonth"
                class="flex items-center gap-1 text-sm font-semibold hover:underline"
                style="color: var(--color-brand-green)">
            ← {{ __('activities.previous_month') }}
        </button>
        <h2 class="font-sans font-bold text-xl capitalize">{{ $this->monthLabel }}</h2>
        <button wire:click="nextMonth"
                class="flex items-center gap-1 text-sm font-semibold hover:underline"
                style="color: var(--color-brand-green)">
            {{ __('activities.next_month') }} →
        </button>
    </div>

    {{-- Activity list --}}
    @forelse ($this->activiteiten as $activiteit)
        <article class="bg-white rounded-lg shadow-sm p-5 mb-4 flex gap-4 {{ $activiteit->status === 'geannuleerd' ? 'opacity-60' : '' }}">
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="font-sans font-bold text-lg leading-tight">
                        <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                           class="hover:underline transition-colors"
                           style="color: var(--color-brand-dark)">
                            {{ $activiteit->titel }}
                        </a>
                    </h3>
                    @if ($activiteit->status === 'geannuleerd')
                        <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded flex-shrink-0">
                            {{ __('activities.cancelled') }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM') }}
                    · {{ substr($activiteit->startuur, 0, 5) }}
                    @if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }} @endif
                    · {{ $activiteit->locatie }}
                </p>
                @if ($activiteit->beschrijving)
                <p class="text-sm mt-2 text-gray-700 line-clamp-2">
                    {!! strip_tags($activiteit->beschrijving) !!}
                </p>
                @endif
                <div class="mt-3 flex items-center gap-4">
                    <span class="text-sm font-semibold" style="color: var(--color-brand-green)">
                        {{ $activiteit->getPrijsLabel(app()->getLocale()) }}
                    </span>
                    <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                       class="text-sm font-semibold hover:underline"
                       style="color: var(--color-brand-green)">
                        {{ __('activities.detail') }} →
                    </a>
                </div>
            </div>
        </article>
    @empty
        <div class="text-center py-12 text-gray-500">
            <p class="text-lg">{{ __('activities.no_activities', ['month' => $this->monthLabel]) }}</p>
        </div>
    @endforelse
</div>
