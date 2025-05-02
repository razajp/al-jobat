<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.'); 
        };
        
        $payments = Payment::with("customer")->get();

        foreach ($payments as $payment) {
            if ($payment['clear_date'] == null) {
                if ($payment['type'] == 'cheque' || $payment['type'] == 'slip'){
                    $payment['clear_date'] = 'Pending';
                }
            }

            if ($payment['remarks'] == null) {
                $payment['remarks'] = 'No Remarks';
            }
        }

        $authLayout = $this->getAuthLayout($request->route()->getName());

        return view("payments.index", compact("payments", "authLayout"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };
        
        $customers = Customer::with('orders', 'payments', 'paymentPrograms.subCategory.bankAccounts.bank')->whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->get();

        foreach ($customers as $customer) {
            foreach ($customer->paymentPrograms as $program) {
                $subCategory = $program->subCategory;
        
                if (isset($subCategory->type)) {
                    if ($subCategory->type === '"App\Models\BankAccount"') {
                        $subCategory = $subCategory;
                    } else {
                        $subCategory = $subCategory->bankAccounts;
                    }
                } else {
                    $subCategory = null; // Handle the case where subCategory is not set
                }

                $program['date'] = date('d-M-Y D', strtotime($program['date']));
            }
        }

        $customers_options = [];
        foreach ($customers as $customer) {
            foreach ($customer['orders'] as $order) {
                $customer['totalAmount'] += $order->netAmount;
            }
            
            foreach ($customer['payments'] as $payment) {
                $customer['totalPayment'] += $payment->amount;
            }

            $customer['balance'] = $customer['totalAmount'] - $customer['totalPayment'];

            $customers_options[(int)$customer->id] = [
                'text' => $customer->customer_name . ' | ' . $customer->city,
                'data_option' => $customer,
            ];
        }

        return view("payments.create", compact("customers", "customers_options"));
        // return $customers;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        return $request->all();

        $validator = Validator::make($request->all(), [
            "customer_id" => "required|integer|exists:customers,id",
            "date" => "required|date",
            "type" => "required|string",
            "amount" => "required|numeric", 
            "program_no" => "required|string", 
            "cheque_no" => "nullable|string",
            "slip_no" => "nullable|string",
            "transition_id" => "nullable|string",
            "cheque_date" => "nullable|date", 
            "slip_date" => "nullable|date",
            "clear_date" => "nullable|date",
            "bank" => "nullable|string",
            "bank_account_id" => "nullable|string",
            "remarks" => "nullable|string",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data["amount"] = (int)str_replace(',', '', $data["amount"]);

        Payment::create($data);

        return redirect()->route('payments.create')->with('success', 'Payment Added successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
