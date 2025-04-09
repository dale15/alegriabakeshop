<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use Livewire\Component;

class CategoryFilter extends Component
{
    public $selectedCategory = '';
    public $open = false;

    public function getSelectedCategoryNameProperty()
    {
        return $this->selectedCategory ? Category::find($this->selectedCategory)?->name : 'All Categories';
    }

    public function selectCategory($categoryId = '')
    {
        $this->selectedCategory = $categoryId;
        $this->dispatch('categorySelected', $categoryId);
        $this->open = false;
    }

    public function render()
    {
        $categories = Category::all();

        return view('livewire.pos.category-filter', [
            'categories' => $categories
        ]);
    }
}
