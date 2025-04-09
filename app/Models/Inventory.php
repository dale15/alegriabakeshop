<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['ingredient_id', 'quantity', 'reorder_level', 'cost_per_unit'];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
