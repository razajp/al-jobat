<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\PhysicalQuantity;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PhysicalQuantityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest', 'store_keeper'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $allQuantities = PhysicalQuantity::with('article')->get();
        $allShipments = Shipment::with('invoices.customer')->get();

        // 🔹 Group by article_id (not id)
        $grouped = $allQuantities->groupBy('article_id')->map(function ($group) {
            $first = $group->first();
            $article = $first->article;

            // Category-wise packets
            $categoryA = $group->where('category', 'a')->sum('packets');
            $categoryB = $group->where('category', 'b')->sum('packets');
            $categoryC = $group->where('category', 'c')->sum('packets');
            $total = $categoryA + $categoryB + $categoryC;

            $latestDate = $group->max('date');

            return (object)[
                'article_id' => $article->id,
                'article' => $article,
                'a_category' => $categoryA,
                'b_category' => $categoryB,
                'c_category' => $categoryC,
                'total_packets' => $total,
                'current_stock' => $total - ($article->sold_quantity / $article->pcs_per_packet),
                'latest_date' => $latestDate,
                'date' => date('d-M-y, D', strtotime($latestDate)),
            ];
        })->values();

        // 🔹 Attach shipment info
        foreach ($allShipments as $shipment) {
            $shipment['articles'] = $shipment->getArticles();

            foreach ($shipment['articles'] as $article) {
                foreach ($grouped as $group) {
                    if ($article['article']['id'] == $group->article_id) {
                        $cityTitle = strtolower($shipment->city);

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

        // 🔹 Determine shipment type per article
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
                $group->shipment = null;
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
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant', 'store_keeper']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $articles = Article::withSum('physicalQuantity', 'packets')
            // ->whereHas('production.work', function ($q) {
            //     $q->where('title', 'CMT');
            // })
            ->orderByDesc('id')
            ->get();

        foreach ($articles as $article) {
            $physical_quantity = $article['physical_quantity_sum_packets'];

            $article['physical_quantity'] = $physical_quantity
                ? $physical_quantity * $article->pcs_per_packet
                : 0;

            $article['category'] = ucfirst(str_replace('_', ' ', $article['category']));
            $article['season']  = ucfirst(str_replace('_', ' ', $article['season']));
            $article['size']    = ucfirst(str_replace('_', '-', $article['size']));
        }

        $articles = $articles->filter(function ($article) {
            return $article['physical_quantity'] < $article->quantity;
        });

        return view('physical-quantities.create', compact('articles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant', 'store_keeper']))
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
