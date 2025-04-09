<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Livewire\WithPagination;

class ProductCostingCardWidget extends Widget
{
    use WithPagination;

    protected static string $view = 'filament.widgets.product-costing-card-widget';

    public function render(): View
    {
        $products = Product::query()
            ->with(['productIngredients.ingredient'])
            ->where('is_box', false)
            ->paginate(6);


        return view('filament.widgets.product-costing-card-widget', compact('products'));
    }
}
