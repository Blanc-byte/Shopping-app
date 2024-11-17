<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
////////////////////////////////////////////////////////////////
Route::get('/admin/dashboard', [AdminController::class, 'index'])
    ->middleware(['auth', 'isAdmin'])
    ->name('admin.dashboard');

Route::get('/user/dashboard', [UserController::class, 'index'])
    ->middleware(['auth', 'isUser'])
    ->name('user.dashboard');

Route::get('/rider/dashboard', [RiderController::class, 'index'])
    ->middleware(['auth', 'isRider'])
    ->name('rider.dashboard');

////////////////////////////////////////////////////////////////
////user Route
Route::get('/user/cart',[UserController::class, 'showCart']
    )->middleware(['auth', 'isUser'])
    ->name('user.cart');
    
Route::post('/add-to-cart', [UserController::class, 'addToCart'])
    ->middleware('auth')
    ->name('cart.add');
    
Route::post('/order/place', [UserController::class, 'insertOrder'])
    ->middleware(['auth', 'isUser'])
    ->name('order.placeOrder');
    
Route::patch('/cart/increase/{productId}', [UserController::class, 'increaseQuantity'])->name('cart.increase');
Route::patch('/cart/decrease/{productId}', [UserController::class, 'decreaseQuantity'])->name('cart.decrease');
Route::delete('/cart/remove/{productId}', [UserController::class, 'removeItem'])->name('cart.remove');
Route::delete('/cart/clear', [UserController::class, 'clearCart'])->name('cart.clear');



Route::get('/user/orderDetails', [UserController::class, 'viewOrderDetails'])
    ->middleware(['auth', 'isUser'])
    ->name('user.orderDetails');

Route::get('/user/dashboard/search', [UserController::class, 'search'])
    ->middleware(['auth', 'isUser'])
    ->name('dashboard.search');

Route::get('/filter-products', [UserController::class, 'filterByCategory'])
    ->middleware(['auth', 'isUser'])
    ->name('filter.products');
    
Route::middleware(['auth', 'isUser'])->group(function () {
    Route::post('/wishlist/add', [UserController::class, 'addToWishlist'])->name('wishlist.add');
    Route::post('/wishlist/remove', [UserController::class, 'removeFromWishlist'])->name('wishlist.remove');
    Route::get('/wishlist', [UserController::class, 'viewWishlist'])->name('wishlist.view');
});    
    
/////////////////////////////////////////////////////////////////
////admin Route
Route::get('/admin/cart', function () {
    return view('admin.products');
})->middleware(['auth', 'isAdmin'])
    ->name('admin.products');
Route::get('/admin/orderDetails', function () {
    return view('admin.users');
})->middleware(['auth', 'isAdmin'])
    ->name('admin.users');
/////////////////////////////////////////////////////////////////
/////rider routes

Route::get('/rider/rider', function () {
    return view('rider.rider');
})->middleware(['auth', 'isRider'])
    ->name('rider.rider');

Route::get('/rider/tobedelivered',[RiderController::class, 'tobedelivered'])
    ->middleware(['auth', 'isRider'])
    ->name('rider.tobedelivered');
Route::get('/rider/delivered',[RiderController::class, 'delivered'])
    ->middleware(['auth', 'isRider'])
    ->name('rider.delivered');

Route::put('/orders/{id}/update-status', [RiderController::class, 'updateStatus'])
    ->middleware(['auth', 'isRider'])
    ->name('orders.updateStatus');

Route::post('/orders/{id}/{origOrdersId}/deliver', [RiderController::class, 'markAsDelivered'])
    ->middleware(['auth', 'isRider'])
    ->name('orders.deliver');

////////////////////////////////////////////////////////////////

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

require __DIR__.'/auth.php';
