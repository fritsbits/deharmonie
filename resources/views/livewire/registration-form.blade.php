<div>
    @if ($submitted)
        @php
            $rawTitel = $activiteit->titel;
            $cleanTitel = trim(preg_replace('/[\x{1F000}-\x{1FFFF}]|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|\x{200D}/u', '', $rawTitel));
            if ($cleanTitel === strtoupper($cleanTitel)) {
                $cleanTitel = ucwords(strtolower($cleanTitel));
            }
            $tijd = substr($activiteit->startuur, 0, 5) . ($activiteit->einduur ? '–' . substr($activiteit->einduur, 0, 5) : '');
        @endphp
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
            style="background-color: rgba(129,181,156,0.10); border: 1px solid rgba(129,181,156,0.4);">

            {{-- Checkmark --}}
            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--color-brand-green); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            {{-- Heading --}}
            <p style="font-family: var(--font-sans); font-weight: 900; font-size: 1.375rem; color: var(--color-brand-dark); margin: 0 0 1rem;">
                @if (app()->getLocale() === 'fr')Vous êtes inscrit(e)&nbsp;!@else Je bent ingeschreven!@endif
            </p>

            {{-- Activity summary — one compact block --}}
            <div style="background: white; border-radius: 8px; padding: 0.875rem 1rem; margin-bottom: 1rem; text-align: left;">
                <p style="font-weight: 700; font-size: 1rem; color: var(--color-brand-dark); margin: 0 0 0.3rem;">{{ $cleanTitel }}</p>
                <p style="font-size: 1rem; color: var(--color-brand-muted); margin: 0;">
                    {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM')) }}
                    &middot; {{ $tijd }}
                    &middot; {{ $activiteit->locatie }}
                </p>
            </div>

            {{-- Email note --}}
            <p style="font-size: 1rem; color: var(--color-brand-muted); margin: 0;">
                @if (app()->getLocale() === 'fr')Vous recevrez une confirmation par e-mail.@else Je ontvangt een bevestiging per e-mail.@endif
            </p>
        </div>
    @else
        <form wire:submit="submit" class="space-y-4">
            {{-- Honeypot --}}
            <div class="hidden" aria-hidden="true">
                <input type="text" wire:model="honeypot" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label for="reg-naam" class="block mb-1" style="font-family: var(--font-sans); font-weight: 700; letter-spacing: 0.03em; color: var(--color-brand-dark); font-size: 1rem; text-transform: uppercase;">
                    {{ __('forms.name') }}
                    <span style="font-size: 0.875rem; font-weight: 500; color: var(--color-brand-muted); background: #ede9e6; border-radius: 3px; padding: 0.1em 0.45em; text-transform: none; letter-spacing: 0; margin-left: 0.4rem; vertical-align: middle;">{{ __('forms.required_label') }}</span>
                </label>
                <input id="reg-naam" type="text" wire:model="naam"
                       class="w-full px-3 py-3 rounded focus:outline-none @error('naam') error-field @enderror"
                       style="border: 1px solid var(--color-brand-gray); background: var(--color-brand-bg); color: var(--color-brand-dark); font-size: 1.125rem; @error('naam') border-color: var(--color-brand-orange); outline: 1px solid var(--color-brand-orange); @enderror">
                @error('naam') <p style="color: var(--color-brand-orange-dark); font-size: 1rem; margin-top: 0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="reg-email" class="block mb-1" style="font-family: var(--font-sans); font-weight: 700; letter-spacing: 0.03em; color: var(--color-brand-dark); font-size: 1rem; text-transform: uppercase;">
                    {{ __('forms.email') }}
                    <span style="font-size: 0.875rem; font-weight: 500; color: var(--color-brand-muted); background: #ede9e6; border-radius: 3px; padding: 0.1em 0.45em; text-transform: none; letter-spacing: 0; margin-left: 0.4rem; vertical-align: middle;">{{ __('forms.required_label') }}</span>
                </label>
                <input id="reg-email" type="email" wire:model="email"
                       class="w-full px-3 py-3 rounded focus:outline-none @error('email') error-field @enderror"
                       style="border: 1px solid var(--color-brand-gray); background: var(--color-brand-bg); color: var(--color-brand-dark); font-size: 1.125rem; @error('email') border-color: var(--color-brand-orange); outline: 1px solid var(--color-brand-orange); @enderror">
                @error('email') <p style="color: var(--color-brand-orange-dark); font-size: 1rem; margin-top: 0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="reg-telefoon" class="block mb-1" style="font-family: var(--font-sans); font-weight: 700; letter-spacing: 0.03em; color: var(--color-brand-dark); font-size: 1rem; text-transform: uppercase;">
                    {{ __('forms.phone') }}
                </label>
                <input id="reg-telefoon" type="tel" wire:model="telefoon"
                       class="w-full px-3 py-3 rounded focus:outline-none"
                       style="border: 1px solid var(--color-brand-gray); background: var(--color-brand-bg); color: var(--color-brand-dark); font-size: 1.125rem;">
            </div>

            <div>
                <label for="reg-bericht" class="block mb-1" style="font-family: var(--font-sans); font-weight: 700; letter-spacing: 0.03em; color: var(--color-brand-dark); font-size: 1rem; text-transform: uppercase;">
                    {{ __('forms.message_label') }}
                </label>
                <textarea id="reg-bericht" wire:model="bericht" rows="3"
                          class="w-full px-3 py-3 rounded focus:outline-none"
                          style="border: 1px solid var(--color-brand-gray); background: var(--color-brand-bg); color: var(--color-brand-dark); font-size: 1.125rem;"></textarea>
            </div>

            <button type="submit"
                    class="w-full font-bold px-5 py-3 rounded"
                    style="background-color: var(--color-brand-green); color: white; font-family: var(--font-sans); font-size: 1.125rem; letter-spacing: 0.03em;"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('forms.submit') }}</span>
                <span wire:loading>...</span>
            </button>
        </form>
    @endif
</div>
