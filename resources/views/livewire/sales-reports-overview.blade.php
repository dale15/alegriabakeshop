<div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white rounded-lg overflow-hidden shadow p-4">

        <div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label for="product-select" class="block mb-2 font-medium">Select a Product:</label>
                    <select id="product-select" wire:model="productId" wire:change="calculateTotalQuantity"
                        class="border rounded px-3 py-2 w-full max-w-sm">
                        <option value="">All Products</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex space-x-2">
                    <div>
                        <label for="startDate" class="block mb-2 font-medium">Start Date:</label>
                        <input type="date" id="startDate" wire:model.lazy="startDate"
                            class="border rounded px-3 py-2 w-full max-w-sm">
                    </div>

                    <div style="margin-left:10px;">
                        <label for="endDate" class="block mb-2 font-medium">End Date:</label>
                        <input type="date" id="endDate" wire:model.lazy="endDate"
                            class="border rounded px-3 py-2 w-full max-w-sm">
                    </div>
                </div>
            </div>

            {{-- Total Quantity Display --}}
            @if ($productId)
                <div class="mt-4 p-4 bg-gray-50 rounded shadow-sm max-w-sm">
                    <strong>Total Quantity Sold:</strong>
                    <span class="ml-2">{{ $totalQuantitySold }}</span>
                </div>
            @endif

            <div id="chart" class="my-4 relative">

            </div>
        </div>

        <div>
            @livewire(\App\Filament\Widgets\ProductSalesReportWidget::class)
        </div>

    </div>

    <div>
        @livewire(\App\Filament\Pages\SalesOverview::class)
        @livewire(\App\Filament\Widgets\SalesReportTable::class)
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let chart = null;

            Livewire.on('refreshChart', ([labels, series, productName]) => {
                setTimeout(() => {
                    const chartEl = document.querySelector("#chart");

                    if (!chartEl) {
                        console.error('Chart container not found');
                        return;
                    }

                    const options = {
                        chart: {
                            type: 'bar',
                            height: 400,
                        },
                        title: {
                            text: productName,  // <-- Add your product name here
                            align: 'center',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold',
                            }
                        },
                        series: [{
                            name: productName + ' Sold',
                            data: series
                        }],
                        xaxis: {
                            categories: labels,
                            title: {
                                text: 'Date'
                            },
                            labels: {
                                formatter: function (value) {
                                    const date = new Date(value);
                                    return date.toLocaleDateString('en-US', {
                                        year: 'numeric', month: 'short', day: 'numeric'
                                    });
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Item Sold'
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val, opts) {
                                return `${val}`; // just quantity, or customize as needed
                            }
                        }
                    };

                    if (chart) {
                        chart.updateOptions(options);
                    } else {
                        chart = new ApexCharts(chartEl, options);
                        chart.render();
                    }

                }, 100); // small delay to allow DOM update
            });


        });
    </script>
</div>