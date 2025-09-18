<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Customer;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                $salesRate = Article::find($request->article_id)?->sales_rate;
                $invoices = $customer->invoices()
                    ->with('order', 'shipment')
                    ->get()
                    ->filter(function ($invoice) use ($request) {
                        return collect($invoice->articles_in_invoice)
                            ->pluck('id')
                            ->contains((int) $request->article_id);
                    })
                    ->map(function ($invoice) use ($request, $salesRate) {
                        // Keep only the requested article
                        $articles = collect($invoice->articles_in_invoice)
                            ->filter(fn($article) => (int) $article['id'] === (int) $request->article_id)
                            ->values();

                        return [
                            'id' => $invoice->id,
                            'invoice_no' => $invoice->invoice_no,
                            'date' => $invoice->date,
                            'articles_in_invoice' => $articles,
                            'discount' => optional($invoice->order)->discount
                                        ?? optional($invoice->shipment)->discount,
                            'sales_rate' => $salesRate,
                        ];
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
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        // return $request;

        $validator = Validator::make($request->all(), [
            'article' => 'required|integer|exists:articles,id',
            'invoice' => 'required|integer|exists:invoices,id',
            'date' => 'required|date',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'article' => $request->article,
            'invoice' => $request->invoice,
            'date' => $request->date,
            'quantity' => $request->quantity,
            'amount' => '',
        ];

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
