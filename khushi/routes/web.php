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
use App\Http\Controllers\Web\SocialAuthController;
use App\Http\Controllers\Web\LocaleController;
use App\Http\Controllers\Web\CurrencyController;
use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\Web\WhatsAppController;
use App\Http\Controllers\Web\TelegramController;
use App\Http\Controllers\Web\BlogPostController;
use App\Http\Controllers\Web\CommentController;
use App\Http\Controllers\Web\StripeController;
use App\Http\Controllers\Web\PayPalController;
use App\Http\Controllers\Web\PWAController;
use App\Http\Controllers\Web\TwoFactorController;
use App\Http\Controllers\Web\PerformanceController;
use App\Http\Controllers\Web\AnalyticsController;
use App\Http\Controllers\Web\SEOController;
use App\Http\Controllers\Web\ComparisonController;
use App\Http\Controllers\Web\RecommendationController;
use App\Http\Controllers\Admin\InventoryController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/enhanced', [HomeController::class, 'enhanced'])->name('enhanced');
Route::get('/simple', [HomeController::class, 'simple'])->name('simple');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/test-features', function () {
    return view('web.test-features');
})->name('test.features');
Route::get('/blog', [BlogPostController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogPostController::class, 'show'])->name('blog.show');
Route::get('/blog/category/{category}', [BlogPostController::class, 'category'])->name('blog.category');
Route::post('/blog/{post}/comments', [CommentController::class, 'store'])->name('blog.comments.store')->middleware('auth');
Route::post('/blog/{post}/comments/guest', [CommentController::class, 'storeGuest'])->name('blog.comments.guest');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/search/suggest', [HomeController::class, 'suggest'])->name('search.suggest');
Route::post('/newsletter/subscribe', [HomeController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');

// Public Product & Category Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{category}/products', [CategoryController::class, 'products'])->name('categories.products');

// Product Comparison Routes
Route::get('/compare', [ComparisonController::class, 'index'])->name('comparison.index');
Route::post('/compare/add', [ComparisonController::class, 'add'])->name('comparison.add');
Route::post('/compare/remove', [ComparisonController::class, 'remove'])->name('comparison.remove');
Route::delete('/compare/clear', [ComparisonController::class, 'clear'])->name('comparison.clear');

// Currency Switching
Route::post('/currency/{currency}', [CurrencyController::class, 'switch'])->name('currency.switch');

// Language Switching
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Chat Routes
Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::post('/start', [ChatController::class, 'quickStart'])->name('start');
    Route::post('/send', [ChatController::class, 'sendChatMessage'])->name('send');
    Route::post('/typing', [ChatController::class, 'typing'])->name('typing');
    Route::post('/upload', [ChatController::class, 'uploadFile'])->name('upload');
    Route::get('/history/{ticket}', [ChatController::class, 'getMessages'])->name('history');
    Route::get('/check/{ticket}', [ChatController::class, 'checkNewMessages'])->name('check');
});

// PWA Routes
Route::get('/manifest.json', [PWAController::class, 'manifest'])->name('pwa.manifest');
Route::get('/sw.js', [PWAController::class, 'serviceWorker'])->name('pwa.sw');
Route::get('/offline', [PWAController::class, 'offline'])->name('pwa.offline');
Route::get('/ping', [PWAController::class, 'ping'])->name('pwa.ping');
Route::get('/csrf-token', [PWAController::class, 'csrfToken'])->name('pwa.csrf');
Route::post('/pwa/install-prompt', [PWAController::class, 'installPrompt'])->name('pwa.install-prompt');

// Two-Factor Authentication Challenge (no auth required)
Route::get('/two-factor/challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');

// Analytics Tracking Routes (public)
Route::post('/analytics/track', [AnalyticsController::class, 'track'])->name('analytics.track');
Route::post('/analytics/batch-track', [AnalyticsController::class, 'batchTrack'])->name('analytics.batch-track');

// SEO Routes (public)
Route::get('/sitemap.xml', [SEOController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SEOController::class, 'robots'])->name('seo.robots');

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

