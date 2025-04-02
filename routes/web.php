<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetaMaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::view('/metamask-login', 'welcome');
Route::post('/metamask-auth', [MetaMaskController::class, 'authenticate']);
Route::get('/dashboard', function () {
    return "Welcome, " . auth()->user()->wallet_address;
})->middleware('auth');

