<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function statement(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'category' => 'required|in:customer,supplier',
        //     'id' => 'required',
        //     'from_date' => 'required|date',
        //     'to_date' => 'required|date|after_or_equal:from_date',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()->first()]);
        // }

        // $customer = Customer::find($request->customer_id);

        // if (!$customer) {
        //     return response()->json(['error' => 'Customer not found']);
        // }

        // // Logic to create statement goes here
        // // ...

        // return response()->json(['status' => 'Statement created successfully']);

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
            })->get(['id', 'name']); // select only needed fields
            return response()->json($customers);
        }

        if ($category === 'supplier') {
            $suppliers = Supplier::whereHas('user', function ($query) {
                $query->where('status', 'active');
            })->get(['id', 'name']);
            return response()->json($suppliers);
        }

        return response()->json(['error' => 'Invalid category'], 400);
    }
}
