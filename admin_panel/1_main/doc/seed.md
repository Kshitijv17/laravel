php artisan make:seeder UserSeeder
php artisan make:seeder AdminSeeder
php artisan make:seeder CategorySeeder
php artisan make:seeder ProductSeeder

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::insert([
            [
                'name' => 'Smartphone',
                'description' => 'Latest Android phone',
                'price' => 14999,
                'category_id' => 1,
            ],
            [
                'name' => 'Novel',
                'description' => 'Best-selling fiction',
                'price' => 499,
                'category_id' => 2,
            ],
        ]);
    }
}


php artisan db:seed
php artisan migrate:fresh --seed
