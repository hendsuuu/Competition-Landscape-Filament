<?php

namespace App\Filament\Resources\LineChartResource\Widgets;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\ChartWidget;


class BlogPostsChart extends ChartWidget
{
    
    protected static ?string $heading = 'EUP vs Total Kuota';

    protected function getData(): array
    {
        $data = Trend::query(Product::where('brand_id',2));
        return [
            'datasets' => [
                [
                    'label' => 'EUP',
                    'data' => $data,
                ],
                
            ],
            'labels' => Product::all()->pluck('product_name')->toArray(),
            
        ];
    
    }

    protected function getType(): string
    {
        return 'line';
    }
}
