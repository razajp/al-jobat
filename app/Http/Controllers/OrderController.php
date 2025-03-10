<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
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
        $customers = Customer::all();

        $customers_options = [];
        foreach ($customers as $customer) {
            $customers_options[(int)$customer->id] = $customer->customer_name . ' | ' . $customer->city;
        }

        $articles = Article::all();

        foreach ($articles as $article) {
            $article["rates_array"] = json_decode($article->rates_array, true);
            $article['date'] = date('d-M-Y, D', strtotime($article['date']));
            $article['sales_rate'] = number_format($article['sales_rate'], 2, '.', ',');
        }

        return view('orders.generate', compact('customers_options', 'articles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'customer_id' => 'required|integer|exists:customers,id',
            'ordered_articles' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $data = $request->all();

        $order = Order::create($data);

        $data['ordered_articles'] = json_decode($data['ordered_articles'], true);
        foreach ($data['ordered_articles'] as $articleData) {
            $article = Article::where('id', $articleData['id'])->first();
            if ($article) {
                $article->sold_quantity += $articleData['ordered_quantity'];
                $article->save();
            }
        }
        
        return redirect()->route('orders.create')->with('success', 'Order generated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
