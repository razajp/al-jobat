<?php

namespace App\Http\Controllers;

use App\Models\Setup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Suppliers = Supplier::with('user')->get();
    
        foreach ($Suppliers as $supplier) {
            // Decode JSON array of category IDs
            $categoriesIdArray = json_decode($supplier->categories_array, true);
    
            // Fetch categories from Setups model
            $categories = Setup::whereIn('id', $categoriesIdArray)
                ->where('type', 'supplier_category')
                ->pluck('title')  // Assuming you want the 'name' field of categories
                ->toArray();
    
            // Attach the categories to the supplier
            $supplier["categories"] = $categories;
        }
    
        // return $suppliers;
        return view("suppliers.index", compact('Suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $supplier_categories = Setup::where('type','supplier_category')->get();

        $categories_options = [];
        foreach ($supplier_categories as $supplier_category) {
            $categories_options[(int)$supplier_category->id] = $supplier_category->title;
        }

        // return $categories_options;

        return view('suppliers.create', compact('categories_options'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255|unique:suppliers,supplier_name',
            'person_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:3',
            'phone_number' => 'required|string|max:255',
            'image_upload' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'date' => 'required|string',
            'categories_array' => 'required|json',
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
            
        $data = $request->all();
        $data['password'] = Hash::make($data['password']); // Hash the password

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            // Upload the image if provided
            if ($request->hasFile('image_upload')) {
                $image = $request->file('image_upload');
                $image_name = time(). '.'. $image->getClientOriginalExtension();
                $image->move(public_path('uploads/suppliers'), $image_name);
                $data['image'] = $image_name;
            } else {
                $data['image'] = "default_avatar.png";
            }

            $user = User::create([
                'name' => $data['supplier_name'],
                'username' => $data['username'],
                'password' => $data['password'],
                'role' =>'supplier',
                'profile_picture' => $data['image'],
            ]);
        } else {
            return redirect()->back()->with('error', 'The user is already exists.')->withInput();
        }

        // Create a new supplier
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'supplier_name' => $user->name,
            'person_name' => $data['person_name'],
            'phone_number' => $data['phone_number'],
            'date' => $data['date'],
            'categories_array' => $data['categories_array'],
        ]);
        
        return redirect()->route('suppliers.create')->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}