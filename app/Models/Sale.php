<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['total_amount', 'payment_method', 'amount_tendered', 'change', 'note', 'status'];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
