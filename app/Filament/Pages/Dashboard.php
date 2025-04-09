<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\TopSellingProductsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-s-home';

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            SalesChart::class,
            TopSellingProductsWidget::class
        ];
    }
}
