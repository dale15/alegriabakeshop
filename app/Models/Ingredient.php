<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name', 'price', 'unit_of_measure', 'quantity_in_stock', 'description'];

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function costings()
    {
        return $this->hasMany(Product_ingredient::class);
    }
}
