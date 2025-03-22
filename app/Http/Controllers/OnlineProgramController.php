<?php

namespace App\Http\Controllers;

use App\Models\OnlineProgram;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OnlineProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::with('orders', 'payments')->whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->get();
        $customers_options = [];

        foreach ($customers as $customer) {
            $user = $customer['user'];
            $customer['status'] = $user->status;

            foreach ($customer['orders'] as $order) {
                $customer['totalAmount'] += $order->netAmount;
            }
            
            foreach ($customer['payments'] as $payment) {
                $customer['totalPayment'] += $payment->amount;
            }

            $customer['balance'] = $customer['totalAmount'] - $customer['totalPayment'];
            
            $customers_options[(int)$customer->id] = [
                'text' => $customer->customer_name . ' | ' . $customer->city,
                'data_option' => $customer
            ];
        }

        return view('online-programs.create', compact('customers_options'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date'=> 'required|date',
            'customer_id'=> 'required|integer|exists:customers,id',
            'category'=> 'required|in:supplier,bank_account,customer,waiting',
            'sub_category'=> 'nullable|integer',
            'amount'=> 'required|integer',
            'remarks'=> 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        $subCategoryModel = null;
    
        // Dynamically associate sub_category based on category
        switch ($data['category']) {
            case 'supplier':
                $subCategoryModel = Supplier::find($data['sub_category']);
                break;
            
            case 'bank_account':
                $subCategoryModel = User::find($data['sub_category']);
                break;
            
            case 'customer':
                $subCategoryModel = Customer::find($data['sub_category']);
                break;
    
            case 'waiting':
                $subCategoryModel = null; // No association for 'waiting'
                break;
        }
    
        // Create Online Program with morph relationship
        $program = new OnlineProgram([
            'date' => $data['date'],
            'customer_id' => $data['customer_id'],
            'category' => $data['category'],
            'amount' => $data['amount'],
            'remarks' => $data['remarks'],
        ]);
    
        if ($subCategoryModel) {
            $subCategoryModel->onlinePrograms()->save($program);
        } else {
            $program->save();
        }
    
        return redirect()->route('online-programs.create')->with('success', 'Online program added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(OnlineProgram $onlineProgram)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OnlineProgram $onlineProgram)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OnlineProgram $onlineProgram)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OnlineProgram $onlineProgram)
    {
        //
    }
}
