<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SalesDateFilterForm;
use App\Filament\Widgets\SalesStatsOverview;
use App\Filament\Widgets\TopSellingProductsChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function widgets(): array
    {
        return [
            SalesDateFilterForm::class,
            SalesStatsOverview::class,
            TopSellingProductsChart::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
