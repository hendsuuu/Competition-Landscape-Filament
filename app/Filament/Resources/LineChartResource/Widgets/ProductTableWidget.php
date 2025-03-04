<?php

namespace App\Filament\Resources\LineChartResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class ProductTableWidget extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.product-table-widget';
    public $brands;
    public $denoms;
    public $tableData;

    public function mount(): void
    {
        $products = Product::all();

        function convertDenomToNumber($denom)
        {
            // Jika ada simbol '~' atau '<', kita perlu mengekstrak angka atau memberi angka default
            if (strpos($denom, '<') === 0) {
                return (int)filter_var($denom, FILTER_SANITIZE_NUMBER_INT) - 1; // Anggap angka yang lebih kecil dari yang tertera
            }

            if (strpos($denom, '~') === 0) {
                return (int)filter_var($denom, FILTER_SANITIZE_NUMBER_INT);
            }

            // Mengambil angka pertama sebelum 'K'
            return (int)filter_var($denom, FILTER_SANITIZE_NUMBER_INT);
        }

        // Ambil daftar brand yang unik
        $this->brands = $products->unique('brand_id')->map(function ($product) {
            return [
                'brand_id' => $product->brand_id,
                'brand_name' => $product->brand->name // Ambil brand_name dari relasi
            ];
        });

        // Ambil denoms yang unik
        $this->denoms = $products->unique('denom')->sortBy(function ($denom) {
            return convertDenomToNumber($denom->denom);
        });

        foreach ($this->denoms as $denom) {
            foreach ($this->brands as $brand) {
                $names = Product::where('denom', $denom->denom)
                    ->where('brand_id', $brand['brand_id'])
                    ->pluck('product_name');

                // Buat list dalam format HTML
                $htmlList = "<ul>";
                foreach ($names as $name) {
                    $htmlList .= "<li>{$name}</li>";
                }
                $htmlList .= "</ul>";

                // Simpan hasil list HTML ke dalam tableData dengan brand_name sebagai header
                $this->tableData[$denom->denom][$brand['brand_name']] = $htmlList;
            }
        }
    }
}
