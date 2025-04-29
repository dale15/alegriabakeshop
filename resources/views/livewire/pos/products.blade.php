<div>
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($products as $product)
            <div wire:key="product-{{ $product->id }}" wire:click="addToCart({{ $product->id }})"
                class="bg-gray-200 rounded-lg overflow-hidden shadow hover:shadow-md transition-shadow duration-300 cursor-pointer">

                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden p-2">
                    @if ($product->image_url)
                        <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}"
                            class="w-full h-54 object-cover rounded-lg">
                    @else
                        <div class="w-full flex items-center justify-center bg-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-full h-54 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <hr class="ms-2 me-2">

                <div class="flex justify-between p-3">
                    <h3 class="text-lg text-gray-900 truncate">{{ $product->name }}</h3>
                    <div class="flex justify-center items-center">
                        <p class="text-sm font-bold">Php {{ number_format($product->selling_price, 2) }}</p>
                        {{-- <span
                            class="text-xs px-2 py-1 bg-{{ $product->quantity > 0 ? 'green' : 'red' }}-100 text-{{ $product->quantity > 0 ? 'green' : 'red' }}-800 rounded">
                            {{ $product->quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                        </span> --}}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-8 text-center text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-2">No products found.</p>
            </div>
        @endforelse
    </div>

    @if ($showBoxModal)
        <div class="fixed inset-0 bg-gray-600/70 flex justify-center items-center z-50 overflow-y-auto p-4">
            <div class="bg-white rounded-lg max-w-4xl w-[600px] max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Customize Your Box</h2>
                            <p class="text-gray-600">Select items to add to your box.</p>
                        </div>
                        <div class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full font-medium">
                            {{ $this->getTotalSelectedQuantity() }}/{{ $boxItemLimit }} Selected
                        </div>
                    </div>
                    <div class="mt-4">
                        <p><strong>Product:</strong> {{ $selectedProduct->name }}</p>
                        <p class="text-sm text-gray-500">Select exactly {{ $boxItemLimit }} items to complete your box</p>
                    </div>

                    {{-- <select wire:model.live="selectedBoxProduct"
                        class="px-4 py-2 mt-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a product</option>
                        @foreach ($boxProducts as $product)
                        <option value="{{ $product->id }}"> {{ $product->name }} </option>
                        @endforeach
                    </select> --}}

                    @if (session()->has('warningLimit'))
                        <div class="bg-red-500 text-white px-4 py-2 rounded-md mb-2 mt-4">
                            {{ session('warningLimit') }}
                        </div>
                    @endif
                </div>


                <div class="p-4 overflow-y-auto ">
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($boxItems as $items)
                            <div wire:key="box-item-{{ $items->id }}"
                                class="bg-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">

                                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden p-2">
                                    @if($items->image_url)
                                        <img src="{{ asset('storage/' . $items->image_url) }}" alt="{{ $items->name }}"
                                            class="w-full h-24 object-cover">
                                    @else
                                        <div class="w-full h-24 flex items-center justify-center bg-gray-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <hr class="ml-4 me-4">

                                <div class="flex justify-between p-4 bg-gray-200">
                                    <h3 class="text-sm font-medium truncate">{{ $items->name }} </h3>
                                    <div class="flex justify-between items-center mt-1">
                                        {{-- <p class="text-sm font-bold">Php {{ number_format($product->selling_price, 2) }}
                                        </p> --}}

                                        <!-- Decrease quantity -->
                                        <button wire:click="decreaseBoxItemQuantity({{ $items['id'] }})"
                                            class="ml-2 text-gray-500 hover:text-gray-700 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <!-- Increase quantity -->
                                        <button wire:click="toggleBoxItem({{ $items['id'] }})"
                                            class="ml-2 text-green-500 hover:text-green-700 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="px-6 py-3">
                    <h4 class="font-medium text-gray-700 mb-2">Selected Items:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($selectedBoxItems as $item)
                            <div class="flex items-center bg-blue-50 px-3 py-1 rounded-full">
                                <span class="text-sm">{{ $item['name'] }} (x{{ $item['quantity'] }})</span>
                                <button wire:click="removeBoxItem({{ $item['id'] }})"
                                    class="ml-2 text-red-500 hover:text-red-700 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-2 p-4">
                    <p class="text-sm font-medium">Notes</p>
                    <textarea wire:model="notes"
                        class="px-4 py-2 mt-4 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Add some notes"></textarea>
                </div>

                <div class="px-6 py-4 border-t bg-gray-50">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        {{-- <div>
                            <p class="text-gray-600 mb-1">Box Total:</p>
                            <p class="text-xl font-bold">Php {{ number_format($boxTotal, 2) }}</p>
                        </div> --}}
                        <div class="flex gap-2">
                            <button wire:click="closeModal"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-red-50 cursor-pointer">
                                Close
                            </button>

                            <button wire:click="addBoxToCart" @if($this->getTotalSelectedQuantity() !== $boxItemLimit)
                            disabled @endif
                                class="cursor-pointer px-4 py-2 rounded-lg text-white font-medium {{ $this->getTotalSelectedQuantity() === $boxItemLimit ? 'bg-blue-500 hover:bg-blue-600' : 'bg-gray-300 cursor-not-allowed' }}">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>