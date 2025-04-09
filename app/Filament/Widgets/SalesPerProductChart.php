<?php

namespace App\Filament\Widgets;

use App\Models\SaleItem;
use Filament\Widgets\ChartWidget;

class SalesPerProductChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Per Product';

    protected function getData(): array
    {
        // Get top-selling products (last 30 days)
        $sales = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->selectRaw('products.name as product, SUM(sale_items.quantity) as total_sold')
            ->where('sale_items.created_at', '>=', now()->subDays(30))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(10) // Show only top 10 products
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Units Sold',
                    'data' => $sales->pluck('total_sold')->toArray(),
                    'backgroundColor' => '#f59e0b', // Orange for sales
                ],
            ],
            'labels' => $sales->pluck('product')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
