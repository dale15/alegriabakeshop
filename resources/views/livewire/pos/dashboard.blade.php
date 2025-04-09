<div class="flex flex-col md:flex-row md:space-x-6">
    <!-- Left Side - Products -->
    <div class="md:w-2/3 space-y-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold">Products</h2>
                <div class="flex space-x-4">
                    <div class="relative">
                        <livewire:pos.category-filter />
                    </div>
                    <div class="relative">
                        <input id="product-search" type="text" placeholder="Search products..."
                            class="border rounded-lg px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <livewire:pos.products />
        </div>
    </div>

    <!-- Right Side - Cart and Checkout -->
    <div class="md:w-1/3 space-y-6 mt-6 md:mt-0">
        <livewire:pos.cart-component />
        <livewire:pos.checkout-component />

        <div class="bg-white rounded-lg shadow p-4">
            <div class="grid grid-cols-2 gap-2">
                {{-- <div class="bg-blue-500 text-white rounded p-2 text-center cursor-pointer hover:bg-blue-600">
                    <div class="text-sm">F2</div>
                    <div class="font-bold">New Sale</div>
                </div>
                <div class="bg-green-500 text-white rounded p-2 text-center cursor-pointer hover:bg-green-600">
                    <div class="text-sm">F3</div>
                    <div class="font-bold">Search</div>
                </div> --}}
                {{-- <div class="bg-yellow-500 text-white rounded p-2 text-center cursor-pointer hover:bg-yellow-600">
                    <div class="text-sm">F4</div>
                    <div class="font-bold">Quick Pay</div>
                </div>
                <div class="bg-purple-500 text-white rounded p-2 text-center cursor-pointer hover:bg-purple-600">
                    <div class="text-sm">F5</div>
                    <div class="font-bold">Hold</div> --}}
                </div>
            </div>
        </div>
    </div>
</div>