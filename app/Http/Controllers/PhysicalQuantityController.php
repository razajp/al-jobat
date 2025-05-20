<?php

namespace App\Http\Controllers;

use App\Events\NewNotificationEvent;
use App\Models\Article;
use App\Models\PhysicalQuantity;
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

        // Group total packets by article (all categories combined)
        $totalPacketsByArticle = $allQuantities
            ->groupBy('article_id')
            ->map(fn($group) => $group->sum('packets'));

        // Now group by article_id + category to show each row
        $grouped = $allQuantities
            ->groupBy(fn($item) => $item->article_id . '-' . $item->category)
            ->map(function ($group) use ($totalPacketsByArticle) {
                $first = $group->first();
                $articleId = $first->article_id;
                $pcsPerPacket = $first->article->pcs_per_packet;

                return (object)[
                    'id' => $first->id,
                    'article' => $first->article,
                    'category' => $first->category,
                    'total_packets_this_category' => $group->sum('packets'),
                    'total_packets_all_categories' => $totalPacketsByArticle[$articleId],
                    'latest_date' => $group->max('date'),
                ];
            })->values();

        foreach ($grouped as $item) {
            $item->date = date('d-M-y, D', strtotime($item->latest_date));
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
        
        $articles = Article::withSum('physicalQuantity', 'packets')->where('sales_rate', '>', '0')->get();
        
        foreach ($articles as $article) {
            $physical_quantity = $article['physical_quantity_sum_packets'];

            if ($physical_quantity) {
                $article['physical_quantity'] = $physical_quantity * $article->pcs_per_packet;
            } else {
                $article['physical_quantity'] = 0;
            }
            $article["rates_array"] = json_decode($article->rates_array, true);
            $article['date'] = date('d-M-Y, D', strtotime($article['date']));
            $article['sales_rate'] = number_format($article['sales_rate'], 2, '.', ',');
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
