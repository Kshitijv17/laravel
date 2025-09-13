
## 📄 README.md for Laravel Multi-Auth Project

# Laravel Multi-Auth System (Web + API)

A robust Laravel project featuring separate authentication systems for **Customers** and **Admins**, with clean separation between **Web views** and **API endpoints**. Built for scalability, maintainability, and future-proofing.

---

## 🚀 Features

- ✅ Customer login/register (Web + API)
- ✅ Admin login/register (Web + API)
- ✅ Separate guards and providers
- ✅ Role-based dashboard access
- ✅ CSRF-protected web routes
- ✅ Stateless API routes with JSON responses
- ✅ Factory + Seeder support
- ✅ Clean folder structure for scalability

---

## 🧱 Tech Stack

- Laravel 10+
- Blade (for web views)
- Laravel Sanctum (optional for API tokens)
- Postman (for API testing)
- MySQL / SQLite

---

## 🛠️ Setup Instructions

```bash
git clone <your-repo-url>
cd laravel-multiauth
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
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
- `/login` → Customer login
- `/register` → Customer register
- `/dashboard` → Customer dashboard
- `/admin/login` → Admin login
- `/admin/dashboard` → Admin dashboard

### API Routes (`routes/api.php`)
- `/api/user/login` → JSON login for customer
- `/api/admin/login` → JSON login for admin

---

## 📦 Seeder & Factory

```bash
php artisan make:seeder UserSeeder
php artisan make:factory UserFactory
php artisan db:seed
```

---

## 🧪 Postman Testing

- Method: `POST`
- URL: `http://127.0.0.1:8000/api/user/login`
- Body: `email`, `password`
- Response: JSON with user info or error

---

## 🧠 Best Practices Followed

- Separation of concerns (Web vs API)
- Scalable architecture
- Clean controller logic
- Reusable validation
- Role-based access control
- Future-ready for mobile or SPA integration

---

## 📂 Folder Structure

```
app/
├── Models/
│   ├── User.php
│   └── Admin.php
├── Http/
│   ├── Controllers/
│   │   ├── Web/
│   │   └── Api/
resources/
├── views/
│   ├── auth/
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
```

---

Tu bole toh main is README ke andar tera naam, repo link, ya custom branding bhi daal du — ekdum personalized. Bataa de bhai, tweak kar du?