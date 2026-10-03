<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Transfer;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\PurchaseItem;
use App\Models\TransferItem;
use Illuminate\Http\Request;
use App\Models\BranchProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StockTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['invoice_no'] = $request->invoice_no;
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;
        $data['product_id'] = $request->product_id;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchId = ($userBranchId == 1) ? $filterBranchId : $userBranchId;

        $query = Transfer::with(['fromBranch', 'toBranch', 'user']);

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)
                  ->orWhere('to_branch_id', $branchId);
            });
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $sdate = Carbon::parse($request->startDate)->toDateString();
            $edate = Carbon::parse($request->endDate)->toDateString();
            $query->whereBetween('date', [$sdate, $edate]);
        }

        $barcode = $request->barcode ?? $request->invoice_no;
        $data['barcode'] = $barcode;
        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->where(function($q) use ($barcodeVal) {
                $q->where('invoice_no', 'like', "%{$barcodeVal}%")
                  ->orWhereHas('transferItems.product', function ($subQ) use ($barcodeVal) {
                      $subQ->where('barcode', $barcodeVal)
                           ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                           ->orWhere('name', 'like', "%{$barcodeVal}%");
                  });
            });
        }

        $data['transfers'] = $query->orderBy('created_at', 'DESC')->paginate(20)->appends($request->all());
        return view('backend.pages.stock-transfer.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['products'] = Product::with('unit.related_unit')->orderBy('name', 'ASC')->paginate(8);
        $data['categories'] = Category::orderBy('name', 'ASC')->get();
        $data['allBranch'] = Branch::get();
        $data['bank_accounts'] = BankAccount::where('status', 1)->get();

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        return view('backend.pages.stock-transfer.create', $data, compact('userBranchId', 'filterBranchId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $last_transfer_id = Transfer::orderBy('id', 'DESC')->select('transfer_no')->first();
        if ($last_transfer_id == null) {
            $transfer_no = "TNO-0000001";
        } else {
            $transfer_no = $last_transfer_id->transfer_no;
            $transfer_no++;
        }

        $userBranchId = auth()->user()->branch_id;
        $fromBranchId = $userBranchId == 1 ? $request->from_branch_id : $userBranchId;
        
        $transfer = new Transfer();
        $transfer->date = $request->date;
        $transfer->transfer_no = $transfer_no;
        $transfer->from_branch_id = $fromBranchId;
        $transfer->to_branch_id = $request->to_branch_id;
        $transfer->transfer_by = auth()->user()->id;
        $transfer->total_amount = $request->estimated_amount;
        $transfer->note = $request->note;

        DB::transaction(function () use ($request, $transfer, $fromBranchId) {

            if ($transfer->save()) {

                foreach ($request->product_id as $key => $product_id) {

                    // Ensure product exists in to-branch
                    $productBranchExists = BranchProduct::where('product_id', $product_id)
                        ->where('branch_id', $request->to_branch_id)
                        ->exists();

                    if (!$productBranchExists) {
                        $productBranch = new BranchProduct();
                        $productBranch->product_id = $product_id;
                        $productBranch->branch_id = $request->to_branch_id;
                        $productBranch->save();
                    }

                    $find_unit = Product::find($product_id);

                    $mainQty = $request->main_qty[$key];
                    $subQty = $request->sub_qty[$key] ?? 0;

                    // Total quantity in main unit
                    if ($find_unit->is_service == 0 && $find_unit->unit->related_unit != null) {
                        $totalQty = ($mainQty * $find_unit->unit->related_value) + $subQty;
                    } else {
                        $totalQty = $mainQty;
                    }

                    $variation_id = $request->variation_id[$key] ?? null;

                    // Reduce stock in from-branch using FIFO
                    $cost = reduceStockFIFO($product_id, $totalQty, $variation_id, $fromBranchId);

                    // Create transfer item
                    $transferItem = new TransferItem();
                    $transferItem->transfer_id = $transfer->id;
                    $transferItem->from_branch_id = $fromBranchId;
                    $transferItem->to_branch_id = $request->to_branch_id;
                    $transferItem->product_id = $product_id;
                    $transferItem->product_variation_id = $variation_id;
                    $transferItem->main_qty = $mainQty;
                    $transferItem->sub_qty = $subQty;
                    $transferItem->total_qty = $totalQty;
                    $transferItem->rate = $request->rate[$key];

                    $imeis = $request->imei[$key] ?? [];
                    $transferItem->imei = is_array($imeis) ? implode(',', $imeis) : $imeis;

                    $transferItem->sub_total = $request->sub_total[$key];
                    $transferItem->date = $request->date;
                    $transferItem->save();

                    if (!empty($imeis)) {
                        $imeiList = is_array($imeis) ? $imeis : array_filter(array_map('trim', explode(',', $imeis)));
                        if (!empty($imeiList)) {
                            \App\Models\SerialNumber::where('product_id', $product_id)
                                ->whereIn('serial', $imeiList)
                                ->update(['status' => 3]);
                        }
                    }


                }


            }
        });

        $transfer->load('fromBranch', 'toBranch', 'transferItems.product');
        session()->flash('success', 'Transfer Created Successfully');
        logActivity('Create Transfer', "Transfer #{$transfer->transfer_no} created from branch {$transfer->from_branch?->name} to {$transfer->to_branch?->name}", $transfer);
        return redirect()->route('transfer.index');
    }

    public function transferReceive(Request $request, $id)
    {
        DB::transaction(function () use ($id) {
            $transfer = Transfer::where('id', $id)->first();
            if ($transfer->status == 1) return; // Prevent double receive
            
            $transfer->update([
                'transfer_receive_by' => auth()->user()->id,
                'status' => 1,
            ]);

            // Create purchase for to-branch
            $purchase_no = Purchase::where('is_transfer', 1)->orderBy('id', 'desc')->value('purchase_no');
            $purchase_no = $purchase_no ? 'TR-' . str_pad((int) filter_var($purchase_no, FILTER_SANITIZE_NUMBER_INT) + 1, 3, '0', STR_PAD_LEFT) : 'TR-001';

            $purchase = new Purchase();
            $purchase->date = $transfer->date;
            $purchase->transfer_id = $transfer->id;
            $purchase->purchase_no = $purchase_no;
            $purchase->estimated_amount = $transfer->total_amount;
            $purchase->discount = 0;
            $purchase->total_amount = $transfer->total_amount;
            $purchase->total_paid = $transfer->total_amount;
            $purchase->note = $transfer->note;
            $purchase->is_transfer = 1;
            $purchase->created_by = auth()->user()->id;
            $purchase->branch_id = $transfer->to_branch_id;
            $purchase->status = 1;
            $purchase->save();

            $transfer_items = TransferItem::where('transfer_id', $id)->get();
            foreach ($transfer_items as $item) {
                $item->update([
                    'status' => 1,
                ]);

                // Update IMEI branch_id to Destination Branch and status back to 1
                if ($item->imei) {
                    $imeis = array_filter(array_map('trim', explode(',', $item->imei)));
                    if (!empty($imeis)) {
                        \App\Models\SerialNumber::whereIn('serial', $imeis)
                            ->where('product_id', $item->product_id)
                            ->update([
                                'branch_id' => $transfer->to_branch_id,
                                'status' => 1
                            ]);
                    }
                }

                // Add to Purchase Item
                $find_unit = Product::find($item->product_id);
                $purchase_item = new PurchaseItem();
                $purchase_item->purchase_id = $purchase->id;
                $purchase_item->product_id = $item->product_id;
                $purchase_item->branch_id = $transfer->to_branch_id;
                $purchase_item->product_variation_id = $item->product_variation_id;
                $purchase_item->rate = $item->rate;
                $purchase_item->main_qty = $item->main_qty;
                $purchase_item->sub_qty = $item->sub_qty;
                
                if ($find_unit->unit->related_unit == null) {
                    $purchase_item->stock_qty = $item->main_qty;
                } else {
                    $purchase_item->stock_qty = ($item->main_qty * $find_unit->unit->related_value) + $item->sub_qty;
                }

                $purchase_item->imei = $item->imei;
                $purchase_item->subtotal = $item->sub_total;
                $purchase_item->date = $transfer->date;
                $purchase_item->save();
            }

            // Transaction
            $transaction = new Transaction();
            $transaction->branch_id = $transfer->to_branch_id;
            $transaction->transaction_type = 'Transfer';
            $transaction->date = $transfer->date;
            $transaction->purchase_id = $purchase->id;
            $transaction->transfer_id = $transfer->id;
            $transaction->debit = null;
            $transaction->credit = $transfer->total_amount;
            $transaction->created_by = auth()->user()->id;
            $transaction->save();
        });

        $transfer = Transfer::where('id', $id)->first();

        session()->flash('success', 'Transfer Product Receive Successfully');
        logActivity('Receive Transfer', "Transfer #{$transfer->transfer_no} received", $transfer);
        return back();
    }

    public function transferCancel(Request $request, $id)
    {
        DB::transaction(function () use ($id) {
            $transfer = Transfer::where('id', $id)->first();
            if (!$transfer || $transfer->status == 2) return;

            $transfer_items = TransferItem::where('transfer_id', $id)->get();
            foreach ($transfer_items as $item) {
                // Restore regular stock to From Branch using FIFO/LIFO restore
                restoreToFIFO($item->product_id, $item->total_qty, $item->product_variation_id, $item->from_branch_id);

                // Move IMEIs back to From Branch and status back to 1
                if ($item->imei) {
                    $imeis = array_filter(array_map('trim', explode(',', $item->imei)));
                    if (!empty($imeis)) {
                        \App\Models\SerialNumber::whereIn('serial', $imeis)
                            ->where('product_id', $item->product_id)
                            ->update([
                                'branch_id' => $item->from_branch_id,
                                'status' => 1
                            ]);
                    }
                }

                $item->update(['status' => 2]);
            }

            // Remove Purchase records from To Branch
            $purchase = Purchase::where('transfer_id', $id)->first();
            if ($purchase) {
                PurchaseItem::where('purchase_id', $purchase->id)->delete();
                Transaction::where('purchase_id', $purchase->id)->delete();
                $purchase->delete();
            }

            // Delete direct transfer transactions
            Transaction::where('transfer_id', $id)->delete();

            $transfer->update([
                'transfer_receive_by' => auth()->user()->id,
                'status' => 2,
            ]);
        });

        session()->flash('success', 'Transfer Product cancelled Successfully and stock restored.');
        logActivity('Cancel Transfer', "Transfer #{$transfer->transfer_no} cancelled and stock restored", $transfer);
        return back();
    }

    public function transferPrint(Request $request, $id)
    {
        //get invoice with supplier and user by id
        $transfer = Transfer::with('transferItems')
            ->where('id', $id)->first();
        // if (get_setting('inv_design') == 'a5') {
        //     return view('backend.pages.invoice.afive-print', compact('transfer'));
        // } else {
        return view('backend.pages.stock-transfer.print', compact('transfer'));
        // }
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
        // Capture info BEFORE delete
        $transfer = Transfer::find($id);
        if ($transfer) {
            $transfer->load('fromBranch', 'toBranch', 'transferItems.product');
            logActivity('Delete Transfer', "Transfer #{$transfer->transfer_no} from {$transfer->from_branch?->name} to {$transfer->to_branch?->name} deleted", $transfer);
        }

        DB::transaction(function () use ($id) {
            $transfer = Transfer::where('id', $id)->first();
            if (!$transfer) return;

            $transfer_items = TransferItem::where('transfer_id', $id)->get();

            foreach ($transfer_items as $item) {
                // Restore regular stock to From Branch (only if not already cancelled)
                if ($transfer->status != 2) {
                    restoreToFIFO($item->product_id, $item->total_qty, $item->product_variation_id, $item->from_branch_id);

                    // Move IMEIs back to From Branch and status back to 1
                    if ($item->imei) {
                        $imeis = array_filter(array_map('trim', explode(',', $item->imei)));
                        if (!empty($imeis)) {
                            \App\Models\SerialNumber::whereIn('serial', $imeis)
                                ->where('product_id', $item->product_id)
                                ->update([
                                    'branch_id' => $item->from_branch_id,
                                    'status' => 1
                                ]);
                        }
                    }
                }
                $item->delete();
            }

            // Remove Purchase records from To Branch
            $purchase = Purchase::where('transfer_id', $id)->first();
            if ($purchase) {
                PurchaseItem::where('purchase_id', $purchase->id)->delete();
                Transaction::where('purchase_id', $purchase->id)->delete();
                $purchase->delete();
            }

            // Delete direct transfer transactions
            Transaction::where('transfer_id', $id)->delete();

            $transfer->delete();
        });

        session()->flash('success', 'Transfer Product deleted Successfully and stock restored.');
        return back();
    }
}
