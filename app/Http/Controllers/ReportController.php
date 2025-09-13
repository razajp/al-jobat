<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function statement(Request $request)
    {
        // if (!empty($request)) {
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

                    return view("reports.statement", compact('data'));
                }

                if ($category === 'supplier') {
                    $supplier = Supplier::find($id);
                    if (!$supplier) {
                        return response()->json(['error' => 'Supplier not found'], 404);
                    }

                    $data = $supplier->getStatement($dateFrom, $dateTo);

                    return view("reports.statement", compact('data'));
                }

                if ($category === 'bank account') {
                    $bank_account = BankAccount::find($id);
                    if (!$bank_account) {
                        return response()->json(['error' => 'Bank account not found'], 404);
                    }

                    $data = $bank_account->getStatement($dateFrom, $dateTo);

                    return view("reports.statement", compact('data'));
                }
            }
        // }

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
            })->get(); // select only needed fields
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
}
