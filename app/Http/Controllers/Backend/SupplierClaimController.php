<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SupplierClaim;
use App\Models\SupplierClaimItem;
use App\Models\WarrantyClaim;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierClaimController extends Controller
{
    public function index()
    {
        $data['claims'] = SupplierClaim::with('supplier')->orderBy('id', 'desc')->paginate(20);
        return view('backend.pages.warranty.supplier-claim.index', $data);
    }

    public function create()
    {
        $data['suppliers'] = Supplier::get();
        $data['ready_claims'] = WarrantyClaim::whereIn('status', ['Checked', 'In progress'])->get();
        return view('backend.pages.warranty.supplier-claim.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'date' => 'required|date',
            'warranty_claim_id' => 'required|array',
        ]);

        $last_claim = SupplierClaim::orderBy('id', 'desc')->first();
        if ($last_claim == null) {
            $claim_no = "SCL-0000001";
        } else {
            $numberPart = (int) substr($last_claim->claim_no, strlen("SCL-"));
            $newNumber = str_pad($numberPart + 1, 7, '0', STR_PAD_LEFT);
            $claim_no = "SCL-" . $newNumber;
        }

        DB::transaction(function () use ($request, $claim_no) {
            $supplierClaim = SupplierClaim::create([
                'supplier_id' => $request->supplier_id,
                'claim_no' => $claim_no,
                'date' => $request->date,
                'status' => 'Pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->warranty_claim_id as $wc_id) {
                $wc = WarrantyClaim::find($wc_id);
                SupplierClaimItem::create([
                    'supplier_claim_id' => $supplierClaim->id,
                    'warranty_claim_id' => $wc->id,
                    'product_id' => $wc->product_id,
                    'serial_no' => $wc->serial_no,
                ]);

                $wc->update(['status' => 'Sent to Supplier']);
            }
        });

        session()->flash('success', __('Supplier Claim Created Successfully'));
        return redirect()->route('supplier-claim.index');
    }
}
