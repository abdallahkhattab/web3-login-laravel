<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetaMaskController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// MetaMask authentication routes
Route::post('/metamask-get-nonce', [MetaMaskController::class, 'getNonce'])->name('metamask.get-nonce');
Route::post('/metamask-auth', [MetaMaskController::class, 'authenticate'])->name('metamask.auth');

Route::view('/metamask-login', 'welcome')->name('metamask.login');

// Dashboard route with authentication middleware
Route::get('/dashboard', function () {
    $user = auth()->user();
    return view('dashboard', ['user' => $user]);
})->middleware('auth')->name('dashboard');

// Logout route
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->middleware('auth')->name('logout');