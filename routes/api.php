<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\OrderController;
use App\Models\Order; 

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// المسار لعرض قائمة المنتجات
Route::get('/products', [ProductController::class, 'index']);

// المسار لعرض منتج واحد بناءً على المعرف
Route::get('/products/{id}', [ProductController::class, 'show']);

// المسار لإضافة منتج جديد
Route::post('/products', [ProductController::class, 'store']);
//  لجلب أفضل الم المستخدمين
Route::get('/top-users', [DashboardController ::class, 'topUsersByOrderValue']);

Route::post('/orders/{order}/apply-discount', [OrderController::class, 'applyOrderDiscount']);

Route::get('/products-display', [ProductController::class, 'showProductsPage'])->name('products.display');