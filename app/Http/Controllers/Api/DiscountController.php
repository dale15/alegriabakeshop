<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Discount::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Discount $discount)
    {
        return response()->json([
            'status' => true,
            'message' => 'Discount Found',
            'data' => $discount
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => "required|string|max:255",
            'value' => "required",
            'is_active' => "required",
        ]);

        $discount = Discount::findOrFail($id);

        $discount->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Discounts Updated Successfully',
            'data' => $discount,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
