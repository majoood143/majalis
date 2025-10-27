<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Route;


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

// Add these authentication routes
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register'); // You'll need to create this
})->name('register');

Route::post('/logout', function () {
    //Auth::logout();
    //auth()->logout();
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
