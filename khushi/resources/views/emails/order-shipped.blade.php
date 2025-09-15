<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Shipped</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .tracking-info {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .tracking-number {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            background: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .order-summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚚 Your Order Has Shipped!</h1>
        <p>Your package is on its way</p>
    </div>

    <div class="content">
        <p>Dear {{ $order->user->name }},</p>
        
        <p>Great news! Your order #{{ $order->order_number }} has been shipped and is on its way to you.</p>

        <div class="tracking-info">
            <h3>Tracking Information</h3>
            <p><strong>Carrier:</strong> {{ $order->shipping_carrier ?? 'Standard Shipping' }}</p>
            <div class="tracking-number">{{ $order->tracking_number ?? 'TRK' . strtoupper(uniqid()) }}</div>
            <p><strong>Estimated Delivery:</strong> {{ now()->addDays(3)->format('F j, Y') }}</p>
            
            @if($order->tracking_number)
                <a href="{{ $order->tracking_url ?? '#' }}" class="btn">Track Your Package</a>
            @endif
        </div>

        <div class="order-summary">
            <h4>Order Summary:</h4>
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y') }}</p>
            <p><strong>Total Items:</strong> {{ $order->orderItems->sum('quantity') }}</p>
            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
        </div>

        <p>Your package will be delivered to:</p>
        <div style="background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0;">
            {{ $order->shipping_address['name'] ?? $order->user->name }}<br>
            {{ $order->shipping_address['address_line_1'] }}<br>
            @if(!empty($order->shipping_address['address_line_2']))
                {{ $order->shipping_address['address_line_2'] }}<br>
            @endif
            {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['postal_code'] }}<br>
            {{ $order->shipping_address['country'] }}
        </div>

        <p>If you have any questions about your shipment, please don't hesitate to contact our customer service team.</p>

        <p>Thank you for your business!</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
