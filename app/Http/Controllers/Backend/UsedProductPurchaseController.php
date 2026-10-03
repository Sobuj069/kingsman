<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\UsedProduct;
use App\Models\UsedPurchase;
use App\Models\UsedPurchaseItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsedProductPurchaseController extends Controller
{
    public function index(Request $request)
    {
        //get all purchase with supplier and user
        $barcode = $request->barcode ?? $request->purchase_no;
        $data['barcode'] = $barcode;
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;
        $data['purchase_no'] = $request->purchase_no;
        $data['supplier_id'] = $request->supplier_id;
        $data['product_id'] = $request->product_id;
        $sdate = $request->startDate ? Carbon::createFromDate($request->startDate)->toDateString() : null;
        $edate = $request->endDate ? Carbon::createFromDate($request->endDate)->toDateString() : null;

        $query = UsedPurchase::with(['supplier', 'user', 'purchaseItems.product']);

        if ($sdate && $edate) {
            $query->whereBetween('date', [$sdate, $edate]);
        }

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

        if ($request->supplier_id != null) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->product_id != null) {
            $prodId = $request->product_id;
            $query->whereHas('purchaseItems', function ($q) use ($prodId) {
                $q->where('product_id', $prodId);
            });
        }

        $data['purchases'] = $query->orderBy('created_at', 'DESC')->paginate(10)->appends($request->all());

        return view('backend.pages.usedProduct.purchase.index', $data);
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 1)->get();
        $categories = Category::orderBy('name', 'asc')->get();
        $products = Product::orderBy('name', 'asc')->get();
        $bank_accounts = BankAccount::where('status', 1)->get();
        $purchase = UsedPurchase::orderBy('id', 'desc')->select('purchase_no')->first();

        if ($purchase == null) {
            $purchase_no = "Buy-0000001";
        } else {
            $purchase_no = $purchase->purchase_no;
            $purchase_no++;
        }

        return view('backend.pages.usedProduct.purchase.create', compact('suppliers', 'categories', 'products', 'bank_accounts', 'purchase_no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request->all();
        //store purchase data
        $purchase = new UsedPurchase();
        $purchase->date = $request->date;
        $purchase->purchase_no = $request->purchase_no;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->estimated_amount = $request->estimated_amount;
        $purchase->discount = $request->discount_amount;
        $purchase->total_amount = $request->total_amount;
        $purchase->note = $request->note;
        $purchase->created_by = auth()->user()->id;

        DB::transaction(function () use ($request, $purchase) {
            if ($purchase->save()) {
                //save product_id and category_id in purchase_items table where $request->product_id is array of product_id
                foreach ($request->new_product as $key => $product_id) {
                    $purchase_item = new UsedPurchaseItem();
                    $find_unit_id = UsedProduct::where('id', $product_id)->first();
                    $purchase_item->date = $request->date;
                    $purchase_item->purchase_id = $purchase->id;
                    $purchase_item->product_id = $product_id;
                    $purchase_item->rate = $request->new_rate[$key];
                    if ($find_unit_id->unit->related_unit == null) {
                        $purchase_item->main_qty = $request->new_main_qty[$key];
                    } else {
                        $purchase_item->main_qty = $request->new_main_qty[$key];
                        $purchase_item->sub_qty = $request->new_sub_qty[$key];
                    }
                    $purchase_item->subtotal = $request->new_subtotal_input[$key];
                    $purchase_item->save();
                }

                //create purchase log call createPurchaseLog function
                $id = $purchase->id;
                $type = 'Purchase';

                // Transaction
                $transaction = new Transaction();
                $transaction->transaction_type = $type;
                $transaction->date = $request->date;
                $transaction->used_purchase_id = $id;
                $transaction->supplier_id = $request->supplier_id;
                $transaction->debit = NULL;
                $transaction->credit = $request->total_amount;
                $transaction->created_by = auth()->user()->id;
                $transaction->save();
                $this->createPurchaseLog($request, $id, $type);
            }
        });

        session()->flash('success', 'New Buy Created Successfully');
        return redirect()->route('usedPurchase.index');
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
        //
    }
    function createPurchaseLog(Request $request, $id, $type)
    {
        //added total_paid and total_due in purchase table
        $purchase = UsedPurchase::find($id);
        if ($request->type == 'Due Paid') {
            $purchase->total_paid = $purchase->total_paid + $request->paid_amount;
            $purchase->total_due = $purchase->total_due - $request->paid_amount;
        } else {
            $purchase->total_paid = $request->paid_amount;
            $purchase->total_due = $request->due_amount;
        }
        //change purchase status to 1 if paid amount is equal to total amount
        if ($request->due_amount == 0) {
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
                    $bank_transaction->used_purchase_id = $id;
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
                    $transaction->used_purchase_id = $id;
                    $transaction->supplier_id = $request->supplier_id;
                    $transaction->debit = $request->paid_amount;
                    $transaction->credit = NULL;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });
    }
}
