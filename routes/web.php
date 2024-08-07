<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Dashboard route with authentication and email verification
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // PayPal routes
    Route::post('/paypal/payment', [PayPalController::class, 'createPayment']);
    Route::get('/paypal/execute', [PayPalController::class, 'executePayment']);
});

// Admin routes
// Route::middleware('auth')->group(function () {
//     Route::get('/admin/credit-transactions', [AdminController::class, 'showCreditTransactions'])->name('admin.creditTransactions');
//     Route::post('/admin/increment-credits', [AdminController::class, 'incrementCredits'])->name('admin.incrementCredits');
//     Route::post('/admin/decrement-credits', [AdminController::class, 'decrementCredits'])->name('admin.decrementCredits');

//     // Ensure these routes are only accessible to admin with id = 1
//     Route::middleware(function ($request, $next) {
//         if (Auth::user()->id !== 1) {
//             return redirect('/')->with('error', 'Access denied.');
//         }
//         return $next($request);
//     });
// });

require __DIR__.'/auth.php';
