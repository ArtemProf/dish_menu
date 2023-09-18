<?php

use App\Http\Controllers\Api\DishController;
use App\Http\Controllers\Api\DishCategoryController;
use App\Http\Controllers\Api\DishIngredientController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\DishIngredientCategoryController;
use App\Http\Controllers\Api\ProductSocController;
use App\Http\Controllers\Api\CookListController;
use App\Http\Controllers\Api\CookListItemController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('details', [AuthController::class, 'details'])->name('details');
    });
});

//Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('dishes', DishController::class)
         ->only('store', 'update', 'show', 'destroy', 'index');

    Route::post('dishes/ocr', [DishController::class, 'ocr'])->name('dishes.ocr');

    Route::resource('dish-categories', DishCategoryController::class)
         ->only('store', 'update', 'show', 'destroy', 'index');

    Route::resource('dish-ingredients', DishIngredientController::class)
         ->only('store', 'update', 'show', 'destroy', 'index');

    Route::resource('dish-ingredient-categories', DishIngredientCategoryController::class)
         ->only('store', 'update', 'show', 'destroy', 'index');

    Route::resource('products', ProductController::class)
         ->only('store', 'update', 'show', 'destroy', 'index');

    Route::resource('product-categories', ProductCategoryController::class)
         ->only('store', 'update', 'show', 'destroy', 'index');

    Route::resource('product-socs', ProductSocController::class)
         ->only('store', 'update', 'show', 'destroy', 'index');

    Route::resource('cook-lists', CookListController::class)
         ->only('store', 'update', 'show', 'destroy', 'index');

    Route::resource('cook-list-items', CookListItemController::class)
         ->only('store', 'update', 'show', 'destroy', 'index');
//});
