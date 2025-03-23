<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bankAccounts = BankAccount::with('subCategory')->get();

        $authLayout = $this->getAuthLayout($request->route()->getName());

        return view("bank-accounts.index", compact("bankAccounts", "authLayout"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("bank-accounts.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|in:self,supplier,customer',
            'sub_category' => 'required|integer',
            'bank' => 'required|string',
            'account_title' => 'required|string',
            'account_no' => 'required|string',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        $subCategoryModel = null;
    
        // Dynamically associate sub_category based on category
        switch ($data['category']) {
            case 'self':
                $subCategoryModel = User::find($data['sub_category']);
                break;

            case 'supplier':
                $subCategoryModel = Supplier::find($data['sub_category']);
                break;
            
            case 'customer':
                $subCategoryModel = Customer::find($data['sub_category']);
                break;
        }
    
        // Create Online Program with morph relationship
        $bankAccount = new BankAccount([
            'category' => $data['category'],
            'bank' => $data['bank'],
            'account_title' => $data['account_title'],
            'account_no' => $data['account_no'],
            'date' => $data['date'],
        ]);
    
        $subCategoryModel->bankAccounts()->save($bankAccount);
    
        return redirect()->route('bank-accounts.create')->with('success', 'Bank account added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        //
    }
}
