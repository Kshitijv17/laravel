<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Middleware\ProtectUserDashboard;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ProductController as ShopkeeperProductController;
use App\Http\Controllers\Admin\CategoryController as ShopkeeperCategoryController;
use App\Http\Controllers\SuperAdmin\ProductController as SuperAdminProductController;
use App\Http\Controllers\SuperAdmin\CategoryController as SuperAdminCategoryController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\PermissionManagementController;

//
// 🏠 Welcome Page
//
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

//
//  Guest Routes
//
Route::get('/guest-login', function () {
    $names = ['CaptainZiggy', 'BubbleNinja', 'ToonBoomer', 'FluffyBolt', 'ZapsterZoom', 'MangoMutt', 'WackyWhirl'];
    $passwords = ['fluffyDragon99', 'splatZap42', 'toonTwist88', 'zapBoom33', 'giggleSnout77', 'bubblePop66'];

    $name = $names[array_rand($names)];
    $password = $passwords[array_rand($passwords)];
    $email = strtolower($name) . '@guest.local';

    $user = User::firstOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'password' => bcrypt($password),
            'expires_at' => now()->addDays(7),
            'is_guest' => true,
        ]
    );

    session([
        'guest_name' => $name,
        'guest_email' => $email,
        'guest_password' => $password,
    ]);

    return redirect()->route('guest.show');
})->name('guest.login');

Route::get('/guest-show', function () {
    return view('guest.show', [
        'name' => session('guest_name'),
        'email' => session('guest_email'),
        'password' => session('guest_password'),
    ]);
})->name('guest.show');

Route::post('/guest-enter', function () {
    $email = session('guest_email');
    $user = User::where('email', $email)->first();
    Auth::guard('web')->login($user);
    return redirect()->route('user.dashboard');
})->name('guest.enter');

//
// User Routes
//
Route::get('/user/login', function () {
    if (Auth::guard('web')->check()) {
        return redirect()->route('user.dashboard');
    }
    return app(UserAuthController::class)->loginForm();
})->name('user.login');

Route::post('/user/login', [UserAuthController::class, 'login'])->name('user.login.submit');

Route::get('/user/register', function () {
    if (Auth::guard('web')->check()) {
        return redirect()->route('user.dashboard');
    }
    return app(UserAuthController::class)->registerForm();
})->name('user.register');

Route::post('/user/register', [UserAuthController::class, 'register'])->name('user.register.submit');

Route::post('/user/logout', [UserAuthController::class, 'logout'])->name('user.logout');

Route::get('/user/profile', function () {
    return view('user.profile', ['user' => Auth::user()]);
})->name('user.profile');

// Protected dashboard
Route::middleware(ProtectUserDashboard::class)->group(function () {
    Route::get('/user/dashboard', [UserAuthController::class, 'dashboard'])->name('user.dashboard');
});

//
// Super Admin Registration (Separate from regular admin)
//
Route::get('/super-admin/register', [SuperAdminController::class, 'registerForm'])->name('super-admin.register');
Route::post('/super-admin/register', [SuperAdminController::class, 'register'])->name('super-admin.register.submit');

//
// Super Admin Routes (Same as Admin but with Super Admin middleware)
//
Route::prefix('super-admin')->group(function () {
    // Auth
    Route::get('/login', [SuperAdminController::class, 'loginForm'])->name('super-admin.login');
    Route::post('/login', [SuperAdminController::class, 'login'])->name('super-admin.login.submit');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('super-admin.logout');

    // Dashboard (Protected by auth middleware)
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super-admin.dashboard');

        // Bulk Product Upload
        Route::get('products/bulk-upload', [SuperAdminProductController::class, 'bulkUploadForm'])->name('super-admin.products.bulk-upload-form');
        Route::post('products/bulk-upload', [SuperAdminProductController::class, 'bulkUpload'])->name('super-admin.products.bulk-upload');
        Route::get('products/csv-template', [SuperAdminProductController::class, 'downloadCsvTemplate'])->name('super-admin.products.csv-template');

        // Product CRUD
        Route::resource('products', SuperAdminProductController::class)->names([
            'index' => 'super-admin.products.index',
            'create' => 'super-admin.products.create',
            'store' => 'super-admin.products.store',
            'show' => 'super-admin.products.show',
            'edit' => 'super-admin.products.edit',
            'update' => 'super-admin.products.update',
            'destroy' => 'super-admin.products.destroy',
        ]);
        Route::delete('products/images/{image}', [SuperAdminProductController::class, 'deleteImage'])->name('super-admin.products.delete-image');

        // Category CRUD
        Route::resource('categories', SuperAdminCategoryController::class)->names([
            'index' => 'super-admin.categories.index',
            'create' => 'super-admin.categories.create',
            'store' => 'super-admin.categories.store',
            'show' => 'super-admin.categories.show',
            'edit' => 'super-admin.categories.edit',
            'update' => 'super-admin.categories.update',
            'destroy' => 'super-admin.categories.destroy',
        ]);
    });
});

