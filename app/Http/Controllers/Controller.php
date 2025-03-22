<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getCategoryData(Request $request)
    {
        switch ($request->category) {
            case 'self':
                $users = User::where('role', 'owner')->where('status', 'active')->get();
                return $users;
                break;
            
            case 'supplier':
                $suppliers = Supplier::with('user')->whereHas('user', function ($query) {
                    $query->where('status', 'active');
                })->get();

                return $suppliers;
                break;
            
            case 'customer':
                $customers = Customer::with('user')->whereHas('user', function ($query) {
                    $query->where('status', 'active');
                })->get();

                return $customers;
                break;
            
            case 'bank_account':
                $bankAccount = BankAccount::with('subCategory')->where('category', 'self')->get();
                return $bankAccount;
                break;
            
            default:
                return "Not Found";
                break;
        }
    }
}
