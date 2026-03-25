<div>
    {{-- Activity list --}}
    <div>
        @php
            $thumbColors = ['#f3dbd5','#d4e8df','#d5e0f0','#f5e8d3','#dde7d5','#e8d9ef','#d9e8f0'];
        @endphp
        @forelse ($this->activiteiten as $activiteit)
            @php
                $colorIdx = abs(crc32($activiteit->slug ?? '')) % count($thumbColors);
                $thumbColor = $thumbColors[$colorIdx];
            @endphp
            <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
               style="display: flex; align-items: center; gap: 1rem; padding: 0.65rem 0; text-decoration: none; opacity: {{ $activiteit->status->value === 'geannuleerd' ? '0.85' : '1' }}; {{ !$loop->last ? 'border-bottom: 1px solid rgba(216,211,210,0.7);' : '' }}">

                {{-- Thumbnail --}}
                <div style="flex-shrink: 0; width: 80px; height: 80px; border-radius: 8px; overflow: hidden; background-color: {{ $thumbColor }};">
                    @if ($activiteit->getFirstMediaUrl('afbeelding'))
                        <img src="{{ $activiteit->getFirstMediaUrl('afbeelding') }}"
                             alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    @endif
                </div>

                {{-- Content --}}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <p style="font-weight: 700; font-size: 1.625rem; line-height: 1.2; color: var(--color-brand-blue); font-family: var(--font-sans); margin: 0;">
                            {{ $activiteit->titel }}
                        </p>
                        @if ($activiteit->status->value === 'geannuleerd')
                            <x-badge type="geannuleerd">&times;</x-badge>
                        @endif
                    </div>
                    <p style="font-size: 1rem; margin: 0.25rem 0 0; color: var(--color-brand-muted);">
                        {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('dddd')) }}
                        {{ $activiteit->datum->format('j/n') }}
                        om {{ substr($activiteit->startuur, 0, 5) }}
                        @if ($activiteit->einduur)
                            &ndash; {{ substr($activiteit->einduur, 0, 5) }}
                        @endif
                        <span style="color: var(--color-brand-gray-dark);">&middot;</span> {{ $activiteit->locatie }}
                    </p>
                </div>

            </a>
        @empty
            <p style="padding: 2rem 0; color: var(--color-brand-muted); font-size: 0.9rem;">
                {{ app()->getLocale() === 'fr' ? 'Pas d\'activités prévues.' : 'Geen activiteiten gepland.' }}
            </p>
        @endforelse
    </div>

    {{-- Bottom CTA --}}
    <div style="margin-top: 1.5rem; padding-bottom: 2rem; display: flex; gap: 1.5rem;">
        <a href="{{ route('nl.activiteiten.index') }}"
           style="font-size: 1rem; font-weight: 700; color: var(--color-brand-green); text-decoration: underline; font-family: var(--font-sans);">
            Alle activiteiten →
        </a>
        <a href="{{ route('fr.activiteiten.index') }}"
           style="font-size: 1rem; font-weight: 700; color: var(--color-brand-green); text-decoration: underline; font-family: var(--font-sans);">
            Toutes les activités →
        </a>
    </div>
</div>
