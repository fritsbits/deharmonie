<x-filament::page>
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 768px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        .dashboard-card {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid rgb(229 231 235);
            background: #fff;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
            text-decoration: none;
            color: inherit;
        }
        .dashboard-card:hover {
            border-color: rgb(245 158 130);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.08), 0 2px 4px -2px rgb(0 0 0 / 0.06);
        }
        .dashboard-card:focus-visible {
            outline: 2px solid #eb6643;
            outline-offset: 2px;
        }
        .dashboard-card__head {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .dashboard-card__icon {
            width: 1.5rem;
            height: 1.5rem;
            color: #eb6643;
            flex-shrink: 0;
        }
        .dashboard-card__label {
            font-size: 1.0625rem;
            font-weight: 600;
            color: #2c2826;
        }
        .dashboard-card__desc {
            font-size: 0.9375rem;
            line-height: 1.5;
            color: #706662;
            margin: 0;
        }
    </style>

    <div class="dashboard-grid">
        @foreach ($this->getCards() as $card)
            <a href="{{ $card['url'] }}" class="dashboard-card">
                <div class="dashboard-card__head">
                    @svg($card['icon'], 'dashboard-card__icon')
                    <span class="dashboard-card__label">{{ $card['label'] }}</span>
                </div>
                <p class="dashboard-card__desc">{{ $card['description'] }}</p>
            </a>
        @endforeach
    </div>
</x-filament::page>
