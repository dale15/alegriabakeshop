<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\SaleItem;
use Livewire\Component;
use Carbon\CarbonPeriod;

class SalesReportsOverview extends Component
{
    public string $activeTab = 'productSalesReport';

    public $productId = null;
    public $products = [];
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
        $query = SaleItem::query()
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);

        if ($this->productId) {
            $query->where('product_id', $this->productId);
        }

        $sales = $query->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date'); // key by date for easy lookup

        // Generate all dates between start and end
        $period = CarbonPeriod::create($this->startDate, $this->endDate);

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

        $this->dispatch('refreshChart', $this->chartLabels, $this->chartData);
    }

    public function render()
    {
        return view('livewire.sales-reports-overview');
    }
}
