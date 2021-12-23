<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']);
    Route::put('update/{id}', [AuthController::class, 'update']);
});

Route::group(['middleware' => 'api'], function () {
    Route::get('category', [CategoryController::class, 'index']);
    Route::get('category/{id}', [CategoryController::class, 'show']);
    Route::post('category', [CategoryController::class, 'store']);
    Route::put('category/{id}', [CategoryController::class, 'update']);
    Route::delete('category/{id}', [CategoryController::class, 'destroy']);
});

Route::group(['middleware' => 'api'], function () {
    Route::get('brand', [BrandController::class, 'index']);
    Route::get('brand/{id}', [BrandController::class, 'show']);
    Route::post('brand', [BrandController::class, 'store']);
    Route::put('brand/{id}', [BrandController::class, 'update']);
    Route::delete('brand/{id}', [BrandController::class, 'destroy']);
});

Route::group(['middleware' => 'api'], function () {
    Route::get('product', [ProductController::class, 'index']);
    Route::get('product/{id}', [ProductController::class, 'show']);
    Route::post('product', [ProductController::class, 'store']);
    Route::post('product/{id}', [ProductController::class, 'update']);
    Route::delete('product/{id}', [ProductController::class, 'destroy']);
});

Route::group(['middleware' => 'api'], function () {
    Route::get('sections', [SectionController::class, 'index']);
    Route::get('section/{id}', [SectionController::class, 'show']);
    Route::post('section', [SectionController::class, 'store']);
    Route::put('section/{id}', [SectionController::class, 'update']);
    Route::delete('section/{id}', [SectionController::class, 'destroy']);
});

Route::group(['middleware' => 'api'], function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::post('user', [UserController::class, 'store']);
    Route::post('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);
});

Route::group(['middleware' => 'api'], function () {
    Route::get('customers', [CustomerController::class, 'index']);
    Route::post('customer/login', [CustomerController::class, 'login']);
    Route::get('customer/{id}', [CustomerController::class, 'show']);
    Route::post('customer', [CustomerController::class, 'store']);
    Route::post('customer/{id}', [CustomerController::class, 'update']);
    Route::delete('customer/{id}', [CustomerController::class, 'destroy']);
});


Route::group(['middleware' => 'api'], function () {
    Route::get('gallery', [ProductController::class, 'index']);
    Route::get('gallery/{id}', [ProductController::class, 'show']);
    Route::post('gallery', [GalleryController::class, 'store']);
    Route::put('gallery/{id}', [GalleryController::class, 'update']);
    Route::delete('gallery/{id}', [ProductController::class, 'destroy']);
});