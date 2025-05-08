<?php

namespace App\Livewire\Pos;

use App\Models\Sale;
use Livewire\Component;

class MyReceipt extends Component
{
    public $sale;

    public function mount($saleId)
    {
        $this->sale = Sale::with('saleItems.product')->findOrFail($saleId);
    }

    public function render()
    {
        return view('livewire.pos.my-receipt');
    }
}
