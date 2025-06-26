<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Fabric;
use App\Models\IssuedFabric;
use App\Models\Setup;
use App\Models\Supplier;
use Illuminate\Http\Request;

class FabricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest', 'store_keeper'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        // Added fabric entries
        $addedFabrics = Fabric::with('supplier', 'fabric')->get()->map(function ($fabric) {
            return [
                'id' => $fabric->id,
                'type' => 'Recived',
                'tag' => $fabric->tag,
                'quantity' => $fabric->quantity,
                'date' => $fabric->date, // Logical fabric addition date
                'supplier_name' => $fabric->supplier->supplier_name,
                'fabric' => $fabric->fabric->title,
                'color' => $fabric->color,
                'unit' => $fabric->unit,
                'remarks' => $fabric->remarks,
                'created_at' => $fabric->created_at,
            ];
        })->toArray();

        // Issued fabric entries
        $issuedFabrics = IssuedFabric::with('worker')->get()->map(function ($issue) {
            return [
                'id' => $issue->id,
                'type' => 'Issued',
                'tag' => $issue->tag,
                'quantity' => $issue->quantity,
                'date' => $issue->date, // Logical fabric issue date
                'employee_name' => $issue->worker->employee_name,
                'remarks' => $issue->remarks,
                'created_at' => $issue->created_at,
            ];
        })->toArray();

        // Combine both arrays manually
        $finalData = array_merge($issuedFabrics, $addedFabrics);

        // Sort the final combined array by date and then by created_at time (both descending)
        usort($finalData, function ($a, $b) {
            if ($a['date'] == $b['date']) {
                return strtotime($b['created_at']) - strtotime($a['created_at']); // time DESC
            }
            return strtotime($b['date']) - strtotime($a['date']); // date DESC
        });

        return view('fabrics.index', compact('finalData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $lastRecord = Fabric::latest()->with('supplier', 'fabric')->first();

        $fabricCategory = Setup::where('title', 'Fabric')->first();

        $suppliers = Supplier::whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->get();

        if ($fabricCategory) {
            $suppliers = $suppliers->filter(function ($supplier) use ($fabricCategory) {
                $ids = json_decode($supplier->categories_array, true);
                return is_array($ids) && in_array($fabricCategory->id, $ids);
            });
        }

        $suppliers_options = [];
        foreach ($suppliers as $supplier) {
            $suppliers_options[$supplier->id] = ["text" => $supplier->supplier_name, "data_option" => $supplier];
        }

        $fabrics_options = [];

        $fabrics = Setup::where('type', 'fabric')->get();
        foreach ($fabrics as $fabric) {
            $fabrics_options[$fabric->id] = ["text" => $fabric->title, "data_option" => $fabric];
        }

        return view('fabrics.add', compact('lastRecord', 'suppliers_options', 'fabrics_options'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'fabric_id' => 'required|exists:setups,id',
            'color' => 'required|string',
            'unit' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'reff_no' => 'nullable|integer',
            'remarks' => 'nullable|string|max:255',
            'tag' => 'required|string|max:255',
        ]);

        Fabric::create($request->all());

        return redirect()->route('fabrics.create')->with('success', 'Fabric added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fabric $fabric)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fabric $fabric)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fabric $fabric)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fabric $fabric)
    {
        //
    }

    public function issue()
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $tags_options = [];

        $all_fabrics = Fabric::all()
            ->groupBy('tag')
            ->map(function ($items) {
                return [ 
                    'tag' => $items->first()->tag,
                    'unit' => $items->first()->unit,
                    'quantity' => $items->sum('quantity'),
                ];
            })
            ->values();

        foreach($all_fabrics as $fabric) {
            $total_issued = IssuedFabric::where('tag', $fabric['tag'])->sum('quantity') ?? 0;
            $fabric['avalaible_sock'] = $fabric['quantity'] - $total_issued;
            $tags_options[$fabric['tag']] = ['text' => $fabric['tag'], "data_option" => json_encode($fabric)];
        }

        $workers_options = [];

        $all_workers = Employee::with('type')
            ->whereHas('type', function ($query) {
                $query->where('title', 'Cutting');
            })
            ->get();

        foreach ($all_workers as $worker) {
            $workers_options[$worker->id] = ['text' => $worker->employee_name];
        }

        return view('fabrics.issue', compact('tags_options', 'workers_options'));
    }

    public function issuePost(Request $request) {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $request->validate([
            'date' => 'required|date',
            'tag' => 'required|string|max:255',
            'worker_id' => 'required|exists:employees,id',
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:255',
        ]);

        IssuedFabric::create($request->all());

        return redirect()->route('fabrics.issue')->with('success', 'Fabric added successfully.');
    }

    public function summary() {
        return view('fabrics.summary');
    }
}
