<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Setup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with('user', 'category')->get();
    
        // return $customers;
        return view("customers.index", compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $suppliers = Supplier::with('user')->get();
        $supplier_categories = Setup::where('type','customer_category')->get();

        $categories_options = [];
        foreach ($supplier_categories as $supplier_category) {
            $categories_options[(int)$supplier_category->id] = [
                'text' => $supplier_category->title,
            ];
        }

        // return $categories_options;

        return view('customers.create', compact('categories_options'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255|unique:customers,customer_name',
            'person_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:3',
            'phone_number' => 'required|string|max:255',
            'image_upload' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'date' => 'required|string',
            'category_id' => 'required|string|max:255|exists:setups,id',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
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
                'name' => $data['customer_name'],
                'username' => $data['username'],
                'password' => $data['password'],
                'role' =>'customer',
                'profile_picture' => $data['image'],
            ]);
        } else {
            return redirect()->back()->with('error', 'This user already exists.')->withInput();
        }

        // Create a new supplier
        $supplier = Customer::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'person_name' => $data['person_name'],
            'phone_number' => $data['phone_number'],
            'date' => $data['date'],
            'category_id' => $data['category_id'],
            'city' => $data['city'],
            'address' => $data['address'],
        ]);
        
        return redirect()->route('customers.create')->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
