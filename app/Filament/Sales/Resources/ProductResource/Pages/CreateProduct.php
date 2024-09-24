<?php

namespace App\Filament\Sales\Resources\ProductResource\Pages;

use App\Filament\Sales\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
