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
use App\Http\Controllers\Web\PaymentController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/search/suggest', [HomeController::class, 'suggest'])->name('search.suggest');
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

// Payments (Razorpay)
Route::middleware('auth')->group(function () {
    Route::get('/payment/{order}/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::post('/payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');
});
// Webhook does not require auth; protect with secret inside controller
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

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
        Route::get('/banners/{banner}', [AdminController::class, 'showBanner'])->name('banners.show');
        Route::get('/banners/{banner}/edit', [AdminController::class, 'editBanner'])->name('banners.edit');
        Route::put('/banners/{banner}', [AdminController::class, 'updateBanner'])->name('banners.update');
        Route::post('/banners/{banner}/toggle-status', [AdminController::class, 'toggleBannerStatus'])->name('banners.toggle-status');
        Route::delete('/banners/{banner}', [AdminController::class, 'destroyBanner'])->name('banners.destroy');
        
        // Coupon Management
        Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons.index');
        Route::get('/coupons/create', [AdminController::class, 'createCoupon'])->name('coupons.create');
        Route::post('/coupons', [AdminController::class, 'storeCoupon'])->name('coupons.store');
        Route::get('/coupons/{coupon}', [AdminController::class, 'showCoupon'])->name('coupons.show');
        Route::get('/coupons/{coupon}/edit', [AdminController::class, 'editCoupon'])->name('coupons.edit');
        Route::put('/coupons/{coupon}', [AdminController::class, 'updateCoupon'])->name('coupons.update');
        Route::post('/coupons/{coupon}/toggle-status', [AdminController::class, 'toggleCouponStatus'])->name('coupons.toggle-status');
        Route::delete('/coupons/{coupon}', [AdminController::class, 'deleteCoupon'])->name('coupons.destroy');
        
        // Support Management
        Route::get('/support', [AdminController::class, 'supportTickets'])->name('support.index');
        Route::post('/support', [AdminController::class, 'createSupportTicket'])->name('support.store');
        Route::get('/support/{ticket}', [AdminController::class, 'showSupportTicket'])->name('support.show');
        Route::put('/support/{ticket}', [AdminController::class, 'updateSupportTicket'])->name('support.update');
        Route::post('/support/{ticket}/status', [AdminController::class, 'updateSupportTicketStatus'])->name('support.status');
        Route::delete('/support/{ticket}', [AdminController::class, 'deleteSupportTicket'])->name('support.destroy');
        
        // Newsletter Management
        Route::get('/newsletter', [AdminController::class, 'newsletterSubscribers'])->name('newsletter.index');
        Route::post('/newsletter/subscribers', [AdminController::class, 'storeNewsletterSubscriber'])->name('newsletter.store');
        // Match JS paths and implicit model binding (parameter name must be 'subscriber')
        Route::put('/newsletter/{subscriber}', [AdminController::class, 'updateNewsletterSubscriber'])->name('newsletter.update');
        Route::delete('/newsletter/{subscriber}', [AdminController::class, 'deleteNewsletterSubscriber'])->name('newsletter.destroy');
        // Optional toggle alias -> uses same update method
        Route::patch('/newsletter/{subscriber}/status', [AdminController::class, 'updateNewsletterSubscriber'])->name('newsletter.toggle');
        Route::post('/newsletter/send', [AdminController::class, 'sendNewsletter'])->name('newsletter.send');
        Route::get('/newsletter/export', [AdminController::class, 'exportNewsletterSubscribers'])->name('newsletter.export');
        
        // Analytics
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        
        // Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings/{group}', [AdminController::class, 'saveSettings'])->name('settings.save');
        Route::post('/cache/clear', [AdminController::class, 'clearCache'])->name('cache.clear');
        Route::post('/database/optimize', [AdminController::class, 'optimizeDatabase'])->name('database.optimize');
    });
});
