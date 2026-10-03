<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {

        $data['supplier_id'] = $request->supplier_id;
        $data['phone_no'] = $request->phone_no;
        $data['allBranch'] = Branch::orderBy('id', 'asc')->get();

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        $query = Supplier::orderBy('id', 'desc');

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $query->where('branch_id', $filterBranchId);
            } else {
                $data['suppliers'] = Supplier::orderBy('id', 'desc')->paginate(20);
            }
        } else {
            $query->where('branch_id', $userBranchId);
        }

        if ($request->supplier_id != null) {
            $query->where('id', $request->supplier_id);
        }
        $barcode = $request->barcode ?? $request->phone_no;
        $data['barcode'] = $barcode;
        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->where(function($q) use ($barcodeVal) {
                $q->where('phone', 'like', "%{$barcodeVal}%")
                  ->orWhere('name', 'like', "%{$barcodeVal}%")
                  ->orWhere('id', $barcodeVal);
            });
        }

        $data['suppliers'] = $query->paginate(20)->appends($request->all());

        return view('backend.pages.supplier.index', $data);
    }

    public function store(Request $request)
    {
        if (auth()->user()->branch_id == 1) {
            $uniBranch = $request->branch_id;
        } else {
            $uniBranch = auth()->user()->branch_id;
        }

        $request->validate([
            'name' => 'required',
            'phone' => [
                'required',
                Rule::unique('suppliers')->where(function ($query) use ($uniBranch) {
                    return $query->where('branch_id', $uniBranch);
                }),
            ],
        ]);

        $supplier = new Supplier();
        $supplier->date = date('Y-m-d');
        $supplier->name = $request->name;
        if (auth()->user()->branch_id == 1) {
            $supplier->branch_id = $request->branch_id;
        } else {
            $supplier->branch_id = auth()->user()->branch_id;
        }
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        $supplier->advance_amount = $request->advance_amount;
        $supplier->due_amount = $request->due_amount;

        DB::transaction(function () use ($request, $supplier) {
            if ($supplier->save()) {
                if ($request->advance_amount != 0) {
                    $transaction = new Transaction();
                    $transaction->transaction_type = 'Previous Advance Amount';
                    $transaction->date = Carbon::now()->format('Y-m-d');
                    if (auth()->user()->branch_id == 1) {
                        $transaction->branch_id = $request->branch_id;
                    } else {
                        $transaction->branch_id = auth()->user()->branch_id;
                    }
                    $transaction->supplier_id = $supplier->id;
                    $transaction->debit = $request->advance_amount;
                    $transaction->credit = NULL;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
                if ($request->due_amount != 0) {
                    $transaction = new Transaction();
                    $transaction->transaction_type = 'Previous Due Amount';
                    $transaction->date = Carbon::now()->format('Y-m-d');
                    if (auth()->user()->branch_id == 1) {
                        $transaction->branch_id = $request->branch_id;
                    } else {
                        $transaction->branch_id = auth()->user()->branch_id;
                    }
                    $transaction->supplier_id = $supplier->id;
                    $transaction->debit = NULL;
                    $transaction->credit = $request->due_amount;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });

        session()->flash('success', __('Supplier created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $supplier = Supplier::find($id);
        $supplier->update([
            'name' => $request->name,
            'email' => $request->email,
            'branch_id' => $request->branch_id,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        session()->flash('success', __('Supplier updated successfully'));
        return back();
    }

    public function getSuppliersByBranch(Request $request)
    {
        $suppliers = Supplier::where('branch_id', $request->branch_id)->get();
        return response()->json($suppliers);
    }


    public function destroy(string $id)
    {
        $supplier = Supplier::find($id);
        $transactions = Transaction::where('supplier_id', $id)->get();
        foreach ($transactions as $transaction) {
            $transaction->delete();
        }
        $supplier->delete();

        session()->flash('success', __('Supplier deleted successfully'));
        return back();
    }
}
