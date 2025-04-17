<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Customer;
use App\Models\Order;
use App\Models\PhysicalQuantity;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;

class ShipmentController extends Controller
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

        // $last_shipment = Shipment::orderby('id', 'desc')->first();
        $last_shipment = [];

        if (!$last_shipment) {
            $last_shipment = new Shipment();
            $last_shipment->shipment_no = '0000-0000';
        }

        return view('shipments.generate', compact('customers_options', 'articles', 'last_shipment'));
        // return $articles;
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
