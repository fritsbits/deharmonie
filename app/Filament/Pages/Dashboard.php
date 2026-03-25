<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ActiviteitResource;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->redirect(ActiviteitResource::getUrl('index'));
    }
}
