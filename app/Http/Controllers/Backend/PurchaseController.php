<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\PurchaseItem;
use App\Models\SerialNumber;
use App\Models\Rack;
use App\Models\BranchRack;
use Illuminate\Http\Request;
use App\Models\ActualPayment;
use App\Models\BranchProduct;
use App\Models\BranchCategory;
use Illuminate\Support\Carbon;
use App\Models\BankTransaction;
use App\Models\ProductVariation;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1) ? $filterBranchId : $userBranchId;

        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;
        $data['purchase_no'] = $request->purchase_no;
        $data['supplier_id'] = $request->supplier_id;
        $data['product_id'] = $request->product_id;

        $data['suppliers'] = Supplier::orderBy('name', 'asc')->get();

        if ($branchId) {
            $productIds = BranchProduct::where('branch_id', $branchId)->pluck('product_id');
            $categoryIds = BranchCategory::where('branch_id', $branchId)->pluck('category_id');
            $data['allProduct'] = Product::whereIn('id', $productIds)->orderBy('name', 'asc')->get();
            $data['allCategory'] = Category::whereIn('id', $categoryIds)->orderBy('name', 'asc')->get();
        } else {
            $data['allProduct'] = Product::orderBy('name', 'asc')->get();
            $data['allCategory'] = Category::orderBy('name', 'asc')->get();
        }

        $query = Purchase::with('supplier', 'user', 'purchaseItems.product')
            ->when($branchId, function ($q) use ($branchId) {
                return $q->where('branch_id', $branchId);
            });

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
                $q->where('purchase_no', 'like', "%{$barcodeVal}%")
                  ->orWhereHas('purchaseItems.product', function ($subQ) use ($barcodeVal) {
                      $subQ->where('barcode', $barcodeVal)
                           ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                           ->orWhere('name', 'like', "%{$barcodeVal}%");
                  });
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('product_id')) {
            $prodId = $request->product_id;
            $query->whereHas('purchaseItems', function ($q) use ($prodId) {
                $q->where('product_id', $prodId);
            });
        }

        $data['purchases'] = $query->orderBy('created_at', 'DESC')
            ->paginate(20)
            ->appends($request->all());

        return view('backend.pages.purchase.index', $data);
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'user', 'branch', 'purchaseItems.product'])->findOrFail($id);
        $purchase_items = $purchase->purchaseItems;
        $payments = BankTransaction::where('purchase_id', $id)->get();
        return view('backend.pages.purchase.show', compact('purchase', 'purchase_items', 'payments'));
    }

    public function getPurchaseNo(Request $request)
    {
        $branch_id = $request->branch_id;

        $lastPurchase = Purchase::where('branch_id', $branch_id)->where('is_transfer', 0)->orderBy('id', 'desc')->first();

        if ($lastPurchase) {
            // Increment number
            $lastNumber = (int) str_replace('PUR', '', $lastPurchase->purchase_no);
            $newNumber = 'PUR' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = 'PUR00001';
        }

        return response()->json(['purchase_no' => $newNumber]);
    }

    public function create()
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $suppliers = Supplier::where('branch_id', $filterBranchId)->where('status', 1)->get();
                $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $categories = Category::whereIn('id', $categoryIds)->orderBy('id', 'desc')->get();
                $products = Product::whereIn('id', $productIds)->orderBy('name', 'asc')->get();
                $rackIds = BranchRack::where('branch_id', $filterBranchId)->pluck('rack_id');
                $racks = Rack::whereIn('id', $rackIds)->orderBy('name', 'asc')->get();
            } else {
                $suppliers = Supplier::where('status', 1)->get();
                $categories = Category::orderBy('name', 'asc')->get();
                $products = Product::orderBy('name', 'asc')->get();
                $racks = Rack::orderBy('name', 'asc')->get();
            }
        } else {
            $suppliers = Supplier::where('branch_id', $userBranchId)->where('status', 1)->get();
            $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $rackIds = BranchRack::where('branch_id', $userBranchId)->pluck('rack_id');
            $categories = Category::whereIn('id', $categoryIds)->orderBy('id', 'desc')->get();
            $products = Product::whereIn('id', $productIds)->orderBy('name', 'asc')->get();
            $racks = Rack::whereIn('id', $rackIds)->orderBy('name', 'asc')->get();
        }

        $variations = ProductVariation::with('size')
            ->get();
        $bank_accounts = BankAccount::where('status', 1)->get();
        $allBranch = Branch::get();

        return view('backend.pages.purchase.create', compact('suppliers', 'filterBranchId', 'categories', 'products', 'bank_accounts', 'allBranch', 'racks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
        ]);

        if (empty($request->new_product)) {
            return back()->with('error', 'Please select at least one product to purchase.');
        }

        // Count how many products actually have a valid main_qty > 0
        $validItemsCount = 0;
        foreach ($request->new_product as $key => $productId) {
            if (!empty($request->new_main_qty[$key]) && $request->new_main_qty[$key] > 0) {
                $validItemsCount++;
            }
        }

        if ($validItemsCount === 0) {
            return back()->with('error', 'Please select at least one product with a quantity greater than zero.');
        }

        // Calculate true estimated amount dynamically
        $true_estimated_amount = 0;
        if ($request->new_product) {
            foreach ($request->new_product as $key => $product_id) {
                if (empty($request->new_main_qty[$key]) || $request->new_main_qty[$key] == 0) continue;
                $find_unit_id = Product::where('id', $product_id)->first();
                if ($find_unit_id) {
                    $conversion_value = $find_unit_id->unit->related_value ?? 1;
                    if ($conversion_value == 0) $conversion_value = 1;

                    if ($find_unit_id->unit->related_unit == null) {
                        $qty_for_calc = $request->new_main_qty[$key];
                    } else {
                        $qty_for_calc = $request->new_main_qty[$key] + (($request->new_sub_qty[$key] ?? 0) / $conversion_value);
                    }
                    $true_estimated_amount += $qty_for_calc * ($request->new_rate[$key] ?? 0);
                }
            }
        }

        $true_discount_amount = $request->discount_amount ?? 0;
        $true_vat_amount = $request->vat_amount ?? 0;
        $true_total_amount = round($true_estimated_amount + $true_vat_amount - $true_discount_amount, 2);

        if (round($request->paid_amount, 2) > $true_total_amount) {
            return back()->with('error', 'Paid amount cannot be greater than the calculated grand total (' . $true_total_amount . ').');
        }

        $supplier = Supplier::find($request->supplier_id);
        if ($supplier && (strtolower($supplier->name) == 'walking supplier' || $supplier->id == 1)) {
            if (round($request->paid_amount, 2) < $true_total_amount) {
                return back()->with('error', 'Walking Supplier cannot make due purchases. Please pay the full amount.');
            }
        }

        $purchase = new Purchase();
        $purchase->date = $request->date;
        $purchase->purchase_no = $request->purchase_no;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->estimated_amount = $true_estimated_amount;
        $purchase->discount = $request->discount ?? 0;
        $purchase->discount_amount = $true_discount_amount;
        $purchase->vat = $request->vat_percent ?? 0;
        $purchase->vat_amount = $true_vat_amount;
        $purchase->total_amount = $true_total_amount;
        $purchase->note = $request->note;
        $purchase->created_by = auth()->user()->id;

        if (auth()->user()->branch_id == 1) {

            $purchase->branch_id = $request->branch_id;
        } else {
            $purchase->branch_id = auth()->user()->branch_id;
        }
        if ($request->has('new_imei')) {
            $allImeis = [];
            // Only check IMEIs that are currently active in stock (status = 1)
            // Sold/Out-of-stock IMEIs can be repurchased when customers sell back or exchange phones
            $existingSerials = SerialNumber::where('status', 1)->pluck('serial')->toArray();
            foreach ($request->new_imei as $key => $imeiText) {
                if (!empty($imeiText)) {
                    $lines = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiText))));
                    foreach ($lines as $line) {
                        $tokens = array_filter(array_map('trim', preg_split('/[\s,\/]+/', $line)));
                        foreach ($tokens as $imei) {
                            if (in_array($imei, $allImeis)) {
                                return back()->with('error', "Duplicate IMEI found in input: $imei");
                            }
                            $allImeis[] = $imei;
                            
                            foreach ($existingSerials as $serialStr) {
                                $existingTokens = array_filter(array_map('trim', preg_split('/[\s,\/]+/', $serialStr)));
                                if (in_array($imei, $existingTokens)) {
                                    return back()->with('error', "IMEI $imei is currently active in stock.");
                                }
                            }
                        }
                    }
                }
            }
        }

        DB::transaction(function () use ($request, $purchase) {
            if ($purchase->save()) {
                // Save product_id and category_id in purchase_items table
                foreach ($request->new_product as $key => $product_id) {

                    // যদি main_qty 0 বা null হয়, তাহলে এই item skip করো
                    // main_qty যদি 0 বা null হয়, তাহলে skip করো (create হবে না)
                    if (empty($request->new_main_qty[$key]) || $request->new_main_qty[$key] == 0) {
                        continue;
                    }

                    $purchase_item = new PurchaseItem();
                    $purchase_item->date = $request->date;

                    $find_unit_id = Product::where('id', $product_id)->first();

                    $purchase_item->purchase_id = $purchase->id;
                    $purchase_item->product_id = $product_id;
                    $purchase_item->branch_id = $purchase->branch_id;

                    $purchase_item->rate = $request->new_rate[$key];

                    if ($find_unit_id->unit->related_unit == null) {
                        $purchase_item->main_qty = $request->new_main_qty[$key];
                        $purchase_item->actual_main = $request->new_main_qty[$key];
                        $purchase_item->stock_qty = $request->new_main_qty[$key];
                    } else {
                        $purchase_item->main_qty = $request->new_main_qty[$key];
                        $purchase_item->sub_qty = $request->new_sub_qty[$key];
                        $purchase_item->actual_main = $request->new_main_qty[$key];
                        $purchase_item->actual_sub = $request->new_sub_qty[$key];
                        $main = $request->new_main_qty[$key] * $find_unit_id->unit->related_value;
                        $sub = $request->new_sub_qty[$key];
                        $purchase_item->stock_qty = $main + $sub;
                    }

                    if ($request->variation != null) {
                        $purchase_item->product_variation_id = $request->variation[$key] ?? null;
                    }

                    if (isset($request->new_imei[$key]) && !empty(trim($request->new_imei[$key]))) {
                        $imeis = explode("\n", str_replace("\r", "", $request->new_imei[$key]));
                        $imeis = array_filter(array_map('trim', $imeis));
                        $purchase_item->imei = implode("\n", $imeis);
                    }

                    $conversion_value = $find_unit_id->unit->related_value ?? 1;
                    if ($conversion_value == 0) $conversion_value = 1;

                    if ($find_unit_id->unit->related_unit == null) {
                        $qty_for_calc = $request->new_main_qty[$key];
                    } else {
                        $qty_for_calc = $request->new_main_qty[$key] + ($request->new_sub_qty[$key] / $conversion_value);
                    }
                    $calc_subtotal = $qty_for_calc * $request->new_rate[$key];

                    $purchase_item->subtotal = $calc_subtotal;
                    $purchase_item->actual_total = $calc_subtotal;
                    $purchase_item->warranty_value = $request->new_warranty_value[$key] ?? null;
                    $purchase_item->warranty_unit = $request->new_warranty_unit[$key] ?? null;
                    $purchase_item->save();

                    // ✅ Add to serial_numbers table
                    if (isset($imeis) && count($imeis) > 0) {
                        foreach ($imeis as $line) {
                            $line = trim($line);
                            if (empty($line)) continue;
                            $sn = new SerialNumber();
                            $sn->purchase_id = $purchase->id;
                            $sn->product_id = $product_id;
                            $sn->branch_id = $purchase->branch_id; // Added branch_id
                            $sn->serial = $line;
                            $sn->status = 1;
                            $sn->warranty_value = $request->new_warranty_value[$key] ?? null;
                            $sn->warranty_unit = $request->new_warranty_unit[$key] ?? null;
                            $sn->save();
                        }
                    }

                    // ✅ Sync / Attach selected racks to product
                    if (isset($request->new_rack_ids[$key]) && is_array($request->new_rack_ids[$key]) && !empty($request->new_rack_ids[$key])) {
                        $find_unit_id->racks()->syncWithoutDetaching($request->new_rack_ids[$key]);
                    }
                }

                // create purchase log
                $id = $purchase->id;
                $type = 'Purchase';

                // Transaction
                $transaction = new Transaction();
                if (auth()->user()->branch_id == 1) {
                    $transaction->branch_id = $request->branch_id;
                } else {
                    $transaction->branch_id = auth()->user()->branch_id;
                }

                $transaction->transaction_type = $type;
                $transaction->date = $request->date;
                $transaction->purchase_id = $id;
                $transaction->supplier_id = $request->supplier_id;
                $transaction->debit = null;
                $transaction->credit = $purchase->total_amount;
                $transaction->created_by = auth()->user()->id;
                $transaction->save();

                $this->createPurchaseInv($request, $id, $type);
            }
        });

        $purchase->load('supplier', 'purchaseItems.product');
        session()->flash('success', 'Purchase Created Successfully');
        logActivity('Create Purchase', "Purchase #{$purchase->purchase_no} from {$purchase->supplier?->name} created", $purchase);
        return redirect()->route('purchase.index');
    }

    public function purchaseEdit($id)
    {
        $supplier = Supplier::where('status', 1)->get();
        $bank_accounts = BankAccount::where('status', 1)->get();
        $purchase = Purchase::where('id', $id)->with(['purchaseItems.product.racks'])->first();

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', $purchase->branch_id ?? auth()->user()->branch_id);
        $branchId = ($userBranchId == 1) ? $filterBranchId : $userBranchId;
        if ($branchId) {
            $rackIds = BranchRack::where('branch_id', $branchId)->pluck('rack_id');
            $racks = Rack::whereIn('id', $rackIds)->orderBy('name', 'asc')->get();
        } else {
            $racks = Rack::orderBy('name', 'asc')->get();
        }

        return view('backend.pages.purchase.edit', compact('purchase', 'supplier', 'bank_accounts', 'racks'));
    }

    public function purchaseUpdate(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
        ]);

        $purchase = Purchase::findOrFail($id);

        \Illuminate\Support\Facades\File::put(base_path('debug_request.json'), json_encode($request->all(), JSON_PRETTY_PRINT));

        if (empty($request->product_id)) {
            return back()->with('error', 'Please select at least one product to purchase.');
        }

        $validItemsCount = 0;
        foreach ($request->product_id as $key => $productId) {
            if (!empty($request->new_main_qty[$key]) && $request->new_main_qty[$key] > 0) {
                $validItemsCount++;
            }
        }

        if ($validItemsCount === 0) {
            return back()->with('error', 'Please select at least one product with a quantity greater than zero.');
        }

        $true_estimated_amount = 0;
        if ($request->product_id) {
            foreach ($request->product_id as $key => $product_id) {
                if (empty($request->new_main_qty[$key]) || $request->new_main_qty[$key] == 0) continue;
                $find_unit_id = Product::find($product_id);
                if ($find_unit_id) {
                    $conversion_value = $find_unit_id->unit->related_value ?? 1;
                    if ($conversion_value == 0) $conversion_value = 1;

                    if ($find_unit_id->unit->related_unit == null) {
                        $qty_for_calc = $request->new_main_qty[$key];
                    } else {
                        $qty_for_calc = $request->new_main_qty[$key] + (($request->new_sub_qty[$key] ?? 0) / $conversion_value);
                    }
                    $true_estimated_amount += $qty_for_calc * ($request->new_rate[$key] ?? 0);
                }
            }
        }

        $true_discount_amount = $request->discount_amount ?? 0;
        $true_vat_amount = $request->vat_amount ?? 0;
        $true_total_amount = round($true_estimated_amount + $true_vat_amount - $true_discount_amount, 2);

        $calculated_due = round($true_total_amount - ($request->paid_amount ?? 0), 2);

        $supplier = Supplier::find($request->supplier_id);
        if ($supplier && (strtolower($supplier->name) == 'walking supplier' || $supplier->id == 1)) {
            if ($calculated_due > 0) {
                return back()->with('error', 'Walking Supplier cannot make due purchases. Please pay the full amount.');
            }
        }

        $purchase->date = $request->date;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->estimated_amount = $true_estimated_amount;
        $purchase->discount = $request->discount_percent ?? 0;
        $purchase->discount_amount = $true_discount_amount;
        $purchase->vat = $request->vat_percent ?? 0;
        $purchase->vat_amount = $true_vat_amount;
        $purchase->total_amount = $true_total_amount;
        $purchase->note = $request->note;

        if ($calculated_due < 0) {
            $purchase->return_amount = abs($calculated_due);
            $purchase->total_due = 0;
        } else {
            $purchase->total_due = $calculated_due;
            $purchase->return_amount = 0;
        }

        $purchase->status = $calculated_due > 0 ? 0 : 1;

        // 🔥 Validate IMEI count matches quantity before proceeding
        foreach ($request->product_id as $key => $product_id) {
            $product = Product::find($product_id);
            if ($product && ($product->imei == 1 || $product->imei == '1')) {
                $imeiText = '';
                if (isset($request->itemID[$key]) && isset($request->old_imei[$request->itemID[$key]])) {
                    $imeiText = $request->old_imei[$request->itemID[$key]];
                } elseif (isset($request->new_imei[$key])) {
                    $imeiText = $request->new_imei[$key];
                }

                $imeiCount = count(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiText)))));
                $inputQty = (int)($request->new_main_qty[$key] ?? 0);

                if ($imeiCount != $inputQty) {
                    return back()->with('error', "Quantity mismatch for product '{$product->name}'. Entered Quantity: {$inputQty}, Total IMEIs: {$imeiCount}. Please ensure they match.");
                }
            }
        }

        // 🔥 Validate active duplicate IMEIs in other purchases
        if ($request->has('new_imei') || $request->has('old_imei')) {
            $allImeis = [];
            $existingSerials = SerialNumber::where('status', 1)
                ->where('purchase_id', '!=', $purchase->id)
                ->pluck('serial')
                ->toArray();

            $imeiInputs = [];
            if ($request->has('old_imei') && is_array($request->old_imei)) {
                foreach ($request->old_imei as $text) $imeiInputs[] = $text;
            }
            if ($request->has('new_imei') && is_array($request->new_imei)) {
                foreach ($request->new_imei as $text) $imeiInputs[] = $text;
            }

            foreach ($imeiInputs as $imeiText) {
                if (!empty($imeiText)) {
                    $lines = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiText))));
                    foreach ($lines as $line) {
                        $tokens = array_filter(array_map('trim', preg_split('/[\s,\/]+/', $line)));
                        foreach ($tokens as $imei) {
                            if (in_array($imei, $allImeis)) {
                                return back()->with('error', "Duplicate IMEI found in input: $imei");
                            }
                            $allImeis[] = $imei;

                            foreach ($existingSerials as $serialStr) {
                                $existingTokens = array_filter(array_map('trim', preg_split('/[\s,\/]+/', $serialStr)));
                                if (in_array($imei, $existingTokens)) {
                                    return back()->with('error', "IMEI $imei is currently active in stock.");
                                }
                            }
                        }
                    }
                }
            }
        }

        // 🔥 Validate new quantity is not less than the already sold quantity
        if ($request->itemID) {
            foreach ($request->product_id as $key => $product_id) {
                if (isset($request->itemID[$key])) {
                    $oldItem = PurchaseItem::with('product.unit.related_unit')->find($request->itemID[$key]);
                    if ($oldItem) {
                        $conversion = 1;
                        if ($oldItem->product && $oldItem->product->unit && $oldItem->product->unit->related_value) {
                            $conversion = $oldItem->product->unit->related_value;
                        }
                        if ($oldItem->product && $oldItem->product->unit && $oldItem->product->unit->related_unit == null) {
                            $old_original_stock = $oldItem->main_qty;
                        } else {
                            $old_original_stock = ($oldItem->main_qty * $conversion) + $oldItem->sub_qty;
                        }
                        $sold_qty = max(0, $old_original_stock - $oldItem->stock_qty);

                        // Calculate new requested quantity in sub-unit terms
                        $new_main = (int)($request->new_main_qty[$key] ?? 0);
                        $new_sub = (int)($request->new_sub_qty[$key] ?? 0);
                        if ($oldItem->product && $oldItem->product->unit && $oldItem->product->unit->related_unit == null) {
                            $new_qty_total = $new_main;
                        } else {
                            $new_qty_total = ($new_main * $conversion) + $new_sub;
                        }

                        if ($new_qty_total < $sold_qty) {
                            $unitName = $oldItem->product->unit->name ?? 'pcs';
                            if ($oldItem->product && $oldItem->product->unit && $oldItem->product->unit->related_unit != null) {
                                $sold_main = (int)($sold_qty / $conversion);
                                $sold_sub = $sold_qty % $conversion;
                                $subUnitName = $oldItem->product->unit->related_unit->name ?? 'sub';
                                $sold_text = "$sold_main $unitName, $sold_sub $subUnitName";
                            } else {
                                $sold_text = "$sold_qty $unitName";
                            }
                            return back()->with('error', "Cannot reduce purchase quantity for '{$oldItem->product->name}' below the quantity already sold from this purchase (Already Sold: {$sold_text}).");
                        }
                    }
                }
            }
        }

        DB::transaction(function () use ($request, $purchase) {

            $purchase->save();

            // Calculate old sold quantity before deleting
            $oldItemsData = [];
            $existingSnStatus = [];

                $existingSnStatus = SerialNumber::where('purchase_id', $purchase->id)
                    ->pluck('status', 'serial')
                    ->toArray();

                $oldItems = PurchaseItem::with('product.unit')->where('purchase_id', $purchase->id)->get();
                foreach ($oldItems as $oldItem) {
                    $conversion = 1;
                    if ($oldItem->product && $oldItem->product->unit && $oldItem->product->unit->related_value) {
                        $conversion = $oldItem->product->unit->related_value;
                    }
                    if ($oldItem->product && $oldItem->product->unit && $oldItem->product->unit->related_unit == null) {
                        $old_original_stock = $oldItem->main_qty;
                    } else {
                        $old_original_stock = ($oldItem->main_qty * $conversion) + $oldItem->sub_qty;
                    }
                    $sold_qty = $old_original_stock - $oldItem->stock_qty;
                    $oldItemsData[$oldItem->id] = [
                        'sold_qty' => max(0, $sold_qty),
                        'rtn_main' => $oldItem->rtn_main,
                        'rtn_sub'  => $oldItem->rtn_sub,
                        'rtn_total'=> $oldItem->rtn_total,
                        'is_return'=> $oldItem->is_return,
                    ];
                }

                // Delete ALL existing purchase items for this purchase (prevents ghost old-rate items)
                PurchaseItem::where('purchase_id', $purchase->id)->delete();
                // Also delete from serial_numbers
                SerialNumber::where('purchase_id', $purchase->id)->delete();
            $transaction = Transaction::where('purchase_id', $purchase->id)->delete();
            $bank_transaction = BankTransaction::where('purchase_id', $purchase->id)->delete();
            // ✅ Save new items
            foreach ($request->product_id as $key => $product_id) {

                $product = Product::with('unit.related_unit')->findOrFail($product_id);

                $purchase_item = new PurchaseItem();
                $purchase_item->date = $request->date;
                $purchase_item->branch_id = $purchase->branch_id;
                $purchase_item->purchase_id = $purchase->id;
                $purchase_item->product_id = $product_id;

                // ✅ variation (nullable)
                $purchase_item->product_variation_id =
                    $request->product_variation_id[$key] ?? null;

                $purchase_item->rate = $request->new_rate[$key];

                $sold_qty = 0;
                $rtn_main = 0;
                $rtn_sub = 0;
                $rtn_total = 0;
                $is_return = 0;
                if (isset($request->itemID[$key]) && isset($oldItemsData[$request->itemID[$key]])) {
                    $sold_qty = $oldItemsData[$request->itemID[$key]]['sold_qty'];
                    $rtn_main = $oldItemsData[$request->itemID[$key]]['rtn_main'];
                    $rtn_sub = $oldItemsData[$request->itemID[$key]]['rtn_sub'];
                    $rtn_total = $oldItemsData[$request->itemID[$key]]['rtn_total'];
                    $is_return = $oldItemsData[$request->itemID[$key]]['is_return'];
                }

                $main = $request->new_main_qty[$key];
                $sub = $request->new_sub_qty[$key] ?? 0;

                if ($product->unit->related_unit == null) {
                    $purchase_item->main_qty = $main;
                    $purchase_item->actual_main = max(0, $main - $rtn_main);
                    $purchase_item->sub_qty = 0;
                    $purchase_item->rtn_main = $rtn_main;
                    $purchase_item->rtn_sub = 0;
                    $purchase_item->rtn_total = $rtn_total;
                    $purchase_item->is_return = $is_return;
                    $new_original_stock = $main;
                    $purchase_item->stock_qty = max(0, $new_original_stock - $sold_qty);
                } else {
                    $conversion = $product->unit->related_value;

                    $purchase_item->main_qty = $main;
                    $purchase_item->actual_main = max(0, $main - $rtn_main);
                    $purchase_item->sub_qty = $sub;
                    $purchase_item->actual_sub = max(0, $sub - $rtn_sub);
                    $purchase_item->rtn_main = $rtn_main;
                    $purchase_item->rtn_sub = $rtn_sub;
                    $purchase_item->rtn_total = $rtn_total;
                    $purchase_item->is_return = $is_return;

                    $new_original_stock = ($main * $conversion) + $sub;
                    $purchase_item->stock_qty = max(0, $new_original_stock - $sold_qty);
                }

                if (isset($request->itemID[$key]) && isset($request->old_imei[$request->itemID[$key]]) && !empty(trim($request->old_imei[$request->itemID[$key]]))) {
                    $imeis = explode("\n", str_replace("\r", "", $request->old_imei[$request->itemID[$key]]));
                    $imeis = array_filter(array_map('trim', $imeis));
                    $purchase_item->imei = implode("\n", $imeis);
                } elseif (isset($request->new_imei[$key]) && !empty(trim($request->new_imei[$key]))) {
                    $imeis = explode("\n", str_replace("\r", "", $request->new_imei[$key]));
                    $imeis = array_filter(array_map('trim', $imeis));
                    $purchase_item->imei = implode("\n", $imeis);
                }

                $conversion_value = $product->unit->related_value ?? 1;
                if ($conversion_value == 0) $conversion_value = 1;

                if ($product->unit->related_unit == null) {
                    $qty_for_calc = $main;
                } else {
                    $qty_for_calc = $main + ($sub / $conversion_value);
                }
                $calc_subtotal = $qty_for_calc * $request->new_rate[$key];

                $purchase_item->subtotal = $calc_subtotal;
                $purchase_item->actual_total = max(0, $calc_subtotal - $rtn_total);
                $purchase_item->warranty_value = $request->new_warranty_value[$key] ?? null;
                $purchase_item->warranty_unit = $request->new_warranty_unit[$key] ?? null;
                $purchase_item->save();

                // ✅ Add to serial_numbers table
                if (isset($imeis) && count($imeis) > 0) {
                    foreach ($imeis as $line) {
                        $line = trim($line);
                        if (empty($line)) continue;
                        $sn = new SerialNumber();
                        $sn->purchase_id = $purchase->id;
                        $sn->product_id = $product_id;
                        $sn->branch_id = $purchase->branch_id; // Added branch_id
                        $sn->serial = $line;
                        $sn->status = $existingSnStatus[$line] ?? 1;
                        $sn->warranty_value = $request->new_warranty_value[$key] ?? null;
                        $sn->warranty_unit = $request->new_warranty_unit[$key] ?? null;
                        $sn->save();
                    }
                }

                // ✅ Sync / Attach selected racks to product
                if (isset($request->new_rack_ids[$key]) && is_array($request->new_rack_ids[$key]) && !empty($request->new_rack_ids[$key])) {
                    $product->racks()->syncWithoutDetaching($request->new_rack_ids[$key]);
                }
            }

            // ✅ Transaction log
            Transaction::create([
                'transaction_type' => 'Purchase',
                'date' => $request->date,
                'purchase_id' => $purchase->id,
                'supplier_id' => $request->supplier_id,
                'debit' => null,
                'credit' => $purchase->total_amount,
                'created_by' => auth()->id(),
            ]);

            $this->createPurchaseUpd($request, $purchase->id, 'Purchase');
        });

        // Recalculate invoice FIFO costs for the modified products
        $productIds = is_array($request->product_id) ? array_unique($request->product_id) : [];
        foreach ($productIds as $product_id) {
            recalculateInvoiceProfit($product_id, $purchase->branch_id);
        }

        $purchase->load('supplier', 'purchaseItems.product');
        session()->flash('success', 'Successfully edit purchase.');
        logActivity('Update Purchase', "Purchase #{$purchase->purchase_no} updated", $purchase);

        $redirectQuery = $request->input('redirect_query');
        if (!empty($redirectQuery)) {
            return redirect()->to(route('purchase.index') . '?' . $redirectQuery);
        }

        return redirect()->route('purchase.index');
    }


    public function destroy(string $id)
    {
        $purchase = Purchase::find($id);

        // Prevent deletion if items have been sold
        $purchaseItemsForCheck = PurchaseItem::with('product.unit')->where('purchase_id', $id)->get();
        $total_qty = 0;
        $stock_qty = 0;
        foreach ($purchaseItemsForCheck as $item) {
            $related_value = ($item->product && $item->product->unit && $item->product->unit->related_value > 0) ? $item->product->unit->related_value : 1;
            $sub_qty = $item->sub_qty ?? 0;
            $total_main = ($item->main_qty * $related_value) + $sub_qty;
            $total_qty += $total_main;
            $stock_qty += $item->stock_qty;
        }

        if ($stock_qty < $total_qty) {
            session()->flash('error', 'Cannot delete purchase because some of its items have already been sold.');
            return back();
        }

        $returnPurchases = \App\Models\ReturnPurchase::where('purchase_id', $purchase->id)->get();
        foreach ($returnPurchases as $rtnPur) {
            // Delete associated transactions & bank transactions
            Transaction::where('return_pur_id', $rtnPur->id)->delete();
            BankTransaction::where('return_pur_id', $rtnPur->id)->delete();

            \App\Models\ReturnPurchaseItem::where('rtnPurchase_id', $rtnPur->id)->delete();
            $rtnPur->delete();
        }
        $purchaseItem = PurchaseItem::where('purchase_id', $id)->get();
        foreach ($purchaseItem as $item) {
            $item->delete();
        }
        SerialNumber::where('purchase_id', $id)->delete();
        $transactions = Transaction::where('purchase_id', $purchase->id)->get();
        $bank_transactions = BankTransaction::where('purchase_id', $purchase->id)->delete();
        foreach ($transactions as $transaction) {
            if ($transaction->actual_pay_id != NULL) {
                $actualpay = ActualPayment::where('id', $transaction->actual_pay_id)->first();
                $actualpay->amount -= $transaction->debit;
                if ($actualpay->amount <= 0) {
                    $actualpay->delete();
                } else {
                    $actualpay->save();
                }
            }
            $transaction->delete();
        }
        // Capture info BEFORE delete
        $purchase->load('supplier', 'purchaseItems.product');
        logActivity('Delete Purchase', "Purchase #{$purchase->purchase_no} from {$purchase->supplier?->name} deleted", $purchase);

        $purchase->delete();
        session()->flash('success', 'Purchase deleted successfully');
        return back();
    }

    public function purchasePay($id)
    {
        //get purchase with supplier and user by id
        $purchase = Purchase::with('supplier', 'user', 'purchaseItems')
            ->where('id', $id)->first();

        if ($purchase->total_due <= 0) {
            return redirect()->back();
        }
        // return response()->json($purchase);
        //get all payment methods
        $bank_accounts = BankAccount::where('status', 1)->get();
        // return response()->json($payment_methods);

        return view('backend.pages.purchase.pay', compact('purchase', 'bank_accounts'));
    }

    public function storePurchaseLog(Request $request)
    {
        $id = $request->purchase_id;
        $type = $request->type;
        $this->createPurchaseDue($request, $id, $type);

        session()->flash('success', 'Due Paid successfully');
        return redirect()->route('purchase.index');
    }

    public function printPurchase($id)
    {
        $purchase = Purchase::with(['supplier', 'user', 'purchaseItems' => function ($query) {
            $query->where('is_return', '=', 0);
        }])->where('id', $id)->first();


        return view('backend.pages.purchase.print', compact('purchase'));
    }
    

    public function printReturnPurchase($id)
    {
        $purchase = Purchase::with(['supplier', 'user', 'purchaseItems' => function ($query) {
            $query->where('is_return', '=', 0);
        }])->where('id', $id)->first();


        return view('backend.pages.purchase.return_print', compact('purchase'));
    }
    
    function createPurchaseLog(Request $request, $id, $type)
    {
        //added total_paid and total_due in purchase table
        $purchase = Purchase::find($id);
        if ($request->type == 'Due Paid') {
            $purchase->total_paid = $purchase->total_paid + $request->paid_amount;
            $purchase->total_due = max(0, $purchase->total_due - $request->paid_amount);
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_paid = $purchase->rtn_total_paid + $request->paid_amount;
                $purchase->rtn_total_due = max(0, $purchase->rtn_total_due - $request->paid_amount);
            }
        } else {
            $purchase->total_paid = $request->paid_amount;
            $purchase->total_due = max(0, $request->due_amount);
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_paid = $request->paid_amount;
                $purchase->rtn_total_due = max(0, $purchase->rtn_total_amount - $request->paid_amount);
            }
        }
        //change purchase status to 1 if paid amount is equal to total amount
        if ($purchase->total_due <= 0.009 || (isset($request->due_amount) && $request->due_amount == 0)) {
            $purchase->total_due = 0;
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_due = 0;
            }
            $purchase->status = 1;
        }
        $purchase->save();

        DB::transaction(function () use ($request, $purchase, $id) {
            if ($purchase->save()) {
                //create bank transaction
                if ($request->paid_amount > 0) {
                    //create bank transaction
                    $bank_transaction = new BankTransaction();
                    $bank_transaction->trans_type = 'withdraw';
                    if ($request->type == 'Due Paid') {
                        $bank_transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $bank_transaction->date = $request->date;
                    }
                    $bank_transaction->bank_id = $request->bank_id;
                    $bank_transaction->purchase_id = $id;
                    $bank_transaction->amount = $request->paid_amount;
                    $bank_transaction->created_by = auth()->user()->id;
                    $bank_transaction->save();

                    // Transaction
                    $transaction = new Transaction();
                    $transaction->transaction_type = 'Paid to Supplier';
                    if ($request->type == 'Due Paid') {
                        $transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $transaction->date = $request->date;
                    }
                    $transaction->bank_id = $request->bank_id;
                    $transaction->purchase_id = $id;
                    $transaction->supplier_id = $request->supplier_id;
                    $transaction->debit = $request->paid_amount;
                    $transaction->credit = NULL;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });
    }

    function createPurchaseInv(Request $request, $id, $type)
    {
        //added total_paid and total_due in purchase table
        $purchase = Purchase::find($id);
        if ($request->type == 'Due Paid') {
            $purchase->total_paid = $purchase->total_paid + $request->paid_amount;
            $purchase->total_due = max(0, $purchase->total_due - $request->paid_amount);
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_paid = $purchase->rtn_total_paid + $request->paid_amount;
                $purchase->rtn_total_due = max(0, $purchase->rtn_total_due - $request->paid_amount);
            }
        } else {
            $purchase->total_paid = $request->paid_amount;
            $purchase->total_due = max(0, $request->due_amount);
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_paid = $request->paid_amount;
                $purchase->rtn_total_due = max(0, $purchase->rtn_total_amount - $request->paid_amount);
            }
        }
        //change purchase status to 1 if paid amount is equal to total amount
        if ($purchase->total_due <= 0.009 || (isset($request->due_amount) && $request->due_amount == 0)) {
            $purchase->total_due = 0;
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_due = 0;
            }
            $purchase->status = 1;
        }
        $purchase->save();

        DB::transaction(function () use ($request, $purchase, $id) {
            if ($purchase->save()) {
                //create bank transaction
                if ($request->paid_amount > 0) {
                    //create bank transaction
                    $bank_transaction = new BankTransaction();
                    $bank_transaction->trans_type = 'withdraw';
                    if (auth()->user()->branch_id == 1) {
                        $bank_transaction->branch_id = $request->branch_id;
                    } else {
                        $bank_transaction->branch_id = auth()->user()->branch_id;
                    }
                    $bank_transaction->pay_type = 'purchase';
                    if ($request->type == 'Due Paid') {
                        $bank_transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $bank_transaction->date = $request->date;
                    }
                    $bank_transaction->bank_id = $request->bank_id;
                    $bank_transaction->purchase_id = $id;
                    $bank_transaction->amount = $request->paid_amount;
                    $bank_transaction->created_by = auth()->user()->id;
                    $bank_transaction->save();

                    // Transaction
                    $transaction = new Transaction();

                    if (auth()->user()->branch_id == 1) {
                        $transaction->branch_id = $request->branch_id;
                    } else {
                        $transaction->branch_id = auth()->user()->branch_id;
                    }
                    $transaction->transaction_type = 'Paid to Supplier';
                    if ($request->type == 'Due Paid') {
                        $transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $transaction->date = $request->date;
                    }
                    $transaction->bank_id = $request->bank_id;
                    $transaction->purchase_id = $id;
                    $transaction->supplier_id = $request->supplier_id;
                    $transaction->debit = $request->paid_amount;
                    $transaction->credit = NULL;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });
    }
    function createPurchaseDue(Request $request, $id, $type)
    {
        //added total_paid and total_due in purchase table
        $purchase = Purchase::find($id);
        if ($request->type == 'Due Paid') {
            $purchase->total_paid = $purchase->total_paid + $request->paid_amount;
            $purchase->total_due = max(0, $purchase->total_due - $request->paid_amount);
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_paid = $purchase->rtn_total_paid + $request->paid_amount;
                $purchase->rtn_total_due = max(0, $purchase->rtn_total_due - $request->paid_amount);
            }
        } else {
            $purchase->total_paid = $request->paid_amount;
            $purchase->total_due = max(0, $request->due_amount);
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_paid = $request->paid_amount;
                $purchase->rtn_total_due = max(0, $purchase->rtn_total_amount - $request->paid_amount);
            }
        }
        //change purchase status to 1 if paid amount is equal to total amount
        if ($purchase->total_due <= 0.009 || (isset($request->due_amount) && $request->due_amount == 0)) {
            $purchase->total_due = 0;
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_due = 0;
            }
            $purchase->status = 1;
        }
        $purchase->save();

        DB::transaction(function () use ($request, $purchase, $id) {
            if ($purchase->save()) {
                //create bank transaction
                if ($request->paid_amount > 0) {
                    //create bank transaction
                    $bank_transaction = new BankTransaction();
                    $bank_transaction->trans_type = 'withdraw';
                    $bank_transaction->pay_type = 'purdue';
                    $bank_transaction->branch_id = $purchase->branch_id;
                    if ($request->type == 'Due Paid') {
                        $bank_transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $bank_transaction->date = $request->date;
                    }
                    $bank_transaction->bank_id = $request->bank_id;
                    $bank_transaction->purchase_id = $id;
                    $bank_transaction->amount = $request->paid_amount;
                    $bank_transaction->created_by = auth()->user()->id;
                    $bank_transaction->save();

                    // Transaction
                    $transaction = new Transaction();
                    $transaction->transaction_type = 'Paid to Supplier';
                    $transaction->branch_id = $purchase->branch_id;
                    if ($request->type == 'Due Paid') {
                        $transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $transaction->date = $request->date;
                    }
                    $transaction->bank_id = $request->bank_id;
                    $transaction->purchase_id = $id;
                    $transaction->supplier_id = $request->supplier_id;
                    $transaction->debit = $request->paid_amount;
                    $transaction->credit = NULL;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });
    }
    function createPurchaseUpd(Request $request, $id, $type)
    {
        //added total_paid and total_due in purchase table
        $purchase = Purchase::find($id);
        if ($request->type == 'Due Paid') {
            $purchase->total_paid = $purchase->total_paid + $request->paid_amount;
            $purchase->total_due = max(0, $purchase->total_due - $request->paid_amount);
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_paid = $purchase->rtn_total_paid + $request->paid_amount;
                $purchase->rtn_total_due = max(0, $purchase->rtn_total_due - $request->paid_amount);
            }
        } else {
            $purchase->total_paid = $request->paid_amount;
            $purchase->total_due = max(0, $request->due_amount);
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_paid = $request->paid_amount;
                $purchase->rtn_total_due = max(0, $purchase->rtn_total_amount - $request->paid_amount);
            }
        }
        //change purchase status to 1 if paid amount is equal to total amount
        if ($purchase->total_due <= 0.009 || (isset($request->due_amount) && $request->due_amount == 0)) {
            $purchase->total_due = 0;
            if ($purchase->rtn_total_amount > 0) {
                $purchase->rtn_total_due = 0;
            }
            $purchase->status = 1;
        }
        $purchase->save();

        DB::transaction(function () use ($request, $purchase, $id) {
            if ($purchase->save()) {
                //create bank transaction
                if ($request->paid_amount > 0) {
                    //create bank transaction
                    $bank_transaction = new BankTransaction();
                    $bank_transaction->trans_type = 'withdraw';
                    $bank_transaction->pay_type = 'purchase';
                    $bank_transaction->branch_id = $purchase->branch_id;
                    if ($request->type == 'Due Paid') {
                        $bank_transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $bank_transaction->date = $request->date;
                    }
                    $bank_transaction->bank_id = $request->bank_id;
                    $bank_transaction->purchase_id = $id;
                    $bank_transaction->amount = $request->paid_amount;
                    $bank_transaction->created_by = auth()->user()->id;
                    $bank_transaction->save();

                    // Transaction
                    $transaction = new Transaction();
                    $transaction->transaction_type = 'Paid to Supplier';
                    if ($request->type == 'Due Paid') {
                        $transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $transaction->date = $request->date;
                    }
                    $transaction->branch_id = $purchase->branch_id;
                    $transaction->bank_id = $request->bank_id;
                    $transaction->purchase_id = $id;
                    $transaction->supplier_id = $request->supplier_id;
                    $transaction->debit = $request->paid_amount;
                    $transaction->credit = NULL;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });
    }

    public function search(Request $request)
    {
        $query = $request->get('req');
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = $request->branch_id ?? session('branch_filter_id', auth()->user()->branch_id);

        $branchProductIds = null;
        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $branchProductIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
            }
        } else {
            $branchProductIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
        }

        // Try to resolve as a variation barcode first
        $resolved = resolveProductAndVariationFromBarcode($query);

        if ($resolved['product_id']) {
            // Exact variation barcode match — return just that product if in branch
            $productsQuery = \App\Models\Product::where('id', $resolved['product_id'])
                ->where('status', 1);

            if ($branchProductIds !== null) {
                $productsQuery->whereIn('id', $branchProductIds);
            }

            $products = $productsQuery->limit(10)->get();
        } else {
            // Fallback: search by name or barcode
            $productsQuery = Product::where('status', 1)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', '%' . $query . '%')
                        ->orWhere('barcode', 'LIKE', '%' . $query . '%');
                });

            if ($branchProductIds !== null) {
                $productsQuery->whereIn('id', $branchProductIds);
            }

            if (env('APP_IMEI') != 'yes') {
                $productsQuery->where(function ($q) {
                    $q->where('imei', '!=', 1)->orWhereNull('imei');
                });
            }

            $products = $productsQuery->limit(10)->get();
        }

        return response()->json($products);
    }
}