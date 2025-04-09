<?php

namespace App\Livewire\Pos;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryId = '';


    public $showBoxModal = false;
    public $selectedProduct;
    public $selectedBoxItems = [];
    public $boxItemLimit;
    public $boxTotal = 0;
    public $selectedBoxProduct;

    protected $listeners = ['categorySelected', 'searchUpdated'];

    public function categorySelected($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->resetPage(); // Resets pagination to page 1
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if ($product) {
            if ($product->is_box) {
                $this->selectedProduct = $product;

                preg_match('/\d+/', $product->name, $matches);
                $this->boxItemLimit = !empty($matches) ? (int)$matches[0] : 6;

                $this->showBoxModal = true;
            } else {
                $this->dispatch('addToCart', [
                    'id' => $product->id,
                    'name' => $product->name,
                    'selling_price' => $product->selling_price,
                    'image_url' => $product->image_url,
                ]);
            }
        }
    }

    public function toggleBoxItem($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return;
        }

        // Check if product is already selected
        $key = $this->findBoxItemKey($productId);

        $currentTotalQuantity = array_sum(array_column($this->selectedBoxItems, 'quantity'));

        if ($key !== false) {
            // If product is already selected, increase quantity if it does not exceed the limit
            if ($currentTotalQuantity < $this->boxItemLimit) {
                $this->selectedBoxItems[$key]['quantity']++;
            } else {
                session()->flash('warningLimit', 'You have exceed the limit, cannot add more!');
                return; // Prevent adding more than the limit
            }
        } else {
            // If product is not selected yet and we haven't reached the limit, add it with quantity 1
            if ($this->getTotalSelectedQuantity() < $this->boxItemLimit) {
                $this->selectedBoxItems[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'selling_price' => $product->selling_price,
                    'image_url' => $product->image_url,
                    'quantity' => 1, // New quantity property
                ];
            }
        }

        $this->calculateBoxTotal();
    }

    public function decreaseBoxItemQuantity($productId)
    {
        $key = $this->findBoxItemKey($productId);

        if ($key !== false) {
            if ($this->selectedBoxItems[$key]['quantity'] > 1) {
                $this->selectedBoxItems[$key]['quantity']--;
            } else {
                array_splice($this->selectedBoxItems, $key, 1); // Remove if quantity reaches 0
            }
        }

        $this->calculateBoxTotal();
    }

    private function findBoxItemKey($productId)
    {
        foreach ($this->selectedBoxItems as $key => $item) {
            if ($item['id'] == $productId) {
                return $key;
            }
        }
        return false;
    }

    public function isBoxItemSelected($productId)
    {
        return $this->findBoxItemKey($productId) !== false;
    }

    public function calculateBoxTotal()
    {
        $this->boxTotal = array_reduce($this->selectedBoxItems, function ($total, $item) {
            return $total + ($item['selling_price'] * $item['quantity']);
        }, 0);
    }

    public function removeBoxItem($productId)
    {
        $key = $this->findBoxItemKey($productId);

        if ($key !== false) {
            if ($this->selectedBoxItems[$key]['quantity'] > 1) {
                array_splice($this->selectedBoxItems, $key, 1);
            } else {
                array_splice($this->selectedBoxItems, $key, 1);
            }
        }

        $this->calculateBoxTotal();
    }

    public function addBoxToCart()
    {
        if ($this->getTotalSelectedQuantity() !== $this->boxItemLimit) {
            return;
        }

        $this->dispatch('addToCart', [
            'id' => $this->selectedProduct->id,
            'name' => $this->selectedProduct->name,
            'selling_price' => $this->selectedProduct->selling_price,
            'image_url' => $this->selectedProduct->image_url,
            'boxItems' => $this->selectedBoxItems,
        ]);

        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showBoxModal = false;
        $this->selectedProduct = null;
        $this->selectedBoxItems = [];
        $this->boxTotal = 0;
        $this->selectedBoxProduct = 0;
    }

    private function getTotalSelectedQuantity()
    {
        return array_sum(array_column($this->selectedBoxItems, 'quantity'));
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->categoryId, function ($query) {
                return $query->where('category_id', $this->categoryId);
            })
            ->paginate(15);

        $availableBoxProducts = collect([]);

        $boxProduct = Product::where('is_box', false)->get();

        if ($this->showBoxModal) {
            $query = Product::where('is_box', false)
                ->where('id', $this->selectedBoxProduct);

            $availableBoxProducts = $query->get();
        }

        return view('livewire.pos.products', [
            'products' => $products,
            'availableBoxProducts' => $availableBoxProducts,
            'boxProducts' => $boxProduct
        ]);
    }
}
