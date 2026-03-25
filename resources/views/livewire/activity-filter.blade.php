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
               style="display: flex; align-items: center; gap: 1rem; padding: 0.65rem 0; text-decoration: none; opacity: {{ $activiteit->status === 'geannuleerd' ? '0.5' : '1' }}; {{ !$loop->last ? 'border-bottom: 1px solid rgba(216,211,210,0.7);' : '' }}">

                {{-- Thumbnail --}}
                <div style="flex-shrink: 0; width: 48px; height: 48px; border-radius: 6px; overflow: hidden; background-color: {{ $thumbColor }};">
                    @if ($activiteit->getFirstMediaUrl('afbeelding'))
                        <img src="{{ $activiteit->getFirstMediaUrl('afbeelding') }}"
                             alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    @endif
                </div>

                {{-- Content --}}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <p style="font-weight: 700; font-size: 0.85rem; line-height: 1.2; color: var(--color-brand-dark); font-family: var(--font-sans); margin: 0;">
                            {{ $activiteit->titel }}
                        </p>
                        @if ($activiteit->status === 'geannuleerd')
                            <span style="flex-shrink: 0; font-size: 0.7rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 4px; background-color: #fde8e3; color: #c0392b;">
                                &times;
                            </span>
                        @endif
                    </div>
                    <p style="font-size: 0.75rem; margin: 0.1rem 0 0; color: var(--color-brand-muted);">
                        {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('dddd')) }}
                        {{ $activiteit->datum->format('j/n') }}
                        {{ __('activities.at') }} {{ substr($activiteit->startuur, 0, 5) }}
                        @if ($activiteit->einduur)
                            &ndash; {{ substr($activiteit->einduur, 0, 5) }}
                        @endif
                        &middot; {{ $activiteit->locatie }}
                    </p>
                </div>

            </a>
        @empty
            <p style="padding: 2rem 0; color: var(--color-brand-muted); font-size: 0.9rem;">
                {{ app()->getLocale() === 'fr' ? 'Pas d\'activités prévues.' : 'Geen activiteiten gepland.' }}
            </p>
        @endforelse
    </div>

    {{-- Bottom buttons / section CTA --}}
    <div style="margin-top: 1.5rem; padding-bottom: 2rem; display: flex; gap: 0.75rem;">
        <a href="{{ route('nl.activiteiten.index') }}"
           style="font-size: 0.9rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 4px; background-color: var(--color-brand-green); color: white; text-decoration: none; font-family: var(--font-sans);">
            Alle activiteiten
        </a>
        <a href="{{ route('fr.activiteiten.index') }}"
           style="font-size: 0.9rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 4px; background-color: var(--color-brand-green); color: white; text-decoration: none; font-family: var(--font-sans);">
            Toutes les activités
        </a>
    </div>
</div>
