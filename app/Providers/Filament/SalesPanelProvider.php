<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Register;
use App\Filament\Resources\LineChartResource\Widgets\BlogPostsChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use App\Filament\Resources\LineChartResource\Widgets\ProductTableWidget;
use App\Filament\Resources\LineChartResource\Widgets\YieldeUp as WidgetsYieldeUp;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SalesPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('sales')
            ->path('sales')
            ->colors([
                'primary' => Color::hex('#FFEB00'),
            ])
            ->login()
            ->registration(Register::class)
            ->brandName('Indosat')
            ->brandLogo(asset('logo-ioh.svg'))
            ->discoverResources(in: app_path('Filament/Sales/Resources'), for: 'App\\Filament\\Sales\\Resources')
            ->discoverPages(in: app_path('Filament/Sales/Pages'), for: 'App\\Filament\\Sales\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Sales/Widgets'), for: 'App\\Filament\\Sales\\Widgets')
            ->widgets([
                BlogPostsChart::class,
                WidgetsYieldeUp::class,
                ProductTableWidget::class
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ]);
    }
}
