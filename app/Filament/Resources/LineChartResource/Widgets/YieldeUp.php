<?php

namespace App\Filament\Resources\LineChartResource\Widgets;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Product;
use App\Models\Brand;
use App\Models\User;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class YieldeUp extends ChartWidget
{

    protected static ?string $heading = 'Yield vs EUP';
    // protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // $scatterData = Product::select('brand_id', 'eup', 'yield')->get();
        $scatterData = Product::where('eup', '<', 400000)
            ->select('brand_id', 'eup', 'yield', 'product_name')
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
                    'y' => $point['yield'],
                    'name' => $point['product_name']
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
            'options' => [
                'plugins' => [
                    'tooltip' => [
                        'callbacks' => [
                            'label' => function ($context) {
                                $xValue = $context->raw->x;
                                $yValue = $context->raw->y;
                                $name = $context->raw->name;  // Ambil nilai name

                                // Menampilkan name bersama dengan x dan y dalam tooltip
                                return "{$name}: (X={$xValue}, Y={$yValue})";
                            }
                        ]
                    ]
                ],
                'scales' => [
                    'x' => [
                        'type' => 'linear',
                        'position' => 'bottom'
                    ]
                ]
            ]
        ];
    }

    protected function getType(): string
    {
        return 'scatter';
    }

    // public function getOptions(): array
    // {
    //     return [
    //         'options' => g[
    //             'plugins' => [
    //                 'tooltip' => [
    //                     'callbacks' => [
    //                         'label' => function ($context) {
    //                             $xValue = $context->raw->x;
    //                             $yValue = $context->raw->y;
    //                             $name = $context->raw->name;  // Ambil nilai name

    //                             // Menampilkan name bersama dengan x dan y dalam tooltip
    //                             return "{$name}: (X={$xValue}, Y={$yValue})";
    //                         }
    //                     ]
    //                 ]
    //             ],
    //             'scales' => [
    //                 'x' => [
    //                     'type' => 'linear',
    //                     'position' => 'bottom'
    //                 ]
    //             ]
    //         ]
    //     ];
    // }
}
