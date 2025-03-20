<?php

namespace App\Http\Controllers;

use App\Models\OnlineProgram;
use Illuminate\Http\Request;

class OnlineProgramController extends Controller
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
        return view('onlne-program.create');
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
    public function show(OnlineProgram $onlineProgram)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OnlineProgram $onlineProgram)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OnlineProgram $onlineProgram)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OnlineProgram $onlineProgram)
    {
        //
    }
}
