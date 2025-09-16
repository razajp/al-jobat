<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Customer;
use App\Models\SalesReturn;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $customerOptions = [
        //     'guest' => ['text' => 'Guest'],
        //     'accountant' => ['text' => 'Accountant'],
        //     'store_keeper' => ['text' => 'Store Keeper '],
        // ];

        $customers = Customer::whereHas('user', function ($query) {
                    $query->where('status', 'active');
                })->get()->makeHidden('creator');

        $customerOptions = $customers->mapWithKeys(function ($customer) {
            return [$customer->id => ['text' => $customer->customer_name]];
        })->toArray();

        return view('sales-return.return', compact('customerOptions'));
    }

    public function getDetails(Request $request)
    {
        if ($request->customer_id && $request->getArticles) {
            $articles = Article::where('sold_quantity', '>', 0)->select(['id', 'article_no'])->get();

            return $articles;
        } else if ($request->customer_id && $request->article_id && $request->getInvoices) {
            $customer = Customer::find($request->customer_id);

            if ($customer) {
                $invoices = $customer->invoices()->get()->filter(function ($invoice) use ($request) {
                    return collect($invoice->articles_in_invoice)
                        ->pluck('id')
                        ->contains((int) $request->article_id);
                });

                return [$invoices, $request->article_id, $customer->invoices()->get()];
            }
        }
        // $customerId = $request->input('customer_id');

        // $customer = Customer::with(['user' => function ($query) {
        //     $query->where('status', 'active');
        // }])->find($customerId);

        // if ($customer) {
        //     return response()->json([
        //         'status' => 'success',
        //         'data' => [
        //             'customer_name' => $customer->customer_name,
        //             'contact_person' => $customer->contact_person,
        //             'phone' => $customer->phone,
        //             'email' => $customer->email,
        //             'address' => $customer->address,
        //         ],
        //     ]);
        // } else {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Customer not found or inactive.',
        //     ], 404);
        // }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesReturn $salesReturn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesReturn $salesReturn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesReturn $salesReturn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesReturn $salesReturn)
    {
        //
    }
}
