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
        }else if ($request->customer_id && $request->article_id && $request->getInvoices) {
            $customer = Customer::find($request->customer_id);

            if ($customer) {
                $invoices = $customer->invoices()
                    ->select(['id', 'articles_in_invoice', 'invoice_no', 'date'])
                    ->get()
                    ->filter(function ($invoice) use ($request) {
                        return collect($invoice->articles_in_invoice)
                            ->pluck('id')
                            ->contains((int) $request->article_id);
                    })
                    ->map(function ($invoice) use ($request) {
                        // Keep only the requested article
                        $invoice->articles_in_invoice = collect($invoice->articles_in_invoice)
                            ->filter(function ($article) use ($request) {
                                return (int) $article['id'] === (int) $request->article_id;
                            })
                            ->values(); // reset array keys
                        return $invoice;
                    });

                return $invoices;
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $request->all();
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
