<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\PaymentProgram;
use App\Models\Setup;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierPaymentController extends Controller
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
        
        $payments = SupplierPayment::with("supplier")->get();

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

        return view("supplier-payments.index", compact("payments", "authLayout"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $cheques_options = [];
        $cheques = CustomerPayment::whereNotNull('cheque_no')->with('customer')->get();
        foreach ($cheques as $cheque) {
            if ($cheque) {
                $cheques_options[$cheque->id] = [
                    'text' => $cheque->cheque_no . ' | ' . $cheque->customer->customer_name . ' | ' . $cheque->cheque_date->format('d-M-Y, D'),
                    'data_option' => $cheque,
                ];
            }
        }

        $slips_options = [];
        $slips = CustomerPayment::whereNotNull('slip_no')->with('customer')->get();
        foreach ($slips as $slip) {
            if ($slip) {
                $slips_options[$slip->id] = [
                    'text' => $slip->slip_no . ' | ' . $slip->customer->customer_name . ' | ' . $slip->slip_date->format('d-M-Y, D'),
                    'data_option' => $slip,
                ];
            }
        }

        $suppliers_options = [];

        $suppliers = Supplier::with(['user', 'payments.program.customer', 'payments' => function ($query) {
            $query->where('method', 'program'); // Only eager load payments with method = 'program'
        }])
        ->whereHas('user', function ($query) {
            $query->where('status', 'active'); // Supplier's user must be active
        })
        ->get();

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

        $last_voucher = [];
        $last_voucher['voucher_no'] = '00/101';

        return view("supplier-payments.create", compact("suppliers", "suppliers_options", 'cheques_options', 'slips_options', 'last_voucher'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        return $request;

        $validator = Validator::make($request->all(), [
            "supplier_id" => "required|integer|exists:suppliers,id",
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
            $paymentDetails['supplier_id'] = $request->supplier_id;
            $paymentDetails['date'] = $request->date;
            $paymentDetails['type'] = $request->type;
            $paymentDetails['program_id'] = $request->program_id;

            $supplierPayment = SupplierPayment::create($paymentDetails);
        }

        return redirect()->route('supplier-payments.create')->with('success', 'Payment Added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierPayment $supplierPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierPayment $supplierPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierPayment $supplierPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierPayment $supplierPayment)
    {
        //
    }
}
