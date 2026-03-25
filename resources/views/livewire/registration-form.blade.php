<div>
    @if ($submitted)
        <div class="rounded-lg p-6 text-center" style="background-color: #edf7f1; border: 1px solid #a8d5b8;">
            <svg class="w-10 h-10 mx-auto mb-3" fill="none" stroke="#2e7d52" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="font-semibold" style="color: #2e7d52">{{ __('forms.success') }}</p>
        </div>
    @elseif (! $activiteit->isBeschikbaar())
        <div class="rounded-lg p-4 text-sm font-semibold" style="background-color: #fff3e0; color: #e65100; border: 1px solid #ffccbc">
            {{ app()->getLocale() === 'fr' ? 'Complet' : 'Volzet' }}
        </div>
    @else
        <h2 class="font-bold text-xl mb-4" style="font-family: var(--font-sans); color: var(--color-brand-dark)">
            {{ app()->getLocale() === 'fr' ? 'S\'inscrire' : 'Inschrijven' }}
        </h2>
        <form wire:submit="submit" class="space-y-4">
            {{-- Honeypot --}}
            <div class="hidden" aria-hidden="true">
                <input type="text" wire:model="honeypot" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase mb-1" style="color: var(--color-brand-muted)">
                    {{ app()->getLocale() === 'fr' ? 'Nom' : 'Naam' }} *
                </label>
                <input type="text" wire:model="naam"
                       class="w-full px-3 py-2 rounded text-sm focus:outline-none @error('naam') ring-1 ring-red-400 @enderror"
                       style="border: 1px solid var(--color-brand-gray); background: white; color: var(--color-brand-dark)">
                @error('naam') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase mb-1" style="color: var(--color-brand-muted)">
                    E-mail *
                </label>
                <input type="email" wire:model="email"
                       class="w-full px-3 py-2 rounded text-sm focus:outline-none @error('email') ring-1 ring-red-400 @enderror"
                       style="border: 1px solid var(--color-brand-gray); background: white; color: var(--color-brand-dark)">
                @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase mb-1" style="color: var(--color-brand-muted)">
                    {{ app()->getLocale() === 'fr' ? 'Téléphone (optionnel)' : 'Telefoon (optioneel)' }}
                </label>
                <input type="tel" wire:model="telefoon"
                       class="w-full px-3 py-2 rounded text-sm focus:outline-none"
                       style="border: 1px solid var(--color-brand-gray); background: white; color: var(--color-brand-dark)">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase mb-1" style="color: var(--color-brand-muted)">
                    {{ app()->getLocale() === 'fr' ? 'Message (optionnel)' : 'Bericht (optioneel)' }}
                </label>
                <textarea wire:model="bericht" rows="3"
                          class="w-full px-3 py-2 rounded text-sm focus:outline-none"
                          style="border: 1px solid var(--color-brand-gray); background: white; color: var(--color-brand-dark)"></textarea>
            </div>

            <button type="submit"
                    class="text-sm font-bold px-6 py-3 rounded text-white"
                    style="background-color: var(--color-brand-orange); font-family: var(--font-sans)"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>{{ app()->getLocale() === 'fr' ? 'S\'inscrire' : 'Inschrijven' }}</span>
                <span wire:loading>...</span>
            </button>
        </form>
    @endif
</div>
