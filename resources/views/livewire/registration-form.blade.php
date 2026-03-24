<div>
    @if ($submitted)
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
            <svg class="w-10 h-10 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="font-semibold text-green-700">{{ __('forms.success') }}</p>
        </div>
    @elseif (! $activiteit->isBeschikbaar())
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-amber-700 font-semibold">
            {{ __('activities.full') }}
        </div>
    @else
        <h2 class="font-sans font-bold text-xl mb-4">{{ __('activities.register') }}</h2>
        <form wire:submit="submit" class="space-y-4">
            {{-- Honeypot --}}
            <div class="hidden" aria-hidden="true">
                <input type="text" wire:model="honeypot" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('forms.name') }} *</label>
                <input type="text" wire:model="naam"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 @error('naam') border-red-400 @enderror"
                       style="--tw-ring-color: var(--color-brand-green)">
                @error('naam') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('forms.email') }} *</label>
                <input type="email" wire:model="email"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 @error('email') border-red-400 @enderror"
                       style="--tw-ring-color: var(--color-brand-green)">
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('forms.phone') }}</label>
                <input type="tel" wire:model="telefoon"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('forms.message') }}</label>
                <textarea wire:model="bericht" rows="3"
                          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2"></textarea>
            </div>

            <button type="submit"
                    class="w-full text-white font-bold py-3 rounded-lg transition-colors"
                    style="background-color: var(--color-brand-green)"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('forms.submit') }}</span>
                <span wire:loading>...</span>
            </button>
        </form>
    @endif
</div>
