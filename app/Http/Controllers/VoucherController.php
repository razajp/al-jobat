<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CustomerPayment;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
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

        $vouchers = Voucher::with("supplier", 'payments.cheque.customer', 'payments.slip.customer', 'payments.program.customer', "payments.bankAccount.bank", 'payments.selfAccount.bank')->get();

        foreach ($vouchers as $voucher) {
            if (isset($voucher['supplier'])) {
                $voucher['previous_balance'] = $voucher['supplier']->calculateBalance(null, $voucher->date, false, false);
            }
            $voucher['total_payment'] = $voucher['payments']->sum('amount');
        }

        $authLayout = $this->getAuthLayout($request->route()->getName());

        return view("vouchers.index", compact("vouchers", "authLayout"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $cheques = CustomerPayment::whereNotNull('cheque_no')->with('customer.city')->whereDoesntHave('cheque')->whereNull('bank_account_id')->get();
        $cheques_options = [];

        foreach ($cheques as $cheque) {
            $cheques_options[(int)$cheque->id] = [
                'text' => $cheque->cheque_no . ' - ' . $cheque->amount,
                'data_option' => $cheque->makeHidden('creator'),
            ];
        }

        $slips = CustomerPayment::whereNotNull('slip_no')->with('customer.city')->whereDoesntHave('slip')->whereNull('bank_account_id')->get();
        $slips_options = [];

        foreach ($slips as $slip) {
            $slips_options[(int)$slip->id] = [
                'text' => $slip->slip_no . ' - ' . $slip->amount,
                'data_option' => $slip->makeHidden('creator'),
            ];
        }

        $self_accounts = BankAccount::where('category', 'self')->with('bank')->get()->makeHidden('creator');

        $self_accounts_options = [];

        foreach ($self_accounts as $account) {
            $self_accounts_options[(int)$account->id] = [
                'text' => $account->account_title . ' - ' . $account->bank->short_title,
                'data_option' => $account,
            ];
        }

        $suppliers_options = [];

        $suppliers = Supplier::with(['user', 'payments' => function ($query) {
            $query->where('method', 'program')
                ->whereNull('voucher_id')
                ->with(['program.customer']); // nested eager load
        }, 'payments.program.customer', 'payments.bankAccount.bank'])
        ->whereHas('user', function ($query) {
            $query->where('status', 'active'); // Supplier's user must be active
        })
        ->with('expenses')
        ->get();

        // return $suppliers;

        foreach ($suppliers as $supplier) {
            foreach ($supplier->paymentPrograms as $program) {
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

        foreach ($suppliers as $supplier) {
            $supplier['balance'] = $supplier['totalAmount'] - $supplier['totalPayment'];

            $suppliers_options[(int)$supplier->id] = [
                'text' => $supplier->supplier_name,
                'data_option' => $supplier,
            ];
        }

        $last_voucher = Voucher::orderBy('id', 'desc')->first();

        if (!$last_voucher) {
            $last_voucher['voucher_no'] = '00/149';
        }

        return view("vouchers.create", compact("suppliers_options", 'cheques_options', 'slips_options', 'self_accounts', 'self_accounts_options', 'last_voucher'));
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
            "supplier_id" => "nullable|integer|exists:suppliers,id",
            "date" => "required|date",
            "program_id" => "nullable|exists:payment_programs,id",
            "payment_details_array" => "required|json",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $voucher = Voucher::create([
            'voucher_no' => $request->voucher_no,
            'supplier_id' => $request->supplier_id,
            'date' => $request->date,
        ]);

        $voucher->save();

        $data = $request->all();

        $paymentDetailsArray = json_decode($data['payment_details_array'], true);

        foreach ($paymentDetailsArray as $paymentDetails) {
            if (isset($paymentDetails['self_account_id'])) {
                if ($paymentDetails['method'] == 'Cash' || $paymentDetails['method'] == 'Adjustment') {
                    CustomerPayment::create([
                        'date' => $request->date,
                        'type' => 'self_account_deposit',
                        'method' => $paymentDetails['method'],
                        'amount' => $paymentDetails['amount'],
                        'remarks' => $paymentDetails['remarks'],
                        'bank_account_id' => $paymentDetails['self_account_id'],
                    ]);

                    SupplierPayment::create([
                        'date' => $request->date,
                        'method' => $paymentDetails['method'],
                        'amount' => $paymentDetails['amount'],
                        'remarks' => $paymentDetails['remarks'],
                        'self_account_id' => $paymentDetails['self_account_id'],
                        'voucher_id' => $voucher->id,
                    ]);
                } else if ($paymentDetails['method'] == 'Cheque') {
                    $customerPayment = CustomerPayment::find($paymentDetails['cheque_id']);
                    if ($customerPayment) {
                        $customerPayment->update([
                            'bank_account_id' => $paymentDetails['self_account_id'],
                            'is_return' => false,
                        ]);

                        SupplierPayment::create([
                            'date' => $request->date,
                            'method' => $paymentDetails['method'],
                            'amount' => $paymentDetails['amount'],
                            'cheque_id' => $paymentDetails['cheque_id'],
                            'remarks' => $paymentDetails['remarks'],
                            'self_account_id' => $paymentDetails['self_account_id'],
                            'voucher_id' => $voucher->id,
                        ]);
                    }
                } else if ($paymentDetails['method'] == 'Slip') {
                    $customerPayment = CustomerPayment::find($paymentDetails['slip_id']);
                    if ($customerPayment) {
                        $customerPayment->update([
                            'bank_account_id' => $paymentDetails['self_account_id'],
                            'is_return' => false,
                        ]);

                        SupplierPayment::create([
                            'date' => $request->date,
                            'method' => $paymentDetails['method'],
                            'amount' => $paymentDetails['amount'],
                            'slip_id' => $paymentDetails['slip_id'],
                            'remarks' => $paymentDetails['remarks'],
                            'self_account_id' => $paymentDetails['self_account_id'],
                            'voucher_id' => $voucher->id,
                        ]);
                    }
                } else if ($paymentDetails['method'] == 'Self Cheque') {
                    CustomerPayment::create([
                        'date' => $request->date,
                        'type' => 'self_account_deposit',
                        'method' => $paymentDetails['method'],
                        'amount' => $paymentDetails['amount'],
                        'cheque_no' => $paymentDetails['cheque_no'],
                        'cheque_date' => $paymentDetails['cheque_date'],
                        'remarks' => $paymentDetails['remarks'],
                        'bank_account_id' => $paymentDetails['self_account_id'],
                    ]);

                    SupplierPayment::create([
                        'date' => $request->date,
                        'method' => $paymentDetails['method'],
                        'amount' => $paymentDetails['amount'],
                        'cheque_no' => $paymentDetails['cheque_no'],
                        'bank_account_id' => $paymentDetails['bank_account_id'],
                        'remarks' => $paymentDetails['remarks'],
                        'self_account_id' => $paymentDetails['self_account_id'],
                        'voucher_id' => $voucher->id,
                    ]);
                } else if ($paymentDetails['method'] == 'ATM') {
                    CustomerPayment::create([
                        'date' => $request->date,
                        'type' => 'self_account_deposit',
                        'method' => $paymentDetails['method'],
                        'amount' => $paymentDetails['amount'],
                        'reff_no' => $paymentDetails['reff_no'],
                        'remarks' => $paymentDetails['remarks'],
                        'bank_account_id' => $paymentDetails['self_account_id'],
                    ]);

                    SupplierPayment::create([
                        'date' => $request->date,
                        'method' => $paymentDetails['method'],
                        'amount' => $paymentDetails['amount'],
                        'reff_no' => $paymentDetails['reff_no'],
                        'bank_account_id' => $paymentDetails['bank_account_id'],
                        'remarks' => $paymentDetails['remarks'],
                        'self_account_id' => $paymentDetails['self_account_id'],
                        'voucher_id' => $voucher->id,
                    ]);
                }
            } else {
                $paymentDetails['supplier_id'] = $request->supplier_id;
                $paymentDetails['date'] = $request->date;
                $paymentDetails['voucher_id'] = $voucher->id;

                if ($paymentDetails['method'] == 'Cheque' || $paymentDetails['method'] == 'Slip') {
                    $customerPayment = CustomerPayment::find($paymentDetails[$paymentDetails['method'] == 'Cheque' ? 'cheque_id' : 'slip_id']);
                    if ($customerPayment) {
                        $customerPayment->update([
                            'bank_account_id' => $paymentDetails['bank_account_id'] ?? null,
                            'is_return' => false,
                        ]);
                    }
                }

                if ($paymentDetails['payment_id'] ?? false) {
                    $payment = SupplierPayment::find($paymentDetails['payment_id']);

                    if ($payment) {
                        $payment->update(['voucher_id' => $voucher->id]);
                    }
                } else {
                    $supplierPayment = SupplierPayment::create($paymentDetails);
                }
            }
        }

        return redirect()->route('vouchers.create')->with('success', 'Voucher Added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Voucher $voucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Voucher $voucher)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $voucher->load('payments.cheque.customer', 'payments.slip.customer', 'payments.program.customer', "payments.bankAccount.bank", 'payments.selfAccount.bank');

        $cheques = CustomerPayment::whereNotNull('cheque_no')->with('customer.city')->whereDoesntHave('cheque')->whereNull('bank_account_id')->get();
        $cheques_options = [];

        foreach ($cheques as $cheque) {
            $cheques_options[(int)$cheque->id] = [
                'text' => $cheque->cheque_no . ' - ' . $cheque->amount,
                'data_option' => $cheque->makeHidden('creator'),
            ];
        }

        $slips = CustomerPayment::whereNotNull('slip_no')->with('customer.city')->whereDoesntHave('slip')->whereNull('bank_account_id')->get();
        $slips_options = [];

        foreach ($slips as $slip) {
            $slips_options[(int)$slip->id] = [
                'text' => $slip->slip_no . ' - ' . $slip->amount,
                'data_option' => $slip->makeHidden('creator'),
            ];
        }

        $self_accounts = BankAccount::where('category', 'self')->with('bank')->get()->makeHidden('creator');

        $self_accounts_options = [];

        foreach ($self_accounts as $account) {
            $self_accounts_options[(int)$account->id] = [
                'text' => $account->account_title . ' - ' . $account->bank->short_title,
                'data_option' => $account,
            ];
        }

        $suppliers = Supplier::with(['user', 'payments' => function ($query) {
            $query->where('method', 'program')
                ->whereNull('voucher_id')
                ->with(['program.customer']); // nested eager load
        }, 'payments.program.customer', 'payments.bankAccount.bank'])
        ->whereHas('user', function ($query) {
            $query->where('status', 'active'); // Supplier's user must be active
        })
        ->with('expenses')
        ->get();

        // return $suppliers;

        foreach ($suppliers as $supplier) {
            foreach ($supplier->paymentPrograms as $program) {
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

        if ($voucher->supplier_id === null && Auth::user()->voucher_type == 'supplier') {
            $user = Auth::user();
            $user->voucher_type = 'self_account';
            $user->save();
        } else if ($voucher->supplier_id !== null && Auth::user()->voucher_type == 'self_account') {
            $user = Auth::user();
            $user->voucher_type = 'supplier';
            $user->save();
        }

        return view("vouchers.edit", compact('voucher', 'cheques_options', 'slips_options', 'self_accounts', 'self_accounts_options'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voucher $voucher)
    {
        // -----------------------------
        // Step 1: Authorization check
        // -----------------------------
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        // -----------------------------
        // Step 2: Validation
        // -----------------------------
        $validator = Validator::make($request->all(), [
            "payment_details_array" => "required|json",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('error', $validator->errors()->first());
        }

        

        $requestPayments = json_decode($request->payment_details_array, true);
        $voucherPayments = $voucher->payments()->get()->keyBy('id');

        DB::transaction(function () use ($voucher, $requestPayments, $voucherPayments) {

            $keepIDs = [];

            // -----------------------------
            // Step 3: Process each request payment
            // -----------------------------
            foreach ($requestPayments as $pd) {

                // -----------------------------
                // CASE: Existing Payment Update
                // -----------------------------
                if (!empty($pd['payment_id']) && isset($voucherPayments[$pd['payment_id']])) {

                    $payment = $voucherPayments[$pd['payment_id']];

                    // Unique validation for cheque_no
                    Validator::make($pd, [
                        'cheque_no' => [
                            'nullable',
                            Rule::unique('supplier_payments', 'cheque_no')->ignore($payment->id),
                        ],
                    ])->validate();

                    $payment->update([
                        'amount'        => $pd['amount'] ?? $payment->amount,
                        'method'        => $pd['method'] ?? $payment->method,
                        'remarks'       => $pd['remarks'] ?? $payment->remarks,
                        'bank_account_id'=> $pd['bank_account_id'] ?? $payment->bank_account_id,
                        'cheque_no'     => $pd['cheque_no'] ?? $payment->cheque_no,
                        'cheque_date'   => $pd['cheque_date'] ?? $payment->cheque_date,
                        'slip_no'       => $pd['slip_no'] ?? $payment->slip_no,
                        'reff_no'       => $pd['reff_no'] ?? $payment->reff_no,
                        'supplier_id'   => $voucher->supplier_id,
                        'date'          => $voucher->date,
                        'voucher_id'    => $voucher->id,
                    ]);

                    // CustomerPayment sync
                    if ($payment->method === "Cheque" && !empty($pd['cheque_id'])) {
                        CustomerPayment::where('id', $pd['cheque_id'])->update([
                            'bank_account_id' => $pd['bank_account_id'] ?? null,
                            'is_return' => false,
                        ]);
                    }
                    if ($payment->method === "Slip" && !empty($pd['slip_id'])) {
                        CustomerPayment::where('id', $pd['slip_id'])->update([
                            'bank_account_id' => $pd['bank_account_id'] ?? null,
                            'is_return' => false,
                        ]);
                    }

                    $keepIDs[] = $payment->id;
                    continue;
                }

                // -----------------------------
                // CASE: New Payment Create
                // -----------------------------
                Validator::make($pd, [
                    'cheque_no' => ['nullable', Rule::unique('supplier_payments', 'cheque_no')],
                ])->validate();

                $pd['supplier_id'] = $voucher->supplier_id;
                $pd['voucher_id'] = $voucher->id;
                $pd['date'] = $voucher->date;

                $newPayment = SupplierPayment::create($pd);
                $keepIDs[] = $newPayment->id;

                // Self Account logic
                if (!empty($pd['self_account_id'])) {
                    $cpBase = [
                        'date' => $pd['date'],
                        'type' => 'self_account_deposit',
                        'method' => $pd['method'],
                        'amount' => $pd['amount'],
                        'remarks' => $pd['remarks'] ?? null,
                        'bank_account_id' => $pd['self_account_id'],
                    ];

                    if (in_array($pd['method'], ['Cash', 'Adjustment'])) {
                        CustomerPayment::create($cpBase);
                    }

                    if ($pd['method'] === "Self Cheque") {
                        CustomerPayment::create(array_merge($cpBase, [
                            'cheque_no' => $pd['cheque_no'],
                            'cheque_date' => $pd['cheque_date'],
                        ]));
                    }

                    if ($pd['method'] === "ATM") {
                        CustomerPayment::create(array_merge($cpBase, [
                            'reff_no' => $pd['reff_no'],
                        ]));
                    }

                    // Existing Cheque/Slip update
                    if ($pd['method'] === "Cheque" && !empty($pd['cheque_id'])) {
                        CustomerPayment::where('id', $pd['cheque_id'])->update([
                            'bank_account_id' => $pd['self_account_id'],
                            'is_return' => false,
                        ]);
                    }
                    if ($pd['method'] === "Slip" && !empty($pd['slip_id'])) {
                        CustomerPayment::where('id', $pd['slip_id'])->update([
                            'bank_account_id' => $pd['self_account_id'],
                            'is_return' => false,
                        ]);
                    }
                }
            }

            // -----------------------------
            // Step 4: Delete or detach old payments
            // -----------------------------
            foreach ($voucherPayments as $old) {
                if (in_array($old->id, $keepIDs)) continue;

                // Program method → only detach voucher_id
                if ($old->method === "Program") {
                    $old->update(['voucher_id' => null]);
                    continue;
                }

                // Delete CustomerPayment for cheque/slip if needed
                if ($old->method === "Cheque" && $old->cheque_id) {
                    CustomerPayment::where('id', $old->cheque_id)->update([
                        'bank_account_id' => null,
                        'is_return' => true,
                    ]);
                }
                if ($old->method === "Slip" && $old->slip_id) {
                    CustomerPayment::where('id', $old->slip_id)->update([
                        'bank_account_id' => null,
                        'is_return' => true,
                    ]);
                }

                $old->delete();
            }

        }); // End transaction

        return redirect()->route('vouchers.edit', $voucher->id)
            ->with('success', 'Voucher updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucher $voucher)
    {
        //
    }
}
