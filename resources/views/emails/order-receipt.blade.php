<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Receipt</title>
</head>
<body style="font-family: sans-serif; color: #0f172a; max-width: 480px; margin: 0 auto; padding: 24px;">
    <h1 style="font-size: 18px;">Thanks for your order, {{ $order->customer_name }}!</h1>
    <p style="font-size: 14px; color: #475569;">
        Order <strong>{{ $order->order_number }}</strong> is confirmed.
    </p>

    <table style="width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 1px solid #e2e8f0; text-align: left;">
                <th style="padding: 8px 0;">Item</th>
                <th style="padding: 8px 0; text-align: right;">Qty</th>
                <th style="padding: 8px 0; text-align: right;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->products as $product)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0;">{{ $product->name }}</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $product->pivot->quantity }}</td>
                    <td style="padding: 8px 0; text-align: right;">
                        ${{ number_format($product->pivot->price_at_time_cents * $product->pivot->quantity / 100, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align: right; font-weight: 600; margin-top: 12px; font-size: 14px;">
        Total: ${{ number_format($order->total_price_cents / 100, 2) }}
    </p>

    <p style="font-size: 13px; color: #94a3b8; margin-top: 24px;">
        Shipping to: {{ $order->customer_address }}
    </p>
</body>
</html>
