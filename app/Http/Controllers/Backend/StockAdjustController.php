<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transfer;
use App\Models\AdjustStock;
use App\Models\BankAccount;
use App\Models\PurchaseItem;
use App\Models\TransferItem;
use Illuminate\Http\Request;
use App\Models\BranchProduct;
use Illuminate\Support\Carbon;
use App\Models\AdjustStockItem;
use App\Models\SerialNumber;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StockAdjustController extends Controller
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

        $query = AdjustStock::with(['branch', 'user']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
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
                  ->orWhereHas('adjustItems.product', function ($subQ) use ($barcodeVal) {
                      $subQ->where('barcode', $barcodeVal)
                           ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                           ->orWhere('name', 'like', "%{$barcodeVal}%");
                  });
            });
        }

        $data['adjust_stocks'] = $query->orderBy('id', 'desc')->paginate(20)->appends($request->all());

        return view('backend.pages.stock-adjust.index', $data);
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

        return view('backend.pages.stock-adjust.create', $data, compact('userBranchId', 'filterBranchId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $last_adjust_id = AdjustStock::orderBy('id', 'DESC')->select('adjust_no')->first();
        if ($last_adjust_id == null) {
            $adjust_no = "AD-00001";
        } else {
            $adjust_no = $last_adjust_id->adjust_no;
            $adjust_no++;
        }
        $adjust = new AdjustStock();
        $adjust->date = $request->date;
        $adjust->adjust_no = $adjust_no;
        $adjust->branch_id = $request->branch_id;
        $adjust->adjust_by = auth()->user()->id;
        $adjust->total_amount = $request->estimated_amount;
        $adjust->note = $request->note;
        $adjust->stock_status = $request->stock_to === 'stock_in' ? 1 : 0;

        DB::beginTransaction();
        try {
            $adjust->save();

            foreach ($request->product_id as $key => $product_id) {

                // Ensure the product exists for this branch
                $productBranch = BranchProduct::firstOrCreate(
                    ['product_id' => $product_id, 'branch_id' => $request->branch_id]
                );

                // Fetch purchase item (with variation if exists)
                $purchase_item_query = PurchaseItem::where('branch_id', $request->branch_id)
                    ->where('product_id', $product_id);

                if (!empty($request->variation_id[$key])) {
                    $purchase_item_query->where('product_variation_id', $request->variation_id[$key]);
                }

                $purchase_item = $purchase_item_query->orderBy('id', 'desc')->first();

                // If no purchase item found, rollback & notify
                if (!$purchase_item) {
                    DB::rollBack();
                    session()->flash('error', "No purchase found for Product");
                    return redirect()->back()->withInput();
                }

                $adjustItem = new AdjustStockItem();
                $adjustItem->adjust_id = $adjust->id;
                $adjustItem->branch_id = $request->branch_id;
                $adjustItem->product_id = $product_id;
                $adjustItem->product_variation_id = $request->variation_id[$key] ?? null;
                $adjustItem->rate = $request->rate[$key];
                $adjustItem->date = $request->date;
                $adjustItem->stock_status = $request->stock_to === 'stock_in' ? 1 : 0;

                $product = Product::find($product_id);

                // Stock calculation
                if ($product->is_service == 0) {
                    if (!$product->unit || !$product->unit->related_unit) {
                        $saleQty = (float) $request->main_qty[$key];
                        $adjustItem->main_qty = $request->main_qty[$key];
                    } else {
                        $mainQty = (float) $request->main_qty[$key] * $product->unit->related_value;
                        $subQty = (float) ($request->sub_qty[$key] ?? 0);
                        $saleQty = $mainQty + $subQty;
                        $adjustItem->main_qty = $request->main_qty[$key];
                        $adjustItem->sub_qty = $request->sub_qty[$key];
                    }

                    if ($saleQty <= 0 || (float) $request->main_qty[$key] < 0 || (float) ($request->sub_qty[$key] ?? 0) < 0) {
                        DB::rollBack();
                        session()->flash('error', "Invalid quantity for product '{$product->name}'. Quantity cannot be negative or zero.");
                        return redirect()->back()->withInput();
                    }

                    // Rule 1: user cant adjust stock out product if the product have 0 stock
                    if ($request->stock_to !== 'stock_in') { // stock_out
                        $current_stock = (float) product_fake_stock_val($product);
                        if ($current_stock <= 0) {
                            DB::rollBack();
                            session()->flash('error', "Cannot adjust stock out for product '{$product->name}' as it has 0 current stock.");
                            return redirect()->back()->withInput();
                        }
                    }

                    // Rule 2: user can have only 10 product stock in
                    if ($request->stock_to === 'stock_in') {
                        if ($saleQty > 10) {
                            DB::rollBack();
                            session()->flash('error', "Stock adjustment violation: You can only adjust up to 10 units for stock in of product '{$product->name}'.");
                            return redirect()->back()->withInput();
                        }
                    }

                    $adjustItem->sub_total = $request->sub_total[$key] ?? 0;

                    // Update purchase stock
                    if ($request->stock_to === 'stock_in') {
                        // RULE: Only 10 product stock in allowed
                        if ($saleQty > 10) {
                            DB::rollBack();
                            session()->flash('error', "Maximum 10 units allowed for Stock In. Product: " . $product->name);
                            return redirect()->back()->withInput();
                        }
                        $purchase_item->increment('stock_qty', $saleQty);
                    } else {
                        reduceStockFIFO($product_id, $saleQty, $request->variation_id[$key] ?? null, $request->branch_id);
                    }
                } else {
                    // For service products
                    $saleQty = (float) $request->main_qty[$key];
                    $adjustItem->main_qty = $saleQty;

                    if ($saleQty <= 0 || $saleQty < 0) {
                        DB::rollBack();
                        session()->flash('error', "Invalid quantity for service '{$product->name}'. Quantity cannot be negative or zero.");
                        return redirect()->back()->withInput();
                    }
                }

                $adjustItem->total_qty = $saleQty;

                // IMEI Handling
                if ((int)$product->imei === 1) {
                    $imeis = $request->imei[$key] ?? [];
                    $adjustItem->imei = is_array($imeis) ? implode(',', $imeis) : $imeis;

                    // Update status for existing IMEIs
                    if (!empty($imeis)) {
                        if ($request->stock_to === 'stock_out') {
                            // Mark IMEIs as adjusted out (status=3)
                            SerialNumber::where('product_id', $product_id)
                                ->whereIn('serial', (array)$imeis)
                                ->update(['status' => 3]);
                        } else {
                            // Stock In - mark IMEIs as available (status=1)
                            SerialNumber::where('product_id', $product_id)
                                ->whereIn('serial', (array)$imeis)
                                ->update(['status' => 1]);
                        }
                    }

                    // Handle new IMEIs on stock in
                    if ($request->stock_to === 'stock_in') {
                        $newImeis = $request->new_imei[$key] ?? '';
                        if (!empty($newImeis)) {
                            $adjustItem->imei = $newImeis;
                            $newImeisArray = array_filter(array_map('trim', explode(',', $newImeis)));
                            foreach ($newImeisArray as $newImei) {
                                // Create if not exists, set status to available (1)
                                \App\Models\SerialNumber::firstOrCreate(
                                    ['product_id' => $product_id, 'serial' => $newImei],
                                    ['status' => 1]
                                );
                            }
                        }
                    }
                }

                $adjustItem->save();
            }

            DB::commit();
            session()->flash('success', 'Stock Adjust Successfully');
            return redirect()->route('stock-adjust.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error adjusting stock: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stock_adjust = AdjustStock::where('id', $id)->first();
        return view('backend.pages.stock-adjust.print', compact('stock_adjust'));
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
        $adjust = AdjustStock::findOrFail($id);
        $adjust_items = AdjustStockItem::where('adjust_id', $id)->get();

        DB::beginTransaction();
        try {
            foreach ($adjust_items as $item) {
                // Revert stock in PurchaseItem
                $purchase_item_query = PurchaseItem::where('branch_id', $item->branch_id)
                    ->where('product_id', $item->product_id);

                if (!empty($item->product_variation_id)) {
                    $purchase_item_query->where('product_variation_id', $item->product_variation_id);
                }

                if ($item->stock_status == 1) {
                    // It was Stock In (incremented), so we decrement to revert
                    $remaining = $item->total_qty;

                    // Query all purchase items for this product/variation/branch
                    $purItemsQuery = PurchaseItem::where('product_id', $item->product_id)
                        ->where('branch_id', $item->branch_id);

                    if (!empty($item->product_variation_id)) {
                        $purItemsQuery->where('product_variation_id', $item->product_variation_id);
                    } else {
                        $purItemsQuery->whereNull('product_variation_id');
                    }

                    $purItems = $purItemsQuery->orderBy('id', 'desc')->get();

                    // Check if total stock is sufficient to decrement
                    $totalAvailable = $purItems->sum('stock_qty');
                    if ($totalAvailable < $remaining) {
                        DB::rollBack();
                        $product_name = $item->product ? $item->product->name : 'Unknown Product';
                        session()->flash('error', "Cannot delete this adjustment because it will result in negative stock for product '{$product_name}'.");
                        return back();
                    }

                    foreach ($purItems as $pItem) {
                        if ($remaining <= 0) break;
                        $toDecrement = min($remaining, $pItem->stock_qty);
                        $pItem->decrement('stock_qty', $toDecrement);
                        $remaining -= $toDecrement;
                    }
                } else {
                    // It was Stock Out (decremented), so we increment to revert using FIFO/LIFO restore
                    restoreToFIFO($item->product_id, $item->total_qty, $item->product_variation_id, $item->branch_id);
                }

                // Revert IMEI statuses
                if (!empty($item->imei)) {
                    $product = Product::find($item->product_id);
                    if ($product && (int)$product->imei === 1) {
                        $imeiArray = array_filter(array_map('trim', explode(',', $item->imei)));
                        if (!empty($imeiArray)) {
                            if ($item->stock_status == 0) {
                                // Was Stock Out - restore IMEIs to available
                                SerialNumber::where('product_id', $item->product_id)
                                    ->whereIn('serial', $imeiArray)
                                    ->update(['status' => 1]);
                            } else {
                                // Was Stock In - mark IMEIs as adjusted out
                                SerialNumber::where('product_id', $item->product_id)
                                    ->whereIn('serial', $imeiArray)
                                    ->update(['status' => 3]);
                            }
                        }
                    }
                }
                
                $item->delete();
            }

            $adjust->delete();
            
            DB::commit();
            session()->flash('success', 'Adjust Stock deleted and stock reverted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error deleting adjust stock: ' . $e->getMessage());
        }

        return back();
    }
}
