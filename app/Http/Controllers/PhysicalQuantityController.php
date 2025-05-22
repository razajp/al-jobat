<?php

namespace App\Http\Controllers;

use App\Events\NewNotificationEvent;
use App\Models\Article;
use App\Models\PhysicalQuantity;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhysicalQuantityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $allQuantities = PhysicalQuantity::with('article')->get();
        $allShipments = Shipment::with('invoices.customer')->get();

        // Group all records by id
        $grouped = $allQuantities->groupBy('id')->map(function ($group) {
            $first = $group->first();
            $article = $first->article;

            // Initialize packet sums
            $categoryA = $group->where('category', 'a')->sum('packets');
            $categoryB = $group->where('category', 'b')->sum('packets');
            $total = $categoryA + $categoryB;

            // Get the latest date across both categories
            $latestDate = $group->max('date');

            return (object)[
                'id' => $article->id,
                'article' => $article,
                'total_packets' => $total,
                'current_stock' => $total - ($article->sold_quantity / $article->pcs_per_packet),
                'a_category' => $categoryA,
                'b_category' => $categoryB,
                'latest_date' => $latestDate,
                'date' => date('d-M-y, D', strtotime($latestDate)),
            ];
        })->values();

        $shipment = '';

        foreach ($allShipments as $shipment) {
            $shipment['articles'] = $shipment->getArticles();

            foreach ($shipment['articles'] as $article) {
                foreach ($grouped as $group) {
                    if ($article['article']['id'] == $group->article->id) {
                        // Append city title to group->city
                        $cityTitle = $shipment->city;

                        if (!isset($group->city)) {
                            $group->city = [];
                        }

                        if (!in_array($cityTitle, $group->city)) {
                            $group->city[] = $cityTitle;
                        }
                    }
                }
            }
        }

        // After collecting city titles, decide shipment type
        foreach ($grouped as $group) {
            $cities = $group->city ?? [];

            $hasKarachi = in_array('karachi', $cities);
            $hasOther = count(array_filter($cities, fn($c) => $c !== 'karachi' && $c !== 'all')) > 0;
            $hasAll = in_array('all', $cities);

            if ($hasAll || ($hasKarachi && $hasOther)) {
                $group->shipment = 'All';
            } elseif ($hasKarachi) {
                $group->shipment = 'Karachi';
            } elseif ($hasOther) {
                $group->shipment = 'Other';
            } else {
                $group->shipment = null; // Or any default
            }
        }

        return view('physical-quantities.index', [
            'physicalQuantities' => $grouped
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };
        
        $articles = Article::withSum('physicalQuantity', 'packets')->get();
        
        foreach ($articles as $article) {
            $physical_quantity = $article['physical_quantity_sum_packets'];

            if ($physical_quantity) {
                $article['physical_quantity'] = $physical_quantity * $article->pcs_per_packet;
            } else {
                $article['physical_quantity'] = 0;
            }

            $article['category'] = ucfirst(str_replace('_', ' ', $article['category']));
            $article['season'] = ucfirst(str_replace('_', ' ', $article['season']));
            $article['size'] = ucfirst(str_replace('_', '-', $article['size']));
        }

        $articles = $articles->filter(function ($article) {
            return $article['physical_quantity'] < $article->quantity; // Keep articles with lesser physical quantity
        });

        return view('physical-quantities.create', compact('articles'));
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
            'article_id' => 'required|integer|exists:articles,id',
            'processed_by' => 'required|string',
            'pcs_per_packet' => 'required|integer|min:1',
            'packets' => 'required|integer|min:1',
        ]);
        
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $data = $request->all();
        
        $article = Article::where('id', $data['article_id'])->update([
            'pcs_per_packet' => $data['pcs_per_packet'],
            'processed_by' => $data['processed_by'],
        ]);

        PhysicalQuantity::create($data);

        return redirect()->route('physical-quantities.create')->with('success', 'Physical quantity added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        // 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhysicalQuantity $physicalQuantity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PhysicalQuantity $physicalQuantity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PhysicalQuantity $physicalQuantity)
    {
        //
    }
}
