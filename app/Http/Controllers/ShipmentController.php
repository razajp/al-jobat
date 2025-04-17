<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Customer;
use App\Models\Order;
use App\Models\PhysicalQuantity;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.'); 
        };

        $orders = Order::with('customer')->get();

        // Collect all article IDs from ordered articles
        $articleIds = $orders->flatMap(function ($order) {
            return collect(json_decode($order->articles, true))->pluck('id');
        })->unique();

        // Fetch all required articles in a single query
        $articles = Article::whereIn('id', $articleIds)->get()->keyBy('id');

        $orders = $orders->transform(function ($order) use ($articles) {
            // Step 1: Decode and normalize articles to indexed array
            $orderedArticlesRaw = json_decode($order->articles, true) ?? [];
            $orderedArticlesArray = array_values($orderedArticlesRaw); // Normalize to indexed array
        
            // Step 2: Map through each ordered article
            $orderedArticles = collect($orderedArticlesArray)->map(function ($orderedArticle) use ($articles) {
                if (isset($articles[$orderedArticle['id']])) {
                    $orderedArticle['article'] = $articles[$orderedArticle['id']];
                }
        
                $orderedArticle['ordered_quantity'] = max(0, $orderedArticle['ordered_quantity'] - ($orderedArticle['invoice_quantity'] ?? 0));
        
                return $orderedArticle;
            })->filter(function ($orderedArticle) {
                return $orderedArticle['ordered_quantity'] > 0;
            })->values(); // 👈 ensures final collection is indexed (not associative)
        
            // Step 3: Put it back into the order
            $order['articles'] = $orderedArticles;
        
            return $order;
        })
        ->filter(function ($order) {
            return $order['articles']->isNotEmpty();
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

        return view('shipments.index', compact('orders', 'authLayout'));
        // return $orders;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };
        
        $customers_options = [];
        $articles = [];

        if ($request->date) {
            $customers = Customer::with('user', 'orders', 'payments')->where('date', '<=', $request->date)->get();

            foreach ($customers as $customer) {
                $user = User::where('id', $customer->user_id)->first();
                $customer['status'] = $user->status;
    
                if ($customer->status == 'active') {
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

        $last_shipment = Shipment::orderby('id', 'desc')->first();

        if (!$last_shipment) {
            $last_shipment = new Shipment();
            $last_shipment->shipment_no = '0000';
        }

        return view('shipments.generate', compact('customers_options', 'articles', 'last_shipment'));
        // return $articles;
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
        
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'discount' => 'required|integer',
            'netAmount' => 'required|string',
            'articles' => 'required|json',
            'shipment_no' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        
        $data['netAmount'] = str_replace(',', '', $data['netAmount']);

        $shipment = Shipment::create($data);
        
        return redirect()->route('shipments.create')->with('success', 'Shipment generated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipment $shipment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipment $shipment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        //
    }
}
