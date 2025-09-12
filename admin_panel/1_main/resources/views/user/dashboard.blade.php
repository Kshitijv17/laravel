<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Customer Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
        .banner {
            background: linear-gradient(to right, #f9d423, #ff4e50);
            color: white;
            padding: 60px 30px;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 40px;
        }
        .product-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            transition: 0.3s;
        }
        .product-card:hover {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">

      @include('components.navbar')

    <div class="container py-5 mt-5">

        <!-- Banner -->
        <div class="banner">
            <h1>Welcome, {{ Auth::user()?->name ?? 'Guest' }} 👋</h1>
            <p class="lead">Discover amazing products curated just for you!</p>
        </div>

        <!-- Product Grid -->
        <h3 class="mb-4">Featured Products</h3>
        <div class="row g-4">
            @foreach ([
                ['name' => 'Wireless Headphones', 'price' => '₹2,499', 'img' => 'https://via.placeholder.com/300x200?text=Headphones'],
                ['name' => 'Smart Watch', 'price' => '₹3,999', 'img' => 'https://via.placeholder.com/300x200?text=Smart+Watch'],
                ['name' => 'Sneakers', 'price' => '₹1,799', 'img' => 'https://via.placeholder.com/300x200?text=Sneakers'],
                ['name' => 'Backpack', 'price' => '₹999', 'img' => 'https://via.placeholder.com/300x200?text=Backpack'],
                ['name' => 'Bluetooth Speaker', 'price' => '₹1,299', 'img' => 'https://via.placeholder.com/300x200?text=Speaker'],
                ['name' => 'Sunglasses', 'price' => '₹699', 'img' => 'https://via.placeholder.com/300x200?text=Sunglasses'],
            ] as $product)
                <div class="col-md-4">
                    <div class="product-card bg-white">
                        <img src="{{ $product['img'] }}" class="img-fluid mb-3 rounded" alt="{{ $product['name'] }}">
                        <h5>{{ $product['name'] }}</h5>
                        <p class="text-muted">{{ $product['price'] }}</p>
                        <button class="btn btn-sm btn-primary">Add to Cart</button>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

</body>
</html>
