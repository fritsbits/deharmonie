<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\EditProfile;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(EditProfile::class, isSimple: false)
            ->colors([
                'primary' => Color::hex('#eb6643'),
                'gray' => Color::hex('#706662'),
            ])
            ->font('Nunito Sans', provider: GoogleFontProvider::class)
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->renderHook('panels::head.end', fn (): HtmlString => new HtmlString('
<style>
/* De Harmonie admin overrides */

/* Form actions footer — dark band visually separated from form content */
.fi-sc-actions {
    background-color: #2c2826;
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    margin-top: 0.75rem;
}
.fi-sc-actions .fi-btn {
    font-weight: 800;
    letter-spacing: 0.01em;
}
/* Cancel button readable on dark background */
.fi-sc-actions .fi-btn-color-gray {
    color: rgba(255,255,255,0.65) !important;
    border-color: rgba(255,255,255,0.2) !important;
    background: transparent !important;
}
.fi-sc-actions .fi-btn-color-gray:hover {
    color: white !important;
    border-color: rgba(255,255,255,0.45) !important;
}
</style>
<script>
    /* Force the sidebar collapsed on every page load. The toggle button still
       lets the user expand mid-session if they need to read labels, but the
       next navigation snaps back to compact. Filament reads this localStorage
       key via Alpine.$persist on every Alpine init. */
    (function () {
        try {
            window.localStorage.setItem("isOpenDesktop", "false");
        } catch (e) { /* localStorage unavailable — ignore */ }
    })();
</script>
            '))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
