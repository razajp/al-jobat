<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Setup;
use Illuminate\Http\Request;

class EmployeeController extends Controller
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
        $all_types = [];

        $staff_types = Setup::where('type', 'staff_type')->get();
        $worker_types = Setup::where('type', 'worker_type')->get();

        $all_types['staff_type'] = $staff_types;
        $all_types['worker_type'] = $worker_types;

        return view('employees.create', compact('all_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $request->validate([
            'category' => 'required|string|in:staff,worker',
            'type_id' => 'required|exists:setups,id',
            'employee_name' => 'required|string',
            'urdu_title' => 'required|string',
            'phone_number' => 'required|string',
            'joining_date' => 'required|date',
            'cnic_no' => 'nullable|string',
            'salary' => 'nullable|integer|min:1',
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }
}
