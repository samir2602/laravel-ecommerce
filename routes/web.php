<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->middleware('auth')->group(function(){
    Route::resource('/product', ProductController::class);
    Route::post('/product_status', [ProductController::class, 'update_status'])->name('product_status');
    Route::resource('/role', RoleController::class);
    Route::resource('/permission', PermissionController::class);
    Route::resource('/user', UserController::class);
});
