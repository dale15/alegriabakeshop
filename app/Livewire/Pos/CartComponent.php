<?php

namespace App\Livewire\Pos;

use App\Models\Discount;
use Livewire\Component;

class CartComponent extends Component
{
    public $cartItems = [];
    public $subtotal = 0;
    public $tax = 0;
    public $taxRate = 0.10; // 10% tax
    public $amountTendered = 0;
    public $total = 0;
    public $change = 0;

    // Discounts
    public ?int $selectedDiscountId = null;
    public float $discountAmount = 0;
    public float $totalAfterDiscount = 0;
    public string $discountType = '';
    public int $discountValue = 0;

    protected $listeners = [
        'addToCart',
        'removeFromCart',
        'clearCart',
        'updateCartItemQuantity',
        'updateAmountTendered' => 'setAmountTendered',
        'updateAppliedDiscount' => 'setDiscount',
    ];

    public function mount()
    {
        $this->cartItems = session('cart', []);
        $this->calculateTotals();
    }

    public function setAmountTendered($amount)
    {
        $this->amountTendered = $amount;
        $this->calculateTotals();
    }

    public function setDiscount($id)
    {
        $discount = Discount::find($id);

        if (!$discount) {
            $this->discountAmount = 0;
            return;
        }

        $this->discountType = $discount->type;
        $this->discountValue = $discount->value;

        $this->calculateTotals();
    }

    public function addToCart($product)
    {
        $productId = $product['id'];

        if (isset($this->cartItems[$productId])) {
            $this->cartItems[$productId]['quantity']++;
        } else {
            $this->cartItems[$productId] = [
                'id' => $productId,
                'name' => $product['name'],
                'price' => $product['selling_price'],
                'quantity' => 1,
                'image' => $product['image_url'] ?? null,
                'boxItems' => $product['boxItems'] ?? null,
            ];
        }

        $this->saveCart();
        $this->calculateTotals();
    }

    public function removeFromCart($productId)
    {
        if (isset($this->cartItems[$productId])) {
            unset($this->cartItems[$productId]);

            $this->amountTendered = 0;
            $this->change = 0;
            $this->discountAmount = 0;
            $this->discountValue = 0;
            $this->dispatch('resetCheckoutBtn');

            $this->saveCart();
            $this->calculateTotals();
        }
    }

    public function updateCartItemQuantity($productId, $quantity)
    {
        if (isset($this->cartItems[$productId])) {
            $this->cartItems[$productId]['quantity'] = max(1, $quantity);
            $this->saveCart();
            $this->calculateTotals();
        }
    }

    public function clearCart()
    {
        $this->cartItems = [];
        $this->amountTendered = 0;
        $this->change = 0;
        $this->discountAmount = 0;
        $this->discountValue = 0;
        $this->dispatch('resetCheckoutBtn');

        $this->saveCart();
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = collect($this->cartItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        if ($this->discountType === 'amount') {
            $this->discountAmount = $this->discountValue;
        } elseif ($this->discountType === 'percent') {
            $this->discountAmount = ($this->discountValue / 100) * $this->subtotal;
        }

        if ($this->discountAmount == 0) {
            $this->total = $this->subtotal;
            $this->change = $this->amountTendered - $this->total;
        } else {
            $this->totalAfterDiscount = $this->subtotal - $this->discountAmount;
            $this->change = $this->amountTendered - $this->totalAfterDiscount;
        }
    }

    public function saveCart()
    {
        session(['cart' => $this->cartItems]);
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.pos.cart-component');
    }
}
