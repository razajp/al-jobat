<?php

namespace App\Http\Controllers;

use App\Models\EmployeePayment;
use Illuminate\Http\Request;

class EmployeePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $payments = EmployeePayment::with('employee.type')->get();

        $authLayout = $this->getAuthLayout($request->route()->getName());

        return view('employee-payments.index', compact('payments', 'authLayout'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        return view('employee-payments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'admin', 'accountant'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'date' => 'required|date',
            'method' => 'required|string',
            'amount' => 'required|integer',
        ]);

        EmployeePayment::create($request->all());

        return redirect()->back()->with('success', 'Payment added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeePayment $employeePayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeePayment $employeePayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeePayment $employeePayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeePayment $employeePayment)
    {
        //
    }
}
