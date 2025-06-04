<div>
    <div class="" style="margin-top:20px;">
        {{-- Product Dropdown --}}
        <div class="bg-white rounded-lg overflow-hidden shadow p-4">
            <label for="product-select" class="block mb-2 font-medium">Select a Product:</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <select id="product-select" wire:model="productId" wire:change="calculateTotalQuantity"
                        class="border rounded px-3 py-2 w-full max-w-sm">
                        <option value="">All Products</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="flex space-x-2 mb-4">
                    <div>
                        <label for="startDate">Start Date:</label>
                        <input type="date" id="startDate" wire:model.lazy="startDate" class="border rounded p-1">
                    </div>

                    <div style="margin-left:10px;">
                        <label for="endDate">End Date:</label>
                        <input type="date" id="endDate" wire:model.lazy="endDate" class="border rounded p-1">
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

    </div>

    <div>
        @livewire(\App\Filament\Pages\SalesOverview::class)
        @livewire(\App\Filament\Widgets\SalesReportTable::class)
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let chart = null;

            Livewire.on('refreshChart', ([labels, data]) => {
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
                        series: [{
                            name: 'Quantity Sold',
                            data: data
                        }],
                        xaxis: {
                            categories: labels,
                            title: {
                                text: 'Date'
                            },
                            labels: {
                                formatter: function (value) {
                                    if (!value) return '';
                                    const date = new Date(value);
                                    if (isNaN(date)) return '';
                                    return date.toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric'
                                    });
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Quantity Sold'
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