<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CustomerPayment;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Voucher;
use Illuminate\Http\Request;
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
        
        $vouchers = Voucher::with("supplier", 'supplierPayments.cheque', 'supplierPayments.slip', 'supplierPayments.program.customer', "supplierPayments.bankAccount.bank")->get();

        foreach ($vouchers as $voucher) {
            $voucher['previous_balance'] = $voucher['supplier']->calculateBalance(null, $voucher->date, false, false);
            $voucher['total_payment'] = $voucher['supplierPayments']->sum('amount');
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

        $cheques = CustomerPayment::whereNotNull('cheque_no')->with('customer.city', 'cheque')->doesntHave('cheque')->get();

        $slips = CustomerPayment::whereNotNull('slip_no')->with('customer.city', 'slip')->doesntHave('slip')->get();

        $self_accounts = BankAccount::where('category', 'self')->with('bank')->get();

        $suppliers_options = [];

        $suppliers = Supplier::with(['user', 'payments' => function ($query) {
            $query->where('method', 'program')
                ->whereNull('voucher_id')
                ->with(['program.customer']); // nested eager load
        }, 'payments.program.customer', 'payments.bankAccount.bank'])
        ->whereHas('user', function ($query) {
            $query->where('status', 'active'); // Supplier's user must be active
        })
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

        return view("vouchers.create", compact("suppliers", "suppliers_options", 'cheques', 'slips', 'self_accounts', 'last_voucher'));
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
            "supplier_id" => "required|integer|exists:suppliers,id",
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
            // return $paymentDetails;
            $paymentDetails['supplier_id'] = $request->supplier_id;
            $paymentDetails['date'] = $request->date;
            $paymentDetails['voucher_id'] = $voucher->id;

            if ($paymentDetails['payment_id'] ?? false) {
                $payment = SupplierPayment::find($paymentDetails['payment_id']);

                if ($payment) {
                    $payment->update(['voucher_id' => $voucher->id]);
                }
            } else {
                $supplierPayment = SupplierPayment::create($paymentDetails);
            }
        }

        return redirect()->route('vouchers.create')->with('success', 'Payment Added successfully.');
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
