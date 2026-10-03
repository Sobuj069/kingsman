<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ReturnTbl;
use App\Models\ReturnItem;
use App\Models\BankAccount;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Models\ActualPayment;
use App\Models\BankTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReturnSaleController extends Controller
{
    public function index(Request $request)
    {
        $data['customers'] = Customer::orderBy('name', 'asc')->get();
        $data['customer_id'] = $request->customer_id;
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;

        $query = ReturnTbl::with(['customer', 'invoice', 'returnItems.product']);

        if ($request->customer_id != null) {
            $query->where('customer_id', $request->customer_id);
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
                $q->whereHas('invoice', function ($subQ) use ($barcodeVal) {
                    $subQ->where('invoice_no', 'like', "%{$barcodeVal}%");
                })
                ->orWhereHas('returnItems.product', function ($subQ) use ($barcodeVal) {
                    $subQ->where('barcode', $barcodeVal)
                         ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                         ->orWhere('name', 'like', "%{$barcodeVal}%");
                })
                ->orWhereHas('returnItems', function ($subQ) use ($barcodeVal) {
                    $subQ->where('imei', 'like', "%{$barcodeVal}%");
                });
            });
        }

        $data['returns'] = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->all());
        return view('backend.pages.return.sale.index', $data);
    }

    public function create($id)
    {
        $invoice = Invoice::with('invoiceItems.product')->where('id', $id)->first();
        if (!$invoice) {
            session()->flash('error', 'Invoice not found');
            return redirect()->back();
        }

        if ($invoice->status == 2) {
            session()->flash('warning', 'Product already returned');
            return redirect()->back();
        }

        // If all items in this invoice are IMEI products and all their IMEIs have been repurchased:
        $hasReturnableItem = false;
        $activeItems = $invoice->invoiceItems->where('is_return', 0);
        foreach ($activeItems as $actItem) {
            if ($actItem->product && (int)$actItem->product->imei === 1) {
                $itemImeis = array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string)$actItem->imei)));
                foreach ($itemImeis as $im) {
                    if (!isImeiRepurchased($actItem->product_id, $im, $invoice)) {
                        $hasReturnableItem = true;
                        break 2;
                    }
                }
            } else {
                $hasReturnableItem = true;
                break;
            }
        }

        if ($activeItems->isNotEmpty() && !$hasReturnableItem) {
            session()->flash('error', __('Cannot return this invoice because all its IMEI products have already been repurchased.'));
            return redirect()->back();
        }

        $data['invoice'] = $invoice;
        $data['bank_accounts'] = BankAccount::where('status', 1)->get();
        return view('backend.pages.return.sale.create', $data);
    }

    public function insert(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        $branch_id = $invoice->branch_id;

        // Validation: Return quantity cannot exceed remaining sold quantity
        if ($request->has('item_id') && is_array($request->item_id)) {
            foreach ($request->item_id as $key => $item_id) {
                $invoiceItem = InvoiceItem::find($item_id);
                if (!$invoiceItem) {
                    continue;
                }

                $mainQty = (float) ($request->main_qty[$key] ?? 0);
                $subQty  = (float) ($request->sub_qty[$key] ?? 0);

                if ($mainQty < 0 || $subQty < 0) {
                    session()->flash('error', __('Return quantity cannot be negative.'));
                    return redirect()->back()->withInput();
                }

                if ($mainQty > $invoiceItem->actual_main || $subQty > $invoiceItem->actual_sub) {
                    $prod = Product::find($request->product_id[$key]);
                    $prodName = $prod?->name ?? __('Product');
                    session()->flash('error', __("Return quantity for ':prod' cannot exceed the remaining sold quantity (:max).", [
                        'prod' => $prodName,
                        'max'  => $invoiceItem->actual_main
                    ]));
                    return redirect()->back()->withInput();
                }
            }
        }

        $returnDate = !empty($request->date) && $request->date !== $invoice->date ? Carbon::parse($request->date)->toDateString() : date('Y-m-d');

        $return = new ReturnTbl();
        $return->date = $returnDate;
        $return->invoice_id = $request->invoice_id;
        $return->branch_id = $branch_id;
        $return->customer_id = $request->customer_id;
        $return->estimated_amount = $request->estimated_amount;
        $return->discount_amount = $request->discount_amount;
        $return->total_return = $request->payable_amount;
        $return->created_by = auth()->id();

        DB::transaction(function () use ($request, $return, $branch_id, $returnDate) {

            if ($return->save()) {

                foreach ($request->item_id as $key => $item_id) {

                    $productId = $request->product_id[$key];
                    $variationId = $request->product_variation_id[$key] ?? null;

                    $product = Product::with('unit')->find($productId);

                    $mainQty = $request->main_qty[$key] ?? 0;
                    $subQty  = $request->sub_qty[$key] ?? 0;

                    if ($product->unit->related_unit == null) {
                        $qty = $mainQty;
                        $pur_sub = $request->purchase_price[$key] * $qty;
                    } else {
                        $main = $mainQty * $product->unit->related_value;
                        $qty = $main + $subQty;
                        $pur_sub = $request->purchase_price[$key] * ($qty / $product->unit->related_value);
                    }

                    /*
                ========================
                RETURN ITEM SAVE
                ========================
                */

                    $returnItem = new ReturnItem();
                    $returnItem->date = $returnDate;
                    $returnItem->return_id = $return->id;
                    $returnItem->branch_id = $branch_id;
                    $returnItem->invoice_id = $request->invoice_id;
                    $returnItem->product_id = $productId;
                    $returnItem->product_variation_id = $variationId;
                    $returnItem->rate = $request->new_rate[$key];
                    $returnItem->purchase_price = $request->purchase_price[$key];
                    $returnItem->main_qty = $mainQty;
                    $returnItem->sub_qty = $subQty;
                    $returnItem->subtotal = $request->sub_total[$key];
                    $returnItem->pur_subtotal = $pur_sub;

                    $returnedImeis = [];
                    if ($product && (int) $product->imei === 1) {
                        $returnedImeis = $this->parseImeis($request->imei[$key] ?? null);
                        if (count($returnedImeis) !== (int)$qty) {
                            throw new \Exception("Returned IMEI count must match returned quantity for product " . ($product->name ?? ''));
                        }

                        foreach ($returnedImeis as $rImei) {
                            $rImeiTrim = trim($rImei);
                            if (!empty($rImeiTrim)) {
                                if (isImeiRepurchased($productId, $rImeiTrim, $invoice)) {
                                    throw new \Exception("IMEI '{$rImeiTrim}' has already been repurchased. Sale Return is not allowed for repurchased IMEIs.");
                                }
                            }
                        }

                        $returnItem->imei = implode("\n", $returnedImeis);
                    }
                    $returnItem->save();


                    /*
                ========================
                INVOICE ITEM UPDATE
                ========================
                */

                    $invoiceItem = InvoiceItem::find($item_id);

                    if ($product && (int) $product->imei === 1 && !empty($returnedImeis)) {
                        $oldImeis = $this->parseImeis($invoiceItem->imei);
                        $remainingImeis = array_diff($oldImeis, $returnedImeis);
                        $invoiceItem->imei = implode("\n", $remainingImeis);
                        
                        \App\Models\SerialNumber::where('product_id', $productId)
                            ->whereIn('serial', array_map('trim', $returnedImeis))
                            ->update(['status' => 1]);
                    }

                    $newMainQty = $invoiceItem->main_qty - $mainQty;
                    $newSubQty  = $invoiceItem->sub_qty - $subQty;

                    $newRtnMain = $invoiceItem->rtn_main + $mainQty;
                    $newRtnSub  = $invoiceItem->rtn_sub + $subQty;

                    $actualMain = $newMainQty;
                    $actualSub  = $newSubQty;

                    $isReturn = ($actualMain <= 0 && $actualSub <= 0) ? 1 : 0;

                    $invoiceItem->update([
                        'imei' => $invoiceItem->imei,

                        'rtn_main' => $newRtnMain,
                        'rtn_sub'  => $newRtnSub,
                        'rtn_total' => $invoiceItem->rtn_total + $request->sub_total[$key],

                        'actual_main' => $actualMain,
                        'actual_sub'  => $actualSub,
                        'actual_total' => max(0, $invoiceItem->actual_total - $request->sub_total[$key]),

                        'is_return' => $isReturn
                    ]);


                    /*
                ========================
                STOCK RESTORE (LIFO)
                ========================
                */
                    restoreToFIFO($productId, $qty, $variationId, $branch_id);
                }

                /*
            ========================
            TRANSACTION
            ========================
            */

                $transaction = new Transaction();
                $transaction->transaction_type = 'Invoice Return';
                $transaction->date = $returnDate;
                $transaction->branch_id = $branch_id;
                $transaction->bank_id = $request->bank_id;
                $transaction->return_id = $return->id;
                $transaction->customer_id = $request->customer_id;
                $transaction->credit = $request->payable_amount;
                $transaction->created_by = auth()->id();
                $transaction->save();

                $this->createReturnLog($request, $return->id, 'Invoice Return', $returnDate);
            }
        });

        $return->load('customer', 'returnItems.product');
        session()->flash('success', 'Sale Product Return Successfully');
        logActivity('Sale Return', "Return for Invoice #{$invoice->invoice_no} by {$return->customer?->name}", $return);

        return redirect()->route('return.sale');
    }

    function createReturnLog(Request $request, $id, $type, $returnDate = null)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        $rtnTable = ReturnTbl::findOrFail($id);
        $returnDate = $returnDate ?: $rtnTable->date ?: date('Y-m-d');
        $branch_id = $invoice->branch_id;

        $returnAmount = (float) $rtnTable->total_return;
        $oldDue = (float) $invoice->total_due;
        $oldPaid = (float) $invoice->total_paid;
        $oldTotal = (float) $invoice->total_amount;

        $newEstimated = max(0, $invoice->estimated_amount - $rtnTable->estimated_amount);
        $newDiscountAmount = max(0, $invoice->discount_amount - $rtnTable->discount_amount);
        $newTotal = max(0, $oldTotal - $returnAmount);

        // Determine cash refund vs due reduction:
        if ($oldDue >= $returnAmount) {
            // Return amount reduces customer due
            $cashRefund = 0;
            $newDue = $oldDue - $returnAmount;
            $newPaid = $oldPaid;
        } else {
            // Return amount covers all remaining due, and the rest is cash refund to customer
            $cashRefund = $returnAmount - $oldDue;
            $newDue = 0;
            $newPaid = max(0, $oldPaid - $cashRefund);
        }

        $invoice->update([
            'estimated_amount' => $newEstimated,
            'discount_amount'  => $newDiscountAmount,
            'total_amount'     => $newTotal,
            'total_due'        => $newDue,
            'total_paid'       => $newPaid,
            'return_amount'    => $invoice->return_amount + $returnAmount,
            'status'           => ($newTotal <= 0 || ($newDue <= 0 && $newPaid <= 0)) ? 2 : ($newDue > 0 ? 0 : 1)
        ]);

        /*
    =========================
    CHECK INVOICE ITEMS RETURN
    =========================
    */
        $invoice_items = InvoiceItem::where('invoice_id', $invoice->id)->get();
        foreach ($invoice_items as $item) {
            if ($item->actual_main <= 0 && $item->actual_sub <= 0) {
                $item->update(['is_return' => 1]);
            }
        }

        $remainingItems = InvoiceItem::where('invoice_id', $invoice->id)
            ->where(function ($q) {
                $q->where('actual_main', '>', 0)->orWhere('actual_sub', '>', 0);
            })->count();

        if ($remainingItems == 0) {
            $invoice->update(['status' => 2]);
        }

        /*
    =========================
    CASH / BANK REFUND TRANSACTION
    =========================
    */
        if ($cashRefund > 0) {
            // 1. Bank Transaction (Withdrawal from selected Bank/Cash account)
            $bank_transaction = new BankTransaction();
            $bank_transaction->trans_type = 'withdraw';
            $bank_transaction->date = $returnDate;
            $bank_transaction->bank_id = $request->bank_id;
            $bank_transaction->branch_id = $branch_id; // Set branch_id so current_balance() deducts it from Cash balance!
            $bank_transaction->return_id = $id;
            $bank_transaction->invoice_id = $invoice->id;
            $bank_transaction->pay_type = 'rtn_pay';
            $bank_transaction->amount = $cashRefund;
            $bank_transaction->created_by = auth()->id();
            $bank_transaction->save();

            // 2. Transaction Log (Money returned to customer)
            $transaction = new Transaction();
            $transaction->transaction_type = 'Return Money to customer';
            $transaction->date = $returnDate;
            $transaction->bank_id = $request->bank_id;
            $transaction->branch_id = $branch_id;
            $transaction->return_id = $id;
            $transaction->invoice_id = $invoice->id;
            $transaction->customer_id = $request->customer_id;
            $transaction->debit = $cashRefund;
            $transaction->credit = null;
            $transaction->created_by = auth()->id();
            $transaction->save();
        }
    }

    public function delete($id)
    {
        $return = ReturnTbl::find($id);
        if (!$return) {
            session()->flash('warning', 'Return not found');
            return redirect()->back();
        }

        $invoice = Invoice::find($return->invoice_id);
        $returnItems = ReturnItem::where('return_id', $return->id)->get();

        // Capture info BEFORE delete
        $return->load('customer', 'returnItems.product');
        logActivity('Delete Sale Return', "Sale Return for Invoice #{$invoice?->invoice_no} deleted", $return);

        try {
            DB::transaction(function () use ($return, $invoice, $returnItems) {
                foreach ($returnItems as $rtnItem) {
                    // Find Invoice Item
                    $invoiceItem = InvoiceItem::where('invoice_id', $return->invoice_id)
                        ->where('product_id', $rtnItem->product_id)
                        ->where('product_variation_id', $rtnItem->product_variation_id)
                        ->first();

                    if ($invoiceItem) {
                        $product = Product::with('unit')->find($rtnItem->product_id);

                        // Restore quantities in InvoiceItem
                        $invoiceItem->rtn_main = max(0, $invoiceItem->rtn_main - $rtnItem->main_qty);
                        $invoiceItem->rtn_sub = max(0, $invoiceItem->rtn_sub - $rtnItem->sub_qty);
                        $invoiceItem->rtn_total = max(0, $invoiceItem->rtn_total - $rtnItem->subtotal);

                        $invoiceItem->actual_main = $invoiceItem->main_qty - $invoiceItem->rtn_main;
                        $invoiceItem->actual_sub = $invoiceItem->sub_qty - $invoiceItem->rtn_sub;
                        $invoiceItem->actual_total = max(0, $invoiceItem->subtotal - $invoiceItem->rtn_total);

                        if ($invoiceItem->actual_main > 0 || $invoiceItem->actual_sub > 0) {
                            $invoiceItem->is_return = 0;
                        }

                        // Handle IMEI restoration
                        if ($product && (int)$product->imei === 1 && !empty($rtnItem->imei)) {
                            $rtnImeis = $this->parseImeis($rtnItem->imei);
                            $currentInvImeis = $this->parseImeis($invoiceItem->imei);
                            $combinedImeis = array_unique(array_merge($currentInvImeis, $rtnImeis));
                            $invoiceItem->imei = implode("\n", $combinedImeis);

                            // Set serial numbers back to status 0 (sold)
                            \App\Models\SerialNumber::where('product_id', $rtnItem->product_id)
                                ->whereIn('serial', array_map('trim', $rtnImeis))
                                ->update(['status' => 0]);
                        }

                        $invoiceItem->save();

                        // Re-deduct stock using FIFO since the returned item is sold again
                        if ($product && $product->is_service == 0) {
                            if ($product->unit->related_unit == null) {
                                $qty = $rtnItem->main_qty;
                            } else {
                                $main = $rtnItem->main_qty * $product->unit->related_value;
                                $qty = $main + $rtnItem->sub_qty;
                            }
                            reduceStockFIFO($rtnItem->product_id, $qty, $rtnItem->product_variation_id, $rtnItem->branch_id);
                        }
                    }
                }

                // Restore Invoice Amount and Status
                if ($invoice) {
                    $invoice->estimated_amount += $return->estimated_amount;
                    $invoice->discount_amount += $return->discount_amount;
                    $invoice->total_amount += $return->total_return;
                    $invoice->return_amount -= $return->total_return;

                    // Paid / Due Logic reversal:
                    // When deleting a return, the total_amount goes up by estimated_amount.
                    // We add it back to their due, unless it was originally paid fully (which we can check by seeing if total_paid + new due != total_amount).
                    // Actually, the simplest way to restore is recalculating due:
                    // New Due = New Total Amount - Current Total Paid
                    $invoice->total_due = $invoice->total_amount - $invoice->total_paid;
                    
                    // Note: The physical cash that was refunded will be handled by deleting the BankTransaction (handled below).
                    // We must also revert the total_paid if we gave them cash back.
                    // How much cash did we give back? It was stored in ReturnTbl's total_return.
                    // But total_return was $request->payable_amount, which might not be the actual cash given if due was cleared.
                    // Let's just rely on the Transactions to find how much cash was given back.
                    $refundedCash = Transaction::where('return_id', $return->id)->where('transaction_type', 'Return Money to customer')->sum('debit');
                    $invoice->total_paid += $refundedCash;
                    $invoice->total_due = $invoice->total_amount - $invoice->total_paid;

                    $invoice->status = 1; // Set back to partial or open status
                    $invoice->save();
                }

                // Delete Transactions
                $transactions = Transaction::where('return_id', $return->id)->get();
                $bank_transactions = BankTransaction::where('return_id', $return->id)->get();
                foreach ($transactions as $transaction) {
                    if ($transaction->actual_pay_id != NULL) {
                        $actualpay = ActualPayment::where('id', $transaction->actual_pay_id)->first();
                        if ($actualpay) {
                            $actualpay->amount -= $transaction->debit;
                            if ($actualpay->amount <= 0) {
                                $actualpay->delete();
                            } else {
                                $actualpay->save();
                            }
                        }
                    }
                    $transaction->delete();
                }
                foreach ($bank_transactions as $bank) {
                    $bank->delete();
                }

                $return->delete();
            });

            session()->flash('success', 'Return deleted successfully. Items moved back to sale and stock updated.');
            return back();

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    private function parseImeis($value): array
    {
        if (is_array($value)) {
            $tokens = [];
            foreach ($value as $v) {
                $sub = preg_split('/[\r\n,]+/', (string)$v) ?: [];
                foreach ($sub as $s) {
                    $s = trim($s);
                    if ($s !== '') $tokens[] = $s;
                }
            }
            return array_values(array_unique($tokens));
        }

        $value = trim((string) $value);
        if ($value === '') {
            return [];
        }

        $parts = preg_split('/[\r\n,]+/', $value) ?: [];
        $parts = array_values(array_filter(array_map(static fn ($v) => trim((string) $v), $parts), static fn ($v) => $v !== ''));
        return $parts;
    }
}
