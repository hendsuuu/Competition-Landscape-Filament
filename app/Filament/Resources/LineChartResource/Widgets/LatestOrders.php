<?php

namespace App\Filament\Resources\LineChartResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\TableWidget as BaseWidget;

// DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");

class LatestOrders extends BaseWidget
{


    protected function getTableQueryw()
    {
        // Pastikan untuk menggunakan DB::raw() dengan benar
        return Product::select('brand_id', 'denom', DB::raw('COUNT(*) as total'))
            ->groupBy('brand_id', 'denom')
            ->orderBy(DB::raw('MAX(id)'), 'asc') // Menggunakan MAX(id) sebagai basis pengurutan
            ->get();
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Ini harus merupakan query builder, bukan hasil dari get()
                Product::select('brand_id', 'denom', DB::raw('count(*) as total'))
                    ->groupBy('brand_id', 'denom')
            )
            ->columns([
                // Definisikan kolom Anda di sini
                TextColumn::make('brand_id')->label('Brand ID'),
                TextColumn::make('denom')->label('Denomination'),
                TextColumn::make('total')->label('Total'),
            ]);
    }
}
