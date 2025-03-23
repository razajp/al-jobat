<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PhysicalQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $invoices = Invoice::with(['order.customer'])->get();
    
        foreach ($invoices as $invoice) {
            $invoice['articles_in_invoice'] = json_decode($invoice->articles_in_invoice, true);

            $articles = [];
    
            foreach ($invoice->articles_in_invoice as $article_in_invoice) {
                $article = Article::find($article_in_invoice['id']);
    
                $articles[] = [
                    'article' => $article,
                    'description' => $article_in_invoice['description'],
                    'invoice_quantity' => $article_in_invoice['invoice_quantity'],
                ];
            }
            $invoice['articles'] = $articles;
            $invoice['date'] = date('d-M-Y, D', strtotime($invoice['date']));
        }

        $authLayout = $this->getAuthLayout($request->route()->getName());
        
        return view('invoices.index', compact('invoices', 'authLayout'));
    }    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $last_Invoice = invoice::orderby('id', 'desc')->first();

        if (!$last_Invoice) {
            $last_Invoice = new invoice();
            $last_Invoice->invoice_no = '0000-0000';
        }

        return view("invoices.generate", compact("last_Invoice"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "invoice_no" => "required|string|unique:invoices,invoice_no",
            "order_no" => "required|string|exists:orders,order_no",
            "date" => "required|date",
            "netAmount" => "required|string",
            "articles_in_invoice" => "required|string",
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $data = $request->all();

        $articles = json_decode($data["articles_in_invoice"], true);

        foreach ($articles as $article) {
            $articleDb = Article::where("id", $article["id"])->increment('sold_quantity', $article["invoice_quantity"]);
        }

        foreach ($articles as $article) {
            $orderDb = Order::where("order_no", $data["order_no"])->first();
            $orderedArticleDb = json_decode($orderDb["ordered_articles"], true);

            // Update all matching articles
            foreach ($orderedArticleDb as &$orderedArticle) { // Pass by reference to modify in place
                if (isset($orderedArticle["id"]) && $orderedArticle["id"] == $article["id"]) {
                    // Update invoice_quantity without overwriting existing value
                    $orderedArticle["invoice_quantity"] = ($orderedArticle["invoice_quantity"] ?? 0) + $article["invoice_quantity"];
                }
            }

            // Save updated ordered_articles back to the database
            $orderDb->ordered_articles = json_encode($orderedArticleDb);
            $orderDb->save();
        }

        $data["netAmount"] = (int) str_replace(',', '', $data["netAmount"]);
        
        Invoice::create($data);

        return redirect()->route('invoices.create')->with('success', 'Invoice generated successfully.');
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

        $orderedArticles = array_filter($orderedArticles, function ($orderedArticle) {
            $article = Article::find($orderedArticle->id);
            $orderedArticle->article = $article;
        
            $totalPhysicalStockPackets = PhysicalQuantity::where("article_id", $article->id)->sum('packets');
            $orderedArticle->total_quantity_in_packets = 0;
            
            if ($totalPhysicalStockPackets > 0 && $article->pcs_per_packet > 0) {
                $avalaibelPhysicalQuantity = $article->sold_quantity > 0 ? $totalPhysicalStockPackets - ($article->sold_quantity / $article->pcs_per_packet) : $totalPhysicalStockPackets;
                $orderedPackets = $orderedArticle->ordered_quantity / $article->pcs_per_packet;
                $pendingPackets = isset($orderedArticle->invoice_quantity) ? $orderedPackets - ($orderedArticle->invoice_quantity / $article->pcs_per_packet) : $orderedPackets;

                $orderedArticle->total_quantity_in_packets = $avalaibelPhysicalQuantity > $pendingPackets ? $pendingPackets : $avalaibelPhysicalQuantity;
            }
        
            return $orderedArticle->total_quantity_in_packets;
        });

        $order->ordered_articles = $orderedArticles;

        if (count($order->ordered_articles) == 0) {
            $order = ['error' => 'data not found'];
        }

        return response()->json($order);
    }
}
