<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'product_variant_id', 'product_id', 'quantity', 'price', 'total'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(Product_variant::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
