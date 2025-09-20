<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import all API controllers
use App\Http\Controllers\Api\{
    ProductController,
    CategoryController,
    ProductVariantController,
    OrderController,
    OrderItemController,
    OrderAddressController,
    CartController,
    CartItemController,
    PaymentController,
    InvoiceController,
    InventoryController,
    WarehouseController,
    ShippingZoneController,
    TaxRuleController,
    CouponController,
    PromoCampaignController,
    ReviewController,
    UserController,
    UserProfileController,
    AdminController,
    RoleController,
    PermissionController,
    AddressController,
    WishlistController,
    WishlistItemController,
    VendorController,
    TransactionController,
    WalletTransactionController,
    SupportTicketController,
    SupportTicketMessageController,
    ReturnRequestController,
    ReferralController,
    NotificationController,
    ActivityLogController,
    CourierTrackingController,
    TrackingUpdateController,
    EmailTemplateController,
    BannerController,
    NewsletterController,
    SettingController,
    FaqController,
    ImageUploadController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => now()
    ]);
});

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Image upload routes (public for demo, consider protecting in production)
    Route::post('/images/upload', [ImageUploadController::class, 'upload']);
    Route::post('/images/upload-base64', [ImageUploadController::class, 'uploadBase64']);
    
    // Authentication routes
    Route::post('/auth/register', [UserController::class, 'register']);
    Route::post('/auth/login', [UserController::class, 'login']);
    Route::post('/auth/admin-login', [AdminController::class, 'login']);
    Route::post('/auth/forgot-password', [UserController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [UserController::class, 'resetPassword']);
    
    // Public product routes
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/statistics', [ProductController::class, 'statistics']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    
    // Public category routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::get('/categories/tree', [CategoryController::class, 'tree']);
    Route::get('/categories/{category}/products', [CategoryController::class, 'products']);
    
    // Public review routes
    Route::get('/products/{product}/reviews', [ReviewController::class, 'productReviews']);
    
    // Newsletter subscription
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
    Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe']);
    
    // Public banners
    Route::get('/banners/position', [BannerController::class, 'getByPosition']);
    Route::post('/banners/{banner}/click', [BannerController::class, 'trackClick']);
    
    // Public FAQs
    Route::get('/faqs', [FaqController::class, 'index']);
    Route::get('/faqs/categories', [FaqController::class, 'getCategories']);
    Route::get('/faqs/category', [FaqController::class, 'getByCategory']);
    Route::get('/faqs/search', [FaqController::class, 'search']);
    
    // Public settings
    Route::get('/settings/public', [SettingController::class, 'getPublic']);
    
    // Tracking (public)
    Route::get('/tracking/{trackingNumber}', [CourierTrackingController::class, 'trackByNumber']);
    
    // Shipping rates
    Route::post('/shipping/calculate', [ShippingZoneController::class, 'calculateRate']);
    
    // Tax calculation
    Route::post('/tax/calculate', [TaxRuleController::class, 'calculateTax']);
});

