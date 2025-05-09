<?php

namespace App\Livewire\Pos;

use App\Models\Discount;
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

    // Discounts
    public ?int $selectedDiscountId = null;
    public float $discountAmount = 0;
    public float $totalAfterDiscount = 0;

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

        $tendered = floatval($this->amountTendered);

        if ($this->discountAmount == 0) {
            if ($tendered < $this->total) {
                session()->flash('error', 'You give an insufficient cash.');
                return;
            }
        } else {
            if ($tendered < $this->totalAfterDiscount) {
                session()->flash('error', 'You give an insufficient cash.');
                return;
            }
        }
    }

    public function calculateChange()
    {
        $tendered = floatval($this->amountTendered);

        if ($this->discountAmount == 0) {
            $total = $this->total;
        } else {
            $total = $this->totalAfterDiscount;
        }
        $this->checkAmountTendered = $tendered;

        $this->change = max(0, $tendered - $total);
    }

    public function calculateTotal()
    {
        $cartItems = session('cart', []);

        $subtotal = collect($cartItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return $subtotal;
    }

    public function updatedSelectedDiscountId($id)
    {
        $discount = Discount::find($id);
        $this->dispatch('updateAppliedDiscount', $id);

        if (!$discount) {
            $this->discountAmount = 0;
            $this->calculateChange();
            return;
        }

        $subtotal = $this->calculateTotal();

        if ($discount->type === 'amount') {
            $this->discountAmount = $discount->value;
        } elseif ($discount->type === 'percent') {
            $this->discountAmount = ($discount->value / 100) * $subtotal;
        }

        $this->totalAfterDiscount = $subtotal - $this->discountAmount;
        $this->calculateChange();
    }

    public function completeCheckout()
    {
        if ($this->amountTendered < $this->total) {
            $this->dispatch('insuf-cash', 'You give an insufficient cash!.');
        } else {
            $this->dispatch('updateAmountTendered', $this->amountTendered);
            $this->selectedDiscountId = 0;
            $this->discountAmount = 0;
            $this->showCheckoutModal = false;
        }
    }

    public function confirmCheckout()
    {
        $cartItems = session('cart', []);

        if (!empty($cartItems)) {
            $this->showCheckoutModal = true;
            $this->total = $this->calculateTotal();
            $this->amountTendered = $this->total;
            $this->checkAmountTendered = $this->amountTendered;
        } else {
            $this->dispatch('cart-empty', 'Your cart is empty. Please add items before checkout.');
        }
    }

    public function proceedCheckout()
    {
        $cartItems = session('cart', []);

        try {
            DB::beginTransaction();

            $date = now()->format('Ymd');
            $countToday = Sale::whereDate('created_at', now()->toDateString())->count() + 1;
            $saleId = $date . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'sales_id' => $saleId,
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
                    'cost_price' => $item['cost_price'],
                    'total' => $item['price'] * $item['quantity'],
                    'total_cost_price' => $item['cost_price'] * $item['quantity'],
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

        $this->checkAmountTendered = 0;
        $this->selectedDiscountId = 0;
        $this->discountAmount = 0;
        $this->change = 0;
        $this->dispatch('updateAppliedDiscount', 0);
    }

    public function printReceipt()
    {
        $sale = Sale::find($this->saleId);

        if ($sale) {
            return redirect()->route('receipt', $this->saleId);
        }
    }

    public function render()
    {
        $cartItems = session('cart', []);

        return view('livewire.pos.checkout-component', [
            'cartItems' => $cartItems
        ]);
    }
}
