<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
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
            padding: 40px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .feature-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .feature-icon {
            font-size: 30px;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
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
        <h1>🎉 Welcome to {{ config('app.name') }}!</h1>
        <p>Your journey to amazing products starts here</p>
    </div>

    <div class="content">
        <p>Dear {{ $user->name }},</p>
        
        <p>Welcome to {{ config('app.name') }}! We're thrilled to have you join our community of savvy shoppers.</p>

        <p>Your account has been successfully created, and you now have access to:</p>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">🛍️</div>
                <h4>Exclusive Deals</h4>
                <p>Access to member-only discounts and special offers</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h4>Personalized Recommendations</h4>
                <p>AI-powered product suggestions just for you</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💝</div>
                <h4>Wishlist & Favorites</h4>
                <p>Save products you love for later</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h4>Order Tracking</h4>
                <p>Real-time updates on your purchases</p>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('products.index') }}" class="btn">Start Shopping Now</a>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h4>🔐 Account Security</h4>
            <p>We recommend enabling Two-Factor Authentication to keep your account secure.</p>
            <a href="{{ route('two-factor.index') }}" style="color: #667eea;">Enable 2FA →</a>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h4>📱 Get Our Mobile App</h4>
            <p>Install our Progressive Web App for the best shopping experience on mobile!</p>
            <p style="font-size: 14px; color: #666;">Look for the "Install App" prompt in your browser</p>
        </div>

        <p>If you have any questions, our customer support team is here to help. Just reply to this email or visit our help center.</p>

        <p>Happy shopping!</p>
        <p>The {{ config('app.name') }} Team</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>You received this email because you created an account on our website.</p>
        <p><a href="{{ route('unsubscribe') }}" style="color: #666;">Unsubscribe</a> | <a href="{{ route('privacy') }}" style="color: #666;">Privacy Policy</a></p>
    </div>
</body>
</html>