//
//  🏪 SHOPKEEPER PANEL (Admin/Shop Owner)
//
Route::prefix('shopkeeper')->group(function () {
    // Authentication Routes (Public)
    Route::get('/login', [AdminAuthController::class, 'loginForm'])->name('shopkeeper.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('shopkeeper.login.submit');
    Route::get('/register', [AdminAuthController::class, 'registerForm'])->name('shopkeeper.register');
    Route::post('/register', [AdminAuthController::class, 'register'])->name('shopkeeper.register.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('shopkeeper.logout');

    // Protected Shopkeeper Routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Shopkeeper\ShopkeeperController::class, 'dashboard'])->name('shopkeeper.dashboard');
        
        // Shop Management
        Route::get('/shop/create', [\App\Http\Controllers\Shopkeeper\ShopkeeperController::class, 'createShop'])->name('shopkeeper.shop.create');
        Route::post('/shop', [\App\Http\Controllers\Shopkeeper\ShopkeeperController::class, 'storeShop'])->name('shopkeeper.shop.store');
        Route::get('/shop/edit', [\App\Http\Controllers\Shopkeeper\ShopkeeperController::class, 'editShop'])->name('shopkeeper.shop.edit');
        Route::put('/shop', [\App\Http\Controllers\Shopkeeper\ShopkeeperController::class, 'updateShop'])->name('shopkeeper.shop.update');
        
        // Product Management (All admin product functionality moved here)
        Route::get('products/bulk-upload', [ShopkeeperProductController::class, 'bulkUploadForm'])->name('shopkeeper.products.bulk-upload-form');
        Route::post('products/bulk-upload', [ShopkeeperProductController::class, 'bulkUpload'])->name('shopkeeper.products.bulk-upload');
        Route::get('products/csv-template', [ShopkeeperProductController::class, 'downloadCsvTemplate'])->name('shopkeeper.products.csv-template');
        
        Route::resource('products', ShopkeeperProductController::class)->names([
            'index' => 'shopkeeper.products.index',
            'create' => 'shopkeeper.products.create',
            'store' => 'shopkeeper.products.store',
            'show' => 'shopkeeper.products.show',
            'edit' => 'shopkeeper.products.edit',
            'update' => 'shopkeeper.products.update',
            'destroy' => 'shopkeeper.products.destroy',
        ]);
        Route::delete('products/images/{image}', [ShopkeeperProductController::class, 'deleteImage'])->name('shopkeeper.products.delete-image');
        Route::put('products/{product}/toggle-status', [\App\Http\Controllers\Shopkeeper\ProductController::class, 'toggleStatus'])->name('shopkeeper.products.toggle-status');
        
        // Category Management (Moved from admin)
        Route::resource('categories', ShopkeeperCategoryController::class)->names([
            'index' => 'shopkeeper.categories.index',
            'create' => 'shopkeeper.categories.create',
            'store' => 'shopkeeper.categories.store',
            'show' => 'shopkeeper.categories.show',
            'edit' => 'shopkeeper.categories.edit',
            'update' => 'shopkeeper.categories.update',
            'destroy' => 'shopkeeper.categories.destroy',
        ]);
        
        // Order Management
        Route::resource('orders', \App\Http\Controllers\Shopkeeper\OrderController::class)->names([
            'index' => 'shopkeeper.orders.index',
            'show' => 'shopkeeper.orders.show',
            'edit' => 'shopkeeper.orders.edit',
            'update' => 'shopkeeper.orders.update',
        ])->except(['create', 'store', 'destroy']);
        
        // Order AJAX Routes
        Route::post('orders/{order}/status', [\App\Http\Controllers\Shopkeeper\OrderController::class, 'updateStatus'])->name('shopkeeper.orders.update-status');
        Route::post('orders/{order}/payment-status', [\App\Http\Controllers\Shopkeeper\OrderController::class, 'updatePaymentStatus'])->name('shopkeeper.orders.update-payment-status');
        Route::get('orders/export', [\App\Http\Controllers\Shopkeeper\OrderController::class, 'export'])->name('shopkeeper.orders.export');
    });
});

