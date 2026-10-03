<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ReturnPurchase;
use App\Models\ReturnPurchaseItem;
use App\Models\Supplier;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['suppliers'] = Supplier::orderBy('name', 'asc')->get();
        $data['supplier_id'] = $request->supplier_id;
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;

        $query = ReturnPurchase::with(['supplier', 'purchase', 'returnPurchaseItems.product']);

        if ($request->supplier_id != null) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $sdate = Carbon::parse($request->startDate)->toDateString();
            $edate = Carbon::parse($request->endDate)->toDateString();
            $query->whereBetween('date', [$sdate, $edate]);
        }

        $barcode = $request->barcode ?? $request->purchase_no;
        $data['barcode'] = $barcode;
        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->where(function($q) use ($barcodeVal) {
                $q->whereHas('purchase', function ($subQ) use ($barcodeVal) {
                    $subQ->where('purchase_no', 'like', "%{$barcodeVal}%");
                })
                ->orWhereHas('returnPurchaseItems.product', function ($subQ) use ($barcodeVal) {
                    $subQ->where('barcode', $barcodeVal)
                         ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                         ->orWhere('name', 'like', "%{$barcodeVal}%");
                });
            });
        }

        $data['returns'] = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->all());
        return view('backend.pages.return.purchase.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

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
        if (Purchase::where('status', 2)->where('id', $id)->first()) {
            session()->flash('warning', 'Product already returned');
            return redirect()->back();
        } else {
            $data['purchase'] = Purchase::where('id', $id)->first();
            // $data['categories'] = Category::orderBy('name', 'ASC')->get();
            $data['suppliers'] = Supplier::get();
            $data['bank_accounts'] = BankAccount::where('status', 1)->get();

            return view('backend.pages.return.purchase.create', $data);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validation: Return quantity, rate, discount and subtotal cannot be negative
        if ($request->has('item_id') && is_array($request->item_id)) {
            $totalQty = 0;
            for ($i = 0; $i < count($request->item_id); $i++) {
                $mainQty = (float) ($request->main_qty[$i] ?? 0);
                $subQty  = (float) ($request->sub_qty[$i] ?? 0);
                $newRate = (float) ($request->new_rate[$i] ?? 0);
                $subTotal = (float) ($request->sub_total[$i] ?? 0);

                if ($mainQty < 0 || $subQty < 0) {
                    session()->flash('error', 'রিটার্ন সংখ্যা অবশ্যই শূন্য বা তার বেশি হতে হবে!');
                    return redirect()->back()->withInput();
                }

                if ($newRate < 0 || $subTotal < 0) {
                    session()->flash('error', 'রেট বা মোট মূল্য নেতিবাচক (negative) হতে পারে না!');
                    return redirect()->back()->withInput();
                }

                $totalQty += ($mainQty + $subQty);
            }

            if ($totalQty <= 0) {
                session()->flash('error', 'কমপক্ষে একটি পণ্যের রিটার্ন সংখ্যা অবশ্যই শূন্যের বেশি হতে হবে!');
                return redirect()->back()->withInput();
            }
        } else {
            session()->flash('error', 'কোনো আইটেম পাওয়া যায়নি!');
            return redirect()->back()->withInput();
        }

        if ((float)$request->payable_amount < 0 || (float)$request->estimated_amount < 0 || (float)$request->discount_amount < 0 || (float)$request->discount < 0) {
            session()->flash('error', 'ডিসকাউন্ট বা মোট পরিশোধ মূল্য নেতিবাচক (negative) হতে পারে না!');
            return redirect()->back()->withInput();
        }

        // dd($request->all());
        $return = new ReturnPurchase();
        $return->date = date('Y-m-d');
        $return->branch_id = $request->branch_id;
        $return->purchase_id = $request->purchase_id;
        $return->supplier_id = $request->supplier_id;
        $return->estimated_amount = $request->estimated_amount;
        $return->discount = $request->discount_amount;
        $return->discount_amount = $request->discount;
        $return->total_return = $request->payable_amount;

        $return->created_by = auth()->user()->id;

        try {
            DB::transaction(function () use ($request, $return) {
                if ($return->save()) {
                    for ($i = 0; $i < count($request->item_id); $i++) {


                        $query = PurchaseItem::where('purchase_id', $request->purchase_id)
                            ->where('id', $request->item_id[$i]);

                        if (!empty($request->product_variation_id[$i])) {
                            $query->where('product_variation_id', $request->product_variation_id[$i]);
                        } else {
                            $query->whereNull('product_variation_id');
                        }

                        $purchase_items = $query->first();

                        if ($purchase_items) {
                            $find_unit_id = Product::where('id', $purchase_items->product_id)->first();
                            $stock = 0;
                            if ($find_unit_id->unit->related_unit == null) {
                                $stock = $request->main_qty[$i];
                            } else {
                                $main = $request->main_qty[$i] * $find_unit_id->unit->related_value;
                                $sub = $request->sub_qty[$i];
                                $stock = $main + $sub;
                            }

                            if ($purchase_items->stock_qty < $stock) {
                                throw new \Exception("পর্যাপ্ত স্টক নেই! প্রোডাক্ট: " . ($find_unit_id->name ?? '') . " - পারচেজ আইটেমে উপলব্ধ স্টক: {$purchase_items->stock_qty}, রিটার্ন করতে চাওয়া স্টক: {$stock}");
                            }

                            $purchase_items->update([
                                'rtn_main' => $purchase_items->rtn_main + $request->main_qty[$i],
                                'rtn_sub' => $purchase_items->rtn_sub + $request->sub_qty[$i],
                                'rtn_total' => $purchase_items->rtn_total + $request->sub_total[$i],
                                'actual_main' => $purchase_items->actual_main - $request->main_qty[$i],
                                'actual_sub' => $purchase_items->actual_sub - $request->sub_qty[$i],
                                'actual_total' => $purchase_items->actual_total - $request->sub_total[$i],
                                'stock_qty' => $purchase_items->stock_qty - $stock,
                            ]);

                            if ($find_unit_id) {
                                $find_unit_id->save();
                            }

                            
                            // Handle IMEI updates
                            $imeiToReturnStr = null;
                            $itemId = $request->item_id[$i];
                            if (isset($request->return_imei) && isset($request->return_imei[$itemId])) {
                                $returnedImeis = $request->return_imei[$itemId];
                                $imeiToReturnStr = implode("\n", $returnedImeis);

                                if (count($returnedImeis) !== (int)$stock) {
                                    throw new \Exception("Returned IMEI count must match returned quantity for product " . ($find_unit_id->name ?? ''));
                                }

                                // Check if any IMEI to be returned is already sold or unavailable (status != 1)
                                $trimmedReturnedImeis = array_map('trim', $returnedImeis);
                                $unavailableSerials = \App\Models\SerialNumber::where('product_id', $purchase_items->product_id)
                                    ->whereIn('serial', $trimmedReturnedImeis)
                                    ->where('status', '!=', 1)
                                    ->pluck('serial')
                                    ->toArray();

                                if (count($unavailableSerials) > 0) {
                                    $list = implode(', ', $unavailableSerials);
                                    throw new \Exception("The following IMEIs cannot be returned because they are already sold or unavailable: " . $list);
                                }

                                if (!empty($purchase_items->imei)) {
                                    $existingImeis = explode("\n", $purchase_items->imei);
                                    $remainingImeis = array_diff($existingImeis, $returnedImeis);
                                    $purchase_items->update([
                                        'imei' => count($remainingImeis) > 0 ? implode("\n", $remainingImeis) : null
                                    ]);

                                    \App\Models\SerialNumber::where('product_id', $purchase_items->product_id)
                                        ->whereIn('serial', $trimmedReturnedImeis)
                                        ->update(['status' => 4]);
                                }
                            }
                        }
                        $returnItem = new ReturnPurchaseItem();
                        $returnItem->date = date('Y-m-d');
                        $returnItem->branch_id = $request->branch_id;
                        $returnItem->purchase_id = $request->purchase_id;
                        $returnItem->rtnPurchase_id = $return->id;
                        $returnItem->product_id = $request->product_id[$i];
                        $returnItem->product_variation_id = $request->product_variation_id[$i];
                        $returnItem->rate = $request->new_rate[$i];
                        $returnItem->main_qty = $request->main_qty[$i];
                        $returnItem->sub_qty = $request->sub_qty[$i];
                        $returnItem->subtotal = $request->sub_total[$i];
                        if (isset($imeiToReturnStr)) {
                            $returnItem->imei = $imeiToReturnStr;
                        }
                        $returnItem->save();
                    }
                    //create purchase log call createPurchaseLog function
                    $id = $return->id;
                    $type = 'Purchase Return';
                    // dd($id);


                    // Transaction
                    $transaction = new Transaction();
                    $transaction->transaction_type = $type;
                    $transaction->date = date('Y-m-d');
                    $transaction->return_pur_id = $id;
                    $transaction->branch_id = $request->branch_id;
                    $transaction->supplier_id = $request->supplier_id;
                    $transaction->debit = $request->payable_amount;
                    $transaction->credit = NULL;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                    $this->createReturnLog($request, $id, $type);
                }
            });

            $return->load('supplier', 'returnPurchaseItems.product');
            session()->flash('success', 'Purchase Return Created Successfully');
            logActivity('Purchase Return', "Return for Purchase #{$return->purchase?->purchase_no} to {$return->supplier?->name}", $return);
            return redirect()->route('rtnPurchase.index');

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    function createReturnLog(Request $request, $id, $type)
    {
        // update item return status
        $items = PurchaseItem::where('purchase_id', $request->purchase_id)->get();

        foreach ($items as $item) {
            if ($item->main_qty == $item->rtn_main) {
                $item->update([
                    'is_return' => 1
                ]);
            }
        }

        $purchase = Purchase::findOrFail($request->purchase_id);

        $returnAmount = $request->payable_amount;

        // new purchase total after return
        $newTotal = $purchase->total_amount - $returnAmount;

        $refund = 0;

        // check if paid greater than new total
        if ($purchase->total_paid > $newTotal) {

            $refund = $purchase->total_paid - $newTotal;

            $purchase->update([
                'estimated_amount' => $purchase->estimated_amount,
                'total_amount' => $newTotal,
                'total_due' => 0,
                'rtn_total_amount'     => $newTotal,
                'rtn_total_paid'       => $newTotal,
                'rtn_total_due'        => 0
            ]);
        } else {
            $purchase->update([
                'estimated_amount' => $purchase->estimated_amount,
                'total_amount' => $newTotal,
                'total_due' => max(0, $newTotal - $purchase->total_paid),
                'rtn_total_amount'     => $newTotal,
                'rtn_total_due'        => $newTotal - $purchase->rtn_total_paid
            ]);
        }

        // update return amount
        $purchase->update([
            'return_amount' => $purchase->return_amount + $returnAmount
        ]);

        // check total items
        $totalItems = PurchaseItem::where('purchase_id', $request->purchase_id)->count();

        // check returned items
        $returnedItems = PurchaseItem::where('purchase_id', $request->purchase_id)
            ->where('is_return', 1)
            ->count();

        // if all items returned
        if ($totalItems == $returnedItems) {
            $purchase->update([
                'status' => 2
            ]);
        }

        // supplier refund -> bank deposit
        if ($refund > 0) {

            $bank_transaction = new BankTransaction();
            $bank_transaction->trans_type = 'deposit';
            $bank_transaction->pay_type = 'pur_return';
            $bank_transaction->date = date('Y-m-d');
            $bank_transaction->bank_id = $request->bank_id;
            $bank_transaction->branch_id = $request->branch_id;
            $bank_transaction->return_pur_id = $id;
            $bank_transaction->amount = $refund;
            $bank_transaction->created_by = auth()->user()->id;
            $bank_transaction->save();

            // Transaction entry
            $transaction = new Transaction();
            $transaction->transaction_type = 'Receive money from supplier';
            $transaction->date = date('Y-m-d');
            $transaction->branch_id = $request->branch_id;
            $transaction->bank_id = $request->bank_id;
            $transaction->return_pur_id = $id;
            $transaction->supplier_id = $request->supplier_id;
            $transaction->debit = null;
            $transaction->credit = $refund;
            $transaction->created_by = auth()->user()->id;
            $transaction->save();
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $return = ReturnPurchase::find($id);
        if (!$return) {
            return back();
        }

        // Capture info BEFORE delete
        $return->load('supplier', 'returnPurchaseItems.product');
        logActivity('Delete Purchase Return', "Purchase Return for Purchase #{$return->purchase?->purchase_no} deleted", $return);

        DB::transaction(function () use ($return) {
            $purchase = Purchase::find($return->purchase_id);
            $returnItems = ReturnPurchaseItem::where('rtnPurchase_id', $return->id)->get();

            foreach ($returnItems as $rItem) {
                $purchase_items = PurchaseItem::where('purchase_id', $return->purchase_id)
                    ->where('product_id', $rItem->product_id);

                if (!empty($rItem->product_variation_id)) {
                    $purchase_items->where('product_variation_id', $rItem->product_variation_id);
                } else {
                    $purchase_items->whereNull('product_variation_id');
                }

                $purchase_item = $purchase_items->first();

                if ($purchase_item) {
                    $find_unit_id = Product::where('id', $purchase_item->product_id)->first();
                    $stock = 0;
                    if ($find_unit_id && $find_unit_id->unit) {
                        if ($find_unit_id->unit->related_unit == null) {
                            $stock = $rItem->main_qty;
                        } else {
                            $main = $rItem->main_qty * $find_unit_id->unit->related_value;
                            $sub = $rItem->sub_qty;
                            $stock = $main + $sub;
                        }
                    } else {
                        $stock = $rItem->main_qty;
                    }

                    // Restore IMEI
                    $newImeiStr = $purchase_item->imei;
                    if (!empty($rItem->imei)) {
                        $existingImeis = !empty($purchase_item->imei) ? explode("\n", $purchase_item->imei) : [];
                        $returnedImeis = explode("\n", $rItem->imei);
                        $mergedImeis = array_merge($existingImeis, $returnedImeis);
                        $mergedImeis = array_unique(array_filter(array_map('trim', $mergedImeis)));
                        $newImeiStr = implode("\n", $mergedImeis);

                        \App\Models\SerialNumber::where('product_id', $rItem->product_id)
                            ->whereIn('serial', array_map('trim', $returnedImeis))
                            ->update(['status' => 1]);
                    }

                    $purchase_item->update([
                        'rtn_main' => max(0, $purchase_item->rtn_main - $rItem->main_qty),
                        'rtn_sub' => max(0, $purchase_item->rtn_sub - $rItem->sub_qty),
                        'rtn_total' => max(0, $purchase_item->rtn_total - $rItem->subtotal),
                        'actual_main' => $purchase_item->actual_main + $rItem->main_qty,
                        'actual_sub' => $purchase_item->actual_sub + $rItem->sub_qty,
                        'actual_total' => $purchase_item->actual_total + $rItem->subtotal,
                        'stock_qty' => $purchase_item->stock_qty + $stock,
                        'imei' => !empty($newImeiStr) ? $newImeiStr : null,
                        'is_return' => 0
                    ]);


                }
            }

            if ($purchase) {
                $returnAmount = $return->total_return;
                $newReturnAmount = max(0, $purchase->return_amount - $returnAmount);
                
                // Increase total_amount because the return is deleted
                $newTotalAmount = $purchase->total_amount + $returnAmount;

                $updateData = [
                    'return_amount' => $newReturnAmount,
                    'total_amount' => $newTotalAmount,
                    'rtn_total_amount' => $newTotalAmount,
                ];

                if ($purchase->total_paid > $newTotalAmount) {
                    $updateData['rtn_total_paid'] = $newTotalAmount;
                    $updateData['rtn_total_due'] = 0;
                    $updateData['total_due'] = 0;
                } else {
                    $updateData['rtn_total_paid'] = $purchase->total_paid;
                    $updateData['rtn_total_due'] = max(0, $newTotalAmount - $purchase->total_paid);
                    $updateData['total_due'] = max(0, $newTotalAmount - $purchase->total_paid);
                }

                $purchase->update($updateData);

                // Update status if it was fully returned
                $totalItems = PurchaseItem::where('purchase_id', $purchase->id)->count();
                $returnedItems = PurchaseItem::where('purchase_id', $purchase->id)->where('is_return', 1)->count();

                if ($returnedItems < $totalItems) {
                    $purchase->update([
                        'status' => $purchase->rtn_total_due > 0 ? 0 : 1
                    ]);
                }
            }

            Transaction::where('return_pur_id', $return->id)->delete();
            BankTransaction::where('return_pur_id', $return->id)->delete();
            ReturnPurchaseItem::where('rtnPurchase_id', $return->id)->delete();
            $return->delete();
        });

        session()->flash('success', 'Purchase Return deleted successfully');
        return back();
    }
}
