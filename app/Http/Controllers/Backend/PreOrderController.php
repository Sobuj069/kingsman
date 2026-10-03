<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreOrderController extends Controller
{
    /**
     * Display a listing of pre-orders.
     */
    public function index(Request $request)
    {
        if (env('APP_ONLINE') != 'yes') {
            abort(403, 'Pre-Order module is disabled.');
        }

        $userBranchId = auth()->user()->branch_id;

        $query = PreOrder::with(['customer', 'branch', 'user', 'items.product']);

        if ($userBranchId != 1) {
            $query->where('branch_id', $userBranchId);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // By default show only pending pre-orders unless explicitly filtered
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        if ($request->filled('pre_order_no')) {
            $query->where('pre_order_no', 'LIKE', '%' . $request->pre_order_no . '%');
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $data['preOrders'] = $query->orderBy('created_at', 'DESC')->paginate(20)->appends($request->all());
        $data['customers'] = Customer::orderBy('name', 'ASC')->get();
        $data['branches']  = Branch::orderBy('name', 'ASC')->get();

        return view('backend.pages.pre_order.index', $data);
    }

    /**
     * Store a new pre-order from POS (without deducting stock).
     */
    public function store(Request $request)
    {
        if (env('APP_ONLINE') != 'yes') {
            return response()->json(['status' => 'error', 'message' => __('Pre-Order module is disabled.')], 403);
        }

        // Map POS input names (main_qty / quantity_input -> quantity, rate -> unit_price)
        $quantities = $request->input('quantity', $request->input('main_qty', $request->input('quantity_input', [])));
        $unitPrices = $request->input('unit_price', $request->input('rate', []));

        $request->merge([
            'quantity'   => $quantities,
            'unit_price' => $unitPrices,
        ]);

        $request->validate([
            'customer_id' => 'required',
            'product_id'   => 'required|array|min:1',
            'quantity'     => 'required|array|min:1',
            'unit_price'   => 'required|array|min:1',
        ]);

        if ($request->customer_id == 1) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Walk-in Customer cannot create a Pre-Order. Please select a registered customer.')
            ], 422);
        }

        if ($request->filled('pre_order_id')) {
            $preOrder = PreOrder::find($request->pre_order_id);
            if ($preOrder && $preOrder->status == 'pending') {
                DB::beginTransaction();
                try {
                    $totalAmount  = 0;
                    $productIds   = $request->product_id;
                    $quantities   = $request->quantity;
                    $unitPrices   = $request->unit_price;
                    $variationIds = $request->input('variation_id', []);
                    $imeis        = $request->input('imei', []);

                    foreach ($productIds as $index => $pid) {
                        $qty   = (float) ($quantities[$index] ?? 1);
                        $price = (float) ($unitPrices[$index] ?? 0);
                        $totalAmount += ($qty * $price);
                    }

                    $preOrder->update([
                        'customer_id'  => $request->customer_id,
                        'total_amount' => $totalAmount,
                        'note'         => $request->note ?? null,
                    ]);

                    PreOrderItem::where('pre_order_id', $preOrder->id)->delete();

                    foreach ($productIds as $index => $pid) {
                        $qty   = (float) ($quantities[$index] ?? 1);
                        $price = (float) ($unitPrices[$index] ?? 0);
                        $varId = !empty($variationIds[$index]) ? (int)$variationIds[$index] : null;
                        $imei  = $imeis[$index] ?? null;

                        PreOrderItem::create([
                            'pre_order_id'         => $preOrder->id,
                            'product_id'           => $pid,
                            'product_variation_id' => $varId,
                            'quantity'             => $qty,
                            'unit_price'           => $price,
                            'subtotal'             => $qty * $price,
                            'imei'                 => $imei,
                        ]);
                    }

                    DB::commit();

                    return response()->json([
                        'status'  => 'success',
                        'message' => __("Pre-Order :no updated successfully!", ['no' => $preOrder->pre_order_no]),
                        'pre_order' => $preOrder
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                }
            }
        }

        DB::beginTransaction();
        try {
            // Generate Pre-Order Number
            $lastPreOrder = PreOrder::orderBy('id', 'desc')->first();
            if (!$lastPreOrder) {
                $preOrderNo = "PRE-0000001";
            } else {
                $numberPart = (int) substr($lastPreOrder->pre_order_no, strlen("PRE-"));
                $preOrderNo = "PRE-" . str_pad($numberPart + 1, 7, '0', STR_PAD_LEFT);
            }

            $userBranchId = auth()->user()->branch_id;
            $branchId = ($userBranchId == 1 && $request->filled('branch_id')) ? $request->branch_id : $userBranchId;

            $totalAmount  = 0;
            $productIds   = $request->product_id;
            $quantities   = $request->quantity;
            $unitPrices   = $request->unit_price;
            $variationIds = $request->input('variation_id', []);
            $imeis        = $request->input('imei', []);

            foreach ($productIds as $index => $pid) {
                $qty   = (float) ($quantities[$index] ?? 1);
                $price = (float) ($unitPrices[$index] ?? 0);
                $totalAmount += ($qty * $price);
            }

            $preOrder = PreOrder::create([
                'pre_order_no' => $preOrderNo,
                'customer_id'  => $request->customer_id,
                'branch_id'    => $branchId,
                'created_by'   => auth()->user()->id,
                'total_amount' => $totalAmount,
                'note'         => $request->note ?? null,
                'status'       => 'pending',
            ]);

            foreach ($productIds as $index => $pid) {
                $qty   = (float) ($quantities[$index] ?? 1);
                $price = (float) ($unitPrices[$index] ?? 0);
                $varId = !empty($variationIds[$index]) ? (int)$variationIds[$index] : null;
                $imei  = $imeis[$index] ?? null;

                PreOrderItem::create([
                    'pre_order_id'         => $preOrder->id,
                    'product_id'           => $pid,
                    'product_variation_id' => $varId,
                    'quantity'             => $qty,
                    'unit_price'           => $price,
                    'subtotal'             => $qty * $price,
                    'imei'                 => $imei,
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => __("Pre-Order :no created successfully! (No stock deducted)", ['no' => $preOrderNo]),
                    'pre_order' => $preOrder
                ]);
            }

            session()->flash('success', __("Pre-Order :no created successfully! (No stock deducted)", ['no' => $preOrderNo]));
            return redirect()->route('pre-orders.index');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            session()->flash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Convert Pre-Order to Sale (validates available stock first).
     */
    public function convertToSale($id)
    {
        if (env('APP_ONLINE') != 'yes') {
            session()->flash('error', __('Pre-Order module is disabled.'));
            return redirect()->back();
        }

        $preOrder = PreOrder::with('items.product', 'customer')->find($id);

        if (!$preOrder) {
            session()->flash('error', __('Pre-Order not found.'));
            return redirect()->back();
        }

        if ($preOrder->status != 'pending') {
            session()->flash('error', __('This Pre-Order has already been processed or cancelled.'));
            return redirect()->back();
        }

        // --- Stock Availability Validation ---
        $insufficientItems = [];

        foreach ($preOrder->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }

            $purchase = (float) purchased_qty($product);
            $sale     = (float) invoiced_qty($product);
            $sale_ret = (float) returned_qty($product);
            $pur_ret  = (float) return_pur_qty($product);
            $damage   = (float) damaged_qty($product);
            $adjust_in_total  = (float) adjust_in($product);
            $adjust_out_total = (float) adjust_out($product);

            $availableStock = $purchase - $sale + $adjust_in_total - $adjust_out_total - $pur_ret - $damage;

            if ($availableStock < $item->quantity) {
                $insufficientItems[] = [
                    'product_name' => $product->name,
                    'required_qty' => $item->quantity,
                    'available_stock' => max(0, (int)$availableStock),
                ];
            }
        }

        if (!empty($insufficientItems)) {
            $errMsg = __('Stock is INSUFFICIENT to convert this Pre-Order into a Sale. Please Purchase/Restock first!') . "<br><ul class='mt-2 mb-0 pl-3'>";
            foreach ($insufficientItems as $err) {
                $errMsg .= "<li><b>" . e($err['product_name']) . "</b> — Required: " . $err['required_qty'] . ", Available: " . $err['available_stock'] . "</li>";
            }
            $errMsg .= "</ul>";

            session()->flash('error', $errMsg);
            return redirect()->back();
        }

        // --- Convert Pre-Order to Invoice (Sale & Stock Out) ---
        if ($request->filled('pre_order_id')) {
            $preOrder = PreOrder::find($request->pre_order_id);
            if ($preOrder && $preOrder->status == 'pending') {
                DB::beginTransaction();
                try {
                    $totalAmount = 0;
                    $productIds  = $request->product_id;
                    $quantities  = $request->quantity;
                    $unitPrices  = $request->unit_price;

                    foreach ($productIds as $index => $pid) {
                        $qty   = (float) ($quantities[$index] ?? 1);
                        $price = (float) ($unitPrices[$index] ?? 0);
                        $totalAmount += ($qty * $price);
                    }

                    $preOrder->update([
                        'customer_id'  => $request->customer_id,
                        'total_amount' => $totalAmount,
                        'note'         => $request->note ?? null,
                    ]);

                    PreOrderItem::where('pre_order_id', $preOrder->id)->delete();

                    foreach ($productIds as $index => $pid) {
                        $qty   = (float) ($quantities[$index] ?? 1);
                        $price = (float) ($unitPrices[$index] ?? 0);

                        PreOrderItem::create([
                            'pre_order_id' => $preOrder->id,
                            'product_id'   => $pid,
                            'quantity'     => $qty,
                            'unit_price'   => $price,
                            'subtotal'     => $qty * $price,
                        ]);
                    }

                    DB::commit();

                    return response()->json([
                        'status'  => 'success',
                        'message' => __("Pre-Order :no updated successfully!", ['no' => $preOrder->pre_order_no]),
                        'pre_order' => $preOrder
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                }
            }
        }

        DB::beginTransaction();
        try {
            // Generate Invoice No
            $lastInvoice = Invoice::where('invoice_no', 'LIKE', 'INV-%')->orderBy('id', 'desc')->first();
            if (!$lastInvoice) {
                $nextNumber = 1;
            } else {
                $digits = preg_replace('/\D/', '', $lastInvoice->invoice_no);
                $nextNumber = ((int) $digits) + 1;
            }

            $invoiceNo = "INV-" . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
            while (Invoice::where('invoice_no', $invoiceNo)->exists()) {
                $nextNumber++;
                $invoiceNo = "INV-" . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
            }

            $invoice = new Invoice();
            $invoice->date = date('Y-m-d');
            $invoice->unique_id = uniqid();
            $invoice->invoice_no = $invoiceNo;
            $invoice->customer_id = $preOrder->customer_id;
            $invoice->estimated_amount = $preOrder->total_amount;
            $invoice->total_amount = $preOrder->total_amount;
            $invoice->branch_id = $preOrder->branch_id;
            $invoice->created_by = auth()->user()->id;
            $invoice->sale_type = 'Online';
            $invoice->note = "Converted from Pre-Order #" . $preOrder->pre_order_no . ($preOrder->note ? (" | " . $preOrder->note) : "");
            $invoice->save();

            foreach ($preOrder->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal'   => $item->subtotal,
                ]);
            }

            $preOrder->status = 'converted';
            $preOrder->save();

            DB::commit();

            session()->flash('success', __("Pre-Order :pre_no converted to Sale Invoice :inv_no successfully! Stock deducted.", ['pre_no' => $preOrder->pre_order_no, 'inv_no' => $invoiceNo]));
            return redirect()->route('invoice.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __('Conversion failed: ') . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Cancel a Pre-Order.
     */
    public function cancel($id)
    {
        $preOrder = PreOrder::find($id);
        if ($preOrder && $preOrder->status == 'pending') {
            $preOrder->status = 'cancelled';
            $preOrder->save();
            session()->flash('success', __('Pre-Order cancelled successfully.'));
        } else {
            session()->flash('error', __('Pre-order cannot be cancelled.'));
        }
        return redirect()->back();
    }

    /**
     * Show edit form for a pending pre-order.
     */
    public function edit($id)
    {
        $preOrder = PreOrder::with('items.product', 'customer', 'branch')->find($id);

        if (!$preOrder) {
            session()->flash('error', __('Pre-Order not found.'));
            return redirect()->route('pre-orders.index');
        }

        if ($preOrder->status != 'pending') {
            session()->flash('error', __('Only pending pre-orders can be edited.'));
            return redirect()->route('pre-orders.index');
        }

        return redirect()->route('invoice.create', ['pre_order_id' => $id]);
    }

    /**
     * Update a pending pre-order.
     * Handles items[] array format sent from POS edit page.
     */
    public function update(Request $request, $id)
    {
        $preOrder = PreOrder::find($id);

        if (!$preOrder || $preOrder->status != 'pending') {
            session()->flash('error', __('Only pending pre-orders can be edited.'));
            return redirect()->route('pre-orders.index');
        }

        // Validate
        $request->validate([
            'customer_id' => 'required',
            'items'       => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $items       = $request->items;
            $totalAmount = 0;

            foreach ($items as $item) {
                $qty   = (float) ($item['quantity'] ?? 1);
                $price = (float) ($item['unit_price'] ?? 0);
                $totalAmount += ($qty * $price);
            }

            $preOrder->update([
                'customer_id'  => $request->customer_id,
                'total_amount' => $totalAmount,
                'note'         => $request->note ?? null,
            ]);

            // Delete old items & recreate
            PreOrderItem::where('pre_order_id', $preOrder->id)->delete();

            foreach ($items as $item) {
                $qty         = (float) ($item['quantity'] ?? 1);
                $price       = (float) ($item['unit_price'] ?? 0);
                $variationId = !empty($item['product_variation_id']) ? (int)$item['product_variation_id'] : null;
                $imei        = $item['imei'] ?? null;

                PreOrderItem::create([
                    'pre_order_id'         => $preOrder->id,
                    'product_id'           => (int) $item['product_id'],
                    'product_variation_id' => $variationId,
                    'quantity'             => $qty,
                    'unit_price'           => $price,
                    'subtotal'             => $qty * $price,
                    'imei'                 => $imei,
                ]);
            }

            DB::commit();

            session()->flash('success', __("Pre-Order :no updated successfully!", ['no' => $preOrder->pre_order_no]));
            return redirect()->route('pre-orders.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __('Update failed: ') . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    /**
     * Show details of a pre-order.
     */
    public function show($id)
    {
        $preOrder = PreOrder::with(['customer', 'branch', 'items.product', 'user'])->findOrFail($id);
        return view('backend.pages.pre_order.show', compact('preOrder'));
    }

    /**
     * Delete a cancelled pre-order permanently.
     */
    public function destroy($id)
    {
        $preOrder = PreOrder::find($id);

        if (!$preOrder) {
            session()->flash('error', __('Pre-Order not found.'));
            return redirect()->back();
        }

        if ($preOrder->status !== 'cancelled') {
            session()->flash('error', __('Only cancelled pre-orders can be deleted.'));
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            PreOrderItem::where('pre_order_id', $preOrder->id)->delete();
            $preOrder->delete();
            DB::commit();
            session()->flash('success', __('Pre-Order :no deleted successfully.', ['no' => $preOrder->pre_order_no]));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __('Delete failed: ') . $e->getMessage());
        }

        return redirect()->route('pre-orders.index');
    }
}
