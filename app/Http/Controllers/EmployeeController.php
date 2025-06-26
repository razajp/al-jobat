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
    public function index(Request $request)
    {
        if (!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest'])) {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $employees = Employee::with('type')->get();

        $authLayout = $this->getAuthLayout($request->route()->getName());

        $all_types = collect()
        ->merge(
            Setup::where('type', 'staff_type')->get()->mapWithKeys(fn($type) => [
                $type->id => [
                    'text' => $type->title,
                    'category' => 'staff',
                ]
            ])
        )
        ->merge(
            Setup::where('type', 'worker_type')->get()->mapWithKeys(fn($type) => [
                $type->id => [
                    'text' => $type->title,
                    'category' => 'worker',
                ]
            ])
        )
        ->toArray();

        return view("employees.index", compact('employees', 'authLayout', 'all_types'));
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
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);
        
        $data = $request->all();

        // Handle the image upload if present
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/images', $fileName, 'public'); // Store in public disk

            $data['profile_picture'] = $fileName; // Save the file path in the database
        }

        Employee::create($data);

        return redirect()->route('employees.create')->with('success', 'Employee added successfully.');
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

    public function updateStatus(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'manager', 'admin']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };
        
        $employee = Employee::find($request->user_id);

        if ($request->status == 'active') {
            $employee->status = 'in_active';
            $employee->save();
        } else {
            $employee->status = 'active';
            $employee->save();
        }
        return redirect()->back()->with('success', 'Status has been updated successfully!');
    }
}
