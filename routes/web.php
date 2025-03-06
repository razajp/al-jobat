<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\SupplierController;
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

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('users', UserController::class);
    Route::post('update-user-status', [UserController::class, 'updateStatus'])->name('update-user-status');
});