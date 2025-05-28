<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Setup;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant', 'guest'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $expenses = Expense::with('supplier')->with('supplier')->get();

        $authLayout = $this->getAuthLayout($request->route()->getName());

        return view('expenses.index', compact('expenses', 'authLayout'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $subCategories = [
            'MTR' => [
                "fusing" => "Fusing",
                "garments_material" => "Garments Marital",
                "oil_expense" => "Oil Expense",
                "poly_bag" => "Poly Bag",
                "rib/collar" => "Rib / Collar",
                "thread" => "Thread",
            ],
            'EMB' => [
                "embroidery" => "Embroidery",
            ],
            'CMT' => [
                "cmt" => "CMT",
            ],
            'FBR' => [
                "fabric" => "Fabric",
            ],
        ];

        $lastExpense = Expense::latest()->with("supplier")->first();

        $suppliers = Supplier::whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->get();

        $suppliers_options = [];
        foreach ($suppliers as $supplier) {
            $suppliers_options[$supplier->id] = ["text" => $supplier->supplier_name, "data_option" => $supplier];
        }

        foreach ($suppliers as $supplier) {
            $categoriesIdArray = json_decode($supplier->categories_array, true);

            $categories = Setup::whereIn('id', $categoriesIdArray)
                ->where('type', 'supplier_category')
                ->get();

            $supplier["categories"] = $categories;

            $supplier["balance"] = 0.00;
        }

        return view('expenses.add', compact('suppliers_options', 'subCategories', 'lastExpense'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'expense' => 'required|string|max:255',
            'reff_no' => 'required|integer',
            'amount' => 'required|integer|min:0',
            'lot_no' => 'nullable|integer',
            'remarks' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $expense = Expense::create($request->all());

        return redirect()->back()->with('success', 'Expense added successfully.');
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
