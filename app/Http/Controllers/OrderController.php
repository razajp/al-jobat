<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Customer;
use App\Models\Order;
use App\Models\PhysicalQuantity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = Order::with('customer')->get();

        // Collect all article IDs from ordered articles
        $articleIds = $orders->flatMap(function ($order) {
            return collect(json_decode($order->ordered_articles, true))->pluck('id');
        })->unique();

        // Fetch all required articles in a single query
        $articles = Article::whereIn('id', $articleIds)->get()->keyBy('id');

        $orders = $orders->transform(function ($order) use ($articles) {
            // Decode ordered_articles and ensure it's an array
            $orderedArticles = json_decode($order->ordered_articles, true) ?? [];

            // Map through each ordered article
            $order['ordered_articles'] = collect($orderedArticles)
                ->map(function ($orderedArticle) use ($articles) {
                    // Attach article details if available
                    if (isset($articles[$orderedArticle['id']])) {
                        $orderedArticle['article'] = $articles[$orderedArticle['id']];
                    }

                    // Subtract invoice quantity (prevent negative values)
                    $orderedArticle['ordered_quantity'] = max(0, $orderedArticle['ordered_quantity'] - ($orderedArticle['invoice_quantity'] ?? 0));

                    return $orderedArticle;
                })
                // Keep only articles with ordered_quantity > 0
                ->filter(function ($orderedArticle) {
                    return $orderedArticle['ordered_quantity'] > 0;
                });

            return $order;
        })
        // ✅ Filter orders: Only return orders with non-empty ordered_articles
        ->filter(function ($order) {
            return $order->ordered_articles->isNotEmpty();
        });

        foreach ($orders as $key => $order) {
            $order['previous_balance'] = $order->customer->calculateBalance(null, $order->date, false, false);
            $order['current_balance'] = $order['previous_balance'] + $order['netAmount'];
        }

        // Format the date and reset balances
        $orders->each(function ($order) {
            $order['date'] = date('d-M-Y, D', strtotime($order->date));
        });

        $authLayout = $this->getAuthLayout($request->route()->getName());

        return view('orders.index', compact('orders', 'authLayout'));
        // return $orders;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $customers_options = [];
        $articles = [];

        if ($request->date) {
            $customers = Customer::with('user', 'orders', 'payments')->where('date', '<=', $request->date)->get();

            foreach ($customers as $customer) {
                $user = User::where('id', $customer->user_id)->first();
                $customer['status'] = $user->status;
    
                if ($customer->status == 'active') {
                    foreach ($customer['orders'] as $order) {
                        $customer['totalAmount'] += $order->netAmount;
                    }
                    
                    foreach ($customer['payments'] as $payment) {
                        $customer['totalPayment'] += $payment->amount;
                    }
    
                    $customer['balance'] = $customer['balance'];
                    
                    $customers_options[(int)$customer->id] = [
                        'text' => $customer->customer_name . ' | ' . $customer->city,
                        'data_option' => $customer
                    ];
                }
            }
            
            $articles = Article::where('date', '<=', $request->date)->where('sales_rate', '>', 0)->whereRaw('ordered_quantity < quantity')->get();
    
            foreach ($articles as $article) {
                $physical_quantity = PhysicalQuantity::where('article_id', $article->id)->sum('packets');
                $article['physical_quantity'] = ( $physical_quantity * $article->pcs_per_packet ) - $article['sold_quantity'];
    
                $article["rates_array"] = json_decode($article->rates_array, true);
                $article['date'] = date('d-M-Y, D', strtotime($article['date']));
                $article['sales_rate'] = number_format($article['sales_rate'], 2, '.', ',');
            }
        }

        $last_order = Order::orderby('id', 'desc')->first();

        if (!$last_order) {
            $last_order = new Order();
            $last_order->order_no = '0000-0000';
        }

        return view('orders.generate', compact('customers_options', 'articles', 'last_order'));
        // return $articles;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'customer_id' => 'required|integer|exists:customers,id',
            'discount' => 'required|integer',
            'netAmount' => 'required|string',
            'ordered_articles' => 'required|json',
            'order_no' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        
        $data['netAmount'] = str_replace(',', '', $data['netAmount']);

        $order = Order::create($data);

        $data['ordered_articles'] = json_decode($data['ordered_articles'], true);
        foreach ($data['ordered_articles'] as $articleData) {
            $article = Article::where('id', $articleData['id'])->first();
            if ($article) {
                $article->ordered_quantity += $articleData['ordered_quantity'];
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
