<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .order-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .item-info {
            flex: 1;
        }
        .item-name {
            font-weight: bold;
            color: #333;
        }
        .item-details {
            color: #666;
            font-size: 14px;
        }
        .item-price {
            font-weight: bold;
            color: #28a745;
        }
        .total-section {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
        .total-final {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            border-top: 2px solid #28a745;
            padding-top: 10px;
        }
        .shipping-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
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
        <h1>Order Confirmation</h1>
        <p>Thank you for your purchase!</p>
    </div>

    <div class="content">
        <p>Dear {{ $order->user->name }},</p>
        
        <p>We're excited to confirm that we've received your order and it's being processed. Here are the details:</p>

        <div class="order-details">
            <h3>Order #{{ $order->order_number }}</h3>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y') }}</p>
            <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
            <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
            
            <h4>Items Ordered:</h4>
            @foreach($order->orderItems as $item)
                <div class="order-item">
                    <div class="item-info">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-details">
                            SKU: {{ $item->product->sku }} | Qty: {{ $item->quantity }}
                        </div>
                    </div>
                    <div class="item-price">${{ number_format($item->price * $item->quantity, 2) }}</div>
                </div>
            @endforeach
        </div>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->tax_amount > 0)
                <div class="total-row">
                    <span>Tax:</span>
                    <span>${{ number_format($order->tax_amount, 2) }}</span>
                </div>
            @endif
            @if($order->shipping_amount > 0)
                <div class="total-row">
                    <span>Shipping:</span>
                    <span>${{ number_format($order->shipping_amount, 2) }}</span>
                </div>
            @endif
            @if($order->discount_amount > 0)
                <div class="total-row">
                    <span>Discount:</span>
                    <span>-${{ number_format($order->discount_amount, 2) }}</span>
                </div>
            @endif
            <div class="total-row total-final">
                <span>Total:</span>
                <span>${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="shipping-info">
            <h4>Shipping Address:</h4>
            <p>
                {{ $order->shipping_address['name'] ?? $order->user->name }}<br>
                {{ $order->shipping_address['address_line_1'] }}<br>
                @if(!empty($order->shipping_address['address_line_2']))
                    {{ $order->shipping_address['address_line_2'] }}<br>
                @endif
                {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['postal_code'] }}<br>
                {{ $order->shipping_address['country'] }}
            </p>
        </div>

        <p>We'll send you another email with tracking information once your order ships.</p>

        <a href="{{ route('orders.show', $order->id) }}" class="btn">Track Your Order</a>

        <p>If you have any questions about your order, please don't hesitate to contact our customer service team.</p>

        <p>Thank you for shopping with us!</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>You received this email because you placed an order on our website.</p>
    </div>
</body>
</html>
