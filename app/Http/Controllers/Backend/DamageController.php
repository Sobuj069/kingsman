<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use App\Models\Damage;
use App\Models\Product;
use App\Models\DamageItem;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Models\BranchProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DamageController extends Controller
{
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchId = ($userBranchId == 1) ? $filterBranchId : $userBranchId;

        if ($branchId) {
            $productIds = BranchProduct::where('branch_id', $branchId)->pluck('product_id');
            $data['products'] = Product::whereIn('id', $productIds)->orderBy('created_at', 'asc')->get();
        } else {
            $data['products'] = Product::orderBy('created_at', 'asc')->get();
        }

        $query = Damage::with(['damageItems.product', 'branch'])->where('status', 1);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $data['sdate'] = Carbon::createFromDate($request->start_date)->toDateString();
            $data['edate'] = Carbon::createFromDate($request->end_date)->toDateString();
            $query->whereBetween('date', [$data['sdate'], $data['edate']]);
        }

        if ($request->filled('product_id')) {
            $prodId = $request->product_id;
            $query->whereHas('damageItems', function ($q) use ($prodId) {
                $q->where('product_id', $prodId);
            });
        }

        $barcode = $request->barcode ?? $request->search;
        $data['barcode'] = $barcode;
        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->whereHas('damageItems.product', function ($q) use ($barcodeVal) {
                $q->where('barcode', $barcodeVal)
                  ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                  ->orWhere('name', 'like', "%{$barcodeVal}%")
                  ->orWhereHas('product_variations', function($vq) use ($barcodeVal) {
                      $vq->where('barcode', 'like', "%{$barcodeVal}%");
                  });
            });
        }

        $data['damages'] = $query->orderBy('created_at', 'DESC')->get();

        return view('backend.pages.damage.index', $data);
    }
    public function create()
    {
        $allBranch = Branch::orderBy('id', 'asc')->get();
        $products = Product::orderBy('id', 'DESC')->get();
        return view('backend.pages.damage.create', compact('products', 'allBranch'));
    }
    public function insert(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'product_id' => 'required',
            'main_qty' => 'required',
        ]);

        $damage = new Damage();
        $damage->date = $request->date;

        if (auth()->user()->branch_id == 1) {
            $damage->branch_id = $request->branch_id;
        } else {
            $damage->branch_id = auth()->user()->branch_id;
        }

        $damage->total_amount = $request->payable_amount;
        try {
            DB::transaction(function () use ($request, $damage) {
                if ($damage->save()) {
                    foreach ($request->product_id as $key => $product_id) {
                        $product = Product::with('unit')->find($product_id);
                        if ($product) {
                            if ($product->unit->related_unit == null) {
                                $total_qty = $request->main_qty[$key];
                            } else {
                                $total_qty = ($request->main_qty[$key] * $product->unit->related_value) + ($request->sub_qty[$key] ?? 0);
                            }

                            if ($product->is_service == 0) {
                                // Deduct stock using FIFO
                                reduceStockFIFO($product_id, $total_qty, null, $damage->branch_id);
                            }

                            $damageItem = new DamageItem();
                            $damageItem->date = $request->date;
                            $damageItem->branch_id = $damage->branch_id;
                            $damageItem->damage_id = $damage->id;
                            $damageItem->product_id = $product_id;
                            $damageItem->main_qty = $request->main_qty[$key];
                            $damageItem->sub_qty = $request->sub_qty[$key] ?? 0;
                            $damageItem->rate = $request->new_rate[$key];
                            $damageItem->subtotal = $request->sub_total[$key];

                            // IMEI Handling
                            if ((int)$product->imei === 1) {
                                $imeis = $request->imei[$key] ?? [];
                                $damageItem->imei = is_array($imeis) ? implode(',', $imeis) : $imeis;

                                if (!empty($imeis)) {
                                    \App\Models\SerialNumber::where('product_id', $product_id)
                                        ->whereIn('serial', (array)$imeis)
                                        ->update(['status' => 2]); // Status 2 for Damaged
                                }
                            }

                            $damageItem->save();
                        }
                    }
                }
            });

            $damage->load('damageItems.product');
            session()->flash('success', __('Damage Created Successfully'));
            logActivity('Create Damage', "Damage created for branch {$damage->branch?->name}", $damage);
            return redirect()->route('damage.index');

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function softDelete($id)
    {
        $damage = Damage::with('damageItems')->find($id);
        if (!$damage) {
            session()->flash('warning', 'Damage not found');
            return back();
        }

        // Capture info BEFORE delete
        $damage->load('damageItems.product');
        logActivity('Delete Damage', "Damage for branch {$damage->branch?->name} deleted", $damage);

        DB::transaction(function () use ($damage) {
            foreach ($damage->damageItems as $item) {
                $product = Product::with('unit')->find($item->product_id);

                // Restore Stock
                if ($product) {
                    if ($product->unit->related_unit == null) {
                        $qty = $item->main_qty;
                    } else {
                        $main = $item->main_qty * $product->unit->related_value;
                        $qty = $main + ($item->sub_qty ?? 0);
                    }

                    // Restore stock using LIFO/FIFO helper
                    restoreToFIFO($item->product_id, $qty, null, $item->branch_id);

                    // Restore IMEIs status to 1 (Available)
                    if ((int)$product->imei === 1 && !empty($item->imei)) {
                        $imeiArray = array_filter(array_map('trim', explode(',', $item->imei)));
                        \App\Models\SerialNumber::where('product_id', $item->product_id)
                            ->whereIn('serial', $imeiArray)
                            ->update(['status' => 1]);
                    }
                }
                $item->delete();
            }
            $damage->delete();
        });

        session()->flash('success', __('Damage deleted and stock restored successfully'));
        return back();
    }
}
