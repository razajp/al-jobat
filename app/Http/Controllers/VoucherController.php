<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CustomerPayment;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            $last_voucher['voucher_no'] = '00/101';
        }

        return view("vouchers.create", compact("suppliers", "suppliers_options", 'cheques_options', 'slips_options', 'self_accounts', 'self_accounts_options', 'last_voucher'));
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voucher $voucher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucher $voucher)
    {
        //
    }
}
