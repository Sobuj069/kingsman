<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\BankAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebOrderController extends Controller
{
    /**
     * Display a listing of pending website orders.
     */
    public function index(Request $request)
    {
        $barcode = $request->barcode ?? $request->invoice_no;
        $data['barcode'] = $barcode;
        $data['invoice_no'] = $request->invoice_no;
        $data['customer_id'] = $request->customer_id;
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;
        $data['product_id'] = $request->product_id;
        $data['payment_status'] = $request->payment_status;
        
        $sdate = $request->startDate ? Carbon::createFromDate($request->startDate)->toDateString() : null;
        $edate = $request->endDate ? Carbon::createFromDate($request->endDate)->toDateString() : null;

        // Query only un-dispatched / pending web orders
        $query = Invoice::with(['customer', 'user', 'invoiceItems.product', 'invoiceItems.product_variation.size', 'invoiceItems.product_variation.color'])
            ->where('sale_type', 'Online')
            ->where('status', '!=', 2)
            ->where(function ($q) {
                $q->whereNull('consignment_id')
                  ->orWhere('consignment_id', '')
                  ->orWhere('order_status', 'Pending');
            });

        if ($sdate && $edate) {
            $query->whereBetween('date', [$sdate, $edate]);
        }

        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->where(function($q) use ($barcodeVal) {
                $q->where('invoice_no', 'like', "%{$barcodeVal}%")
                  ->orWhereHas('customer', function($cq) use ($barcodeVal) {
                      $cq->where('name', 'like', "%{$barcodeVal}%")
                         ->orWhere('phone', 'like', "%{$barcodeVal}%");
                  })
                  ->orWhereHas('invoiceItems.product', function ($subQ) use ($barcodeVal) {
                      $subQ->where('barcode', $barcodeVal)
                           ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                           ->orWhere('name', 'like', "%{$barcodeVal}%");
                  });
            });
        }

        if ($request->customer_id != null) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->product_id != null) {
            $keyword = $request->product_id;
            $query->whereHas('invoiceItems.product', function ($q) use ($keyword) {
                $q->where('id', $keyword);
            });
        }

        if ($request->payment_status == 'paid') {
            $query->where('total_due', '<=', 0);
        } elseif ($request->payment_status == 'due') {
            $query->where('total_due', '>', 0);
        }

        $allPendingQuery = clone $query;
        $data['totalPendingCount'] = $allPendingQuery->count();
        $data['totalPendingAmount'] = $allPendingQuery->sum('total_amount');
        $data['todayPendingCount'] = (clone $query)->whereDate('created_at', Carbon::today())->count();

        $data['webOrders'] = $query->orderBy('created_at', 'DESC')->paginate(20)->appends($request->all());
        $data['customers'] = Customer::orderBy('name', 'ASC')->get();
        $data['products'] = Product::orderBy('name', 'ASC')->get();
        $data['bankAccounts'] = BankAccount::where('status', 1)->orderBy('id', 'asc')->get();

        return view('backend.pages.web_orders.index', $data);
    }

    /**
     * Dispatch single web order to Courier and transition to Online Sale List.
     */
    public function sendToCourier(Request $request, $id)
    {
        $invoice = Invoice::with(['customer', 'invoiceItems.product'])->findOrFail($id);

        $request->validate([
            'courier_type'     => 'required|string',
            'recipient_name'   => 'required|string|max:150',
            'recipient_phone'  => 'required|string|max:20',
            'recipient_address'=> 'required|string|max:500',
            'cod_amount'       => 'required|numeric|min:0',
        ]);

        $courierType = strtolower(trim($request->input('courier_type')));
        $recipientName = trim($request->input('recipient_name'));
        $recipientPhone = preg_replace('/[^0-9]/', '', $request->input('recipient_phone'));
        $recipientAddress = trim($request->input('recipient_address'));
        $codAmount = (int) $request->input('cod_amount');
        $orderNote = $request->input('note', $invoice->note ?? '');

        // Update customer details if changed
        if ($invoice->customer) {
            $invoice->customer->update([
                'name' => $recipientName,
                'phone' => $recipientPhone,
                'address' => $recipientAddress,
            ]);
        }

        if (in_array($courierType, ['steadfast', 'stead fast', 'stead_fast', 'stead-fast'])) {
            $response = placeOrder($invoice->invoice_no, $recipientName, $recipientPhone, $recipientAddress, $codAmount, $orderNote);
            
            if (isset($response['status']) && $response['status'] == 200 && !empty($response['consignment'])) {
                $invoice->consignment_id = $response['consignment']['consignment_id'] ?? ('STDF-' . time());
                $invoice->courier_type = 'Steadfast';
                $invoice->order_status = $response['consignment']['status'] ?? 'in_review';
                $invoice->note = $orderNote;
                $invoice->save();

                session()->flash('success', __("Web Order :inv dispatched to Steadfast Courier! Consignment ID: :cid. Moved to Online Sale List.", [
                    'inv' => $invoice->invoice_no,
                    'cid' => $invoice->consignment_id
                ]));
                return redirect()->route('invoice.online.sale');
            } else {
                $errMsg = $response['message'] ?? __('Steadfast API submission failed.');
                session()->flash('error', __('Courier placement failed: ') . $errMsg);
                return redirect()->back()->withInput();
            }
        } elseif ($courierType == 'pathao') {
            $token = getPathaoAccessToken();
            if (!$token) {
                session()->flash('error', __('Pathao API credentials are invalid or missing in Settings.'));
                return redirect()->back()->withInput();
            }

            $pathaoRes = placePathaoOrder(
                $request->store_id ?? 1,
                $invoice->invoice_no,
                $recipientName,
                $recipientPhone,
                $recipientAddress,
                $request->recipient_city ?? 1,
                $request->recipient_zone ?? 1,
                $request->recipient_area ?? 1,
                $request->delivery_type ?? 48,
                $request->item_type ?? 2,
                $orderNote,
                $request->item_quantity ?? $invoice->invoiceItems->sum('main_qty'),
                $request->item_weight ?? 0.5,
                $codAmount
            );

            if (isset($pathaoRes['data']['consignment_id'])) {
                $invoice->consignment_id = $pathaoRes['data']['consignment_id'];
                $invoice->courier_type = 'Pathao';
                $invoice->order_status = $pathaoRes['data']['order_status'] ?? 'pending';
                $invoice->note = $orderNote;
                $invoice->save();

                session()->flash('success', __("Web Order :inv dispatched to Pathao Courier! Consignment ID: :cid. Moved to Online Sale List.", [
                    'inv' => $invoice->invoice_no,
                    'cid' => $invoice->consignment_id
                ]));
                return redirect()->route('invoice.online.sale');
            } else {
                $errMsg = $pathaoRes['message'] ?? __('Pathao API Error');
                session()->flash('error', __('Courier placement failed: ') . $errMsg);
                return redirect()->back()->withInput();
            }
        } else {
            // Manual or Other Courier
            $customConsignment = $request->filled('manual_consignment_id') ? trim($request->input('manual_consignment_id')) : ('MAN-' . strtoupper(substr(uniqid(), -6)));
            $customCourierName = $request->filled('custom_courier_name') ? trim($request->input('custom_courier_name')) : 'Manual Delivery';

            $invoice->consignment_id = $customConsignment;
            $invoice->courier_type = $customCourierName;
            $invoice->order_status = 'shipped';
            $invoice->note = $orderNote;
            $invoice->save();

            session()->flash('success', __("Web Order :inv marked as Dispatched via :courier! Tracking/ID: :cid. Moved to Online Sale List.", [
                'inv' => $invoice->invoice_no,
                'courier' => $customCourierName,
                'cid' => $customConsignment
            ]));
            return redirect()->route('invoice.online.sale');
        }
    }

    /**
     * Bulk dispatch selected web orders to Steadfast or Manual courier.
     */
    public function bulkSendToCourier(Request $request)
    {
        $rawIds = $request->input('order_ids');
        $ids = is_array($rawIds) ? $rawIds : json_decode($rawIds, true);
        $courierType = strtolower($request->input('bulk_courier_type', 'steadfast'));

        if (empty($ids) || !is_array($ids)) {
            session()->flash('error', __('Please select at least one web order to dispatch.'));
            return redirect()->back();
        }

        $invoices = Invoice::with(['customer', 'invoiceItems'])->whereIn('id', $ids)->get();
        $successCount = 0;
        $failCount = 0;

        foreach ($invoices as $inv) {
            $recipientName = $inv->customer?->name ?? 'Web Customer';
            $recipientPhone = $inv->customer?->phone ?? '';
            $recipientAddress = $inv->customer?->address ?? '';
            $codAmount = (int)$inv->total_due;

            if (in_array($courierType, ['steadfast', 'stead fast', 'stead_fast', 'stead-fast'])) {
                $res = placeOrder($inv->invoice_no, $recipientName, $recipientPhone, $recipientAddress, $codAmount, $inv->note ?? 'Web Order');
                if (isset($res['status']) && $res['status'] == 200 && !empty($res['consignment'])) {
                    $inv->consignment_id = $res['consignment']['consignment_id'] ?? ('STDF-' . time());
                    $inv->courier_type = 'Steadfast';
                    $inv->order_status = $res['consignment']['status'] ?? 'in_review';
                    $inv->save();
                    $successCount++;
                } else {
                    $failCount++;
                }
            } else {
                $inv->consignment_id = 'MAN-' . strtoupper(substr(uniqid(), -6));
                $inv->courier_type = 'Manual Delivery';
                $inv->order_status = 'shipped';
                $inv->save();
                $successCount++;
            }
        }

        if ($successCount > 0) {
            session()->flash('success', __(":count web order(s) successfully dispatched and moved to Online Sale List!", ['count' => $successCount]));
        }
        if ($failCount > 0) {
            session()->flash('warning', __(":count order(s) failed during API courier dispatch.", ['count' => $failCount]));
        }

        return redirect()->route('invoice.online.sale');
    }

    /**
     * Cancel a pending web order and restore variation stock via FIFO.
     */
    public function cancel(Request $request, $id)
    {
        $invoice = Invoice::with('invoiceItems.product')->findOrFail($id);

        if ($invoice->status == 2 || $invoice->order_status == 'Cancelled') {
            session()->flash('info', __('This order has already been cancelled.'));
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            // Restore inventory for each item
            foreach ($invoice->invoiceItems as $item) {
                if ($item->product && $item->product->is_service != 1) {
                    // Restore variation stock in purchase_items
                    if (function_exists('restoreToFIFO')) {
                        restoreToFIFO($item->product_id, $item->main_qty, $item->product_variation_id, 1);
                    }
                    // Restore main product stock
                    $item->product->increment('main_qty', $item->main_qty);
                }
            }

            $invoice->order_status = 'Cancelled';
            $invoice->status = 2; // Cancelled
            $invoice->note = ($invoice->note ? $invoice->note . ' | ' : '') . 'Cancelled by Admin on ' . date('Y-m-d H:i');
            $invoice->save();

            DB::commit();
            session()->flash('success', __("Web Order :inv has been cancelled and stock was restored.", ['inv' => $invoice->invoice_no]));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel Web Order Error: ' . $e->getMessage());
            session()->flash('error', __('Failed to cancel order: ') . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Delete web order.
     */
    public function destroy($id)
    {
        $invoice = Invoice::with('invoiceItems.product')->findOrFail($id);

        DB::beginTransaction();
        try {
            // If active and not yet cancelled, restore stock before deleting
            if ($invoice->status != 2 && $invoice->order_status != 'Cancelled') {
                foreach ($invoice->invoiceItems as $item) {
                    if ($item->product && $item->product->is_service != 1) {
                        if (function_exists('restoreToFIFO')) {
                            restoreToFIFO($item->product_id, $item->main_qty, $item->product_variation_id, 1);
                        }
                        $item->product->increment('main_qty', $item->main_qty);
                    }
                }
            }

            $invoice->invoiceItems()->delete();
            $invoice->delete();

            DB::commit();
            session()->flash('success', __('Web order deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Web Order Error: ' . $e->getMessage());
            session()->flash('error', __('Failed to delete web order.'));
        }

        return redirect()->back();
    }
}
