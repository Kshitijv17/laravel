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

    // Permission Management (Super Admin only)
    Route::middleware('auth')->group(function () {
        // Permission CRUD
        Route::get('permissions', [PermissionManagementController::class, 'index'])->name('admin.permissions.index');
        Route::get('permissions/create', [PermissionManagementController::class, 'create'])->name('admin.permissions.create');
        Route::post('permissions', [PermissionManagementController::class, 'store'])->name('admin.permissions.store');
        Route::get('permissions/{id}', [PermissionManagementController::class, 'show'])->name('admin.permissions.show');
        Route::get('permissions/{id}/edit', [PermissionManagementController::class, 'edit'])->name('admin.permissions.edit');
        Route::put('permissions/{id}', [PermissionManagementController::class, 'update'])->name('admin.permissions.update');
        Route::delete('permissions/{id}', [PermissionManagementController::class, 'destroy'])->name('admin.permissions.destroy');
        
        // User Permission Management
        Route::get('permissions/users/{user}', [PermissionManagementController::class, 'showUser'])->name('admin.permissions.user.show');
        Route::put('permissions/users/{user}', [PermissionManagementController::class, 'updateUser'])->name('admin.permissions.user.update');
        
        // AJAX Routes
        Route::post('permissions/bulk-update', [PermissionManagementController::class, 'bulkUpdate'])->name('admin.permissions.bulk-update');
        Route::post('permissions/bulk-assign', [PermissionManagementController::class, 'bulkAssign'])->name('admin.permissions.bulk-assign');
        Route::post('permissions/remove-from-user', [PermissionManagementController::class, 'removeFromUser'])->name('admin.permissions.remove-from-user');
        Route::get('permissions/api/get-permissions', [PermissionManagementController::class, 'getPermissions'])->name('admin.permissions.api.get');
        Route::get('permissions/api/user/{user}', [PermissionManagementController::class, 'getUserPermissions'])->name('admin.permissions.api.user');

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
});
