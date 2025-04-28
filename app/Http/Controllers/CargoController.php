<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Invoice;
use Illuminate\Http\Request;

class CargoController extends Controller
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
        $invoices = Invoice::with('customer')->whereNotNull('shipment_no')->get();

        $last_cargo = [];
        // $last_cargo = Cargo::orderby('id', 'desc')->first();

        if (!$last_cargo) {
            $last_cargo = new Cargo();
            $last_cargo->cargo_no = '0000';
        }
        
        return view('cargos.generate', compact('invoices', 'last_cargo'));
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
    public function show(Cargo $cargo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cargo $cargo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cargo $cargo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cargo $cargo)
    {
        //
    }
}
