<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function statement(Request $request)
    {
        if (!empty($request)) {
            $type = $request->type;
            $category = $request->category;
            $id = $request->id;
            $dateFrom = $request->date_from;
            $dateTo = $request->date_to;


            if ($request->withData) {
                // return $request;
                if ($category === 'customer') {
                    $customer = Customer::find($id);
                    if (!$customer) {
                        return response()->json(['error' => 'Customer not found'], 404);
                    }

                    $data = $customer->getStatement($dateFrom, $dateTo, $type);

                    return view("reports.statement", compact('data'));
                }

                if ($category === 'supplier') {
                    $supplier = Supplier::find($id);
                    if (!$supplier) {
                        return response()->json(['error' => 'Supplier not found'], 404);
                    }

                    $data = $supplier->getStatement($dateFrom, $dateTo, $type);

                    return view("reports.statement", compact('data'));
                }

                if ($category === 'bank account') {
                    $bank_account = BankAccount::find($id);
                    if (!$bank_account) {
                        return response()->json(['error' => 'Bank account not found'], 404);
                    }

                    $data = $bank_account->getStatement($dateFrom, $dateTo, $type);

                    return view("reports.statement", compact('data'));
                }
            }
        }

        return view("reports.statement");
    }

    // fucntion get names based on category
    public function getNames(Request $request)
    {
        $category = $request->category;

        if (!$category) {
            return response()->json(['error' => 'Category required'], 400);
        }

        if ($category === 'customer') {
            $customers = Customer::whereHas('user', function ($query) {
                $query->where('status', 'active');
            })->with('city')->get(); // select only needed fields
            return response()->json($customers);
        }

        if ($category === 'supplier') {
            $suppliers = Supplier::whereHas('user', function ($query) {
                $query->where('status', 'active');
            })->get();
            return response()->json($suppliers);
        }

        if ($category === 'bank_account') {
            $bank_accounts = BankAccount::where('status', 'active')->get();
            return response()->json($bank_accounts);
        }

        return response()->json(['error' => 'Invalid category'], 400);
    }
    public function pendingPayments(Request $request)
    {
        if (!empty($request)) {
            $date = $request->input('date'); // e.g. 2025-10-10
            if ($date) {
                // Base payments query
                $payments = CustomerPayment::with([
                        'customer.city',
                        'paymentClearRecord',
                    ])
                    ->whereNotNull('customer_id')
                    ->whereIn('method', ['cheque', 'slip'])
                    ->get()
                    ->filter(function ($payment) use ($date) {
                        // Get payment date according to method
                        $paymentDate = $payment->method === 'cheque'
                            ? $payment->cheque_date
                            : $payment->slip_date;

                        // Skip if date not before given date
                        if (!$paymentDate || $paymentDate >= $date) {
                            return false;
                        }

                        // Calculate received/cleared amount
                        $receivedAmount = 0;
                        $totalAmount = floatval($payment->amount);

                        if ($payment->paymentClearRecord && count($payment->paymentClearRecord) > 0) {
                            $receivedAmount = collect($payment->paymentClearRecord)->sum('amount');
                        } elseif ($payment->clear_date !== null) {
                            // If clear_date is set but no clear records, treat full amount as received
                            $receivedAmount = $totalAmount;
                        }

                        // Determine if payment is pending
                        $isFullyCleared = $receivedAmount >= $totalAmount;

                        // Pending condition:
                        // (clear_date null or not fully cleared)
                        if ($payment->clear_date === null || !$isFullyCleared) {
                            // Add computed values for clarity
                            $payment->received_amount = $receivedAmount;
                            $payment->balance = $totalAmount - $receivedAmount;
                            return true;
                        }

                        return false;
                    })
                    ->values();

                // ✅ Group payments by customer
                $grouped = $payments->groupBy(function ($p) {
                    $cityTitle = $p->customer?->city?->title ?? '';
                    return ($p->customer?->customer_name ?? 'Unknown') . ' | ' . $cityTitle;
                })
                ->map(function ($group, $customerKey) {
                    // Prepare totals
                    $totalAmount = $group->sum('amount');
                    $totalReceived = $group->sum('received_amount');
                    $totalBalance = $totalAmount - $totalReceived;

                    // Prepare simplified payment list
                    $paymentsArray = $group->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'method' => $p->method,
                            'date' => $p->method === 'cheque' ? $p->cheque_date : $p->slip_date,
                            'amount' => $p->amount,
                            'received_amount' => $p->received_amount,
                            'balance' => $p->balance,
                        ];
                    })->values();

                    return [
                        'customer' => $customerKey,
                        'payments' => $paymentsArray,
                        'totals' => [
                            'amount' => $totalAmount,
                            'received_amount' => $totalReceived,
                            'balance' => $totalBalance,
                        ],
                    ];
                })
                ->values();

                $data = $grouped;

                // return response()->json($data);
                return view("reports.pending-payments", compact('data'));
            }
        }

        return view("reports.pending-payments");
    }

}
