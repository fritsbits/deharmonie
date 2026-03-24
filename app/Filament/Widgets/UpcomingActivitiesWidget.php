<?php

namespace App\Filament\Widgets;

use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UpcomingActivitiesWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $upcoming = Activiteit::where('status', 'gepubliceerd')
            ->where('datum', '>=', today())
            ->where('datum', '<=', today()->addDays(30))
            ->count();

        $open = Deelnameverzoek::where('status', 'te_contacteren')->count();

        return [
            Stat::make('Komende activiteiten (30 dagen)', $upcoming)
                ->icon('heroicon-o-calendar')
                ->color('success'),
            Stat::make('Openstaande inschrijvingen', $open)
                ->icon('heroicon-o-user-group')
                ->color($open > 0 ? 'warning' : 'gray'),
        ];
    }
}
