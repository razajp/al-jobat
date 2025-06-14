<?php

namespace App\Http\Controllers;

use App\Models\Fabric;
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
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $lastRecord = Fabric::latest()->with('supplier', 'fabric', 'color')->first();

        $suppliers = Supplier::whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->get();

        $suppliers_options = [];
        foreach ($suppliers as $supplier) {
            $suppliers_options[$supplier->id] = ["text" => $supplier->supplier_name, "data_option" => $supplier];
        }

        $fabrics_options = [];

        $fabrics = Setup::where('type', 'fabric')->get();
        foreach ($fabrics as $fabric) {
            $fabrics_options[$fabric->id] = ["text" => $fabric->title, "data_option" => $fabric];
        }

        $colors_options = [];

        $fabric_colors = Setup::where('type', 'fabric_color')->get();
        foreach ($fabric_colors as $fabric_color) {
            $colors_options[$fabric_color->id] = ["text" => $fabric_color->title, "data_option" => $fabric_color];
        }

        return view('fabrics.add', compact('lastRecord', 'suppliers_options', 'fabrics_options', 'colors_options'));
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
            'color_id' => 'required|exists:setups,id',
            'unit' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'reff_no' => 'nullable|integer',
            'remarks' => 'nullable|string|max:255',
            'tag' => 'required|string|max:255',
        ]);

        Fabric::create($request->all());

        return redirect()->route('fabrics.index')->with('success', 'Fabric added successfully.');
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
}
