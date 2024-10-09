<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductTableWidget extends Widget
{
    protected static string $view = 'filament.widgets.product-table-widget'; // Correct the type declaration here


    public $brands;
    public $denoms;
    public $tableData;

    public function mount(): void
    {
        $products = Product::all();
        $this->brands = $products->unique('brand_id');
        $this->denoms = $products->unique('denom');

        foreach ($this->denoms as $denom) {
            foreach ($this->brands as $brand) {
                $names = Product::where('denom', $denom->denom)
                    ->where('brand_id', $brand->brand_id)
                    ->pluck('product_name')->join(', ');
                $this->tableData[$denom->denom][$brand->brand_id] = $names;
            }
        }
    }
}
