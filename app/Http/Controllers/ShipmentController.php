<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PhysicalQuantity;
use App\Models\Setup;
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
        if(!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $shipments = Shipment::all();

        if ($shipments->isNotEmpty()) {
            foreach ($shipments as $shipment) {
                // Articles attach kar do
                $shipment->articles = $shipment->getArticles();

                // Invoice exist check
                $shipment->isInvoiceHas = Invoice::where('shipment_no', $shipment->shipment_no)->exists();
            }
        }

        $authLayout = $this->getAuthLayout($request->route()->getName());

        return view('shipments.index', compact('shipments', 'authLayout'));
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
            $customers = Customer::with('user', 'orders', 'payments', 'city')->where('date', '<=', $request->date)->get();

            foreach ($customers as $customer) {
                $user = User::where('id', $customer->user_id)->first();
                $customer['status'] = $user->status;

                if ($customer->status == 'active') {
                    $customers_options[(int)$customer->id] = [
                        'text' => $customer->customer_name . ' | ' . $customer->city->title,
                        'data_option' => $customer
                    ];
                }
            }

            $articles = Article::where('date', '<=', $request->date)->where('sales_rate', '>', 0)->whereNotNull(['category', 'fabric_type'])->whereRaw('ordered_quantity < quantity')->get();

            foreach ($articles as $article) {
                $physical_quantity = PhysicalQuantity::where('article_id', $article->id)->sum('packets');
                $article['physical_quantity'] = ( $physical_quantity * $article->pcs_per_packet ) - $article['sold_quantity'];
            }
        }

        $last_shipment = Shipment::orderby('id', 'desc')->first();

        if (!$last_shipment) {
            $last_shipment = new Shipment();
            $last_shipment->shipment_no = '0000';
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'articles' => $articles
            ]);
        }

        return view('shipments.generate', compact('customers_options', 'last_shipment'));
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

        // return $request->all();

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'discount' => 'required|integer',
            'netAmount' => 'required|string',
            'articles' => 'required|json',
            'city' => 'required|string',
            'shipment_no' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        $data['netAmount'] = str_replace(',', '', $data['netAmount']);
        $data['articles'] = json_decode($data['articles'], true);

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
        if (!$this->checkRole(['developer', 'owner', 'admin'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $selectedArticles = [];

        foreach ($shipment->articles as $rawArticle) {
            $article = Article::find($rawArticle['id']);

            $article['description'] = $rawArticle['description'];
            $article['shipmentQuantity'] = $rawArticle['shipment_quantity'];

            $selectedArticles[] = $article;
        }

        $shipment['selectedArticles'] = $selectedArticles;

        return view('shipments.edit', compact('shipment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipment $shipment)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $validator = Validator::make($request->all(), [
            'netAmount' => 'required|string',
            'articles' => 'required|json',
            'city' => 'required|string',
        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        $data['netAmount'] = str_replace(',', '', $data['netAmount']);
        $data['articles'] = json_decode($data['articles'], true);

        // Update the shipment
        $shipment->update($data);

        return redirect()->route('shipments.index')->with('success', 'Shipment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        //
    }
}
