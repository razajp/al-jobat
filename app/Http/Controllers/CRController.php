<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;

class CRController extends Controller
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
    public function create(Request $request)
    {
        $payment_options = [];

        $method = $request->method;
        $payment_options = [];

        if ($method === 'cheque') {
            $cheques = CustomerPayment::whereNotNull('cheque_no')->with('customer.city')->whereDoesntHave('cheque')->whereNull('bank_account_id')->get();

            foreach ($cheques as $cheque) {
                $payment_options[(int)$cheque->id] = [
                    'text' => $cheque->cheque_no . ' - ' . $cheque->amount,
                    'data_option' => $cheque->makeHidden('creator'),
                ];
            }
        } else if ($method === 'slip') {
            $slips = CustomerPayment::whereNotNull('slip_no')->with('customer.city')->whereDoesntHave('slip')->whereNull('bank_account_id')->get();

            foreach ($slips as $slip) {
                $payment_options[(int)$slip->id] = [
                    'text' => $slip->slip_no . ' - ' . $slip->amount,
                    'data_option' => $slip->makeHidden('creator'),
                ];
            }
        } else if ($method === 'self_cheque') {
            $self_accounts = BankAccount::where('category', 'self')->get()->makeHidden('creator');

            foreach ($self_accounts as $self_account) {
                foreach ($self_account->available_cheques as $available_cheque) {
                    $payment_options[(int)$available_cheque] = [
                        'text' => $available_cheque . ' |' . explode('|', $self_account->account_title)[1],
                    ];
                }
            }
        } else if ($method === 'program') {
            $slips = CustomerPayment::whereNotNull('slip_no')->with('customer.city')->whereDoesntHave('slip')->whereNull('bank_account_id')->get();

            foreach ($slips as $slip) {
                $payment_options[(int)$slip->id] = [
                    'text' => $slip->slip_no . ' - ' . $slip->amount,
                    'data_option' => $slip->makeHidden('creator'),
                ];
            }
        }

        return view('cr.generate', compact('payment_options'));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
