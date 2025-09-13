
## 📄 README.md for Laravel E-Commerce Project

# 🛒 Laravel Multi-Auth E-Commerce System

A scalable Laravel-based eCommerce platform featuring **Customer** and **Admin** authentication, product management, cart, checkout, and order tracking. Designed for clean architecture, future SPA/mobile integration, and real-world deployment.

---

## 🚀 Features

- ✅ Customer & Admin login/register (Web + API)
- ✅ Product & Category management (Admin)
- ✅ Product listing & detail view (Customer)
- ✅ Cart operations (add/update/remove)
- ✅ Checkout flow with address & payment
- ✅ Order placement & tracking
- ✅ Role-based access control
- ✅ API-ready structure with Laravel Sanctum
- ✅ Factory + Seeder support
- ✅ Clean folder structure for scalability

---

## 🧱 Tech Stack

- Laravel 10+
- Blade (for web views)
- Laravel Sanctum (for API auth)
- MySQL / SQLite
- Postman (for API testing)
- Stripe / Razorpay (for payments)

---

## 🛠️ Setup Instructions

```bash
git clone <your-repo-url>
cd laravel-ecom
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run dev
php artisan serve
```

---

## 🔐 Authentication Structure

### Guards (`config/auth.php`)
- `web` → Customer (session-based)
- `admin` → Admin (session-based or token)

### Providers
- `users` → `App\Models\User`
- `admins` → `App\Models\Admin`

---

## 🛣️ Routes Overview

### Web Routes (`routes/web.php`)
- `/products` → Product listing
- `/product/{id}` → Product detail
- `/cart` → Cart view
- `/checkout` → Checkout flow
- `/admin/products` → Admin product CRUD
- `/admin/orders` → Admin order dashboard

### API Routes (`routes/api.php`)
- `/api/products` → Product listing
- `/api/cart` → Cart operations
- `/api/order` → Place order
- `/api/user/login` → Customer login
- `/api/admin/login` → Admin login

---

## 📦 Seeder & Factory

```bash
php artisan make:seeder ProductSeeder
php artisan make:factory ProductFactory
php artisan db:seed
```

---

## 🧪 Postman Testing

- Method: `POST`
- URL: `http://127.0.0.1:8000/api/order`
- Body: `cart_items`, `address`, `payment_method`
- Response: JSON with order confirmation

---

## 📂 Folder Structure

```
app/
├── Models/
│   ├── Product.php
│   ├── Category.php
│   ├── Order.php
│   └── ...
├── Http/
│   ├── Controllers/
│   │   ├── Web/
│   │   │   ├── Customer/
│   │   │   └── Admin/
│   │   └── Api/
resources/
├── views/
│   ├── customer/
│   └── admin/
routes/
├── web.php
└── api.php
```

---

## 🤝 Contributing

Pull requests welcome. For major changes, please open an issue first to discuss what you would like to change.

---

## 📄 License

[MIT](LICENSE)

---

Tu bole toh ab seedha product CRUD bana du — migration, model, controller, view — ekdum ready-to-roll. Ya pehle cart logic pe chalein? Bata bhai, kya banaaye agla?