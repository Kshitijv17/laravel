<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = new User();
$user->name = 'Admin';
$user->email = 'admin@admin.com';
$user->password = bcrypt('password');
$user->save();

echo "Admin user created successfully!\n";
echo "Email: admin@admin.com\n";
echo "Password: password\n";
