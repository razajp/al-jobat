<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DR;
use App\Models\SupplierPayment;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DRController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    //     $crs = CR::with('voucher.supplier')->orderBy('id', 'desc')->get()->makeHidden('creator');

    //     return view('cr.index', compact('crs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $customer_options = Customer::select('id', 'customer_name', 'city_id')
            ->distinct()
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('customer_name')
            ->get()
            ->mapWithKeys(function ($customer) {
                return [
                    (int)$customer->id => [
                        'text' => ucfirst($customer->customer_name) . ' | ' . strtoupper($customer->city->short_title),
                    ]
                ];
            });

        return view('dr.generate', compact('customer_options'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'voucher_no' => 'required|string',
            'voucher_id' => 'required|integer|exists:vouchers,id',
            'c_r_no' => 'required|string',
            'returnPayments' => 'required|string',
            'newPayments' => 'required|string',
        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['return_payments'] = json_decode($data['returnPayments'] ?? '[]');
        $data['new_payments'] = json_decode($data['newPayments'] ?? '[]');

        if (!str_starts_with($data['c_r_no'], 'CR')) {
            $data['c_r_no'] = 'CR' . $data['c_r_no'];
        }

        $returnEmpty = empty($data['return_payments']);
        $newEmpty = empty($data['new_payments']);

        if ($returnEmpty && $newEmpty) {
            return redirect()->back()->with('error', 'Payments not selected and Payments not added.');
        }

        if ($returnEmpty) {
            return redirect()->back()->with('error', 'Payments not selected.');
        }

        if ($newEmpty) {
            return redirect()->back()->with('error', 'Payments not added.');
        }

        foreach($data['return_payments'] as $payment) {
            SupplierPayment::find($payment->id)->update(['is_return' => true]);
            CustomerPayment::find($payment->payment_id)->update(['is_return' => true]);
        }

        $cr = new DR($data);
        $cr->save(); // 👈 pehle save karenge taake $cr->id mil jaye

        foreach ($data['new_payments'] as $payment) {
            if ($payment->method == 'Payment Program') {
                SupplierPayment::find($payment->data_value)
                    ->update(['method' => $payment->method . ' | CR']);
            } else {
                $columnMap = [
                    'Self Cheque' => 'cheque_no',
                    'Cheque'      => 'cheque_id',
                    'Slip'        => 'slip_id',
                ];

                // Skip unknown methods
                if (!isset($columnMap[$payment->method])) {
                    continue;
                }

                $newSupplierPayment = SupplierPayment::create([
                    'supplier_id'      => Voucher::find($data['voucher_id'])->supplier_id,
                    'date'             => $data['date'],
                    'method'           => $payment->method . ' | CR',
                    'amount'           => $payment->amount,
                    'bank_account_id'  => $payment->bank_account_id,
                    'voucher_id'       => null,
                    'c_r_id'           => $cr->id, // 👈 ab yahan id set ho jaegi
                    $columnMap[$payment->method] => $payment->data_value,
                ]);

                $payment->payment_id = $newSupplierPayment->id;
            }
        }

        $cr->new_payments = $data['new_payments'];
        $cr->save(); // 👈 dubara save karenge taake new_payments update ho jaye

        return redirect()->route('cr.create')->with('success', 'CR Generated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DR $dR)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DR $dR)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DR $dR)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DR $dR)
    {
        //
    }

    public function getPayments(Request $request)
    {
        $payments = CustomerPayment::where('customer_id', $request->customer_id)->where(function ($q) {
            $q->whereDoesntHave('cheque')
            ->orWhere('is_return', true);
        })->get();

        return response()->json(['status' => 'success', 'data' => $payments]);
    }
}
