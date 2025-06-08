<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendReceiptEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'sales_id' => 'required',
            'date' => 'required',
            'total_amount' => 'required|numeric',
            'discount' => 'required|numeric',
            'items' => 'required|array',
            'email' => 'required|email',
        ]);

        Mail::to($validated['email'])->send(new SendReceiptEmail($validated));

        return response()->json(['message' => 'Email sent successfully']);
    }
}
