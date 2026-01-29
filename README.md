# PHP_Laravel12_Secure_Logout_From_All_Devices

# Introduction:
```php
⦁	PHP_Laravel12_Secure_Logout_From_All_Devices is a Laravel 12–based authentication security feature that allows a user to log out from all active devices and browsers at once.
⦁	This implementation uses Laravel Breeze, database-driven sessions, and secure session invalidation logic, following Laravel’s best security practices.
```
# Features:
```php
⦁	Instantly logs the user out from every active session
⦁	Works across browsers, devices, and locations
```

# Step 1: Install Fresh Laravel 12 Create Project
Run command:
```php
        composer create-project laravel/laravel PHP_Laravel12_Secure_Logout_From_All_Devices
```
# Step 2: Setup Database for .env file
```php
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3307
DB_DATABASE=laravel12_secure_logout
DB_USERNAME=root
DB_PASSWORD=
```
# Step 3: Create Database
```php
CREATE DATABASE laravel12_secure_logout;
```
# Step 4: Install Authentication (Laravel Breeze)
1.Install Breeze:
```php
composer require laravel/breeze --dev
```
2.Install Breeze scaffolding:
```php
php artisan breeze:install
```
3.Run migrations:
```php
php artisan migrate
```
4.Build frontend assets:
```php
npm install
npm run dev
```

# Step 5: Configure Session Driver for Security
1.Update .env file:
```php
SESSION_DRIVER=database
```
2.Generate session table and migrate:
```php
php artisan session:table
php artisan migrate
```

# Step 6: Create Controller for Secure Logout
Create controller:
```php
php artisan make:controller Auth/SecurityController
```
File Path:
```php
app/Http/Controllers/Auth/SecurityController.php
```
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SecurityController extends Controller
{
    public function logoutAllDevices(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Password does not match']);
        }

        Auth::logoutOtherDevices($request->password);

        return back()->with('success', 'Logged out from all other devices successfully!');
    }
}
```
# Step 7: Create Routes
routes/web.php
```php
<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SecurityController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/logout-all-devices', [SecurityController::class, 'logoutAllDevices'])
    ->middleware('auth')
    ->name('logout.all');

Route::get('/security', function () {
    return view('profile.security');
})->middleware('auth')->name('security.page');


require __DIR__.'/auth.php';
```

# Step 8: Create Blade File 
File Path:
```php
resources/views/profile/security.blade.php
```
Blade Code:
```php
@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <h3 class="mb-4">Security Settings</h3>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <h5 class="card-title mb-3">
                Logout from all devices
            </h5>

            <form method="POST" action="{{ route('logout.all') }}">
                @csrf

                <div class="mb-3">
                    <label for="password" class="form-label">
                        Confirm your password
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Enter your current password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-danger">
                    Logout From All Devices
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
```
# Step 9: Start Server
Explanation:
```php
⦁	After running the Laravel project successfully, the application can be accessed through the web browser.
⦁	This step is used to verify that the Secure Logout From All Devices feature is working correctly.
```
Run command:
```php
php artisan serve
```
Open browser:
```php
http://127.0.0.1:8000
```

<img width="1193" height="513" alt="image" src="https://github.com/user-attachments/assets/fcbcd414-1c3d-4f27-927d-090917e060a7" />
Output Description:

```php
⦁	Laravel Breeze default home page is displayed
⦁	User can see Login and Register options
⦁	Authentication system is active and working
⦁	This confirms that Laravel application is running properly
```
After login, Open:
```php
http://127.0.0.1:8000/security
```
<img width="1312" height="630" alt="image" src="https://github.com/user-attachments/assets/d261e937-6c42-4e24-9abd-393d15b07067" />

Output Description:
```php
⦁	Laravel checks whether the user is logged in
⦁	If user is not logged in, redirect to login page
⦁	If user is logged in, Security Settings page is displayed
```
# Project Folder Structure:
```php
PHP_Laravel12_Secure_Logout_From_All_Devices
├── app/
│   └── Http/
│       └── Controllers/
│           └── Auth/
│               └── SecurityController.php
│
├── database/
│   └── migrations/
│       └── create_sessions_table.php
│
├── resources/
│   └── views/
│       └── profile/
│           └── security.blade.php
│
├── routes/
│   └── web.php
│
├── .env
└── artisan
```

