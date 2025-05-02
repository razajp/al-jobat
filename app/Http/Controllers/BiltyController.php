<?php

namespace App\Http\Controllers;

use App\Models\Bilty;
use Illuminate\Http\Request;

class BiltyController extends Controller
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
        return view("bilties.add");
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
    public function show(Bilty $bilty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bilty $bilty)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bilty $bilty)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bilty $bilty)
    {
        //
    }
}