// Protected routes (authentication required)
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    
    // Protected image upload routes
    Route::post('/images/delete', [ImageUploadController::class, 'delete']);
    
    // User routes
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/wallet', [UserController::class, 'getWallet']);
    Route::post('/wallet/add-funds', [UserController::class, 'addFunds']);
    Route::get('/referral-code', [UserController::class, 'generateReferralCode']);
    
    // User profile routes
    Route::apiResource('user-profiles', UserProfileController::class);
    Route::get('/user-profiles/user/{user_id}', [UserProfileController::class, 'getByUser']);
    Route::put('/user-profiles/{userProfile}/preferences', [UserProfileController::class, 'updatePreferences']);
    
    // Address routes
    Route::apiResource('addresses', AddressController::class);
    Route::post('/addresses/{address}/set-default', [AddressController::class, 'setDefault']);
    Route::get('/users/{user}/addresses', [AddressController::class, 'userAddresses']);
    
    // Cart routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'addItem']);
    Route::put('/cart/{cartItem}', [CartController::class, 'updateItem']);
    Route::delete('/cart/{cartItem}', [CartController::class, 'removeItem']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::get('/cart/summary', [CartController::class, 'summary']);
    
    // Cart items routes
    Route::apiResource('cart-items', CartItemController::class);
    Route::get('/carts/{cart}/items', [CartItemController::class, 'cartItems']);
    
    // Wishlist routes
    Route::apiResource('wishlists', WishlistController::class);
    Route::post('/wishlists/{wishlist}/add-product', [WishlistController::class, 'addProduct']);
    Route::delete('/wishlists/{wishlist}/remove-product/{product}', [WishlistController::class, 'removeProduct']);
    Route::delete('/wishlists/{wishlist}/clear', [WishlistController::class, 'clear']);
    Route::get('/wishlists/{wishlist}/check/{product}', [WishlistController::class, 'checkProduct']);
    
    // Wishlist items routes
    Route::apiResource('wishlist-items', WishlistItemController::class);
    Route::get('/wishlists/{wishlist}/items', [WishlistItemController::class, 'wishlistItems']);
    Route::post('/wishlist-items/{wishlistItem}/move-to-cart', [WishlistItemController::class, 'moveToCart']);
    
    // Order routes
    Route::apiResource('orders', OrderController::class);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::get('/users/{userId}/orders', [OrderController::class, 'userOrders']);
    Route::get('/orders/statistics', [OrderController::class, 'statistics']);
    
    // Order items routes
    Route::apiResource('order-items', OrderItemController::class);
    Route::get('/orders/{order}/items', [OrderItemController::class, 'orderItems']);
    
    // Order addresses routes
    Route::apiResource('order-addresses', OrderAddressController::class);
    Route::get('/orders/{order}/addresses', [OrderAddressController::class, 'orderAddresses']);
    
    // Payment routes
    Route::apiResource('payments', PaymentController::class);
    Route::post('/payments/{payment}/complete', [PaymentController::class, 'complete']);
    Route::post('/payments/{payment}/fail', [PaymentController::class, 'fail']);
    Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund']);
    Route::get('/payments/statistics', [PaymentController::class, 'statistics']);
    
    // Review routes
    Route::apiResource('reviews', ReviewController::class);
    Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve']);
    Route::post('/reviews/{review}/reject', [ReviewController::class, 'reject']);
    
    // Support ticket routes
    Route::apiResource('support-tickets', SupportTicketController::class);
    Route::post('/support-tickets/{supportTicket}/assign', [SupportTicketController::class, 'assign']);
    Route::post('/support-tickets/{supportTicket}/close', [SupportTicketController::class, 'close']);
    Route::get('/support-tickets/statistics', [SupportTicketController::class, 'statistics']);
    
    // Support ticket messages routes
    Route::apiResource('support-ticket-messages', SupportTicketMessageController::class);
    Route::get('/support-tickets/{supportTicket}/messages', [SupportTicketMessageController::class, 'ticketMessages']);
    
    // Return request routes
    Route::apiResource('return-requests', ReturnRequestController::class);
    Route::post('/return-requests/{returnRequest}/approve', [ReturnRequestController::class, 'approve']);
    Route::post('/return-requests/{returnRequest}/reject', [ReturnRequestController::class, 'reject']);
    Route::post('/return-requests/{returnRequest}/complete', [ReturnRequestController::class, 'complete']);
    Route::get('/return-requests/statistics', [ReturnRequestController::class, 'statistics']);
    
    // Referral routes
    Route::apiResource('referrals', ReferralController::class);
    Route::post('/referrals/{referral}/complete', [ReferralController::class, 'complete']);
    Route::get('/users/{user}/referrals', [ReferralController::class, 'userReferrals']);
    Route::post('/referrals/validate-code', [ReferralController::class, 'validateCode']);
    Route::get('/referrals/statistics', [ReferralController::class, 'statistics']);
    
    // Notification routes
    Route::apiResource('notifications', NotificationController::class);
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/{notification}/mark-unread', [NotificationController::class, 'markAsUnread']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll']);
    Route::get('/notifications/counts', [NotificationController::class, 'getCounts']);
    
    // Activity log routes
    Route::apiResource('activity-logs', ActivityLogController::class)->only(['index', 'show']);
    Route::get('/users/{user_id}/activity-logs', [ActivityLogController::class, 'userLogs']);
    Route::get('/activity-logs/statistics', [ActivityLogController::class, 'statistics']);
    Route::delete('/activity-logs/clear-old', [ActivityLogController::class, 'clearOld']);
    
    // Transaction routes
    Route::apiResource('transactions', TransactionController::class);
    Route::get('/users/{user}/transactions', [TransactionController::class, 'userTransactions']);
    Route::get('/transactions/statistics', [TransactionController::class, 'statistics']);
    
    // Wallet transaction routes
    Route::apiResource('wallet-transactions', WalletTransactionController::class);
    Route::get('/users/{user}/wallet-transactions', [WalletTransactionController::class, 'userTransactions']);
    Route::get('/users/{user}/wallet-balance', [WalletTransactionController::class, 'getBalance']);
    Route::get('/wallet-transactions/statistics', [WalletTransactionController::class, 'statistics']);
    
    // Courier tracking routes
    Route::get('/orders/{order}/tracking', [CourierTrackingController::class, 'orderTracking']);
    
});

