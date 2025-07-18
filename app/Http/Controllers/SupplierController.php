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
    public function index(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.'); 
        };

        $suppliers = Supplier::with('user')->get();

        foreach ($suppliers as $supplier) {
            // foreach ($supplier['orders'] as $order) {
            //     $supplier['totalAmount'] += $order->netAmount;
            // }
            
            // foreach ($supplier['payments'] as $payment) {
            //     $supplier['totalPayment'] += $payment->amount;
            // }

            // $supplier['balance'] = $supplier['totalAmount'] - $supplier['totalPayment'];
            $supplier['balance'] = 0;

            $supplier['balance'] = number_format($supplier['balance'], 1, '.', ',');
        }

        $supplier_categories = Setup::where('type','supplier_category')->get();

        $categories_options = [];
        foreach ($supplier_categories as $supplier_category) {
            $categories_options[strtolower($supplier_category->short_title)] = ['text' => $supplier_category->title];
        }
        
        // foreach ($suppliers as $supplier) {
        //     // Decode JSON array of category IDs
        //     $categoriesIdArray = json_decode($supplier->categories_array, true);
    
        //     // Fetch categories from Setups model
        //     $categories = Setup::whereIn('id', $categoriesIdArray)
        //         ->where('type', 'supplier_category')
        //         ->get();
    
        //     // Attach the categories to the supplier
        //     $supplier["categories"] = $categories;
        // }

        $authLayout = $this->getAuthLayout($request->route()->getName());
    
        // return $suppliers;
        return view("suppliers.index", compact('suppliers', 'categories_options', 'authLayout'));
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
        
        $suppliers = Supplier::with('user')->get();
        $supplier_categories = Setup::where('type','supplier_category')->get();

        $categories_options = [];
        foreach ($supplier_categories as $supplier_category) {
            $categories_options[(int)$supplier_category->id] = [ 'text' => $supplier_category->title,];
        }

        $usernames = User::pluck('username')->toArray();

        return view('suppliers.create', compact('categories_options', 'suppliers', 'usernames'));
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
            'supplier_name' => 'required|string|max:255|unique:suppliers,supplier_name',
            'urdu_title' => 'nullable|string|max:255',
            'person_name' => 'required|string|max:255',
            'username' => 'required|string|min:6|max:255|regex:/^[a-z0-9]+$/|unique:users,username',
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
            $data['image'] = "default_avatar.png";

            $user = User::create([
                'name' => $data['supplier_name'],
                'username' => $data['username'],
                'password' => $data['password'],
                'role' =>'supplier',
                'profile_picture' => $data['image'],
            ]);
        } else {
            return redirect()->back()->with('error', 'This user already exists.')->withInput();
        }

        // Create a new supplier
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'supplier_name' => $user->name,
            'urdu_title' => $data['urdu_title'],
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
        if(!$this->checkRole(['developer', 'owner', 'admin']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {if(!$this->checkRole(['developer', 'owner', 'admin']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:255',
            'image_upload' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
            
        $data = $request->all();

        $user = User::where('username', $supplier->user->username)->first();

        if ($user) {
            if ($request->hasFile('image_upload')) {
                $file = $request->file('image_upload');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/images', $fileName, 'public'); // Store in public disk

                $data['image'] = $fileName; // Save the file path in the database
            } else {
                $data['image'] = "default_avatar.png";
            }

            // Update the user
            $user->update([
                'profile_picture' => $data['image'],
            ]);
        } else {
            return redirect()->back()->with('error', 'This user does not exist.')->withInput();
        }

        // Update the customer
        $supplier->update([
            'phone_number' => $data['phone_number'],
        ]);
        
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
    public function updateSupplierCategory(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };
        
        // Validate input first
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'integer|required|exists:suppliers,id',
            'categories_array' => 'required|json',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
    
        Supplier::where('id', $request->supplier_id)->update(['categories_array' => $data['categories_array']]);

        return redirect()->route('suppliers.index')->with('success', 'Categoies updated successfully');
    }
}