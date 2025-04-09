<?php

namespace App\Livewire\Pos;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('POS')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.pos.dashboard');
    }
}