// Social Authentication Routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/{provider}', [SocialAuthController::class, 'redirect'])->name('redirect');
    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback'])->name('callback');
    Route::post('/{provider}/link', [SocialAuthController::class, 'link'])->name('link')->middleware('auth');
    Route::delete('/{provider}/unlink', [SocialAuthController::class, 'unlink'])->name('unlink')->middleware('auth');
});

// Locale and Currency Routes
Route::post('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/locales', [LocaleController::class, 'available'])->name('locales.available');
Route::post('/currency/{currency}', [CurrencyController::class, 'switch'])->name('currency.switch');
Route::get('/currencies', [CurrencyController::class, 'available'])->name('currencies.available');
Route::get('/currency/convert', [CurrencyController::class, 'convert'])->name('currency.convert');

// Chat Routes
Route::prefix('chat')->name('chat.')->middleware('auth')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/create', [ChatController::class, 'create'])->name('create');
    Route::post('/', [ChatController::class, 'store'])->name('store');
    Route::get('/{chatRoom}', [ChatController::class, 'show'])->name('show');
    Route::post('/{chatRoom}/message', [ChatController::class, 'sendMessage'])->name('message');
    Route::post('/{chatRoom}/close', [ChatController::class, 'closeChat'])->name('close');
    Route::get('/{chatRoom}/messages', [ChatController::class, 'getMessages'])->name('messages');
    Route::post('/{chatRoom}/typing', [ChatController::class, 'typing'])->name('typing');
});

// Public chat routes (for guests)
Route::post('/chat/quick-start', [ChatController::class, 'quickStart'])->name('chat.quick-start');
Route::get('/chat/widget', [ChatController::class, 'widget'])->name('chat.widget');

// WhatsApp Integration
Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
    Route::match(['get', 'post'], '/webhook', [WhatsAppController::class, 'webhook'])->name('webhook');
    Route::post('/chat/{chatRoom}/message', [WhatsAppController::class, 'sendMessage'])->name('message')->middleware('auth:admin');
    Route::post('/chat/{chatRoom}/template', [WhatsAppController::class, 'sendTemplate'])->name('template')->middleware('auth:admin');
});

// Telegram Integration
Route::prefix('telegram')->name('telegram.')->group(function () {
    Route::post('/webhook', [TelegramController::class, 'webhook'])->name('webhook');
    Route::post('/chat/{chatRoom}/message', [TelegramController::class, 'sendMessageToChat'])->name('message')->middleware('auth:admin');
    Route::post('/set-webhook', [TelegramController::class, 'setWebhook'])->name('set-webhook')->middleware('auth:admin');
});

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

// Comparison and Recommendation Routes
Route::prefix('compare')->name('comparison.')->group(function () {
    Route::get('/', [ComparisonController::class, 'index'])->name('index');
    Route::post('/add/{product}', [ComparisonController::class, 'add'])->name('add');
    Route::delete('/remove/{product}', [ComparisonController::class, 'remove'])->name('remove');
    Route::delete('/clear', [ComparisonController::class, 'clear'])->name('clear');
    Route::get('/export', [ComparisonController::class, 'export'])->name('export');
    Route::get('/share', [ComparisonController::class, 'share'])->name('share');
    Route::get('/widget', [ComparisonController::class, 'widget'])->name('widget');
});

