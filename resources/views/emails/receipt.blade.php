<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            width: 300px;
            /* Approx. 80mm */
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0 auto;
            padding: 10px;
        }

        h2,
        h3 {
            text-align: center;
            margin: 0 0 10px 0;
        }

        p,
        li {
            margin: 2px 0;
        }

        ul {
            padding-left: 0;
            list-style: none;
        }

        .item-line {
            display: flex;
            justify-content: space-between;
        }

        .total,
        .discount {
            font-weight: bold;
            margin-top: 8px;
        }

        .thanks {
            text-align: center;
            margin-top: 10px;
        }

        hr {
            border: 0;
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
    </style>
</head>

<body>
    <h2>Receipt</h2>

    <p><strong>Sales ID:</strong> {{ $data['sales_id'] }}</p>
    <p><strong>Date:</strong> {{ $data['date'] }}</p>

    <hr>

    <h3>Items</h3>
    <ul>
        @foreach ($data['items'] as $item)
            <li class="item-line">
                <span>{{ $item['quantity'] }} x {{ $item['product_name'] }}</span>
                <span>₱{{ number_format($item['price'], 2) }}</span>
            </li>
        @endforeach
    </ul>

    <hr>

    <p class="discount">Discount: ₱{{ number_format($data['discount'], 2) }}</p>
    <p class="total">Total: ₱{{ number_format($data['total_amount'], 2) }}</p>

    <hr>

    <p class="thanks">Thank you for your purchase!</p>
</body>

</html>