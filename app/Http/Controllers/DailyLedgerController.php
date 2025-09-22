<?php

namespace App\Http\Controllers;

use App\Models\DailyLedgerDeposit;
use App\Models\DailyLedgerUse;
use App\Models\Setup;
use Illuminate\Http\Request;

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

    public function deposit()
    {
        $totalDeposit = DailyLedgerDeposit::sum('amount');
        $totalUse = DailyLedgerUse::sum('amount');
        $balance = $totalDeposit - $totalUse;
        return view('daily-ledger.deposit', compact('balance'));
    }

    public function depositStore(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'method' => 'required|string',
            'amount' => 'required|integer',
            'reff_no' => 'required|string|unique:daily_ledger_deposits,reff_no',
        ]);

        DailyLedgerDeposit::create($request->all());

        return redirect()->back()->with('success', 'Amount Deposit successfully.');
    }

    public function use()
    {
        $totalDeposit = DailyLedgerDeposit::sum('amount');
        $totalUse = DailyLedgerUse::sum('amount');
        $balance = $totalDeposit - $totalUse;
        return view('daily-ledger.use', compact('balance'));
    }

    public function useStore(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'case' => 'required|string',
            'amount' => 'required|integer',
            'remarks' => 'nullable|string',
        ]);

        DailyLedgerUse::create($request->all());

        return redirect()->back()->with('success', 'Amount Use successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
