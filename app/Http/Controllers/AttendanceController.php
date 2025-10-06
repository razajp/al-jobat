<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function create()
    {
        return view('attendances.record');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attendances = collect(json_decode($request->attendances, true));

        $validAttendances = collect();
        $invalidEmployeeNames = collect();

        // Step 1-3 combined: filter, deduplicate per employee per date, map employee_id
        $attendances
            ->filter(fn($item) => $item['state'] === "C/In")
            ->sortBy('datetime')
            ->groupBy(fn($item) => \Carbon\Carbon::createFromFormat('d-M-y g:i A', $item['datetime'])->toDateString() . '_' . $item['employee_name'])
            ->each(function ($group) use ($validAttendances, $invalidEmployeeNames) {
                $item = $group->first(); // first record per employee per date
                $employee = Employee::where('employee_name', $item['employee_name'])->first();

                if ($employee) {
                    $validAttendances->push([
                        'employee_id' => $employee->id,
                        'datetime'    => $item['datetime'],
                        'state'       => $item['state'],
                    ]);
                } else {
                    $invalidEmployeeNames->push($item['employee_name']);
                }
            });

        // Remove duplicates from invalid employee names
        $invalidEmployeeNames = $invalidEmployeeNames->unique()->values()->toArray();

        // Step 4: Upsert valid attendances
        Attendance::upsert(
            $validAttendances->toArray(),
            ['employee_id', 'datetime'],
            ['state']
        );

        // Step 5: Redirect back with invalid employee names
        return redirect()->back()->with('invalid_employees', $invalidEmployeeNames);
    }

    public function manageSalary()
    {
        $employee_options = [];
        $employees = Employee::where('status', 'active')->whereNotNull('salary')->with('type')->get();

        foreach($employees as $employee) {
            $employee_options[(int)$employee->id] = [
                'text' => ucfirst($employee->employee_name) . ' | ' . $employee->type->title,
                'data_option' => $employee,
            ];
        }

        return view('attendances.manage-salary', compact('employee_options'));
    }

    public function manageSalaryPost(Request $request)
    {
        return $request;
    }
}
