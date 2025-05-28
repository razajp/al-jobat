<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BiltyController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PhysicalQuantityController;
use App\Http\Controllers\PaymentProgramController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPaymentController;
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

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'loginPost'])->name('loginPost');

Route::group(['middleware' => ['auth', 'activeSession']], function () {
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
    Route::post('update-image', [ArticleController::class, 'updateImage'])->name('update-image');
    Route::post('add-rate', [ArticleController::class, 'addRate'])->name('add-rate');
    
    Route::resource('orders', OrderController::class);
    
    Route::resource('shipments', ShipmentController::class);
    
    Route::resource('physical-quantities', PhysicalQuantityController::class);
    
    Route::resource('invoices', InvoiceController::class);
    Route::get('print-invoices', [InvoiceController::class, 'print'])->name('invoices.print');
    
    Route::resource('customer-payments', CustomerPaymentController::class);

    Route::resource('supplier-payments', SupplierPaymentController::class);
    
    Route::resource('payment-programs', PaymentProgramController::class);
    Route::post('payment-programs.update-program', [PaymentProgramController::class, 'updateProgram'])->name('payment-programs.update-program');
    
    Route::resource('bank-accounts', BankAccountController::class);
    
    Route::resource('cargos', CargoController::class);
    
    Route::resource('bilties', BiltyController::class);

    Route::resource('expenses', ExpenseController::class);
    
    Route::post('get-order-details', [Controller::class, 'getOrderDetails'])->name('get-order-details');
    Route::post('get-category-data', [Controller::class, 'getCategoryData'])->name('get-category-data');
    Route::post('change-data-layout', [Controller::class, 'changeDataLayout'])->name('change-data-layout');
    Route::post('get-program-details', [Controller::class, 'getProgramDetails'])->name('get-program-details');
    Route::post('set-invoice-type', [Controller::class, 'setInvoiceType'])->name('set-invoice-type');
    Route::post('get-shipment-details', [Controller::class, 'getShipmentDetails'])->name('get-shipment-details');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('update-last-activity', [AuthController::class, 'updateLastActivity'])->name('update-last-activity');

    Route::resource('users', UserController::class);
    Route::post('update-user-status', [UserController::class, 'updateStatus'])->name('update-user-status');
});