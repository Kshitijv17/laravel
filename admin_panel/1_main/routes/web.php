<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Middleware\ProtectUserDashboard;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;

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
//  Admin Routes
//
Route::prefix('admin')->group(function () {
    // Auth
    Route::get('/login', function () {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return app(AdminAuthController::class)->loginForm();
    })->name('admin.login');

    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::get('/register', function () {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return app(AdminAuthController::class)->registerForm();
    })->name('admin.register');

    Route::post('/register', [AdminAuthController::class, 'register'])->name('admin.register.submit');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Dashboard
    Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');

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
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);
});
