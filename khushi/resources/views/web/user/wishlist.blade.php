@extends('layouts.app')

@section('title', 'My Wishlist')

@push('styles')
<style>
 .modern-container {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
     padding: 1.25rem 0 2rem;
 }
 .profile-card {
     background: rgba(255, 255, 255, 0.95);
     backdrop-filter: blur(20px);
     border-radius: 24px;
     border: none;
     box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
     overflow: hidden;
     max-width: 900px;
     margin: 0 auto;
 }
 .profile-header {
     background: linear-gradient(135deg, #4285f4 0%, #34a853 100%);
     padding: 1.5rem 2rem;
     text-align: center;
     position: relative;
     color: #fff;
 }
 .profile-name { font-size: 1.4rem; font-weight: 700; margin: 0; }
 .profile-email { opacity: .85; margin: .25rem 0 0; }
 .profile-body { padding: 1.25rem 1.25rem 1.5rem; }
 .wishlist-empty { text-align:center; padding: 3rem 1rem; }
 .wishlist-empty i { opacity: .35; }
 .item-row { display:flex; gap:1rem; align-items:center; }
 .item-img { width:80px; height:80px; border-radius:12px; object-fit:cover; flex:0 0 auto; }
 .item-title { font-weight:600; margin:0 0 .25rem; }
 .item-meta { color:#6b7280; font-size:.9rem; }
 .item-actions { display:flex; flex-direction:column; gap:.5rem; }
 @media (max-width: 640px){ .item-row { flex-direction:column; align-items:flex-start; } .item-actions{ width:100%; flex-direction:row; } }
</style>
@endpush

@section('content')
<div class="modern-container">
    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <h2 class="profile-name">My Wishlist</h2>
                <p class="profile-email">Items you've saved for later</p>
            </div>
            <div class="profile-body">
                @if($wishlistItems->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="fw-semibold">{{ $wishlistItems->count() }} {{ Str::plural('Item', $wishlistItems->count()) }}</div>
                        <button onclick="clearWishlist()" class="btn btn-sm btn-outline-danger">Clear All</button>
                    </div>

                    <div class="list-group">
                        @foreach($wishlistItems as $item)
                            <div class="list-group-item wishlist-item py-3" data-product-id="{{ $item->product->id }}">
                                <div class="item-row">
                                    <img src="{{ $item->product->primary_image }}" alt="{{ $item->product->name }}" class="item-img">
                                    <div class="flex-grow-1">
                                        <h3 class="item-title">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none">{{ $item->product->name }}</a>
                                        </h3>
                                        <div class="item-meta mb-2">{{ Str::limit($item->product->description, 100) }}</div>
                                        <div class="d-flex align-items-center gap-3">
                                            <div>
                                                @if($item->product->sale_price && $item->product->sale_price < $item->product->price)
                                                    <span class="fw-bold text-danger">${{ number_format($item->product->sale_price, 2) }}</span>
                                                    <span class="text-muted text-decoration-line-through">${{ number_format($item->product->price, 2) }}</span>
                                                @else
                                                    <span class="fw-bold">${{ number_format($item->product->price, 2) }}</span>
                                                @endif
                                            </div>
                                            @if($item->product->stock_quantity > 0)
                                                <span class="badge bg-success">In Stock</span>
                                            @else
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="item-actions">
                                        @if($item->product->stock_quantity > 0)
                                            <button onclick="addToCart({{ $item->product->id }})" class="btn btn-primary btn-sm">Add to Cart</button>
                                        @endif
                                        <button onclick="removeFromWishlist({{ $item->product->id }})" class="btn btn-outline-danger btn-sm">Remove</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">Total: {{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }}</div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('home') }}" class="btn btn-light btn-sm">Continue Shopping</a>
                            @if($wishlistItems->where('product.stock_quantity', '>', 0)->count() > 0)
                                <button onclick="addAllToCart()" class="btn btn-primary btn-sm">Add All to Cart</button>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="wishlist-empty">
                        <i class="fas fa-heart fa-3x mb-3"></i>
                        <h3 class="fw-semibold mb-2">Your wishlist is empty</h3>
                        <p class="text-muted mb-3">Start adding items to your wishlist by clicking the heart icon on products you love.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-shopping-bag me-2"></i>Start Shopping</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Wishlist Actions -->
<script>
function addToCart(productId) {
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Product added to cart!', 'success');
            // Update cart count if you have one
            updateCartCount();
        } else {
            showNotification(data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function removeFromWishlist(productId) {
    if (!confirm('Are you sure you want to remove this item from your wishlist?')) {
        return;
    }

    fetch(`/products/${productId}/wishlist/remove`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the item from the page
            document.querySelector(`[data-product-id="${productId}"]`).remove();
            showNotification('Item removed from wishlist', 'success');
            
            // Reload page if no items left
            const remainingItems = document.querySelectorAll('.wishlist-item');
            if (remainingItems.length === 0) {
                location.reload();
            }
        } else {
            showNotification(data.message || 'Failed to remove from wishlist', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function clearWishlist() {
    if (!confirm('Are you sure you want to clear your entire wishlist?')) {
        return;
    }

    // You would implement this endpoint
    fetch('/user/wishlist/clear', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message || 'Failed to clear wishlist', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function addAllToCart() {
    const inStockItems = document.querySelectorAll('.wishlist-item');
    let addedCount = 0;
    
    inStockItems.forEach(item => {
        const productId = item.dataset.productId;
        // Add logic to check if item is in stock before adding
        addToCart(productId);
        addedCount++;
    });
    
    if (addedCount > 0) {
        showNotification(`${addedCount} items added to cart!`, 'success');
    }
}

function showNotification(message, type) {
    // Simple notification system - you can enhance this
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function updateCartCount() {
    // Update cart count in header if you have one
    // This would typically fetch the current cart count
}
</script>
@endsection
