<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\SaleItem;
use Carbon\Carbon;
use Livewire\Component;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class SalesReportsOverview extends Component
{
    use WithPagination;

    public string $activeTab = 'productSalesReport';

    public $productId = null;
    public $products;
    public $totalQuantitySold = 0;


    public $startDate;
    public $endDate;
    public $chartLabels = [];
    public $chartData = [];

    public function mount()
    {
        $this->products = Product::all();
        $this->endDate = now()->toDateString(); // today
        $this->startDate = now()->subDays(6)->toDateString(); // 6 days ago (7 days total)

        $this->prepareChartData(); // show all product sales initially
    }

    public function updatedProductId()
    {
        $this->calculateTotalQuantity();
        $this->prepareChartData();
    }

    public function updatedStartDate()
    {
        $this->prepareChartData();
    }

    public function updatedEndDate()
    {
        $this->prepareChartData();
    }

    public function calculateTotalQuantity()
    {
        if ($this->productId) {
            $query = SaleItem::where('product_id', $this->productId);

            $this->totalQuantitySold = $query->sum('quantity');
        } else {
            $this->totalQuantitySold = 0;
        }
    }

    public function prepareChartData()
    {
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();

        // Compose cache key based on product and date range
        $cacheKey = "sales_{$this->productId}_{$startDate->toDateString()}_{$endDate->toDateString()}";

        // Retrieve cached sales data or run the query if cache misses
        $sales = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($startDate, $endDate) {
            $query = SaleItem::query()
                ->whereBetween('created_at', [$startDate, $endDate]);

            if ($this->productId) {
                $query->where('product_id', $this->productId);
            }

            return $query->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
        });

        $productName = 'All Products';
        if ($this->productId) {
            $productName = Product::find($this->productId)?->name ?? $productName;
        }

        // Generate all dates between start and end
        $period = CarbonPeriod::create($startDate, $endDate);

        $labels = [];
        $data = [];

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $labels[] = $formattedDate;

            // If we have sales for this date, use total; else zero
            $data[] = $sales->has($formattedDate) ? $sales->get($formattedDate)->total : 0;
        }

        $this->chartLabels = $labels;
        $this->chartData = $data;

        $this->dispatch('refreshChart', $this->chartLabels, $this->chartData, $productName);
    }

    public function render()
    {
        return view('livewire.sales-reports-overview');
    }
}
