<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\Setup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect(route('rates.create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $type_options = [];
        $workerTypes = Setup::where('type', 'worker_type')->get();
        foreach($workerTypes as $workerType) {
            $type_options[(int)$workerType->id] = [
                'text' => $workerType->title
            ];
        }

        return view('rates.add', compact('type_options'));
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
            'type_id' => 'required|integer|exists:setups,id',
            'effective_date' => 'required|date',
            'categories' => 'required|string', 
            'seasons' => 'required|string',
            'sizes' => 'required|string',
            'title' => 'required|string|unique:rates,title',
            'rate' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $rate = Rate::create([
            'type_id' => $request['type_id'],
            'effective_date' => $request['effective_date'],
            'categories' => json_decode($request['categories'], true),
            'seasons' => json_decode($request['seasons'], true),
            'sizes' => json_decode($request['sizes'], true),
            'title' => $request['title'],
            'rate' => $request['rate'],
        ]);

        return redirect()->route('rates.create')->with('success', 'Rates Added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Rate $rate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rate $rate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rate $rate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rate $rate)
    {
        //
    }
}
