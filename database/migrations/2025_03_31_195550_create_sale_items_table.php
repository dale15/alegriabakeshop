<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained(); // -- Link to the Sale
            $table->foreignId('product_variant_id')->nullable()->constrained(); // -- Link to the variant
            $table->foreignId('product_id')->constrained(); // -- Link to the Flavor (product)
            $table->integer('quantity'); // -- Quantity of the selected flavor (product)
            $table->decimal('cost_price', 10, 2); // -- Cost Price of goods
            $table->decimal('price', 10, 2); // -- Price at the time of sale
            $table->decimal('total', 10, 2); // -- price * quantity
            $table->decimal('total_cost_price', 10, 2); // -- cost price * quantity
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