// Customer/Public Routes
Route::name('customer.')->group(function () {
    // Homepage and product browsing
    Route::get('/', [\App\Http\Controllers\Customer\HomeController::class, 'index'])->name('home');
    Route::get('/category/{category}', [\App\Http\Controllers\Customer\HomeController::class, 'category'])->name('category');
    Route::get('/shop/{shop}', [\App\Http\Controllers\Customer\HomeController::class, 'shop'])->name('shop');
    
    // Product pages
    Route::get('/product/{product}', [\App\Http\Controllers\Customer\ProductController::class, 'show'])->name('product.show');
    Route::get('/search', [\App\Http\Controllers\Customer\ProductController::class, 'search'])->name('product.search');
    
    // Buy Now functionality (no auth required for guest purchases)
    Route::get('/product/{product}/buy-now', [\App\Http\Controllers\Customer\OrderController::class, 'buyNow'])->name('buy-now');
    Route::post('/product/{product}/buy-now', [\App\Http\Controllers\Customer\OrderController::class, 'processBuyNow'])->name('process-buy-now');
    
    // Order pages
    Route::get('/order/{order}/success', [\App\Http\Controllers\Customer\OrderController::class, 'success'])->name('order.success');
    Route::get('/order/{order}/details', [\App\Http\Controllers\Customer\OrderController::class, 'show'])->name('order.details');
    
    // Customer Profile and Account Management (Protected Routes)
    Route::middleware('auth')->group(function () {
        // Profile Management
        Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [\App\Http\Controllers\Customer\ProfileController::class, 'updatePassword'])->name('password.update');
        
        // Order Management
        Route::get('/orders', [\App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [\App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');
        
        // Wishlist Management
        Route::get('/wishlist', [\App\Http\Controllers\Customer\WishlistController::class, 'index'])->name('wishlist');
        Route::post('/wishlist/{product}', [\App\Http\Controllers\Customer\WishlistController::class, 'add'])->name('wishlist.add');
        Route::delete('/wishlist/{product}', [\App\Http\Controllers\Customer\WishlistController::class, 'remove'])->name('wishlist.remove');
        
        // Address Management
        Route::get('/addresses', [\App\Http\Controllers\Customer\AddressController::class, 'index'])->name('addresses');
        Route::post('/addresses', [\App\Http\Controllers\Customer\AddressController::class, 'store'])->name('addresses.store');
        Route::put('/addresses/{address}', [\App\Http\Controllers\Customer\AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{address}', [\App\Http\Controllers\Customer\AddressController::class, 'destroy'])->name('addresses.destroy');
        
        // Reviews
        Route::post('/products/{product}/reviews', [\App\Http\Controllers\Customer\ReviewController::class, 'store'])->name('reviews.store');
        Route::put('/reviews/{review}', [\App\Http\Controllers\Customer\ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\Customer\ReviewController::class, 'destroy'])->name('reviews.destroy');
        
        // Support
        Route::get('/support', [\App\Http\Controllers\Customer\SupportController::class, 'index'])->name('support');
        Route::post('/support', [\App\Http\Controllers\Customer\SupportController::class, 'store'])->name('support.store');
    });
});
