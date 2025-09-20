<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Middleware\ProtectUserDashboard;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
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
        Route::get('products/bulk-upload', [ProductController::class, 'bulkUploadForm'])->name('super-admin.products.bulk-upload-form');
        Route::post('products/bulk-upload', [ProductController::class, 'bulkUpload'])->name('super-admin.products.bulk-upload');
        Route::get('products/csv-template', [ProductController::class, 'downloadCsvTemplate'])->name('super-admin.products.csv-template');

        // Product CRUD
        Route::resource('products', ProductController::class)->names([
            'index' => 'super-admin.products.index',
            'create' => 'super-admin.products.create',
            'store' => 'super-admin.products.store',
            'show' => 'super-admin.products.show',
            'edit' => 'super-admin.products.edit',
            'update' => 'super-admin.products.update',
            'destroy' => 'super-admin.products.destroy',
        ]);
        Route::delete('products/images/{image}', [ProductController::class, 'deleteImage'])->name('super-admin.products.delete-image');

        // Category CRUD
        Route::resource('categories', CategoryController::class)->names([
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
    //  Admin Routes
    //
Route::prefix('admin')->group(function () {
    // Auth
    Route::get('/login', [AdminAuthController::class, 'loginForm'])->name('admin.login');

    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::get('/register', [AdminAuthController::class, 'registerForm'])->name('admin.register');

    Route::post('/register', [AdminAuthController::class, 'register'])->name('admin.register.submit');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Dashboard (Protected)
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');
    });

    // Bulk Product Upload - defined before resource route to avoid conflicts
    Route::get('products/bulk-upload', [ProductController::class, 'bulkUploadForm'])->name('admin.products.bulk-upload-form');
    Route::post('products/bulk-upload', [ProductController::class, 'bulkUpload'])->name('admin.products.bulk-upload');
    Route::get('products/csv-template', [ProductController::class, 'downloadCsvTemplate'])->name('admin.products.csv-template');

    // Product CRUD
    Route::resource('products', ProductController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'show' => 'admin.products.show',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);
    Route::delete('products/images/{image}', [ProductController::class, 'deleteImage'])->name('admin.products.delete-image');

    // Category CRUD
    Route::resource('categories', CategoryController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'show' => 'admin.categories.show',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);

    // Super Admin Routes
    Route::middleware(['auth', 'role:superadmin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        
        // Permission Management (Super Admin Only)
        Route::resource('permissions', PermissionManagementController::class)->names([
            'index' => 'admin.permissions.index',
            'create' => 'admin.permissions.create',
            'store' => 'admin.permissions.store',
            'show' => 'admin.permissions.show',
            'edit' => 'admin.permissions.edit',
            'update' => 'admin.permissions.update',
            'destroy' => 'admin.permissions.destroy',
        ]);
        
        // User Permission Management
        Route::get('permissions/users/{user}', [PermissionManagementController::class, 'showUser'])->name('admin.permissions.user.show');
        Route::put('permissions/users/{user}', [PermissionManagementController::class, 'updateUser'])->name('admin.permissions.user.update');
        
        // AJAX Routes
        Route::post('permissions/bulk-update', [PermissionManagementController::class, 'bulkUpdate'])->name('admin.permissions.bulk-update');
        Route::post('permissions/bulk-assign', [PermissionManagementController::class, 'bulkAssign'])->name('admin.permissions.bulk-assign');
        Route::post('permissions/remove-from-user', [PermissionManagementController::class, 'removeFromUser'])->name('admin.permissions.remove-from-user');
        Route::get('permissions/api/get-permissions', [PermissionManagementController::class, 'getPermissions'])->name('admin.permissions.api.get');
        Route::get('permissions/api/user/{user}', [PermissionManagementController::class, 'getUserPermissions'])->name('admin.permissions.api.user');

        // Order Management (All Orders - Super Admin View)
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->names([
            'index' => 'admin.orders.index',
            'show' => 'admin.orders.show',
            'edit' => 'admin.orders.edit',
            'update' => 'admin.orders.update',
            'destroy' => 'admin.orders.destroy',
        ])->except(['create', 'store']);
        
        // Order AJAX Routes
        Route::post('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
        Route::post('orders/{order}/payment-status', [\App\Http\Controllers\Admin\OrderController::class, 'updatePaymentStatus'])->name('admin.orders.update-payment-status');
        Route::post('orders/{order}/cancel', [\App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('admin.orders.cancel');
        Route::post('orders/{order}/refund', [\App\Http\Controllers\Admin\OrderController::class, 'refund'])->name('admin.orders.refund');
        Route::post('orders/{id}/restore', [\App\Http\Controllers\Admin\OrderController::class, 'restore'])->name('admin.orders.restore');
        Route::get('orders/export', [\App\Http\Controllers\Admin\OrderController::class, 'export'])->name('admin.orders.export');

        // Admin Management
        Route::resource('admins', AdminManagementController::class)->names([
            'index' => 'admin.admins.index',
            'create' => 'admin.admins.create',
            'store' => 'admin.admins.store',
            'show' => 'admin.admins.show',
            'edit' => 'admin.admins.edit',
            'update' => 'admin.admins.update',
            'destroy' => 'admin.admins.destroy',
        ]);
    });

    // Shopkeeper Routes (Admin with Shop)
    Route::middleware(['auth', 'role:admin'])->prefix('shopkeeper')->name('shopkeeper.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Shopkeeper\ShopkeeperController::class, 'dashboard'])->name('dashboard');
        
        // Shop Setup
        Route::get('/shop/create', [\App\Http\Controllers\Shopkeeper\ShopkeeperController::class, 'createShop'])->name('shop.create');
        Route::post('/shop', [\App\Http\Controllers\Shopkeeper\ShopkeeperController::class, 'storeShop'])->name('shop.store');
        Route::get('/shop/edit', [\App\Http\Controllers\Shopkeeper\ShopkeeperController::class, 'editShop'])->name('shop.edit');
        Route::put('/shop', [\App\Http\Controllers\Shopkeeper\ShopkeeperController::class, 'updateShop'])->name('shop.update');
        
        // Product Management (Shopkeeper's own products)
        Route::resource('products', \App\Http\Controllers\Shopkeeper\ProductController::class);
        Route::put('products/{product}/toggle-status', [\App\Http\Controllers\Shopkeeper\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        
        // Order Management (Shopkeeper's own orders)
        Route::resource('orders', \App\Http\Controllers\Shopkeeper\OrderController::class)->except(['create', 'store', 'destroy']);
        
        // Order AJAX Routes
        Route::post('orders/{order}/status', [\App\Http\Controllers\Shopkeeper\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('orders/{order}/payment-status', [\App\Http\Controllers\Shopkeeper\OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
        Route::get('orders/export', [\App\Http\Controllers\Shopkeeper\OrderController::class, 'export'])->name('orders.export');
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
});
