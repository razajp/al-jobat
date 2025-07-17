<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BiltyController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FabricController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PhysicalQuantityController;
use App\Http\Controllers\PaymentProgramController;
use App\Http\Controllers\PaymetnTransferController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
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

    Route::resource('setups', SetupController::class);

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
    Route::post('customer-payments/{id}/clear', [CustomerPaymentController::class, 'clear'])->name('customer-payments.clear');
    Route::post('customer-payments/{id}/partial-clear', [CustomerPaymentController::class, 'partialClear'])->name('customer-payments.partial-clear');
    Route::post('customer-payments/{id}/transfer', [CustomerPaymentController::class, 'transfer'])->name('customer-payments.transfer');

    Route::resource('payment-transfer', PaymetnTransferController::class);

    Route::resource('supplier-payments', SupplierPaymentController::class);
    
    Route::resource('payment-programs', PaymentProgramController::class);
    // Route::post('payment-programs.mark_paid', [PaymentProgramController::class, 'markPaid'])->name('payment-programs.mark_paid');
    Route::post('payment-programs.update-program', [PaymentProgramController::class, 'updateProgram'])->name('payment-programs.update-program');
    Route::get('payment-programs/{id}/mark-paid', [PaymentProgramController::class, 'markPaid'])->name('payment-programs.mark-paid');

    Route::resource('bank-accounts', BankAccountController::class);
    Route::post('update-bank-account-status', [BankAccountController::class, 'updateStatus'])->name('update-bank-account-status');
    
    Route::resource('cargos', CargoController::class);
    
    Route::resource('bilties', BiltyController::class);

    Route::resource('expenses', ExpenseController::class);
    
    Route::resource('vouchers', VoucherController::class);
    
    Route::get('fabrics/issue', [FabricController::class, 'issue'])->name('fabrics.issue');
    Route::post('fabrics/issuePost', [FabricController::class, 'issuePost'])->name('fabrics.issuePost');
    Route::resource('fabrics', FabricController::class);

    Route::resource('productions', ProductionController::class);
    
    Route::resource('employees', EmployeeController::class);
    Route::post('update-employee-status', [EmployeeController::class, 'updateStatus'])->name('update-employee-status');
    
    Route::post('get-order-details', [Controller::class, 'getOrderDetails'])->name('get-order-details');
    Route::post('get-category-data', [Controller::class, 'getCategoryData'])->name('get-category-data');
    Route::post('change-data-layout', [Controller::class, 'changeDataLayout'])->name('change-data-layout');
    Route::post('get-program-details', [Controller::class, 'getProgramDetails'])->name('get-program-details');
    Route::post('set-invoice-type', [Controller::class, 'setInvoiceType'])->name('set-invoice-type');
    Route::post('get-shipment-details', [Controller::class, 'getShipmentDetails'])->name('get-shipment-details');
    Route::post('set-voucher-type', [Controller::class, 'setVoucherType'])->name('set-voucher-type');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('update-last-activity', [AuthController::class, 'updateLastActivity'])->name('update-last-activity');

    Route::resource('users', UserController::class);
    Route::post('update-user-status', [UserController::class, 'updateStatus'])->name('update-user-status');
});