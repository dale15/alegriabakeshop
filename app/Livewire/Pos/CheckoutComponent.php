<?php

namespace App\Livewire\Pos;

use App\Models\Sale;
use App\Models\SaleItem;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CheckoutComponent extends Component
{
    public $amountTendered;
    public $change;
    public $total;
    public $saleId = null;
    public $saleComplete = false;
    public $showCheckoutModal = false;
    public $checkAmountTendered = 0;

    protected $listeners = [
        'cartUpdated',
        'resetCheckoutBtn' => 'resetCheckout',
    ];

    public function cartUpdated()
    {
        $this->calculateChange();
    }

    public function updatedAmountTendered()
    {
        $this->calculateChange();

        if ($this->amountTendered < $this->total) {
            session()->flash('error', 'You give an insufficient cash.');
            return;
        }
    }

    public function calculateChange()
    {
        $total = $this->calculateTotal();
        $this->checkAmountTendered = $this->amountTendered;

        $this->change = max(0, $this->amountTendered - $total);
    }

    public function calculateTotal()
    {
        $cartItems = session('cart', []);

        $subtotal = collect($cartItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return $subtotal;
    }

    public function completeCheckout()
    {
        $this->dispatch('updateAmountTendered', $this->amountTendered);
        $this->showCheckoutModal = false;
    }

    public function confirmCheckout()
    {
        $cartItems = session('cart', []);

        if (!empty($cartItems)) {
            $this->showCheckoutModal = true;
            $this->total = $this->calculateTotal();
        } else {
            $this->dispatch('cart-empty', 'Your cart is empty. Please add items before checkout.');
        }
    }

    public function proceedCheckout()
    {
        $cartItems = session('cart', []);

        try {
            DB::beginTransaction();

            $sale = Sale::create([
                'total_amount' => $this->total,
                'payment_method' => 'cash',
                'amount_tendered' => $this->amountTendered,
                'change' => $this->change,
                'status' => 'completed'
            ]);

            foreach ($cartItems as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                // Deduce stock 
                $productIngredient = DB::table('product_ingredients')
                    ->where('product_id', $item['id'])
                    ->get();

                foreach ($productIngredient as $pi) {
                    $totalUsed = $pi->quantity * $item['quantity'];

                    $inventory = DB::table('inventories')
                        ->where('ingredient_id', $pi->ingredient_id)
                        ->lockForUpdate()
                        ->first();

                    if ($inventory->quantity < $totalUsed) {
                        $this->dispatch('insuf-stock', 'Insufficient stock for some ingredients');
                        throw new \Exception("Insufficient stock for ingredient ID: {$pi->ingredient_id}");
                    }

                    DB::table('inventories')
                        ->where('ingredient_id', $pi->ingredient_id)
                        ->decrement('quantity', $totalUsed);
                }
                // End of Deduce stock
            };

            DB::commit();

            $this->saleComplete = true;
            $this->saleId = $sale->id;
            session()->forget('cart');
            $this->dispatch('clearCart');

            $this->dispatch('checkout-succesful', 'Thank you for your purchase!.');
        } catch (\Exception $e) {
            logger('test ' . $e->getMessage());
            DB::rollBack();
        }
    }

    public function resetCheckout()
    {
        $this->amountTendered = 0;
    }

    public function closeModal()
    {
        $this->showCheckoutModal = false;
    }

    public function render()
    {
        $cartItems = session('cart', []);

        return view('livewire.pos.checkout-component', [
            'cartItems' => $cartItems
        ]);
    }
}
