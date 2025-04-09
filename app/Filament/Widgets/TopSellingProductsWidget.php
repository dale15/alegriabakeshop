<?php

namespace App\Filament\Widgets;

use App\Models\SaleItem;
use Filament\Widgets\ChartWidget;

class TopSellingProductsWidget extends ChartWidget
{
    protected static ?string $heading = 'Top Selling Products';

    protected function getData(): array
    {
        $topProducts = SaleItem::selectRaw('product_id, SUM(quantity) as total_quantity')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(3)
            ->with('product')
            ->get();


        return [
            'datasets' => [
                [
                    'label' => 'Products Sold',
                    'data' => $topProducts->pluck('total_quantity'),
                    'backgroundColor' => [
                        '#ff6384',
                        '#36a2eb',
                        '#cc65fe',
                        '#ffce56',
                        '#4bc0c0'
                    ], // You can add more colors if needed
                ],
            ],
            'labels' => $topProducts->pluck('product.name'),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    // Add custom CSS for the chart size
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false, // Allow resizing
            'plugins' => [
                'legend' => ['position' => 'top'],
            ],
        ];
    }
}
