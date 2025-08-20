<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function statement(Request $request)
    {
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

                $data = $customer->getStatement($dateFrom, $dateTo);
                // return view("reports.statement", compact('data'));
                return response()->json($data);
            }

            if ($category === 'supplier') {
                // supplier ka similar logic (purchaseOrders + bills + supplierPayments)
            }
        }

        $mockData = [
            'date' => '01-01-2025 - 02-02-2025',
            'opening_balance' => 50000,
            'closing_balance' => 40000,
            'name' => 'Al-Shaka',
            'totals' => [
                'amount' => 40000,
                'payment' => 60000,
                'balance' => 50000,
            ],
            'statements' => [
                [
                    'date' => '00-00-00',
                    'reff_no' => 'dd141',
                    'type' => 'invoice',
                    'method' => 'Cash',
                    'amount' => 25000,
                    'account' => 'Al-Das',
                ],
                [
                    'date' => '00-00-00',
                    'reff_no' => 'dd141',
                    'type' => 'invoice',
                    'method' => 'Cash',
                    'amount' => 25000,
                    'account' => 'Al-Das',
                ]
            ]
        ];

        return view("reports.statement", compact('mockData'));
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
            })->get(); // select only needed fields
            return response()->json($customers);
        }

        if ($category === 'supplier') {
            $suppliers = Supplier::whereHas('user', function ($query) {
                $query->where('status', 'active');
            })->get();
            return response()->json($suppliers);
        }

        return response()->json(['error' => 'Invalid category'], 400);
    }
}
