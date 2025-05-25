<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ExpenseController extends Controller
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
        $subCategories = [
            'material' => [
                "adjustment" => "Adjustment",
                "fusing" => "Fusing",
                "garments_material" => "Garments Marital",
                "oil_expense" => "Oil Expense",
                "poly_bag" => "Poly Bag",
                "rib/collar" => "Rib / Collar",
                "thread" => "Thread",
            ],
            'embroidery' => [
                "embroidery" => "Embroidery",
            ],
            'fabric' => [
                "fabric" => "Fabric",
            ],
        ];
        
        $suppliers = Supplier::whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->get();

        $suppliers_options = [];
        foreach ($suppliers as $supplier) {
            $suppliers_options[$supplier->id] = ["text" => $supplier->supplier_name, "data_option" => $supplier];
        }

        return view('expenses.add', compact('suppliers_options', 'subCategories'));
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
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        //
    }

    public function getSupplierData(Request $request)
    {
        return "done hai";
    }
}
