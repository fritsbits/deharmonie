<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ActiviteitResource;
use App\Filament\Resources\TeamLidResource;
use App\Filament\Resources\WeekMenuDagResource;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Welkom';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    /**
     * @return array<int, array{label: string, description: string, icon: string, url: string}>
     */
    public function getCards(): array
    {
        return [
            [
                'label' => 'Activiteiten',
                'description' => 'Voeg nieuwe activiteiten toe of pas bestaande aan.',
                'icon' => 'heroicon-o-calendar',
                'url' => ActiviteitResource::getUrl('index'),
            ],
            [
                'label' => 'Restaurant & Menu',
                'description' => 'Werk het weekmenu bij.',
                'icon' => 'heroicon-o-clipboard-document-list',
                'url' => WeekMenuDagResource::getUrl('index'),
            ],
            [
                'label' => 'Over ons',
                'description' => 'Pas de tekst en cijfers van de Over ons-pagina aan.',
                'icon' => 'heroicon-o-document-text',
                'url' => ManageOverOnsContent::getUrl(),
            ],
            [
                'label' => 'Wie is wie',
                'description' => 'Beheer de teamleden.',
                'icon' => 'heroicon-o-user-group',
                'url' => TeamLidResource::getUrl('index'),
            ],
        ];
    }
}
