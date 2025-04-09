<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\HtmlString;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // ---------------- TODAY VS YESTERDAY ----------------
        $salesToday = Sale::whereDate('created_at', Carbon::today())
            ->sum('total_amount');
        $salesYesterday = Sale::whereDate('created_at', Carbon::yesterday())
            ->sum('total_amount');
        $salesChangeToday = $this->calculatePercentageChange($salesToday, $salesYesterday);

        $description = is_null($salesChangeToday) ? '∞% vs Yesterday'
            : (($salesChangeToday >= 0 ? '▲ ' : '▼ ') . abs(number_format($salesChangeToday, 2)) . '% vs Yesterday');

        $iconToday = $this->getTrendIcon($salesChangeToday);
        $colorToday = $this->getTrendColor($salesChangeToday);

        // ---------------- WEEKLY TREND (LAST 7 DAYS) ----------------
        $salesThisWeek = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total_amount');
        $salesLastWeek = Sale::whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->sum('total_amount');
        $salesChangeWeek = $this->calculatePercentageChange($salesThisWeek, $salesLastWeek);
        $iconWeek = $this->getTrendIcon($salesChangeWeek);
        $colorWeek = $this->getTrendColor($salesChangeWeek);

        // ---------------- MONTHLY TREND (THIS MONTH VS LAST MONTH) ----------------
        $salesThisMonth = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('total_amount');
        $salesLastMonth = Sale::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
            ->sum('total_amount');
        $salesChangeMonth = $this->calculatePercentageChange($salesThisMonth, $salesLastMonth);
        $iconMonth = $this->getTrendIcon($salesChangeMonth);
        $colorMonth = $this->getTrendColor($salesChangeMonth);

        // $topProducts = SaleItem::selectRaw('product_id, SUM(quantity) as total_quantity')
        //     ->groupBy('product_id')
        //     ->orderByDesc('total_quantity')
        //     ->take(3)
        //     ->with('product')
        //     ->get();

        // $topProductsHtml = $topProducts->map(function ($item, $index) {
        //     return ($index + 1) . '. ' . $item->product->name . ' - ' . $item->total_quantity;
        // })->implode('<br>');

        return [
            Stat::make('Sales Today', '₱' . number_format($salesToday, 2))
                ->description($description)
                ->descriptionIcon($iconToday, IconPosition::Before)
                ->color($colorToday),

            Stat::make('This Week Sales', '₱' . number_format($salesThisWeek, 2))
                ->description(($salesChangeWeek >= 0 ? '▲ ' : '▼ ') . abs(number_format($salesChangeWeek, 2)) . '% vs Last Week')
                ->descriptionIcon($iconWeek, IconPosition::Before)
                ->color($colorWeek),

            Stat::make('This Month Sales', '₱' . number_format($salesThisMonth, 2))
                ->description(($salesChangeMonth >= 0 ? '▲ ' : '▼ ') . abs(number_format($salesChangeMonth, 2)) . '% vs Last Month')
                ->descriptionIcon($iconMonth, IconPosition::Before)
                ->color($colorMonth),

            // Stat::make('Top 3 products', '')
            //     ->description(new HtmlString($topProductsHtml))
        ];
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current == 0 ? 0 : null; // null = undefined or infinite increase
        }

        return (($current - $previous) / $previous) * 100;
    }

    // Get trending icon
    private function getTrendIcon($change)
    {
        return $change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
    }

    // Get color based on trend
    private function getTrendColor($change)
    {
        return $change >= 0 ? 'success' : 'danger';
    }
}
