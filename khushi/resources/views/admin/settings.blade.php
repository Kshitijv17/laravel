@extends('layouts.admin')

@section('title', 'System Settings')
@section('subtitle', 'Configure your application settings')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Settings</li>
    </ol>
    </nav>

<div class="row">
    <!-- General Settings -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cog me-2"></i>General Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="generalSettingsForm">
                    <div class="mb-3">
                        <label class="form-label">Site Name</label>
                        <input type="text" class="form-control" name="site_name" value="{{ $settings['site_name']->value ?? 'E-Commerce Store' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Site Description</label>
                        <textarea class="form-control" name="site_description" rows="3">{{ $settings['site_description']->value ?? 'Your premier online shopping destination' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="email" class="form-control" name="contact_email" value="{{ $settings['contact_email']->value ?? 'admin@example.com' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Support Phone</label>
                        <input type="text" class="form-control" name="support_phone" value="{{ $settings['support_phone']->value ?? '+1 (555) 123-4567' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Timezone</label>
                        <select class="form-select" name="timezone">
                            <option value="UTC" {{ ($settings['timezone']->value ?? 'Asia/Kolkata') == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ ($settings['timezone']->value ?? 'Asia/Kolkata') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                            <option value="America/Chicago" {{ ($settings['timezone']->value ?? 'Asia/Kolkata') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                            <option value="America/Denver" {{ ($settings['timezone']->value ?? 'Asia/Kolkata') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                            <option value="America/Los_Angeles" {{ ($settings['timezone']->value ?? 'Asia/Kolkata') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                            <option value="Asia/Kolkata" {{ ($settings['timezone']->value ?? 'Asia/Kolkata') == 'Asia/Kolkata' ? 'selected' : '' }}>India Standard Time</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save General Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- E-commerce Settings -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>E-commerce Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="ecommerceSettingsForm">
                    <div class="mb-3">
                        <label class="form-label">Default Currency</label>
                        <select class="form-select" name="currency">
                            <option value="USD" {{ ($settings['currency']->value ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ ($settings['currency']->value ?? 'USD') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="GBP" {{ ($settings['currency']->value ?? 'USD') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                            <option value="INR" {{ ($settings['currency']->value ?? 'USD') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tax Rate (%)</label>
                        <input type="number" class="form-control" name="tax_rate" value="{{ $settings['tax_rate']->value ?? '10' }}" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Free Shipping Threshold</label>
                        <input type="number" class="form-control" name="free_shipping_threshold" value="{{ $settings['free_shipping_threshold']->value ?? '50' }}" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Low Stock Alert</label>
                        <input type="number" class="form-control" name="low_stock_alert" value="{{ $settings['low_stock_alert']->value ?? '5' }}">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="guest_checkout" id="guestCheckout" value="1" {{ ($settings['guest_checkout']->value ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="guestCheckout">
                                Allow Guest Checkout
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="inventory_tracking" id="inventoryTracking" value="1" {{ ($settings['inventory_tracking']->value ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="inventoryTracking">
                                Enable Inventory Tracking
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save E-commerce Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Email Settings -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-envelope me-2"></i>Email Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="emailSettingsForm">
                    <div class="mb-3">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" class="form-control" name="smtp_host" value="{{ $settings['smtp_host']->value ?? 'smtp.gmail.com' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SMTP Port</label>
                        <input type="number" class="form-control" name="smtp_port" value="{{ $settings['smtp_port']->value ?? '587' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SMTP Username</label>
                        <input type="text" class="form-control" name="smtp_username" value="{{ $settings['smtp_username']->value ?? 'your-email@gmail.com' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SMTP Password</label>
                        <input type="password" class="form-control" name="smtp_password" value="{{ $settings['smtp_password']->value ?? 'your-app-password' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">From Email</label>
                        <input type="email" class="form-control" name="from_email" value="{{ $settings['from_email']->value ?? 'noreply@example.com' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">From Name</label>
                        <input type="text" class="form-control" name="from_name" value="{{ $settings['from_name']->value ?? 'E-Commerce Store' }}">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Email Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Settings -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>Payment Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="paymentSettingsForm">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="stripe_enabled" id="stripeEnabled" value="1" {{ ($settings['stripe_enabled']->value ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="stripeEnabled">
                                Enable Stripe
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stripe Publishable Key</label>
                        <input type="text" class="form-control" name="stripe_publishable_key" value="{{ $settings['stripe_publishable_key']->value ?? 'pk_test_...' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stripe Secret Key</label>
                        <input type="password" class="form-control" name="stripe_secret_key" value="{{ $settings['stripe_secret_key']->value ?? 'sk_test_...' }}">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="paypal_enabled" id="paypalEnabled" value="1" {{ ($settings['paypal_enabled']->value ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="paypalEnabled">
                                Enable PayPal
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PayPal Client ID</label>
                        <input type="text" class="form-control" name="paypal_client_id" value="{{ $settings['paypal_client_id']->value ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PayPal Client Secret</label>
                        <input type="password" class="form-control" name="paypal_client_secret" value="{{ $settings['paypal_client_secret']->value ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cod_enabled" id="codEnabled" value="1" {{ ($settings['cod_enabled']->value ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="codEnabled">
                                Enable Cash on Delivery
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Payment Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shield-alt me-2"></i>Security Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="securitySettingsForm">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="two_factor_enabled" id="twoFactorEnabled" value="1" {{ ($settings['two_factor_enabled']->value ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="twoFactorEnabled">
                                Enable Two-Factor Authentication
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Session Timeout (minutes)</label>
                        <input type="number" class="form-control" name="session_timeout" value="{{ $settings['session_timeout']->value ?? '30' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Login Attempts</label>
                        <input type="number" class="form-control" name="max_login_attempts" value="{{ $settings['max_login_attempts']->value ?? '5' }}">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode" value="1" {{ ($settings['maintenance_mode']->value ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenanceMode">
                                Enable Maintenance Mode
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Security Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cache Settings -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-database me-2"></i>Cache & Performance
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Cache Management</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary" onclick="clearCache('config')">
                            <i class="fas fa-refresh me-1"></i>Clear Config Cache
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="clearCache('route')">
                            <i class="fas fa-refresh me-1"></i>Clear Route Cache
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="clearCache('view')">
                            <i class="fas fa-refresh me-1"></i>Clear View Cache
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="clearCache('all')">
                            <i class="fas fa-trash me-1"></i>Clear All Cache
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <h6>Database Optimization</h6>
                    <button type="button" class="btn btn-outline-info" onclick="optimizeDatabase()">
                        <i class="fas fa-tools me-1"></i>Optimize Database
                    </button>
                </div>
                <div class="mb-3">
                    <h6>System Information</h6>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">PHP Version:</small>
                            <div>{{ PHP_VERSION }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Laravel Version:</small>
                            <div>{{ app()->version() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Form submission handlers
        $('#generalSettingsForm').on('submit', function(e) {
            e.preventDefault();
            saveSettings('general', $(this));
        });

        $('#ecommerceSettingsForm').on('submit', function(e) {
            e.preventDefault();
            saveSettings('ecommerce', $(this));
        });

        $('#emailSettingsForm').on('submit', function(e) {
            e.preventDefault();
            saveSettings('email', $(this));
        });

        $('#paymentSettingsForm').on('submit', function(e) {
            e.preventDefault();
            saveSettings('payment', $(this));
        });

        $('#securitySettingsForm').on('submit', function(e) {
            e.preventDefault();
            saveSettings('security', $(this));
        });

        // Save settings function
        function saveSettings(group, form) {
            const formData = new FormData(form[0]);
            
            // Handle checkboxes - if not checked, add them as 0
            form.find('input[type="checkbox"]').each(function() {
                if (!this.checked) {
                    formData.append(this.name, '0');
                }
            });
            
            $.ajax({
                url: `/admin/settings/${group}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message || 'Failed to save settings');
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to save settings';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Handle validation errors
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        message = errors.join(', ');
                    }
                    showAlert('error', message);
                }
            });
        }

        // Alert function
        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Remove existing alerts
            $('.alert').remove();
            
            // Add new alert at the top of the page
            $('.container-fluid').prepend(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
    });

    // Cache clearing function
    function clearCache(type = 'all') {
        $.ajax({
            url: '/admin/cache/clear',
            method: 'POST',
            data: {
                type: type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Cache cleared successfully!');
                } else {
                    alert('Error clearing cache: ' + response.message);
                }
            },
            error: function() {
                alert('Error clearing cache');
            }
        });
    }

    // Database optimization function
    function optimizeDatabase() {
        $.ajax({
            url: '/admin/database/optimize',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Database optimized successfully!');
                } else {
                    alert('Error optimizing database: ' + response.message);
                }
            },
            error: function() {
                alert('Error optimizing database');
            }
        });
    }
</script>
@endpush
