# Laravel Filament E-commerce Admin Panel

A comprehensive e-commerce admin panel built with Laravel 12 and Filament 4. This admin panel provides complete management capabilities for products, categories, orders, and customers.

## Features

### 📦 Product Management
- Complete product CRUD operations
- Product categories with hierarchical structure
- Image gallery support
- Inventory tracking
- SEO optimization fields
- Pricing management (regular, compare, cost prices)
- Product variants and attributes

### 📋 Order Management
- Order tracking and status management
- Order details with line items
- Customer information
- Payment status tracking
- Shipping management
- Order analytics

### 👥 Customer Management
- Customer profiles
- Order history
- User authentication

### 📊 Analytics Dashboard
- Revenue statistics
- Order trends
- Product performance
- Customer insights

## Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite

### Step-by-Step Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd filament_tem
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database configuration**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Install Filament**
   ```bash
   php artisan filament:install --panels
   ```

8. **Create admin user**
   ```bash
   php create_admin.php
   ```
   Or manually create using:
   ```bash
   php artisan tinker
   ```
   Then run:
   ```php
   $user = new App\Models\User();
   $user->name = 'Admin';
   $user->email = 'admin@admin.com';
   $user->password = bcrypt('password');
   $user->save();
   ```

9. **Build assets**
   ```bash
   npm run build
   ```

10. **Start the development server**
    ```bash
    php artisan serve
    ```

## Usage

### Accessing the Admin Panel
1. Visit `http://127.0.0.1:8000/admin`
2. Login with:
   - **Email:** admin@admin.com
   - **Password:** password

### Key Commands

#### Development
```bash
# Start development server
php artisan serve

# Watch for file changes (if using Vite)
npm run dev

# Build for production
npm run build
```

#### Database
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration (drops all tables)
php artisan migrate:fresh

# Seed database
php artisan db:seed
```

#### Filament
```bash
# Create new resource
php artisan make:filament-resource ModelName

# Create widget
php artisan make:filament-widget WidgetName

# Create page
php artisan make:filament-page PageName

# Upgrade Filament
php artisan filament:upgrade
```

#### Cache Management
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Database Structure

### Tables
- **users** - Customer/admin accounts
- **categories** - Product categories (hierarchical)
- **products** - Product catalog
- **orders** - Customer orders
- **order_items** - Order line items

### Key Relationships
- Categories can have parent-child relationships
- Products belong to categories
- Orders belong to users
- Order items link orders and products

## File Structure

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── CategoryResource.php
│   │   ├── ProductResource.php
│   │   ├── OrderResource.php
│   │   └── UserResource.php
│   └── Widgets/
│       ├── EcommerceStatsWidget.php
│       └── OrdersChartWidget.php
├── Models/
│   ├── Category.php
│   ├── Product.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── User.php
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php
```

## Customization

### Adding New Fields
1. Create migration: `php artisan make:migration add_field_to_table`
2. Update model's `$fillable` array
3. Update Filament resource form and table

### Creating New Resources
```bash
php artisan make:filament-resource ModelName
```

### Custom Widgets
```bash
php artisan make:filament-widget CustomWidget
```

## Production Deployment

1. **Optimize for production**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   npm run build
   ```

2. **Set environment**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

3. **File permissions**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

## Troubleshooting

### Common Issues

1. **"Target class [admin] does not exist"**
   - Remove conflicting routes in `routes/web.php`
   - Clear route cache: `php artisan route:clear`

2. **Filament not showing**
   - Ensure AdminPanelProvider is registered in `bootstrap/providers.php`
   - Run: `php artisan filament:upgrade`

3. **Database connection issues**
   - Check `.env` database credentials
   - Ensure database exists
   - Test connection: `php artisan migrate:status`

4. **Permission errors**
   - Set proper file permissions on `storage/` and `bootstrap/cache/`
   - Check web server user permissions

### Debug Commands
```bash
# Check application status
php artisan about

# View logs
tail -f storage/logs/laravel.log

# Check routes
php artisan route:list

# Check configuration
php artisan config:show
```

## Security

- Always use strong passwords for admin accounts
- Keep Laravel and Filament updated
- Use HTTPS in production
- Regularly backup your database
- Monitor access logs

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions:
- Laravel Documentation: https://laravel.com/docs
- Filament Documentation: https://filamentphp.com/docs
- Laravel Community: https://laracasts.com

---

**Admin Credentials:**
- Email: admin@admin.com
- Password: password

**Admin Panel URL:** http://127.0.0.1:8000/admin
