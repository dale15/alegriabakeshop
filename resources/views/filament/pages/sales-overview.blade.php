<x-filament-panels::page>
    <div x-data="{ isOpen: true }" class="bg-gray-100 p-4 shadow rounded-lg">

        <!-- Toggle Button -->
        <button @click="isOpen = !isOpen" class="text-primary-600 px-4 py-2 mb-4">
            <span x-show="!isOpen">Show Sales Overview</span>
            <span x-show="isOpen">Hide Sales Overview</span>
        </button>

        <!-- Collapsible Content -->
        <div x-show="isOpen" class="mt-4 flex gap-4">
            <!-- Sales Table -->
            <div class="w-2/3">
                {{ $this->table }}
            </div>

            <!-- Sale Items Grid -->
            <div class="w-full bg-white p-4 shadow rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Product Items</h2>

                @if($selectedSaleId)
                    @if (count($saleItems) == 0)
                        <div class="col-span-full py-8 text-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2">No items found.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($saleItems as $item)
                                <div class="border p-2 rounded-lg bg-gray-50 shadow">
                                    <p><strong>Product:</strong> {{ $item->product->name }}</p>
                                    <p><strong>Quantity:</strong> {{ $item->quantity }}</p>
                                    <p><strong>Price:</strong> Php {{ $item->price }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p class="text-gray-500">Select a sales id to view items.</p>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>