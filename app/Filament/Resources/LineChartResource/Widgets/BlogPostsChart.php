<?php

namespace App\Filament\Resources\LineChartResource\Widgets;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Product;
use App\Models\Brand;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Filament\Support\RawJs;


class BlogPostsChart extends ChartWidget
{

    protected static ?string $heading = 'EUP vs Total Kuota';

    protected function getData(): array
    {
        $scatterData = Product::where('eup', '<', 400000)
            ->select('brand_id', 'eup', 'product_name', 'total_kuota')
            ->get();
        $groupedData = $scatterData->groupBy('brand_id');


        // Mendefinisikan array warna
        $colors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#C9CBCF',
            '#FF6384',
            '#36A2EB',
            '#FFCE56'
        ];

        $datasets = [];
        $brandIndex = 0; // Index untuk mengakses warna

        foreach ($groupedData as $brand_id => $dataPoints) {
            $brand = Brand::where('id', $brand_id)->first();  // Mengambil nama brand
            $brandName = $brand ? $brand->name : 'Unknown Brand';

            $scatterPoints = [];
            foreach ($dataPoints as $point) {
                $scatterPoints[] = [
                    'x' => $point['eup'],
                    'y' => $point['total_kuota'],
                    'name' => $point['product_name'], //tambahan
                ];
            }

            // Gunakan warna dari array colors, loop kembali ke awal jika melebihi jumlah warna yang ada
            $color = $colors[$brandIndex % count($colors)];
            $datasets[] = [
                'label' => $brandName,  // Memperjelas label
                'data' => $scatterPoints,
                'borderColor' => $color,
                'backgroundColor' => $color,
            ];

            $brandIndex++; // Pindah ke warna selanjutnya
        }

        return [
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'scatter';
    }

    // protected function getOptions(): array
    // {
    //     return [
    //         'scales' => [
    //             'x' => [
    //                 'title' => [
    //                     'display' => true,
    //                     'text' => 'EUP',
    //                 ],
    //             ],
    //             'y' => [
    //                 'title' => [
    //                     'display' => true,
    //                     'text' => 'Total Kuota',
    //                 ],
    //             ],
    //         ],
    //         'plugins' => [
    //             'tooltip' => [
    //                 'callbacks' => [
    //                     'label' => function ($tooltipItem) {
    //                         // Menampilkan informasi default ditambah dengan 'name' dari data
    //                         $name = $tooltipItem->raw['name'];
    //                         $x = $tooltipItem->raw['x'];
    //                         $y = $tooltipItem->raw['y'];

    //                         return "$name: ($x, $y)"; // Modifikasi dengan menampilkan 'name' bersama koordinat
    //                     },
    //                 ],
    //             ],
    //         ],
    //     ];
    // }
}
