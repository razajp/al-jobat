<?php

namespace App\Http\Controllers;

use App\Models\DailyLedgerDeposit;
use App\Models\DailyLedgerUse;
use App\Models\Setup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyLedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalDeposit = DailyLedgerDeposit::all()->map(fn($d) => [
                'date' => $d->date ?? null,
                'description' => ucfirst($d->method) . ' | ' . ($d->reff_no ?? '-'),
                'deposit' => $d->amount,
                'use' => 0,
                'created_at' => $d->created_at ?? null,
            ]);

        $totalUse = DailyLedgerUse::all()->map(fn($u) => [
                'date' => $u->date ?? null,
                'description' => ucfirst($u->case) . ' | ' . ($u->remarks ?? '-'),
                'deposit' => 0,
                'use' => $u->amount,
                'created_at' => $u->created_at ?? null,
            ]);

        $dailyLedgers = $totalDeposit->merge($totalUse)
            ->sort(function ($a, $b) {
                $aDate = $a['date'] ?? '1970-01-01';
                $bDate = $b['date'] ?? '1970-01-01';
                $dateCompare = strcmp($aDate, $bDate); // ascending (oldest first)

                if ($dateCompare === 0) {
                    $aCreated = $a['created_at'] ?? '1970-01-01 00:00:00';
                    $bCreated = $b['created_at'] ?? '1970-01-01 00:00:00';
                    return strtotime($aCreated) <=> strtotime($bCreated); // oldest created_at first
                }

                return $dateCompare;
            })->values();

        return view('daily-ledger.index', compact('dailyLedgers'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $totalDeposit = DailyLedgerDeposit::sum('amount');
        $totalUse = DailyLedgerUse::sum('amount');
        $balance = $totalDeposit - $totalUse;
        return view('daily-ledger.create', compact('balance'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $type = Auth::user()->daily_ledger_type;

        $commonRules = [
            'date'   => 'required|date',
            'amount' => 'required|integer',
        ];

        if ($type === 'deposit') {
            $rules = array_merge($commonRules, [
                'method'  => 'required|string',
                'reff_no' => 'required|string|unique:daily_ledger_deposits,reff_no',
            ]);

            $validated = $request->validate($rules);

            DailyLedgerDeposit::create($request->only(['date', 'method', 'amount', 'reff_no']));

            $message = 'Amount Deposit successfully.';
        } else {
            $rules = array_merge($commonRules, [
                'case'    => 'required|string',
                'remarks' => 'nullable|string',
            ]);

            $validated = $request->validate($rules);

            DailyLedgerUse::create($request->only(['date', 'case', 'amount', 'remarks']));

            $message = 'Amount Use successfully.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $Request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $Request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Request $Request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $Request)
    {
        //
    }
}
