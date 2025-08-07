<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Employee;
use App\Models\Fabric;
use App\Models\Production;
use App\Models\Rate;
use App\Models\Setup;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        if (!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest', 'store_keeper'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        if (Auth::user()->production_type === 'issue') {
            $articles = Article::whereHas('production.work', function($query) {
                $query->where('title', 'Cutting');
            })->with('production.work')->get();
        } else {
            $articles = Article::whereNotNull('fabric_type')->whereNotNull('category')->with('production')->get();
        }
        $work_options = [];
        $workerTypes = Setup::where('type', 'worker_type')->get();
        foreach($workerTypes as $workerType) {
            $work_options[(int)$workerType->id] = [
                'text' => $workerType->title
            ];
        }
        $worker_options = [];
        $workers = Employee::with('type')->where('category', 'worker',)->where('status', 'active')->get();
        foreach($workers as $worker) {
            $worker['taags'] = $worker['tags']
                ->groupBy('tag')
                ->map(function ($items, $tag) use ($articles) {
                    $fabric = Fabric::where('tag', $tag)->first();

                    $supplier = null;
                    if ($fabric && $fabric->supplier_id) {
                        $supplier = Supplier::find($fabric->supplier_id);
                    }

                    $sum = $articles->flatMap->production
                        ->flatMap->tags
                        ->filter(fn($tagObj) => $tagObj['tag'] === $tag)
                        ->sum('quantity');

                    return [
                        'tag' => $tag,
                        'unit' => ucfirst($fabric->unit),
                        'available_quantity' => $items->sum('quantity') - $sum,
                        'supplier_name' => $supplier->supplier_name ?? null,
                    ];
                })->values();

            $worker_options[(int)$worker->id] = [
                'text' => $worker->employee_name,
                'data_option' => $worker->makeHidden('tags'),
            ];
        }

        $rates = Rate::with('type')->get();

        return view('productions.add', compact('articles', 'work_options', 'worker_options', 'rates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest', 'store_keeper'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        // return $request;

        $validator = Validator::make($request->all(), [
            'article_id' => 'required|integer|exists:articles,id',
            'work_id' => 'required|integer|exists:setups,id',
            'worker_id' => 'required|integer|exists:employees,id',
            'tags' => 'required|string',
            'quantity' => 'nullable|integer|min:1',
            'title' => 'nullable|string',
            'rate' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        $data['tags'] = json_decode($data['tags']);

        if ($request->quantity) {
            Article::where('id', $request->article_id)->update(['quantity' => $request->quantity]);
        }

        Production::create($data);

        $issueOrReceive = '';
        if ($request->issue_date) {
            $issueOrReceive = 'issue';
        } else {
            $issueOrReceive = 'receive';
        }

        return redirect()->route('productions.create')->with('success', 'Production ' . $issueOrReceive . ' successfully.');
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
