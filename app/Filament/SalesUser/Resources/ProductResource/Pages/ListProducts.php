<?php

namespace App\Filament\SalesUser\Resources\ProductResource\Pages;

use App\Filament\Sales\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getTableQuery(): Builder
    {
        // Query dasar untuk mengambil semua produk
        return static::getResource()::getEloquentQuery()
            ->when(Auth::id() !== 1, function (Builder $query) {
                // Jika user bukan admin, filter produk berdasarkan user_id
                return $query->where('user_id', Auth::id());
            });
    }
}
