<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pay', function () {
    return view('pay');
});
Route::prefix('/files')->group(function () {
    Route::prefix('/temp')->group(function () {
        Route::get('/{disk}/{path}', function (string $disk, string $path) {
            $decrypted_path = Crypt::decryptString($path);
            return Storage::disk($disk)->download($decrypted_path);
        })->name('file.temp')->middleware('signed');
    });
});

Route::prefix('web')->group(function () {
    Route::prefix('/login')->group(function () {
        Route::get('/admin', function () {
            return view('admin-login');
        });
        Route::get('/buyer', function () {
            return view('buyer-login');
        });
    });
    Route::prefix('/cart')->group(function () {
        Route::get('/add', function () {
            return view('add-to-cart');
        });
        Route::get('/all', function () {
            return session('cart');
        });
    });
    Route::get('/session-id', function () {
        return json_encode(session()->getName());
    });
});
