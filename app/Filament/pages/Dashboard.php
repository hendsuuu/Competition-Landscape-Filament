<?php

namespace App\Filament\pages;

class Dashboard extends \Filament\pages\Dashboard
{
    public function getColumns(): int | string | array
    {
        return 1;
    }
}
