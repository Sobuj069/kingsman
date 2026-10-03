<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bank_accounts = BankAccount::orderBy('id','asc')->paginate(10);
        return view('backend.pages.account.bank_account.index', compact('bank_accounts'));
    }

    public function store(Request $request)
    {
        $bank_account = new BankAccount();
        $bank_account->bank_name = $request->bank_name;
        $bank_account->account_number = $request->account_number;
        $bank_account->opening_balance = 0;
        $bank_account->created_by = auth()->user()->id;
        $bank_account->save();

        session()->flash('success', __('Bank Account created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $bank_account = BankAccount::find($id);
        // dd($bank_account);
        $bank_account->bank_name = $request->bank_name;
        $bank_account->account_number = $request->account_number;
        $bank_account->save();

        session()->flash('success', __('Bank Account updated successfully'));
        return back();
    }

    public function destroy(string $id)
    {
        $bank_account = BankAccount::find($id);
        $bank_account->delete();
        session()->flash('success', __('Bank Account deleted successfully'));
        return back();
    }
}
