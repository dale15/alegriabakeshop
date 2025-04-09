<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_ingredient extends Model
{
    protected $table = 'product_ingredients'; // Explicitly define the pivot table name
    protected $fillable = ['product_id', 'ingredient_id', 'quantity', 'cost_per_unit'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
