========= 0001_01_01_000000_create_users_table ====
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->enum('role', ['customer', 'admin'])->default('customer');
        $table->rememberToken();
        $table->timestamps();
    });
}

php artisan migrate:refresh 

========= auth.php  =========

  'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'admin' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],
        'adminsa' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],


    php artisan make:middleware AdminRedirect
    php artisan make:middleware AdminAuthentication

    php artisan make:controller LoginController 
    php artisan make:view login 


    "Laravel Folder Structure`n" | Out-File structure.txt
"--- APP ---`n" | Out-File structure.txt -Append
tree .\app /F | Out-File structure.txt -Append
"`n--- DATABASE ---`n" | Out-File structure.txt -Append
tree .\database /F | Out-File structure.txt -Append
"`n--- RESOURCES ---`n" | Out-File structure.txt -Append
tree .\resources /F | Out-File structure.txt -Append
"`n--- ROUTES ---`n" | Out-File structure.txt -Append
tree .\routes /F | Out-File structure.txt -Append


php artisan make:seeder UserSeeder
php artisan make:seeder AdminSeeder
php artisan make:seeder CategorySeeder
php artisan make:seeder ProductSeeder
