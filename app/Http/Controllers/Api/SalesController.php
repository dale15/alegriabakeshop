<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Sale::with('saleItems.product')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $date = now()->format('Ymd');
        $countToday = Sale::whereDate('created_at', now()->toDateString())->count() + 1;
        $saleId = $date . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        $data = $request->validate([
            "total_amount" => "required|numeric|min:0",
            "amount_tendered" => "required|numeric|min:0",
            "change" => "required|numeric|min:0",
            "status" => "required|string",
            "cartItem" => "required|array",
            "total_discount" => "required|numeric|min:0",
        ]);

        $data['sales_id'] = $saleId;

        $sale = Sale::create([
            'sales_id' => $data['sales_id'],
            'total_amount' => $data['total_amount'],
            'payment_method' => 'cash',
            'amount_tendered' => $data['amount_tendered'],
            'change' => $data['change'],
            'total_discount' => $data['total_discount'],
            'status' => 'completed'
        ]);

        foreach ($data['cartItem'] as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'cost_price' => $item['cost_price'],
                'total' => $item['price'] * $item['quantity'],
                'total_cost_price' => $item['cost_price'] * $item['quantity'],
            ]);
        }

        return response()->json([
            'message' => 'Sale created successfully',
            'sales_id' => $sale->sales_id,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
