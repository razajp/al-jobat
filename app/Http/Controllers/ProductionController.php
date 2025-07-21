<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Employee;
use App\Models\Fabric;
use App\Models\Production;
use App\Models\Setup;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductionController extends Controller
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
        $articles = Article::all();
        $worke_options = [];
        $workerTypes = Setup::where('type', 'worker_type')->get();
        foreach($workerTypes as $workerType) {
            $worke_options[(int)$workerType->id] = [
                'text' => $workerType->title
            ];
        }
        $worker_options = [];
        $workers = Employee::where('category', 'worker',)->where('status', 'active')->get();
        foreach($workers as $worker) {
            $worker['taags'] = $worker['tags']
                ->groupBy('tag')
                ->map(function ($items, $tag) {
                    $fabric = Fabric::where('tag', $tag)->first();

                    $supplier = null;
                    if ($fabric && $fabric->supplier_id) {
                        $supplier = Supplier::find($fabric->supplier_id);
                    }

                    return [
                        'tag' => $tag,
                        'total_quantity' => $items->sum('quantity'),
                        'supplier_name' => $supplier->supplier_name ?? null,
                    ];
                })->values();
                
            $worker_options[(int)$worker->id] = [
                'text' => $worker->employee_name,
                'data_option' => $worker->makeHidden('tags'),
            ];
        }
        return view('productions.add', compact('articles', 'worke_options', 'worker_options'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Production $production)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Production $production)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Production $production)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Production $production)
    {
        //
    }
}
