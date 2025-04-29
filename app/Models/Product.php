<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'is_box' => 'boolean',
        'allowed_items_ids' => 'array',
    ];

    protected $fillable = ['name', 'category_id', 'cost_price', 'selling_price', 'image_url', 'is_box', 'sku', 'allowed_items_ids'];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productVariants()
    {
        return $this->hasMany(Product_variant::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function productIngredients()
    {
        return $this->hasMany(Product_ingredient::class);
    }

    public function getCostAttribute()
    {
        return $this->productIngredients->sum(function ($costing) {
            return $costing->quantity * $costing->ingredient->cost_per_unit;
        });
    }

    public function getAllowedItemsAttribute()
    {
        return Product::whereIn('id', $this->allowed_item_ids ?? [])->get();
    }

    // public static function boot()
    // {
    //     parent::boot();


    //     // Recalculate the product cost after saving the product
    //     static::saved(function ($product) {
    //         logger("afterSave go here");
    //         $product->updateTotalCost();
    //     });

    //     // Optionally, if you're updating ingredients frequently, you can use the `updated` event
    //     static::updated(function ($product) {
    //         logger("afterUpdate go here");
    //         $product->updateTotalCost();
    //     });
    // }

    // public function updateTotalCost()
    // {
    //     logger("Update Total Cost");
    //     $costPrice = $this->productIngredients->sum(function ($ingredient) {
    //         return $ingredient->quantity * $ingredient->cost_per_unit;
    //     });

    //     $this->cost_price = $costPrice;
    //     $this->save();
    // }

    //     public function updateCostPrice()
    //     {
    //         $costPrice = $this->productIngredients->sum(function ($ingredient) {
    //             return $ingredient->quantity * $ingredient->cost_per_unit;
    //         });

    //         $this->update(['cost_price'], $costPrice);
    //     }

    //     protected static function booted()
    //     {
    //         static::saved(function ($product) {
    //             $product->updateCostPrice();
    //         });
    //     }
}