Route::prefix('recommendations')->name('recommendations.')->group(function () {
    Route::get('/product/{product}', [RecommendationController::class, 'forProduct'])->name('product');
    Route::get('/personalized', [RecommendationController::class, 'personalized'])->name('personalized');
    Route::get('/similar/{product}', [RecommendationController::class, 'similar'])->name('similar');
    Route::get('/frequently-bought/{product}', [RecommendationController::class, 'frequentlyBought'])->name('frequently-bought');
    Route::get('/recently-viewed', [RecommendationController::class, 'recentlyViewed'])->name('recently-viewed');
    Route::get('/abandoned-cart', [RecommendationController::class, 'abandonedCart'])->name('abandoned-cart');
    Route::post('/click', [RecommendationController::class, 'trackClick'])->name('click');
    Route::get('/widget', [RecommendationController::class, 'widget'])->name('widget');
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

// Payment routes
Route::middleware('auth')->group(function () {
    Route::get('/payment/{order}/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::post('/payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');
    
    // Stripe routes
    Route::get('/stripe/{order}/pay', [StripeController::class, 'initiate'])->name('stripe.initiate');
    Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
    
    // PayPal routes
    Route::get('/paypal/{order}/pay', [PayPalController::class, 'initiate'])->name('paypal.initiate');
    Route::get('/paypal/{order}/success', [PayPalController::class, 'success'])->name('paypal.success');
    Route::get('/paypal/{order}/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
});

// Payment webhooks (no auth required)
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');
Route::post('/paypal/webhook', [PayPalController::class, 'webhook'])->name('paypal.webhook');

// User Dashboard Routes
Route::prefix('user')->name('user.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [UserController::class, 'changePasswordForm'])->name('change-password');
    Route::put('/change-password', [UserController::class, 'changePassword'])->name('change-password.update');
    
    Route::get('/orders', [UserController::class, 'orders'])->name('orders');
    
    // Two-Factor Authentication routes
    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::delete('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::post('/two-factor/regenerate-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.regenerate-codes');
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
        Route::post('/users/bulk-action', [AdminController::class, 'bulkUserAction'])->name('users.bulk-action');
        
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
        
        // Reviews Management
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [AdminController::class, 'reviewsIndex'])->name('index');
            Route::get('/{review}', [AdminController::class, 'showReview'])->name('show');
            Route::post('/{review}/approve', [AdminController::class, 'approveReview'])->name('approve');
            Route::post('/{review}/reject', [AdminController::class, 'rejectReview'])->name('reject');
            Route::delete('/{review}', [AdminController::class, 'deleteReview'])->name('destroy');
            Route::post('/bulk-action', [AdminController::class, 'bulkReviewAction'])->name('bulk-action');
            Route::post('/{review}/respond', [AdminController::class, 'respondToReview'])->name('respond');
        });
        
        // Media/File Manager
        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [AdminController::class, 'mediaIndex'])->name('index');
            Route::get('/files', [AdminController::class, 'getFiles'])->name('files');
            Route::post('/upload', [AdminController::class, 'uploadFile'])->name('upload');
            Route::post('/folder', [AdminController::class, 'createFolder'])->name('folder.create');
            Route::put('/rename', [AdminController::class, 'renameFile'])->name('rename');
            Route::delete('/delete', [AdminController::class, 'deleteFiles'])->name('delete');
            Route::get('/download', [AdminController::class, 'downloadFile'])->name('download');
        });
        
        // System Logs
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/', function () { return redirect()->route('admin.system.logs'); })->name('index');
            Route::get('/logs', [AdminController::class, 'systemLogs'])->name('logs');
            Route::post('/logs/clear', [AdminController::class, 'clearLogs'])->name('logs.clear');
            Route::get('/logs/download', [AdminController::class, 'downloadLogs'])->name('logs.download');
            Route::get('/logs/data', [AdminController::class, 'getLogsData'])->name('logs.data');
        });
        
        // Shipping Management
        Route::prefix('shipping')->name('shipping.')->group(function () {
            Route::get('/', [AdminController::class, 'shippingIndex'])->name('index');
            Route::post('/zones', [AdminController::class, 'storeShippingZone'])->name('zones.store');
            Route::put('/zones/{zone}', [AdminController::class, 'updateShippingZone'])->name('zones.update');
            Route::post('/zones/{zone}/toggle', [AdminController::class, 'toggleShippingZone'])->name('zones.toggle');
            Route::delete('/zones/{zone}', [AdminController::class, 'deleteShippingZone'])->name('zones.destroy');
            Route::post('/methods', [AdminController::class, 'storeShippingMethod'])->name('methods.store');
            Route::put('/methods/{method}', [AdminController::class, 'updateShippingMethod'])->name('methods.update');
            Route::delete('/methods/{method}', [AdminController::class, 'deleteShippingMethod'])->name('methods.destroy');
            Route::post('/settings', [AdminController::class, 'saveShippingSettings'])->name('settings');
            Route::post('/calculate', [AdminController::class, 'calculateShipping'])->name('calculate');
        });
        
        // Tax Management
        Route::prefix('taxes')->name('taxes.')->group(function () {
            Route::get('/', [AdminController::class, 'taxesIndex'])->name('index');
            Route::post('/rates', [AdminController::class, 'storeTaxRate'])->name('rates.store');
            Route::put('/rates/{rate}', [AdminController::class, 'updateTaxRate'])->name('rates.update');
            Route::post('/rates/{rate}/toggle', [AdminController::class, 'toggleTaxRate'])->name('rates.toggle');
            Route::delete('/rates/{rate}', [AdminController::class, 'deleteTaxRate'])->name('rates.destroy');
            Route::post('/classes', [AdminController::class, 'storeTaxClass'])->name('classes.store');
            Route::put('/classes/{class}', [AdminController::class, 'updateTaxClass'])->name('classes.update');
            Route::delete('/classes/{class}', [AdminController::class, 'deleteTaxClass'])->name('classes.destroy');
            Route::post('/settings', [AdminController::class, 'saveTaxSettings'])->name('settings');
            Route::post('/calculate', [AdminController::class, 'calculateTax'])->name('calculate');
        });
        
        // Admin Chat Management
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\ChatController::class, 'dashboard'])->name('dashboard');
            Route::get('/', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('index');
            Route::get('/{chatRoom}', [\App\Http\Controllers\Admin\ChatController::class, 'show'])->name('show');
            Route::post('/{chatRoom}/assign', [\App\Http\Controllers\Admin\ChatController::class, 'assign'])->name('assign');
            Route::post('/{chatRoom}/take', [\App\Http\Controllers\Admin\ChatController::class, 'takeChat'])->name('take');
            Route::post('/{chatRoom}/message', [\App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])->name('message');
            Route::post('/{chatRoom}/close', [\App\Http\Controllers\Admin\ChatController::class, 'closeChat'])->name('close');
            Route::post('/{chatRoom}/transfer', [\App\Http\Controllers\Admin\ChatController::class, 'transferChat'])->name('transfer');
            Route::post('/{chatRoom}/priority', [\App\Http\Controllers\Admin\ChatController::class, 'updatePriority'])->name('priority');
            Route::post('/{chatRoom}/typing', [\App\Http\Controllers\Admin\ChatController::class, 'typing'])->name('typing');
            Route::get('/stats/live', [\App\Http\Controllers\Admin\ChatController::class, 'getStats'])->name('stats');
        });
        
        // Performance Management
        Route::get('/performance', [\App\Http\Controllers\Web\PerformanceController::class, 'dashboard'])->name('performance.dashboard');
        Route::post('/performance/clear-cache', [\App\Http\Controllers\Web\PerformanceController::class, 'clearCache'])->name('performance.clear-cache');
        Route::post('/performance/warm-cache', [\App\Http\Controllers\Web\PerformanceController::class, 'warmUpCache'])->name('performance.warm-cache');
        Route::post('/performance/optimize-db', [\App\Http\Controllers\Web\PerformanceController::class, 'optimizeDatabase'])->name('performance.optimize-db');
        Route::get('/performance/metrics', [\App\Http\Controllers\Web\PerformanceController::class, 'getPerformanceMetrics'])->name('performance.metrics');
        
        // Inventory Management Routes
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('index');
            Route::post('/adjust', [\App\Http\Controllers\Admin\InventoryController::class, 'adjust'])->name('adjust');
            Route::get('/movements/{product}', [\App\Http\Controllers\Admin\InventoryController::class, 'movements'])->name('movements');
            Route::get('/forecast/{product}', [\App\Http\Controllers\Admin\InventoryController::class, 'forecast'])->name('forecast');
            Route::get('/report', [\App\Http\Controllers\Admin\InventoryController::class, 'report'])->name('report');
            Route::get('/low-stock', [\App\Http\Controllers\Admin\InventoryController::class, 'lowStockAlert'])->name('low-stock');
            Route::post('/bulk-adjust', [\App\Http\Controllers\Admin\InventoryController::class, 'bulkAdjustment'])->name('bulk-adjust');
            Route::get('/export-movements', [\App\Http\Controllers\Admin\InventoryController::class, 'exportMovements'])->name('export-movements');
        });
    });
});
