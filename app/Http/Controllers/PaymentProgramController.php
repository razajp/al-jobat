<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\PaymentProgram;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentProgramController extends Controller
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
        
        // Fetch and sort orders by date and created_at
        $orders = Order::with(['customer.city', 'paymentPrograms.subCategory'])
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($order) {
                $order['document'] = 'Order';
                return $order;
            });

        foreach($orders as $order) {
            $order['payment'] = 0;
            $order['balance'] = 0;
            if ($order['paymentPrograms'] && $order['paymentPrograms']['payments']) {
                foreach($order['paymentPrograms']['payments'] as $payment) {
                    $order['payment'] += $payment['amount'];
                }
                $order['balance'] =  $order['paymentPrograms']['amount'] - $order['payment'];
            }
        }
        // Fetch and sort payment programs by date and created_at
        $paymentPrograms = PaymentProgram::with('customer.city', 'subCategory')
            ->where('order_no', null)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->withPaymentDetails()
            ->get()
            ->map(function ($paymentPrograms) {
                $paymentPrograms['document'] = 'Program';
                return $paymentPrograms;
            });

        // Convert collections to arrays
        $ordersArray = $orders->toArray();
        $paymentProgramsArray = $paymentPrograms->toArray();

        // Combine both arrays manually
        $finalData = array_merge($ordersArray, $paymentProgramsArray);

        // Sort the final combined array by date and created_at
        usort($finalData, function ($a, $b) {
            if ($a['date'] == $b['date']) {
                return strtotime($b['created_at']) - strtotime($a['created_at']); // time DESC
            }
            return strtotime($b['date']) - strtotime($a['date']); // date DESC
        });

        // return $finalData;
        return view("payment-programs.index", compact('finalData'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $start = microtime(true); // ⏱️ Start timing

        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $lastProgram = PaymentProgram::orderBy('id', 'DESC')->first();

        if (!$lastProgram) {
            $lastProgram = new PaymentProgram();
            $lastProgram->program_no = '0';
        }

        $customers = Customer::with('city:id,title')
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->select('id', 'customer_name', 'city_id', 'balance') // make sure balance is selected
            ->get()
            ->makeHidden('creator');

        $customers_options = [];

        foreach ($customers as $customer) {
            $customers_options[(int)$customer->id] = [
                'text' => $customer->customer_name . ' | ' . ($customer->city->title ?? 'N/A') . ' | Balance: ' . number_format($customer->balance, 1),
                'data_option' => $customer
            ];
        }

        $loadTime = round(microtime(true) - $start, 3); // ⏱️ End timing

        Log::info("💡 PaymentProgram create() load time: {$loadTime} seconds");

        return view('payment-programs.create', compact('customers_options', 'lastProgram', 'loadTime'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };
        
        $validator = Validator::make($request->all(), [
            'program_no'=> 'required|integer',
            'date'=> 'required|date',
            'customer_id'=> 'required|integer|exists:customers,id',
            'category'=> 'required|in:supplier,self_account,customer,waiting',
            'sub_category'=> 'nullable|integer',
            'amount'=> 'required|numeric',
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
    
        // Create payment Program with morph relationship
        $program = new PaymentProgram([
            'program_no' => $data['program_no'],
            'date' => $data['date'],
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
    public function update(Request $request)
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
    public function updateProgram(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|integer',
            'category' => 'required|string',
            'sub_category' => 'nullable|integer',
            'remarks' => 'nullable|string',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        $program = PaymentProgram::find($data['program_id']);

        $program->category = $data['category'];
        $program->remarks = $data['remarks'];
        $program->amount = $data['amount'];

        $subCategoryModel = null;

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

        
        if ($subCategoryModel) {
            $subCategoryModel->paymentPrograms()->save($program);
        } else {
            $program->save();
        }

        return redirect()->route('payment-programs.index')->with('success', 'Program updated successfully.');
    }

    public function markPaid($id, Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $program = PaymentProgram::find($id);
        
        $program->status = 'Paid';
        $program->save();

        return redirect()->route('payment-programs.index')->with('success', 'Program marked as paid successfully.');
    }
}
