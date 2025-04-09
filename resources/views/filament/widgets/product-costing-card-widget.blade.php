<x-filament-widgets::widget>
    {{-- Widget content --}}
    <div x-data="{ isOpen: true }" class="bg-gray-100 p-4 shadow rounded-lg">
        <button @click="isOpen = !isOpen" class="text-primary-600 px-4 py-2 mb-2">
            <span x-show="!isOpen">Show Product Costing</span>
            <span x-show="isOpen">Hide Product Costing</span>
        </button>

        <div x-show="isOpen" class="space-y-4">
            <!-- Product Cards in Grid Layout -->
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($products as $product)
                            <x-filament::card
                                class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-xl transition duration-300">
                                <!-- Product Name -->
                                <div class="px-4 py-3">
                                    <h2 class="text-xl font-semibold text-gray-800">{{ $product->name }}</h2>
                                </div>

                                <!-- Ingredients List -->
                                <div class="px-4 py-2 space-y-2">
                                    @foreach ($product->productIngredients as $productIngredient)
                                        <div
                                            class="flex items-center justify-between py-1 px-2 rounded-md bg-gray-50 border border-gray-200">
                                            <!-- Ingredient Name -->
                                            <span class="text-gray-700 text-sm font-medium">
                                                {{ $productIngredient->ingredient->name }}
                                            </span>

                                            <!-- Ingredient Quantity & Price -->
                                            <span class="text-sm text-gray-500">
                                                ₱{{ number_format($productIngredient->cost_per_unit, 2) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Total Cost (optional) -->
                                <div class="px-4 py-3 border-t border-gray-200 text-right">
                                    <span class="text-lg font-semibold text-gray-700">
                                        Total Cost: ₱{{ number_format($product->productIngredients->sum(function ($item) {
                    return $item->cost_per_unit; }), 2) }}
                                    </span>
                                </div>
                            </x-filament::card>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-4 py-4">
                <div class="flex justify-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>