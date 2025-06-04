<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-s-home';

    protected static string $view = 'filament.pages.dashboard';

    // public function getWidgets(): array
    // {
    //     return [
    //         StatsOverviewWidget::class,
    //         SalesChart::class,
    //         SalesPerProductChart::class,
    //         TopSellingProductsWidget::class
    //     ];
    // }
}
