<div class="bg-white rounded-lg shadow p-4">
    {{-- <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-4">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-bold">Sale Complete!</span>
        </div>
        <p class="mt-2">Sale ID: #</p>
        <p>Change: </p>
    </div> --}}

    <div class="flex justify-between">
        @if ($checkAmountTendered == 0)
            <button wire:click="confirmCheckout"
                class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 w-full cursor-pointer">
                Confirm Checkout
            </button>
        @else
            <button wire:click="proceedCheckout"
                class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 w-full cursor-pointer">
                Checkout
            </button>
        @endif
        {{-- <button class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">
            Print Receipt
        </button> --}}
    </div>

    @if ($showCheckoutModal)
        <div class="fixed inset-0 bg-gray-600/70 flex justify-center items-center z-50 overflow-y-auto p-4">
            <div class="p-4 bg-white rounded shadow w-[500px]">
                <h2 class="text-lg font-semibold">Checkout</h2>

                <div class="mt-4">
                    <label class="block">Total: <strong>Php {{ number_format($total, 2) }}</strong></label>
                </div>

                <div class="mt-4">
                    <label for="amountTendered" class="block">Amount Tendered:</label>
                    <input type="number" wire:model.live="amountTendered" class="w-full p-2 border rounded" />
                </div>

                <div class="mt-4">
                    <label class="block">Change: <strong>Php {{ number_format($change, 2) }}</strong></label>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button wire:click="completeCheckout"
                        class="px-4 py-2 bg-blue-600 text-white rounded cursor-pointer">Confirm</button>
                    <button wire:click="closeModal"
                        class="px-4 py-2 bg-gray-500 text-white rounded cursor-pointer">Cancel</button>
                </div>

                @if (session()->has('error'))
                    <p class="text-red-600">{{ session('error') }}</p>
                @endif

                @if (session()->has('success'))
                    <p class="text-green-600">{{ session('success') }}</p>
                @endif
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.Livewire) {
            Livewire.on('cart-empty', message => {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cart is Empty',
                    text: message,
                });
            });

            Livewire.on('checkout-succesful', message => {
                Swal.fire({
                    icon: 'success',
                    title: 'Checkout successfully',
                    text: message,
                });
            });

            Livewire.on('insuf-stock', message => {
                Swal.fire({
                    icon: 'warning',
                    title: 'Insufficient stock',
                    text: message,
                });
            });
        }
    });
</script>