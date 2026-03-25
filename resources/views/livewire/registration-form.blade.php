<div>
    @if ($submitted)
        <div class="rounded-lg p-6 text-center" style="background-color: rgba(129,181,156,0.12); border: 1px solid var(--color-brand-green);">
            <svg class="w-10 h-10 mx-auto mb-3" fill="none" stroke="var(--color-brand-green)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="font-semibold" style="color: var(--color-brand-green)">{{ __('forms.success') }}</p>
        </div>
    @elseif (! $activiteit->isBeschikbaar())
        <div class="rounded-lg p-4">
            <x-badge type="volzet" />
        </div>
    @else
        <h2 class="font-bold text-xl mb-4" style="font-family: var(--font-sans); color: var(--color-brand-dark)">
            {{ __('forms.heading') }}
        </h2>
        <form wire:submit="submit" class="space-y-4">
            {{-- Honeypot --}}
            <div class="hidden" aria-hidden="true">
                <input type="text" wire:model="honeypot" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label class="block mb-1" style="color: var(--color-brand-dark); font-size: 1.125rem;">
                    {{ __('forms.name') }} *
                </label>
                <input type="text" wire:model="naam"
                       class="w-full px-3 py-2 rounded focus:outline-none @error('naam') error-field @enderror"
                       style="border: 1px solid var(--color-brand-gray); background: white; color: var(--color-brand-dark); font-size: 1.125rem; @error('naam') border-color: var(--color-brand-orange); outline: 1px solid var(--color-brand-orange); @enderror">
                @error('naam') <p style="color: var(--color-brand-orange); font-size: 1rem; margin-top: 0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1" style="color: var(--color-brand-dark); font-size: 1.125rem;">
                    {{ __('forms.email') }}
                </label>
                <input type="email" wire:model="email"
                       class="w-full px-3 py-2 rounded focus:outline-none @error('email') error-field @enderror"
                       style="border: 1px solid var(--color-brand-gray); background: white; color: var(--color-brand-dark); font-size: 1.125rem; @error('email') border-color: var(--color-brand-orange); outline: 1px solid var(--color-brand-orange); @enderror">
                @error('email') <p style="color: var(--color-brand-orange); font-size: 1rem; margin-top: 0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1" style="color: var(--color-brand-dark); font-size: 1.125rem;">
                    {{ __('forms.phone') }} *
                </label>
                <input type="tel" wire:model="telefoon"
                       class="w-full px-3 py-2 rounded focus:outline-none"
                       style="border: 1px solid var(--color-brand-gray); background: white; color: var(--color-brand-dark); font-size: 1.125rem;">
            </div>

            <div>
                <label class="block mb-1" style="color: var(--color-brand-dark); font-size: 1.125rem;">
                    {{ __('forms.message_label') }} *
                </label>
                <textarea wire:model="bericht" rows="3"
                          class="w-full px-3 py-2 rounded focus:outline-none"
                          style="border: 1px solid var(--color-brand-gray); background: white; color: var(--color-brand-dark); font-size: 1.125rem;"></textarea>
            </div>

            <button type="submit"
                    class="font-semibold px-5 py-2.5 rounded"
                    style="background-color: var(--color-brand-dark); color: white; font-family: var(--font-sans); font-size: 1rem;"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('forms.submit') }}</span>
                <span wire:loading>...</span>
            </button>
        </form>
    @endif
</div>
