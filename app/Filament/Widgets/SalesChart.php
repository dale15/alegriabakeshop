<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Sale;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Revenue';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'this_week' => 'This week',
            'last_week' => 'Last week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? 'today';

        // Default to 'yearly' if no filter is set
        $startDate = now()->startOfDay();
        $endDate = now()->endOfDay();
        $grouping = 'perHour';

        switch ($activeFilter) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                $grouping = 'perHour';
                break;

            case 'this_week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                $grouping = 'perDay';
                break;

            case 'last_week':
                $startDate = now()->subWeek()->startOfWeek();
                $endDate = now()->subWeek()->endOfWeek();
                $grouping = 'perDay';
                break;

            case 'this_month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $grouping = 'perDay';
                break;

            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                $grouping = 'perDay';
                break;

            case 'this_year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                $grouping = 'perMonth';
                break;

            case 'last_year':
                $startDate = now()->subYear()->startOfYear();
                $endDate = now()->subYear()->endOfYear();
                $grouping = 'perMonth';
                break;

            case 'all_time':
                $startDate = Sale::min('created_at') ?? now()->startOfYear();
                $endDate = Sale::max('created_at') ?? now();
                $grouping = 'perYear';
                break;
        }

        $trend = Trend::model(Sale::class)->between(start: $startDate, end: $endDate);
        $trend = match ($grouping) {
            'perHour' => $trend->perHour(),
            'perDay' => $trend->perDay(),
            'perMonth' => $trend->perMonth(),
            'perYear' => $trend->perYear(),
            default => $trend->perDay(),
        };

        $data = $trend->sum('total_amount');

        return [
            'datasets' => [
                [
                    'label' => 'Total Sales',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
