<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\invoice;
use App\Models\Order;
use App\Models\PhysicalQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
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
        // $last_Invoice = invoice::orderby('id', 'desc')->first();

        // if (!$last_Invoice) {
            $last_Invoice = new invoice();
            $last_Invoice->invoice_no = '0000-0000';
        // }

        return view("invoices.generate", compact("last_Invoice"));
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
    public function show(invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(invoice $invoice)
    {
        //
    }

    public function getOrderDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "order_no" => "required|exists:orders,order_no",
        ]);
        
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()->first()]);
        }
        
        $order = Order::with('customer')->where("order_no", $request->order_no)->first();
        $order->ordered_articles = json_decode($order->ordered_articles);

        $orderedArticles = $order->ordered_articles;
        
        foreach ($orderedArticles as $orderedArticle) {
            $article = Article::find($orderedArticle->id);
            $orderedArticle->article = $article;

            $totalPhysicalStockPackets = PhysicalQuantity::where("article_id", $article->id)->sum('packets');
            $orderedArticle->total_physical_stock_packets = $totalPhysicalStockPackets;
        }

        $order->ordered_articles = $orderedArticles;

        return response()->json($order);
    }
}
