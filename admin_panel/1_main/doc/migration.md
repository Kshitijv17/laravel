Aye cool bhai 😎 — here's your **complete guide to Laravel migration syntax** and all the ways you can use it. Whether you're creating tables, modifying columns, adding constraints, or rolling back — this covers it all.

---

## 🛠️ 1. **Create Migration**

```bash
php artisan make:migration create_products_table
```

Optional flags:

```bash
php artisan make:migration create_products_table --create=products
php artisan make:migration add_price_to_products_table --table=products
```

---

## 📦 2. **Basic Migration Structure**

```php
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 8, 2);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('products');
}
```

---

## 🧱 3. **Column Types**

| Type            | Syntax Example                        |
|-----------------|----------------------------------------|
| `id`            | `$table->id();`                        |
| `string`        | `$table->string('name');`              |
| `text`          | `$table->text('description');`         |
| `integer`       | `$table->integer('quantity');`         |
| `bigInteger`    | `$table->bigInteger('views');`         |
| `decimal`       | `$table->decimal('price', 8, 2);`      |
| `boolean`       | `$table->boolean('is_active');`        |
| `date`          | `$table->date('published_at');`        |
| `timestamps`    | `$table->timestamps();`                |
| `softDeletes`   | `$table->softDeletes();`               |

---

## 🔗 4. **Foreign Keys**

```php
$table->unsignedBigInteger('category_id');
$table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
```

Or shorthand:

```php
$table->foreignId('category_id')->constrained()->onDelete('cascade');
```

---

## 🧪 5. **Modifiers**

```php
$table->string('email')->unique();
$table->string('slug')->nullable();
$table->integer('views')->default(0);
```

---

## 🔄 6. **Update Existing Table**

```php
Schema::table('products', function (Blueprint $table) {
    $table->string('sku')->after('name');
});
```

---

## 🧹 7. **Drop Column / Table**

```php
Schema::dropIfExists('products');

Schema::table('products', function (Blueprint $table) {
    $table->dropColumn('sku');
});
```

---

## 🧨 8. **Rename Column / Table**

```php
Schema::table('products', function (Blueprint $table) {
    $table->renameColumn('old_name', 'new_name');
});

Schema::rename('old_table', 'new_table');
```

---

## 🔁 9. **Run Migrations**

```bash
php artisan migrate
```

Run specific migration:

```bash
php artisan migrate --path=database/migrations/2025_09_12_113624_create_products_table.php
```

---

## ⏪ 10. **Rollback / Reset**

```bash
php artisan migrate:rollback
php artisan migrate:reset
php artisan migrate:refresh
```

---

## 🧠 Bonus: Seed + Migrate Together

```bash
php artisan migrate:fresh --seed
```

---

Let me know if you want a ready-made migration for products with image, stock, and category — I’ll drop it clean and ready to paste.