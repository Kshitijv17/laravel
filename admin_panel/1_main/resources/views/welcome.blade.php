<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e0eafc, #cfdef3);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quote-box {
            max-width: 600px;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="quote-box">
        @php
            $quotes = [
                "Success is not final, failure is not fatal: It is the courage to continue that counts.",
                "Believe you can and you're halfway there.",
                "The only way to do great work is to love what you do.",
                "Don't watch the clock; do what it does. Keep going.",
                "Your time is limited, so don’t waste it living someone else’s life."
            ];
            $quote = $quotes[array_rand($quotes)];
        @endphp

        <h4 class="mb-4">🌟 {{ $quote }}</h4>

        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('user.login') }}" class="btn btn-primary">Customer Login</a>
            <a href="{{ route('admin.login') }}" class="btn btn-dark">Admin Login</a>
            <a href="{{ route('guest.login') }}" class="btn btn-outline-secondary">Just Looking</a>
        </div>
    </div>
</body>
</html>
