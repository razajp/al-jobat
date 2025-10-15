<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\PaymentClear;
use App\Models\PaymentProgram;
use App\Models\Setup;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $payments = CustomerPayment::with(
            "customer.city",
            "cheque.cr",
            "slip.cr",
            "cheque.voucher.supplier.bankAccounts",
            "slip.voucher.supplier.bankAccounts",
            "bankAccount",
            "paymentClearRecord"
        )
            ->whereNotNull("customer_id")
            ->whereNot("type", 'DR')
            ->orderBy("id", "desc")
            ->get();

        // Issued/Return/Not Issued flag
        $payments->each(function ($payment) {
            if ((($payment->cheque()->exists() || $payment->slip()->exists()) ||
                    (($payment->method == "cheque" || $payment->method == "slip") && $payment->bank_account_id != null)) &&
                !$payment->is_return
            ) {
                $payment->issued = "Issued";
            } elseif ($payment->is_return) {
                $payment->issued = "Return";
            } else {
                $payment->issued = "Not Issued";
            }
        });

        foreach ($payments as $payment) {
            // Clear date pending check
            if ($payment->clear_date === null) {
                if ($payment->type == "cheque" || $payment->type == "slip") {
                    $payment->clear_date = "Pending";
                }
            }

            // Load supplier if cheque has supplier_id
            if ($payment->cheque && $payment->cheque->supplier_id) {
                $payment->cheque->supplier = Supplier::with("bankAccounts")->find($payment->cheque->supplier_id);
            }

            // Clear amount logic
            if ($payment->clear_date !== null && $payment->clear_date !== "Pending") {
                $payment->clear_amount = $payment->amount;
            } else {
                if (!empty($payment->paymentClearRecord)) {
                    $clearAmount = collect($payment->paymentClearRecord)->sum("amount");
                    $payment->clear_amount = $clearAmount;

                    if (floatval($clearAmount) >= floatval($payment->amount)) {
                        $lastPartial = collect($payment->paymentClearRecord)->last();
                        if (isset($lastPartial["clear_date"])) {
                            $payment->clear_date = $lastPartial["clear_date"];
                        }
                    }
                }
            }

            // City title formatting
            if ($payment->customer && $payment->customer->city) {
                $payment->customer->city->title =
                    $payment->customer->city->title . " | " . $payment->customer->city->short_title;
            }

            // Remarks fallback
            if ($payment->remarks === null) {
                $payment->remarks = "No Remarks";
            }

            // Handle program vouchers
            if ($payment->method == "program" && $payment->program_id) {
                $supplierPayment = SupplierPayment::where("program_id", $payment->program_id)
                    ->where("supplier_id", $payment->bankAccount->sub_category_id ?? null)
                    ->where("transaction_id", $payment->transaction_id ?? null)
                    ->with("voucher")
                    ->first();

                $payment->voucher = $supplierPayment?->voucher; // assign directly as object (or null)
            }

            // Base reference number
            $baseRef = null;
            if ($payment->method === 'cheque') {
                $raw = $payment->cheque_no;
            } elseif ($payment->method === 'slip') {
                $raw = $payment->slip_no;
            } elseif ($payment->method === 'program') {
                $raw = $payment->transaction_id;
            } else {
                $raw = $payment->reff_no;
            }

            // Split by | aur 0 index le lo
            $baseRef = explode('|', $raw)[0];  // base part
            $baseRef = trim($baseRef);         // whitespace remove

            $payment->existing_reff_nos = [];
            $payment->max_reff_suffix = 0;
            $payment->has_pipe = str_contains($raw, '|');

            if ($baseRef) {
                $field = match ($payment->method) {
                    'cheque'  => 'cheque_no',
                    'slip'    => 'slip_no',
                    'program' => 'transaction_id',
                    default   => 'reff_no',
                };

                $refs = CustomerPayment::where(function($q) use ($field, $baseRef) {
                        $q->where($field, $baseRef)
                        ->orWhere($field, 'like', $baseRef.' |%');
                    })
                    ->pluck($field)
                    ->toArray();

                $payment->existing_reff_nos = $refs;

                // Max suffix calculate
                $max = 0;
                foreach ($refs as $ref) {
                    if (str_contains($ref, '|')) {
                        [$b, $n] = array_map('trim', explode('|', $ref));
                        if (trim($b) == explode('|', $baseRef)[0] && is_numeric($n)) {
                            $max = max($max, (int) $n);
                        }
                    }
                }
                $payment->max_reff_suffix = $max;
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
                $banks_options[(int)$bank->id] = [
                    'text' => $bank->title,
                    'data_option' => $bank,
                ];
            }
        }

        $customers_options = [];
        $programId = $request->query('program_id');

        $lastRecord = CustomerPayment::latest('id')->with('customer', 'customer.paymentPrograms.subCategory.bankAccounts.bank')->whereNotNull('customer_id')->first();

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

        $cheque_nos = CustomerPayment::pluck('cheque_no')->toArray();
        $slip_nos = CustomerPayment::pluck('slip_no')->toArray();

        return view("customer-payments.create", compact("customers", "customers_options", 'banks_options', 'lastRecord', 'cheque_nos', 'slip_nos'));
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
            "cheque_no" => "nullable|string|unique:customer_payments,cheque_no",
            "slip_no" => "nullable|string|unique:customer_payments,slip_no",
            "clear_date" => "nullable|date",
            "bank_account_id" => "nullable|integer|exists:bank_accounts,id",
            "transaction_id" => "nullable|string|unique:customer_payments,transaction_id",
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
                } else if ($program['category'] == 'customer') {
                    $data['customer_id'] = $program->sub_category_id;
                    CustomerPayment::create($data);
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

        return redirect()->back()->with('success', 'Payment Added successfully.');
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
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $customerPayment->load('customer.paymentPrograms.subCategory.bankAccounts.bank');

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

        $cheque_nos = CustomerPayment::where('cheque_no', "!==", $customerPayment['cheque_no'])->pluck('cheque_no')->toArray();
        $slip_nos = CustomerPayment::where('slip_no', "!==", $customerPayment['slip_no'])->pluck('slip_no')->toArray();

        return view('customer-payments.edit', compact('customerPayment', 'banks_options', 'cheque_nos', 'slip_nos'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerPayment $customerPayment)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $validator = Validator::make($request->all(), [
            "date" => "required|date",
            "type" => "required|string",
            "method" => "required|string",
            "amount" => "required|integer",
            "bank_id" => "nullable|integer|exists:setups,id",
            "cheque_date" => "nullable|date",
            "slip_date" => "nullable|date",
            "cheque_no" => [
                "nullable",
                "string",
                Rule::unique('customer_payments', 'cheque_no')->ignore($customerPayment->id),
            ],
            "slip_no" => [
                "nullable",
                "string",
                Rule::unique('customer_payments', 'slip_no')->ignore($customerPayment->id),
            ],
            "clear_date" => "nullable|date",
            "bank_account_id" => "nullable|integer|exists:bank_accounts,id",
            "transaction_id" => [
                "nullable",
                "string",
                Rule::unique('customer_payments', 'transaction_id')->ignore($customerPayment->id),
            ],
            "program_id" => "nullable|exists:payment_programs,id",
            "remarks" => "nullable|string",
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $data = $request->all();

        $customerPayment->update($data);

        if (isset($data['program_id']) && $data['program_id']) {
            SupplierPayment::where([
                'program_id'     => $data['program_id'],
                'method'         => $data['method'],
                'transaction_id' => $data['transaction_id'],
                'bank_account_id'=> $data['bank_account_id'],
            ])->delete();

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

        return redirect()->route('customer-payments.index')->with('success', 'Payment update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerPayment $customerPayment)
    {
        //
    }

    public function clear(Request $request, $id) {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $validator = Validator::make($request->all(), [
            'clear_date' => 'required|date',
            'method_select' => 'required|string',
            'bank_account_id' => 'required|integer|exists:bank_accounts,id',
            'amount' => 'required|integer',
            'reff_no' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $data = $request->all();
        $data['method'] = $data['method_select'];
        $data['payment_id'] = $id;

        PaymentClear::create($data);

        return redirect()->back()->with('success', 'Payment partial cleared successfully.');
    }

    public function split(Request $request, CustomerPayment $payment)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $validator = Validator::make($request->all(), [
            'split_amount' => 'required|integer|min:1|max:' . ($payment->amount - 1),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Determine reference field
        $reffField = match ($payment->method) {
            'cheque'   => 'cheque_no',
            'slip'     => 'slip_no',
            'program'  => 'transaction_id',
            default    => 'reff_no',
        };

        // Get base (before | n)
        $currentReff = $payment->$reffField;
        $parts = explode('|', $currentReff);
        $baseReff = trim($parts[0]);

        // Find max suffix already used for this base
        $maxSuffix = CustomerPayment::where($reffField, 'like', $baseReff.' | %')
            ->pluck($reffField)
            ->map(function ($r) use ($baseReff) {
                $pieces = explode('|', $r);
                return isset($pieces[1]) ? (int) trim($pieces[1]) : 0;
            })
            ->max();

        // If no suffix found, start from 1
        if (!$maxSuffix) {
            $maxSuffix = 1;
            // Update original payment reff_no → base | 1
            $payment->$reffField = $baseReff . ' | ' . $maxSuffix;
        }

        // Step 1: Reduce amount in original payment
        $payment->amount = $payment->amount - $request->split_amount;
        $payment->save();

        // Step 2: Create duplicate with next suffix
        $newPayment = $payment->replicate();
        $newPayment->amount = $request->split_amount;
        $newPayment->$reffField = $baseReff . ' | ' . ($maxSuffix + 1);
        $newPayment->save();

        return redirect()->back()->with('success', 'Payment split successfully.');
    }
}
