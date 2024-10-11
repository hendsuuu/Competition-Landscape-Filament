<?php

namespace App\Providers\Filament;

use App\Filament\Resources\StatsResource\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use App\Filament\Resources\LineChartResource\Widgets\BlogPostsChart;
use App\Filament\Resources\LineChartResource\Widgets\ProductTableWidget;
use App\Filament\Resources\LineChartResource\Widgets\YieldeUp as WidgetsYieldeUp;
use Filament\View\LegacyComponents\Widget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use PhpParser\Node\Expr\Yield_;
use Filament\Support\Enums\MaxWidth;

class AdminPanelProvider extends PanelProvider
{
    // public function getWidgets(): array
    // {
    //     return [
    //         BlogPostsChart::class,
    //         BlogPostsChart::class,
    //         // BlogPostsChart::class,
    //         // BlogPostsChart::class,
    //     ];
    // }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::hex('#FFEB00'),
            ])
            ->brandName('Indosat')
            ->brandLogo(asset('logo-ioh.svg'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                StatsOverview::class,
                BlogPostsChart::class,
                WidgetsYieldeUp::class,
                ProductTableWidget::class
                // LatestOrders::class
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
