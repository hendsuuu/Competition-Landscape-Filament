<?php

namespace App\Filament\SalesUser\Resources\ProductResource\Pages;

use App\Filament\SalesUser\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
