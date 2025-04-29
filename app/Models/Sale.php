<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['total_amount', 'payment_method', 'amount_tendered', 'change', 'note', 'status', 'sales_id'];

    protected static function booted()
    {
        static::creating(function ($sale) {
            $date = now()->format('Ymd');
            $countToday = self::whereDate('created_at', now())->count() + 1;
            $sale->sales_id = 'ALGR-' . $date . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);
        });
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
