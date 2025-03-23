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
use Illuminate\Support\Facades\Auth;

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

    public function changeDataLayout(Request $request)
    {
        $previousRoute = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();

        $authUser = Auth::user();

        $layout = [];
    
        if (!empty($authUser->layout)) {
            // Parse the existing layout from JSON
            $layout = json_decode($authUser->layout, true);
        }

        $newLayout = $request->layout == 'grid' ? 'table' : 'grid';
    
        // Update the layout for the specified page
        $layout[$previousRoute] = $newLayout;
    
        // Save the updated layout back to the user
        $authUser->layout = json_encode($layout);

        $authUser->save();
    
        return redirect()->back()->with('success', 'Layout updated successfully.');
    }

    protected function getAuthLayout($routeName, $default = 'grid')
    {
        $layout = Auth::user()->layout ?? '';

        if (!empty($layout)) {
            $layout = json_decode($layout, true);
            return $layout[$routeName] ?? $default;
        }

        return $default;
    }
}
