<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\SaleItem;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SalesPerProductChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Per Product';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'this_week' => 'This week',
            'last_week' => 'Last week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
        ];
    }

    private function generateColorPalette(int $count): array
    {
        $colors = [];

        for ($i = 0; $i < $count; $i++) {
            $hue = intval(360 * $i / max($count, 1)); // even spacing
            $saturation = 70; // %
            $lightness = 60; // %

            // Convert HSL to hex
            $colors[] = $this->hslToHex($hue, $saturation, $lightness);
        }

        return $colors;
    }

    private function hslToHex($h, $s, $l): string
    {
        $s /= 100;
        $l /= 100;

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;

        if ($h < 60) {
            [$r, $g, $b] = [$c, $x, 0];
        } elseif ($h < 120) {
            [$r, $g, $b] = [$x, $c, 0];
        } elseif ($h < 180) {
            [$r, $g, $b] = [0, $c, $x];
        } elseif ($h < 240) {
            [$r, $g, $b] = [0, $x, $c];
        } elseif ($h < 300) {
            [$r, $g, $b] = [$x, 0, $c];
        } else {
            [$r, $g, $b] = [$c, 0, $x];
        }

        $r = intval(($r + $m) * 255);
        $g = intval(($g + $m) * 255);
        $b = intval(($b + $m) * 255);

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? 'today';

        // Default to 'yearly' if no filter is set
        $startDate = now()->startOfDay();
        $endDate = now()->endOfDay();

        switch ($activeFilter) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;

            case 'this_week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;

            case 'last_week':
                $startDate = now()->subWeek()->startOfWeek();
                $endDate = now()->subWeek()->endOfWeek();
                break;

            case 'this_month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;

            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                break;
        }

        $rawData = SaleItem::selectRaw('DATE(created_at) as date, product_id, SUM(total) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date', 'product_id')
            ->orderBy('date')
            ->get()
            ->groupBy('product_id');

        $products = Product::where('is_box', false)->get()->keyBy('id');

        $labels = collect();
        $datasets = [];

        $allDates = collect();
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $allDates->push($current->format('Y-m-d'));
            $current->addDay();
        }

        $labels = $allDates;

        $productIds = $rawData->keys(); // array of product IDs
        $colorPalette = $this->generateColorPalette($productIds->count());

        $colorIndex = 0;

        foreach ($rawData as $productId => $records) {
            $productName = $products[$productId]->name ?? "Product $productId";
            $recordsByDate = $records->keyBy('date');

            $color = $colorPalette[$colorIndex++];

            $datasets[] = [
                'label' => $productName,
                'data' => $allDates->map(fn($date) => $recordsByDate[$date]->total ?? 0),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'fill' => true,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];

        // $trend = Trend::model(SaleItem::class)->between(start: $startDate, end: $endDate);
        // $trend = match ($grouping) {
        //     'perHour' => $trend->perHour(),
        //     'perDay' => $trend->perDay(),
        //     'perMonth' => $trend->perMonth(),
        //     'perYear' => $trend->perYear(),
        //     default => $trend->perDay(),
        // };

        // $data = $trend->sum('total');

        // dd($data);

        // return [
        //     'datasets' => [
        //         [
        //             'label' => 'Total Sales',
        //             'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
        //         ],
        //     ],
        //     'labels' => $data->map(fn(TrendValue $value) => $value->date),
        // ];

        // Get top-selling products (last 30 days)
        // $sales = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
        //     ->selectRaw('products.name as product, SUM(sale_items.quantity) as total_sold')
        //     ->where('sale_items.created_at', '>=', now()->subDays(30))
        //     ->groupBy('products.name')
        //     ->orderByDesc('total_sold')
        //     ->limit(10) // Show only top 10 products
        //     ->get();

        // return [
        //     'datasets' => [
        //         [
        //             'label' => 'Units Sold',
        //             'data' => $sales->pluck('total_sold')->toArray(),
        //             'backgroundColor' => '#f59e0b', // Orange for sales
        //         ],
        //     ],
        //     'labels' => $sales->pluck('product')->toArray(),
        // ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
