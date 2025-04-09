<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Sale;
use Carbon\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Revenue';

    protected function getFilters(): ?array
    {
        return [
            'daily' => 'Last 7 Days',
            'monthly' => 'Last 6 Months',
            'yearly' => 'Last 1 Year',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? 'daily'; // Default to daily if no filter selected

        if ($filter === 'daily') {
            $sales = Sale::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } elseif ($filter === 'monthly') {
            $sales = Sale::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as date, SUM(total_amount) as revenue')
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else { // Yearly
            $sales = Sale::selectRaw('YEAR(created_at) as date, SUM(total_amount) as revenue')
                ->where('created_at', '>=', Carbon::now()->subYears(1))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $sales->pluck('revenue')->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $sales->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
