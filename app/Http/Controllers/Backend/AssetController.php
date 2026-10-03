<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\BankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function index()
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        $query = Asset::with('bank_account')->orderBy('purchase_date', 'desc');

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $query->where('branch_id', $filterBranchId);
            }
        } else {
            $query->where('branch_id', $userBranchId);
        }

        $assets = $query->paginate(20);
        $totalCost = (clone $query)->sum(DB::raw('purchase_cost * quantity'));

        return view('backend.pages.expense.asset.index', compact('assets', 'totalCost'));
    }

    public function create()
    {
        $bank_accounts = BankAccount::where('status', 1)->get();
        $allBranch = Branch::orderBy('id', 'asc')->get();
        return view('backend.pages.expense.asset.create', compact('bank_accounts', 'allBranch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'bank_id' => 'required|exists:bank_accounts,id',
            'note' => 'nullable|string',
        ]);

        $asset = new Asset();
        
        if (auth()->user()->branch_id == 1) {
            $asset->branch_id = $request->branch_id;
        } else {
            $asset->branch_id = auth()->user()->branch_id;
        }
        $asset->name = $request->name;
        $asset->purchase_date = $request->purchase_date;
        $asset->purchase_cost = $request->purchase_cost;
        $asset->quantity = $request->quantity;
        $asset->bank_id = $request->bank_id;
        $asset->note = $request->note;
        $asset->created_by = auth()->id();

        $totalAmount = $request->purchase_cost * $request->quantity;

        DB::transaction(function () use ($request, $asset, $totalAmount) {
            if ($asset->save()) {
                // Create bank transaction
                $bank_transaction = new BankTransaction();
                $bank_transaction->trans_type = 'withdraw';
                $bank_transaction->pay_type = 'asset';
                
                if (auth()->user()->branch_id == 1) {
                    $bank_transaction->branch_id = $request->branch_id;
                } else {
                    $bank_transaction->branch_id = auth()->user()->branch_id;
                }
                
                $bank_transaction->date = $request->purchase_date;
                $bank_transaction->bank_id = $request->bank_id;
                $bank_transaction->amount = $totalAmount;
                $bank_transaction->note = __('Asset Purchased: ') . $asset->name;
                $bank_transaction->created_by = auth()->id();
                $bank_transaction->save();
            }
        });

        session()->flash('success', __('Asset recorded successfully'));
        return redirect()->route('asset.index');
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        
        DB::transaction(function () use ($asset) {
            // Delete matching bank transaction (matched by withdraw & pay_type=asset & amount & note containing asset name)
            BankTransaction::where('trans_type', 'withdraw')
                ->where('pay_type', 'asset')
                ->where('bank_id', $asset->bank_id)
                ->where('note', 'like', '%' . $asset->name . '%')
                ->delete();

            $asset->delete();
        });

        session()->flash('success', __('Asset deleted successfully'));
        return redirect()->route('asset.index');
    }
}
