<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total {
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 10px;
            text-align: right;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
        }
        @media print {
            body { margin: 0; padding: 0; width: 100%; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>REHMAN ENGINEERING</h2>
        <p>Supply Chain Receipt</p>
        <p>Date: {{ $sale->sale_date }}</p>
        <p>Receipt #: {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="items">
        <div class="item">
            <span>Product:</span>
            <span>{{ $sale->product->name }}</span>
        </div>
        <div class="item">
            <span>Qty:</span>
            <span>{{ $sale->quantity }}</span>
        </div>
        <div class="item">
            <span>Price:</span>
            <span>Rs. {{ number_format($sale->product->price) }}</span>
        </div>
    </div>

    <div class="total">
        TOTAL: Rs. {{ number_format($sale->total_amount) }}
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <button onclick="window.print()" style="margin-top: 20px; padding: 5px 10px; cursor: pointer;">Print Receipt</button>
    </div>
</body>
</html>
