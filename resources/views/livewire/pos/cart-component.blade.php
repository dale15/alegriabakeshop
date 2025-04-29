<div class="bg-white rounded-lg shadow p-4">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Current Sale</h2>
        <button wire:click="clearCart" class="p-2 text-red-500 hover:text-red-700 cursor-pointer">
            Clear All
        </button>
    </div>

    @if (count($cartItems) > 0)
        <div class="max-h-64 overflow-y-auto mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Item</th>
                        <th class="text-center py-2">Qty</th>
                        <th class="text-right py-2">Price</th>
                        <th class="text-right py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr wire:key="cart-item-{{ $item['id'] }}">
                            <td class="py-2">
                                <div class="flex items-center">
                                    <div class="truncate text-lg">{{ $item['name'] }}</div>
                                </div>

                                @if ($item['boxItems'] != null)
                                    @foreach ($item['boxItems'] as $boxItems)
                                        <div class="flex items-center">
                                            <div class="truncate text-sm ml-2">{{ $boxItems['name'] }} ({{ $boxItems['quantity'] }} x)
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </td>
                            <td class="py-2">
                                <div class="flex items-center justify-center space-x-3">
                                    <button wire:click="updateCartItemQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                        class="cursor-pointer w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 12H6" />
                                        </svg>
                                    </button>
                                    <span>{{ $item['quantity'] }}</span>
                                    <button wire:click="updateCartItemQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                        class="cursor-pointer w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="py-2 text-right font-bold">Php {{ number_format($item['price'] * $item['quantity'], 2) }}
                            </td>
                            <td class="py-2 text-right">
                                <button wire:click="removeFromCart({{ $item['id'] }})"
                                    class="text-red-500 hover:text-red-700 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pt-4 space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span>Php {{ number_format($subtotal, 2) }}</span>
            </div>

            @if ($discountAmount == 0)
                <div class="flex justify-between font-bold text-lg">
                    <span>Total:</span>
                    <span>Php {{ number_format($total, 2) }}</span>
                </div>
            @else
                <div class="flex justify-between">
                    <span class="text-gray-600">Discount:</span>
                    <span>Php {{ number_format($discountAmount, 2) }}</span>
                </div>

                <div class="flex justify-between font-bold text-lg">
                    <span>Total:</span>
                    <span>Php {{ number_format($totalAfterDiscount, 2) }}</span>
                </div>
            @endif

            @if ($amountTendered > 0)
                <div class="flex justify-between mt-4">
                    <span class="text-gray-600">Amount Tendered</span>
                    <span>Php {{ number_format($amountTendered, 2) }}</span>
                </div>

                <div class="flex justify-between mt-4">
                    <span class="text-gray-600">Change Due:</span>
                    <span>Php {{ number_format($change, 2) }}</span>
                </div>
            @endif
        </div>
    @else
        <div class="py-8 text-center text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="mt-2">Your cart is empty</p>
        </div>
    @endif
</div>