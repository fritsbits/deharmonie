<div>
    @if ($submitted)
        <div
            x-data
            x-init="
                const ids = JSON.parse(localStorage.getItem('bookedActivities') || '[]');
                if (!ids.includes({{ $activiteit->id }})) {
                    ids.push({{ $activiteit->id }});
                    localStorage.setItem('bookedActivities', JSON.stringify(ids));
                }
            "
            class="rounded-lg p-6 text-center"
            style="background-color: rgba(129,181,156,0.12); border: 1px solid var(--color-brand-green);">
            <svg class="w-10 h-10 mx-auto mb-3" fill="none" stroke="var(--color-brand-green)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="font-bold text-lg mb-3" style="color: var(--color-brand-dark);">
                @if (app()->getLocale() === 'fr')
                    Vous êtes inscrit(e)&nbsp;!
                @else
                    Je bent ingeschreven!
                @endif
            </p>
            <p style="font-size: 1.05rem; color: var(--color-brand-dark); font-weight: 600; margin-bottom: 0.25rem;">
                {{ $activiteit->titel }}
            </p>
            <p style="font-size: 0.95rem; color: var(--color-brand-muted); margin-bottom: 0.25rem;">
                {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY')) }}
                &middot; {{ substr($activiteit->startuur, 0, 5) }}@if ($activiteit->einduur)&ndash;{{ substr($activiteit->einduur, 0, 5) }}@endif
            </p>
            <p style="font-size: 0.95rem; color: var(--color-brand-muted); margin-bottom: 1rem;">
                {{ $activiteit->locatie }}
            </p>
            <p style="font-size: 0.9rem; color: var(--color-brand-muted);">
                @if (app()->getLocale() === 'fr')
                    Vous recevrez une confirmation par e-mail.
                @else
                    Je ontvangt een bevestiging per e-mail.
                @endif
            </p>
        </div>
    @else
        <form wire:submit="submit" class="space-y-4">
            {{-- Honeypot --}}
            <div class="hidden" aria-hidden="true">
                <input type="text" wire:model="honeypot" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label class="block mb-1" style="font-family: var(--font-sans); font-weight: 700; letter-spacing: 0.03em; color: var(--color-brand-dark); font-size: 0.95rem; text-transform: uppercase;">
                    {{ __('forms.name') }}
                    <span style="font-size: 0.7rem; font-weight: 500; color: var(--color-brand-muted); background: #ede9e6; border-radius: 3px; padding: 0.1em 0.45em; text-transform: none; letter-spacing: 0; margin-left: 0.4rem; vertical-align: middle;">{{ __('forms.required_label') }}</span>
                </label>
                <input type="text" wire:model="naam"
                       class="w-full px-3 py-3 rounded focus:outline-none @error('naam') error-field @enderror"
                       style="border: 1px solid var(--color-brand-gray); background: var(--color-brand-bg); color: var(--color-brand-dark); font-size: 1.125rem; @error('naam') border-color: var(--color-brand-orange); outline: 1px solid var(--color-brand-orange); @enderror">
                @error('naam') <p style="color: var(--color-brand-orange); font-size: 1rem; margin-top: 0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1" style="font-family: var(--font-sans); font-weight: 700; letter-spacing: 0.03em; color: var(--color-brand-dark); font-size: 0.95rem; text-transform: uppercase;">
                    {{ __('forms.email') }}
                    <span style="font-size: 0.7rem; font-weight: 500; color: var(--color-brand-muted); background: #ede9e6; border-radius: 3px; padding: 0.1em 0.45em; text-transform: none; letter-spacing: 0; margin-left: 0.4rem; vertical-align: middle;">{{ __('forms.required_label') }}</span>
                </label>
                <input type="email" wire:model="email"
                       class="w-full px-3 py-3 rounded focus:outline-none @error('email') error-field @enderror"
                       style="border: 1px solid var(--color-brand-gray); background: var(--color-brand-bg); color: var(--color-brand-dark); font-size: 1.125rem; @error('email') border-color: var(--color-brand-orange); outline: 1px solid var(--color-brand-orange); @enderror">
                @error('email') <p style="color: var(--color-brand-orange); font-size: 1rem; margin-top: 0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1" style="font-family: var(--font-sans); font-weight: 700; letter-spacing: 0.03em; color: var(--color-brand-dark); font-size: 0.95rem; text-transform: uppercase;">
                    {{ __('forms.phone') }}
                </label>
                <input type="tel" wire:model="telefoon"
                       class="w-full px-3 py-3 rounded focus:outline-none"
                       style="border: 1px solid var(--color-brand-gray); background: var(--color-brand-bg); color: var(--color-brand-dark); font-size: 1.125rem;">
            </div>

            <div>
                <label class="block mb-1" style="font-family: var(--font-sans); font-weight: 700; letter-spacing: 0.03em; color: var(--color-brand-dark); font-size: 0.95rem; text-transform: uppercase;">
                    {{ __('forms.message_label') }}
                </label>
                <textarea wire:model="bericht" rows="3"
                          class="w-full px-3 py-3 rounded focus:outline-none"
                          style="border: 1px solid var(--color-brand-gray); background: var(--color-brand-bg); color: var(--color-brand-dark); font-size: 1.125rem;"></textarea>
            </div>

            <button type="submit"
                    class="w-full font-bold px-5 py-3 rounded"
                    style="background-color: var(--color-brand-green); color: white; font-family: var(--font-sans); font-size: 1rem; letter-spacing: 0.03em;"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('forms.submit') }}</span>
                <span wire:loading>...</span>
            </button>
        </form>
    @endif
</div>
