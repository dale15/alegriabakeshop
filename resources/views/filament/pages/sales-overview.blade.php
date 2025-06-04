<x-filament-panels::page>
    <!-- Sales Table -->
    {{ $this->table }}

    <!-- Sale Items Grid -->
    <div x-data="{showModal: @entangle('showModal')}">
        <div x-show="showModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

            <div @click.away="showModal = false" class="bg-white w-full max-w-2xl p-6 rounded-lg shadow-lg">

                <h2 class="text-xl font-semibold mb-4">Product Items</h2>
                @if($selectedSaleId)
                    @if (count($saleItems) == 0)
                        <div class="text-center text-gray-500 py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2">No items found.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($saleItems as $item)
                                <div class="border p-4 rounded-lg bg-gray-50 shadow">
                                    <p><strong>Product:</strong> {{ $item->product->name }}</p>
                                    <p><strong>Quantity:</strong> {{ $item->quantity }}</p>
                                    <p><strong>Price:</strong> Php {{ $item->price }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>