<?php

namespace App\Filament\Resources\StatsResource\Widgets;

use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Sales', User::query()->count() - 1)
                ->description('Total Sales')
                ->descriptionIcon('heroicon-m-user'),
            Stat::make('Total Product', Product::query()->count()),
            Stat::make('Indosat', Product::query()->where('brand_id', '1')->count()),
            Stat::make('Tri', Product::query()->where('brand_id', '2')->count()),
            Stat::make('Telkomsel', Product::query()->where('brand_id', '3')->count()),
            Stat::make('XL', Product::query()->where('brand_id', '4')->count()),
            Stat::make('Axis', Product::query()->where('brand_id', '5')->count()),
            Stat::make('Smartfren', Product::query()->where('brand_id', '6')->count()),
        ];
    }
}
