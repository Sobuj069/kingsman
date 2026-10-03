<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Customer;
use App\Models\OwnerShip;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OwnershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bank_accounts = BankAccount::where('status',1)->get();
        $ownerships = OwnerShip::where('status',1)->get();
        return view('backend.pages.account.ownership.index',compact('ownerships','bank_accounts'));
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
        $request->validate([
            'name'=> 'required',
            'phone' => 'required | unique:owner_ships,phone',
        ]);
        $ownership = new OwnerShip();
        $ownership->date = date('Y-m-d');
        $ownership->name = $request->name;
        $ownership->email = $request->email;
        $ownership->phone = $request->phone;
        $ownership->address = $request->address;
        $ownership->deposit = $request->deposit;

        DB::transaction(function() use($request, $ownership) {
            if ($ownership->save()) {
                if ($request->deposit != NULL) {
                    $transaction = new Transaction();
                    $transaction->transaction_type = 'Owner Deposit';
                    $transaction->date = Carbon::now()->format('Y-m-d');
                    $transaction->owner_id = $ownership->id;
                    $transaction->bank_id = $request->bank_id;
                    $transaction->debit = $request->deposit;
                    $transaction->credit = NULL;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();

                    $bank_transaction = new BankTransaction();
                    $bank_transaction->trans_type = 'deposit';
                    $bank_transaction->pay_type = 'ownpay';
                    $bank_transaction->date = Carbon::now()->format('Y-m-d');
                    $bank_transaction->owner_id = $ownership->id;
                    $bank_transaction->bank_id = $request->bank_id;
                    $bank_transaction->amount = $request->deposit;
                    $bank_transaction->status = 1;
                    $bank_transaction->created_by = auth()->user()->id;
                    $bank_transaction->save();
                }
            }
        });
        session()->flash('success', 'Owner created successfully');
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request , string $id)
    {
        // $data['ownerships'] = OwnerShip::orderBy('created_at', 'desc')->get();
        // if ($request->all() != NULL) {
            $data['oneCustomer'] = OwnerShip::where('id', $id)->where('status', 1)->first();
            $data['sdate'] = Carbon::createFromDate($request->start_date)->format('Y-m-d');
            $data['edate'] = Carbon::createFromDate($request->end_date)->format('Y-m-d');
            // dd($data['sdate'],$data['edate']);
            $data['transaction'] = Transaction::with('invoice', 'purchase', 'ownerShip')
                                    ->where('owner_id', $id)
                                    ->whereBetween('date', [$data['sdate'], $data['edate']])
                                    ->get();
        // }
        return view('backend.pages.report.ownership_ledger', $data);
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
        $ownership = OwnerShip::find($id);
        $ownership->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'address'=>$request->address,
        ]);
        session()->flash('success', 'Owner updated successfully');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
