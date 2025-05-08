<style>
    @media print {
        body {
            width: 58mm;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .receipt {
            width: 100%;
            padding: 8px 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        h2 {
            font-size: 14px;
            margin-bottom: 4px;
        }

        p {
            margin: 0 0 4px 0;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 2px 0;
        }

        .totals {
            margin-top: 6px;
            font-weight: bold;
        }

        button {
            display: none;
        }
    }
</style>

<div class="receipt">
    <h2 class="text-center">Alegria Bakeshop</h2>
    <p class="text-center">Brgy. Coffee Lane, Brewtown</p>
    <p class="text-center">TIN: 123-456-789</p>

    <hr>

    <p>Receipt #: {{ $sale->sales_id }}</p>
    <p>Date: {{ $sale->created_at->format('Y-m-d H:i') }}</p>

    <hr>

    <table>
        <thead>
            <tr>
                <th class="text-left">Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $item)
                <tr>
                    <td class="text-left">{{ Str::limit($item->product->name, 16) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">
                        {{ number_format($item->quantity * $item->price, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <div class="totals text-right">
        Total: ₱{{ number_format($sale->total_amount, 2) }}
    </div>

    <hr>

    <p class="text-center">Thank you!</p>

    <div class="bg-white rounded-lg shadow p-4 mt-4 app-buttons">
        <div class="grid grid-cols-2 gap-2">
            <!-- Your shortcut buttons remain unchanged -->
            <div class="text-center">
                <button onclick="window.print()"
                    class="bg-green-500 text-white py-2 px-4 mt-4 rounded hover:bg-blue-600 cursor-pointer">🖨️
                    Print</button>
            </div>

            <div class="text-center">
                <button class="bg-green-500 text-white py-2 px-4 mt-4 rounded hover:bg-blue-600 cursor-pointer">Send to
                    Email</button>
            </div>
        </div>
    </div>


</div>