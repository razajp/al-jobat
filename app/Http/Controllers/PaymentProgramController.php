<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\PaymentProgram;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Fetch and sort orders by date and created_at
        $orders = Order::with('customer')->orderBy('date', 'asc')->orderBy('created_at', 'asc')->get();

        // Fetch and sort payment programs by date and created_at
        $paymentPrograms = PaymentProgram::with('customer', 'subCategory')
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Convert collections to arrays
        $ordersArray = $orders->toArray();
        $paymentProgramsArray = $paymentPrograms->toArray();

        // Combine both arrays manually
        $finalData = array_merge($ordersArray, $paymentProgramsArray);

        // Sort the final combined array by date and created_at
        usort($finalData, function ($a, $b) {
            if ($a['date'] == $b['date']) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            }
            return strtotime($a['date']) - strtotime($b['date']);
        });

        // return $finalData;
        return view("payment-programs.index", compact('finalData'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lastProgram = PaymentProgram::orderBy('id', 'DESC')->first();

        if (!$lastProgram) {
            $lastProgram = new PaymentProgram();
            $lastProgram->prg_no = '0';
        }

        $customers = Customer::with('orders', 'payments')->whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->get();
        $customers_options = [];

        foreach ($customers as $customer) {
            $user = $customer['user'];
            $customer['status'] = $user->status;
            
            $customers_options[(int)$customer->id] = [
                'text' => $customer->customer_name . ' | ' . $customer->city . ' | ' . $customer->balance,
                'data_option' => $customer
            ];
        }

        return view('payment-programs.create', compact('customers_options', 'lastProgram'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prg_no'=> 'required|integer',
            'date'=> 'required|date',
            'order_no'=> 'nullable|string',
            'customer_id'=> 'required|integer|exists:customers,id',
            'category'=> 'required|in:supplier,self_account,customer,waiting',
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
            
            case 'self_account':
                $subCategoryModel = BankAccount::find($data['sub_category']);
                break;
            
            case 'customer':
                $subCategoryModel = Customer::find($data['sub_category']);
                break;
    
            case 'waiting':
                $subCategoryModel = null; // No association for 'waiting'
                break;
        }

        $data['order_no'] = str_replace(' ', '', explode('|', $data['order_no'])[0]);
    
        // Create payment Program with morph relationship
        $program = new PaymentProgram([
            'prg_no' => $data['prg_no'],
            'date' => $data['date'],
            'order_no' => $data['order_no'],
            'customer_id' => $data['customer_id'],
            'category' => $data['category'],
            'amount' => $data['amount'],
            'remarks' => $data['remarks'],
        ]);
    
        if ($subCategoryModel) {
            $subCategoryModel->paymentPrograms()->save($program);
        } else {
            $program->save();
        }
    
        return redirect()->route('payment-programs.create')->with('success', 'Payment program added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentProgram $paymentProgram)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentProgram $paymentProgram)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentProgram $paymentProgram)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentProgram $paymentProgram)
    {
        //
    }
}
