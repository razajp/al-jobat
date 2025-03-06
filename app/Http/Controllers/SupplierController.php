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
        //
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
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:3',
            'phone_number' => 'required|string|max:255',
            'image_upload' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'date' => 'required|string',
            'category_id' => 'required|exists:setups,id',
        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']); // Hash the password

        $user = User::where('phone_number', $request->phone_number)->orWhere('username', $request->username)->first();

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
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => $data['password'],
                'role' =>'supplier',
                'profile_picture' => $data['image'],
            ]);
        } else {
            $existingSupplier = Supplier::where('category_id', $data['category_id'])->andWhere('phone_number', $data['phone_number'])->first();
            
            // $existingSupplier = Supplier::where('category_id', $data['category_id'])->whereHas('user', function ($query) use ($request) {
            //     $query->where('username', $request->username);
            // })->first();
            
            if ($existingSupplier) {
                return redirect()->back()->with('error', 'A supplier with this category and (username or phone number) already exists.')->withInput();
            }
        }

        // Create a new supplier
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'category_id' => $data['category_id'],
            'phone_number' => $data['phone_number'],
            'date' => $data['date'],
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