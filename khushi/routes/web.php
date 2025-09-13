<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\AdminController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::post('/newsletter/subscribe', [HomeController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Product Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
    Route::post('/{product}/wishlist', [ProductController::class, 'addToWishlist'])->name('wishlist.add');
    Route::delete('/{product}/wishlist', [ProductController::class, 'removeFromWishlist'])->name('wishlist.remove');
    Route::post('/{product}/review', [ProductController::class, 'submitReview'])->name('review');
    Route::post('/{product}/review/submit', [ProductController::class, 'submitReview'])->name('review.submit');
    Route::get('/{product}/quick-view', [ProductController::class, 'quickView'])->name('quick-view');
});

// Category Routes
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
    Route::get('/navigation/ajax', [CategoryController::class, 'navigation'])->name('navigation');
});

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/{itemId}', [CartController::class, 'update'])->name('update');
    Route::delete('/{itemId}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
    Route::post('/coupon/apply', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::delete('/coupon/remove', [CartController::class, 'removeCoupon'])->name('coupon.remove');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// Checkout Routes (accessible to guests and authenticated users)
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout/process', [OrderController::class, 'processOrder'])->name('checkout.process');

// Order Routes
Route::prefix('orders')->name('orders.')->middleware('auth')->group(function () {
    Route::post('/process', [OrderController::class, 'processOrder'])->name('process');
    Route::get('/{orderId}/success', [OrderController::class, 'success'])->name('success');
    Route::get('/track', [OrderController::class, 'track'])->name('track');
    Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
});

// Order success route (accessible without auth for guest orders)
Route::get('/order/{orderId}/success', [OrderController::class, 'success'])->name('order.success');

// User Dashboard Routes
Route::prefix('user')->name('user.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [UserController::class, 'changePasswordForm'])->name('change-password');
    Route::put('/change-password', [UserController::class, 'changePassword'])->name('change-password.update');
    
    Route::get('/orders', [UserController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [UserController::class, 'orderDetails'])->name('order-details');
    
    Route::get('/addresses', [UserController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [UserController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/addresses/{address}', [UserController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{address}', [UserController::class, 'deleteAddress'])->name('addresses.delete');
    
    Route::get('/wishlist', [UserController::class, 'wishlist'])->name('wishlist');
    
    Route::get('/support', [UserController::class, 'supportTickets'])->name('support');
    Route::get('/support-tickets', [UserController::class, 'supportTickets'])->name('support-tickets');
    Route::get('/support-tickets/{id}', [UserController::class, 'supportTicketDetails'])->name('support-ticket-details');
    Route::post('/support-tickets', [UserController::class, 'createSupportTicket'])->name('support-tickets.create');
    Route::post('/support-tickets/{ticket}/reply', [UserController::class, 'replySupportTicket'])->name('support-tickets.reply');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showAdminLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'adminLogin']);
    });
    
    Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');
    
    // Admin Dashboard and Management
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
        Route::get('/change-password', [AdminController::class, 'changePasswordForm'])->name('change-password');
        Route::put('/change-password', [AdminController::class, 'changePassword'])->name('change-password.update');
        
        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.destroy');
        
        // Order Management
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
        Route::get('/orders/{id}/edit', [AdminController::class, 'editOrder'])->name('orders.edit');
        Route::put('/orders/{id}', [AdminController::class, 'updateOrder'])->name('orders.update');
        Route::put('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
        
        // Product Management
        Route::get('/products', [AdminController::class, 'products'])->name('products.index');
        Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
        Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
        Route::get('/products/{id}', [AdminController::class, 'showProduct'])->name('products.show');
        Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
        Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('products.destroy');
        
        // Category Management
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
        Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
        Route::get('/categories/{id}', [AdminController::class, 'showCategory'])->name('categories.show');
        Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
        Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');
        
        // Banner Management
        Route::get('/banners', [AdminController::class, 'banners'])->name('banners.index');
        Route::get('/banners/create', [AdminController::class, 'createBanner'])->name('banners.create');
        Route::post('/banners', [AdminController::class, 'storeBanner'])->name('banners.store');
        Route::get('/banners/{id}', [AdminController::class, 'showBanner'])->name('banners.show');
        Route::get('/banners/{id}/edit', [AdminController::class, 'editBanner'])->name('banners.edit');
        Route::put('/banners/{id}', [AdminController::class, 'updateBanner'])->name('banners.update');
        Route::delete('/banners/{id}', [AdminController::class, 'deleteBanner'])->name('banners.destroy');
        
        // Coupon Management
        Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons.index');
        Route::get('/coupons/create', [AdminController::class, 'createCoupon'])->name('coupons.create');
        Route::post('/coupons', [AdminController::class, 'storeCoupon'])->name('coupons.store');
        Route::get('/coupons/{id}', [AdminController::class, 'showCoupon'])->name('coupons.show');
        Route::get('/coupons/{id}/edit', [AdminController::class, 'editCoupon'])->name('coupons.edit');
        Route::put('/coupons/{id}', [AdminController::class, 'updateCoupon'])->name('coupons.update');
        Route::delete('/coupons/{id}', [AdminController::class, 'deleteCoupon'])->name('coupons.destroy');
        
        // Support Management
        Route::get('/support', [AdminController::class, 'supportTickets'])->name('support.index');
        Route::get('/support/{id}', [AdminController::class, 'showSupportTicket'])->name('support.show');
        Route::put('/support/{id}', [AdminController::class, 'updateSupportTicket'])->name('support.update');
        Route::delete('/support/{id}', [AdminController::class, 'deleteSupportTicket'])->name('support.destroy');
        Route::post('/support/{ticket}/reply', [AdminController::class, 'replySupportTicket'])->name('support.reply');
        
        // Newsletter Management
        Route::get('/newsletter', [AdminController::class, 'newsletterSubscribers'])->name('newsletter.index');
        Route::get('/newsletter/{id}', [AdminController::class, 'showNewsletterSubscriber'])->name('newsletter.show');
        Route::delete('/newsletter/{id}', [AdminController::class, 'deleteNewsletterSubscriber'])->name('newsletter.destroy');
        
        // Analytics and Reports
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });
});
