<?php

namespace App\Http\Controllers;

use App\Models\Setup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SetupController extends Controller
{
    public function addSetup()
    {
        return view('setups.add');
    }
    public function addSetupPost(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:setups,title',
            'short_title' => 'nullable|string|max:255|unique:setups,short_title',
            'type' => 'required|string|max:255',
        ]);

        // If validation fails, return with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        Setup::create($request->all());

        return redirect()->back()->with('success', 'Setup added successfully');
    }
}
