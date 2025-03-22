<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhysicalQuantityController;
use App\Http\Controllers\OnlineProgramController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\OnlineProgram;
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

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'loginPost'])->name('loginPost');

Route::group(['middleware' => 'auth'], function () {
    Route::get('', function () {
        return redirect(route('home'));
    });

    Route::get('home', function () {
        return view('home');
    })->name('home');

    Route::post('update-theme', [AuthController::class, 'updateTheme']);
    
    Route::get('add-setup', [SetupController::class, 'addSetup'])->name('addSetup');
    Route::post('add-setup', [SetupController::class, 'addSetupPost'])->name('addSetupPost');

    Route::resource('suppliers', SupplierController::class);
    Route::post('update-supplier-category', [SupplierController::class, 'updateSupplierCategory'])->name('update-supplier-category');
    
    Route::resource('customers', CustomerController::class);
    
    Route::resource('articles', ArticleController::class);
    Route::post('add-image', [ArticleController::class, 'addImage'])->name('add-image');
    Route::post('add-rate', [ArticleController::class, 'addRate'])->name('add-rate');
    
    Route::resource('orders', OrderController::class);
    
    Route::resource('physical-quantities', PhysicalQuantityController::class);
    
    Route::resource('invoices', InvoiceController::class);
    Route::post('get-order-details', [InvoiceController::class, 'getOrderDetails'])->name('get-order-details');
    
    Route::resource('payments', PaymentController::class);
    
    Route::resource('online-programs', OnlineProgramController::class);

    Route::resource('bank-accounts', BankAccountController::class);
    
    Route::post('get-category-data', [Controller::class, 'getCategoryData'])->name('get-category-data');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('users', UserController::class);
    Route::post('update-user-status', [UserController::class, 'updateStatus'])->name('update-user-status');
});