// Admin routes (admin authentication required)
Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->group(function () {
    
    // Admin auth routes
    Route::get('/profile', [AdminController::class, 'profile']);
    Route::put('/profile', [AdminController::class, 'updateProfile']);
    Route::post('/change-password', [AdminController::class, 'changePassword']);
    Route::post('/logout', [AdminController::class, 'logout']);
    
    // Admin management
    Route::apiResource('admins', AdminController::class);
    
    // Role and permission management
    Route::apiResource('roles', RoleController::class);
    Route::post('/roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions']);
    Route::post('/roles/{role}/remove-permissions', [RoleController::class, 'removePermissions']);
    
    Route::apiResource('permissions', PermissionController::class);
    Route::get('/permissions/by-module', [PermissionController::class, 'getByModule']);
    Route::get('/permissions/modules', [PermissionController::class, 'getModules']);
    
    // Product management
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    
    // Product variants
    Route::apiResource('product-variants', ProductVariantController::class);
    Route::get('/products/{product}/variants', [ProductVariantController::class, 'productVariants']);
    Route::post('/product-variants/{productVariant}/update-stock', [ProductVariantController::class, 'updateStock']);
    
    // Category management
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    
    // Inventory management
    Route::apiResource('inventory', InventoryController::class);
    Route::post('/inventory/{inventory}/adjust-stock', [InventoryController::class, 'adjustStock']);
    Route::post('/inventory/{inventory}/reserve', [InventoryController::class, 'reserve']);
    Route::post('/inventory/{inventory}/release', [InventoryController::class, 'release']);
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock']);
    Route::get('/inventory/statistics', [InventoryController::class, 'statistics']);
    
    // Warehouse management
    Route::apiResource('warehouses', WarehouseController::class);
    Route::get('/warehouses/{warehouse}/inventory', [WarehouseController::class, 'getInventory']);
    Route::get('/warehouses/statistics', [WarehouseController::class, 'statistics']);
    
    // Vendor management
    Route::apiResource('vendors', VendorController::class);
    Route::post('/vendors/{vendor}/verify', [VendorController::class, 'verify']);
    Route::post('/vendors/{vendor}/unverify', [VendorController::class, 'unverify']);
    Route::get('/vendors/{vendor}/products', [VendorController::class, 'getProducts']);
    Route::get('/vendors/statistics', [VendorController::class, 'statistics']);
    
    // Coupon management
    Route::apiResource('coupons', CouponController::class);
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);
    Route::post('/coupons/apply', [CouponController::class, 'apply']);
    Route::get('/coupons/statistics', [CouponController::class, 'statistics']);
    
    // Promo campaign management
    Route::apiResource('promo-campaigns', PromoCampaignController::class);
    Route::post('/promo-campaigns/{promoCampaign}/check-applicability', [PromoCampaignController::class, 'checkApplicability']);
    Route::get('/promo-campaigns/active', [PromoCampaignController::class, 'getActiveCampaigns']);
    
    // Tax rule management
    Route::apiResource('tax-rules', TaxRuleController::class);
    
    // Shipping zone management
    Route::apiResource('shipping-zones', ShippingZoneController::class);
    
    // Email template management
    Route::apiResource('email-templates', EmailTemplateController::class);
    Route::post('/email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview']);
    Route::get('/email-templates/type/{type}', [EmailTemplateController::class, 'getByType']);
    
    // Banner management
    Route::apiResource('banners', BannerController::class);
    Route::get('/banners/statistics', [BannerController::class, 'statistics']);
    
    // Newsletter management
    Route::apiResource('newsletters', NewsletterController::class);
    Route::get('/newsletters/statistics', [NewsletterController::class, 'statistics']);
    Route::get('/newsletters/export', [NewsletterController::class, 'export']);
    
    // Settings management
    Route::apiResource('settings', SettingController::class);
    Route::get('/settings/group/{group}', [SettingController::class, 'getByGroup']);
    Route::post('/settings/bulk-update', [SettingController::class, 'bulkUpdate']);
    Route::get('/settings/groups', [SettingController::class, 'getGroups']);
    
    // FAQ management
    Route::apiResource('faqs', FaqController::class)->except(['index']);
    
    // Invoice management
    Route::apiResource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send']);
    Route::get('/invoices/statistics', [InvoiceController::class, 'statistics']);
    
    // Courier tracking management
    Route::apiResource('courier-tracking', CourierTrackingController::class);
    Route::post('/courier-tracking/{courierTracking}/add-update', [CourierTrackingController::class, 'addUpdate']);
    Route::get('/courier-tracking/statistics', [CourierTrackingController::class, 'statistics']);
    
    // Tracking updates management
    Route::apiResource('tracking-updates', TrackingUpdateController::class);
    Route::get('/courier-tracking/{courierTracking}/updates', [TrackingUpdateController::class, 'courierUpdates']);
    
    // User management
    Route::apiResource('users', UserController::class)->except(['store']);
    
    // All admin routes for other resources
    Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'update']);
    Route::apiResource('payments', PaymentController::class)->only(['index', 'show', 'update']);
    Route::apiResource('reviews', ReviewController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::apiResource('support-tickets', SupportTicketController::class)->only(['index', 'show', 'update']);
    Route::apiResource('return-requests', ReturnRequestController::class)->only(['index', 'show', 'update']);
    Route::apiResource('referrals', ReferralController::class)->only(['index', 'show', 'update']);
    Route::apiResource('notifications', NotificationController::class)->only(['index', 'show', 'store']);
    Route::apiResource('transactions', TransactionController::class)->only(['index', 'show', 'store', 'update']);
    Route::apiResource('wallet-transactions', WalletTransactionController::class)->only(['index', 'show', 'store']);
    Route::apiResource('activity-logs', ActivityLogController::class)->only(['index', 'show', 'store']);
    
});