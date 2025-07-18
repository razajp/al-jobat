<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\PartialClear;
use App\Models\PaymentProgram;
use App\Models\Setup;
use App\Models\Supplier;
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
        
        $payments = CustomerPayment::with("customer", 'cheque', 'partialRecord')->whereNotNull('customer_id')->get();

        $payments->each(function ($payment) {
            if ($payment->cheque()->exists() || (($payment->method == 'cheque' || $payment->method == 'slip') && $payment->bank_account_id != null)) {
                $payment['issued'] = 'Issued';
            } else {
                $payment['issued'] = 'Not Issued';
            }
        });

        foreach ($payments as $payment) {
            if ($payment['clear_date'] == null) {
                if ($payment['type'] == 'cheque' || $payment['type'] == 'slip') {
                    $payment['clear_date'] = 'Pending';
                }
            }

            if ($payment['cheque'] && $payment['cheque']['supplier_id']) {
                $payment['cheque']['supplier'] = Supplier::with('bankAccounts')->find($payment['cheque']['supplier_id']);
            }

            if (!empty($payment['partialRecord'])) {
                $clearAmount = collect($payment['partialRecord'])->sum('amount');
                $payment['clear_amount'] = $clearAmount;

                if (floatval($clearAmount) >= floatval($payment['amount'])) {
                    // Get last partial record's clear date
                    $lastPartial = collect($payment['partialRecord'])->last();
                    if (isset($lastPartial['clear_date'])) {
                        $payment['clear_date'] = $lastPartial['clear_date'];
                    }
                }
            }

            if ($payment['remarks'] == null) {
                $payment['remarks'] = 'No Remarks';
            }
        }

        $self_accounts = BankAccount::with('bank')->where('category', 'self')->where('status', 'active')->get();
        $self_accounts_options = [];

        foreach ($self_accounts as $self_account) {
            $self_accounts_options[(int)$self_account->id] = [
                'text' => $self_account->account_title . ' | ' . $self_account->bank->short_title,
            ];
        }

        $authLayout = $this->getAuthLayout($request->route()->getName());

        return view("customer-payments.index", compact("payments", "self_accounts_options", "authLayout"));
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
                $banks_options[(int)$bank->id] = [
                    'text' => $bank->title,
                    'data_option' => $bank,
                ];
            }
        }

        $customers_options = [];
        $programId = $request->query('program_id');

        $lastRecord = CustomerPayment::latest('id')->with('customer', 'customer.paymentPrograms.subCategory.bankAccounts.bank')->first();

        if (!empty($programId)) {
            $program = PaymentProgram::with('customer', 'subCategory.bankAccounts.bank')->withPaymentDetails()->where('balance', '>', 0)->find($programId);

            if ($program && $program->customer) {
                $customers = $program->customer->toArray();
                $program->customer['payment_programs'] = $program->toArray();
                
                $customers_options = [(int)$program->customer->id => [
                    'text' => $program->customer->customer_name . ' | ' . $program->customer->city->title,
                    'data_option' => $program->customer,
                ]];
        
                return view("customer-payments.create", compact("customers", "customers_options", "banks_options", 'lastRecord'));
            }
        }
        
        $customers = Customer::with([
            'orders',
            'payments',
            'paymentPrograms' => function ($query) {
                $query->where('status', 'Unpaid');
            },
            'paymentPrograms.subCategory.bankAccounts.bank'
        ])
        ->whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->select('id', 'customer_name', 'city_id', 'payment_programs', 'date')
        ->get();

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
            }
        }

        foreach ($customers as $customer) {
            foreach ($customer['orders'] as $order) {
                $customer['totalAmount'] += $order->netAmount;
            }
            
            foreach ($customer['payments'] as $payment) {
                $customer['totalPayment'] += $payment->amount;
            }

            $customers_options[(int)$customer->id] = [
                'text' => $customer->customer_name . ' | ' . $customer->city->title,
                'data_option' => $customer,
            ];
        }

        return view("customer-payments.create", compact("customers", "customers_options", 'banks_options', 'lastRecord'));
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
            "method" => "required|string",
            "amount" => "required|integer",
            "bank_id" => "nullable|integer|exists:setups,id",
            "cheque_date" => "nullable|date",
            "slip_date" => "nullable|date",
            "cheque_no" => "nullable|string",
            "slip_no" => "nullable|string",
            "clear_date" => "nullable|date",
            "bank_account_id" => "nullable|integer|exists:bank_accounts,id",
            "transaction_id" => "nullable|string",
            "program_id" => "nullable|exists:payment_programs,id",
            "remarks" => "nullable|string",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $data = $request->all();

        CustomerPayment::create($data);
        
        if (isset($data['program_id']) && $data['program_id']) {
            $program = PaymentProgram::find($data['program_id']);
            if ($program && $data['method'] == 'program') {
                if ($program['category'] == 'supplier') {
                    $data['supplier_id'] = $program->sub_category_id;
                    SupplierPayment::create($data);
                }
            }
        }

        $currentProgram = PaymentProgram::find($request->program_id);
        
        if (isset($currentProgram)) {
            if ($currentProgram->balance <= 1000 && $currentProgram->balance >= 0) {
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
    /**
     * Clear the specified customer payment.
     */
    public function clear($id, Request $request) {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $validator = Validator::make($request->all(), [
            'clear_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $data = $request->all();

        $data['clear_date'] = $request->clear_date;
        $data['remarks'] = $request->remarks;

        $customerPayment = CustomerPayment::findOrFail($id);
        $customerPayment->update($data);
        $customerPayment->save();

        return redirect()->back()->with('success', 'Payment cleared successfully.');
    }

    public function partialClear(Request $request, $id) {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $validator = Validator::make($request->all(), [
            'clear_date' => 'required|date',
            'bank_account_id' => 'required|integer|exists:bank_accounts,id',
            'amount' => 'required|integer',
            'reff_no' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $data = $request->all();
        $data['payment_id'] = $id;

        PartialClear::create($data);

        return redirect()->back()->with('success', 'Payment partial cleared successfully.');
    }
    
    public function transfer($id, Request $request) {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|integer|exists:bank_accounts,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $data = $request->all();

        $data['bank_account_id'] = $request->bank_account_id;
        $data['remarks'] = $request->remarks;

        $customerPayment = CustomerPayment::findOrFail($id);
        $customerPayment->update($data);
        $customerPayment->save();

        return redirect()->back()->with('success', 'Cheque transferd successfully.');
    }
}
