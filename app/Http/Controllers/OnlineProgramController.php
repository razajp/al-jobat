<?php

namespace App\Http\Controllers;

use App\Models\OnlineProgram;
use App\Models\Customer;
use Illuminate\Http\Request;

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
        $customers = Customer::with('user', 'category', 'orders', 'payments')->get();
        $customers_options = [];

        foreach ($customers as $customer) {
            $user = $customer['user'];
            $customer['status'] = $user->status;

            if ($customer->status == 'active') {
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
        }

        return view('online-programs.create', compact('customers_options'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
