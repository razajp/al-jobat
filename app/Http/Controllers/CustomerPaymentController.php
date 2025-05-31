<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\PaymentProgram;
use App\Models\Setup;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerPaymentController extends Controller
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
        
        $payments = CustomerPayment::with("customer")->get();

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

        return view("customer-payments.index", compact("payments", "authLayout"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };
        
        $banks_options = [];
        $banks = Setup::where('type', 'bank_name')->get();
        foreach ($banks as $bank) {
            if ($bank) {
                $banks_options[$bank->title] = [
                    'text' => $bank->title,
                    'data_option' => $bank,
                ];
            }
        }

        $customers_options = [];
        $programId = $request->query('program_id');

        if (!empty($programId)) {
            $program = PaymentProgram::with('customer', 'subCategory.bankAccounts.bank')->withPaymentDetails()->find($programId);

            if ($program && $program->customer) {
                $customers = $program->customer->toArray();
                $program->customer['payment_programs'] = $program->toArray();
                
                $customers_options = [(int)$program->customer->id => [
                    'text' => $program->customer->customer_name . ' | ' . $program->customer->city->title,
                    'data_option' => $program->customer,
                ]];
        
                return view("customer-payments.create", compact("customers", "customers_options", "banks_options"));
            }
        }
        
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

        foreach ($customers as $customer) {
            foreach ($customer['orders'] as $order) {
                $customer['totalAmount'] += $order->netAmount;
            }
            
            foreach ($customer['payments'] as $payment) {
                $customer['totalPayment'] += $payment->amount;
            }

            // $customer['balance'] = $customer['totalAmount'] - $customer['totalPayment'];

            $customers_options[(int)$customer->id] = [
                'text' => $customer->customer_name . ' | ' . $customer->city->title,
                'data_option' => $customer,
            ];
        }

        return view("customer-payments.create", compact("customers", "customers_options", 'banks_options'));
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

        $validator = Validator::make($request->all(), [
            "customer_id" => "required|integer|exists:customers,id",
            "date" => "required|date",
            "type" => "required|string",
            "program_id" => "nullable|exists:payment_programs,id", 
            "payment_details_array" => "required|json",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $data = $request->all();

        $paymentDetailsArray = json_decode($data['payment_details_array'], true);

        foreach ($paymentDetailsArray as $paymentDetails) {
            $paymentDetails['customer_id'] = $request->customer_id;
            $paymentDetails['date'] = $request->date;
            $paymentDetails['type'] = $request->type;
            $paymentDetails['program_id'] = $request->program_id;

            // return $paymentDetails;
            CustomerPayment::create($paymentDetails);
            
            if ($paymentDetails['program_id']) {
                $program = PaymentProgram::find($paymentDetails['program_id']);
                if ($program && $paymentDetails['method'] == 'program') {
                    $paymentDetails['supplier_id'] = $program->sub_category_id;
                    SupplierPayment::create($paymentDetails);
                }
            }
        }

        if ($request->program_id) {
            $currentProgram = PaymentProgram::find($request->program_id);
            
            if ($currentProgram->balance <= 1000.0 && $currentProgram->balance > 0.0) {
                $currentProgram->status = 'Nominal Due';
                $currentProgram->save();
            } else if ($currentProgram->balance == 0.0) {
                $currentProgram->status = 'Paid';
                $currentProgram->save();
            } else if ($currentProgram->balance < 0.0) {
                $currentProgram->status = 'Overpaid';
                $currentProgram->save();
            } else {
                $currentProgram->status = 'Unpaid';
                $currentProgram->save();
            }
        }

        return redirect()->route('customer-payments.create')->with('success', 'Payment Added successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(CustomerPayment $customerPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerPayment $customerPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerPayment $customerPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerPayment $customerPayment)
    {
        //
    }
}
