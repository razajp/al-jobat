<?php

namespace App\Http\Controllers;

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
        if(!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.'); 
        };
        
        $physicalQuantities = PhysicalQuantity::with('article')->get();
        
        foreach ($physicalQuantities as $physicalQuantity) {
            $physicalQuantity['date'] = date('d-M-y, D', strtotime($physicalQuantity['date']));
        }

        return view("physical-quantities.index", compact("physicalQuantities"));
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

        PhysicalQuantity::create([
            'date' => $data['date'],
            'article_id' => $data['article_id'],
            'packets' => $data['packets'],
        ]);

        return redirect()->route('physical-quantities.create')->with('success', 'Physical quantity added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(PhysicalQuantity $physicalQuantity)
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
