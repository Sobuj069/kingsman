<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ServiceCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\SerialNumber;

class WarrantyClaimController extends Controller
{
    public function ajaxSearch(Request $request)
    {
        $search = $request->search;
        
        // 1. Search by IMEI/Serial
        $invoice_item = InvoiceItem::with('invoice.customer', 'product')
            ->where('imei', 'like', '%' . $search . '%')
            ->first();

        // 2. Search by Invoice No
        if (!$invoice_item) {
            $invoice = Invoice::with('customer', 'invoiceItems.product')->where('invoice_no', $search)->first();
            if ($invoice && $invoice->invoiceItems->count() > 0) {
                $invoice_item = $invoice->invoiceItems->first();
            }
        }

        // 3. Search by Product Barcode
        if (!$invoice_item) {
            $product = Product::where('barcode', $search)->first();
            if ($product) {
                // Find latest sale for this product
                $invoice_item = InvoiceItem::with('invoice.customer', 'product')
                    ->where('product_id', $product->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
        }

        if ($invoice_item) {
            $warranty_text = '';
            $is_expired = false;
            $days_remaining = 0;
            $expiry_date_formatted = '';
            
            if ($invoice_item->warranty_value && $invoice_item->warranty_unit) {
                $warranty_text = $invoice_item->warranty_value . ' ' . $invoice_item->warranty_unit;
                
                // Calculate expiry
                $sale_date = Carbon::parse($invoice_item->invoice->date);
                $expiry_date = clone $sale_date;
                $unit = strtolower($invoice_item->warranty_unit);
                if (str_contains($unit, 'day')) {
                    $expiry_date->addDays($invoice_item->warranty_value);
                } elseif (str_contains($unit, 'month')) {
                    $expiry_date->addMonths($invoice_item->warranty_value);
                } elseif (str_contains($unit, 'year')) {
                    $expiry_date->addYears($invoice_item->warranty_value);
                }
                
                $today = Carbon::today();
                $expiry = $expiry_date->copy()->startOfDay();
                $is_expired = $today->gt($expiry);
                $days_remaining = (int) $today->diffInDays($expiry, false);
                $expiry_date_formatted = $expiry_date->format('d-m-Y');
                
                if ($is_expired) {
                    $days_expired = abs($days_remaining);
                    $warranty_text .= ' (Expired on: ' . $expiry_date_formatted . ' - Expired ' . $days_expired . ' Days Ago)';
                } else {
                    $warranty_text .= ' (Expires on: ' . $expiry_date_formatted . ' - ' . $days_remaining . ' Days Remaining)';
                }
            } else {
                $warranty_text = 'No Warranty Info Found';
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'product_id' => $invoice_item->product_id,
                    'product_name' => $invoice_item->product->name,
                    'customer_id' => $invoice_item->invoice->customer_id,
                    'customer_name' => $invoice_item->invoice->customer ? $invoice_item->invoice->customer->name : 'Walking Customer',
                    'invoice_id' => $invoice_item->invoice_id,
                    'invoice_no' => $invoice_item->invoice->invoice_no,
                    'serial_no' => $invoice_item->imei,
                    'sale_date' => Carbon::parse($invoice_item->invoice->date)->format('d-m-Y'),
                    'warranty_info' => $warranty_text,
                    'has_warranty' => ($invoice_item->warranty_value && $invoice_item->warranty_unit) ? true : false,
                    'is_expired' => $is_expired,
                    'days_remaining' => $days_remaining,
                    'expiry_date' => $expiry_date_formatted,
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No records found']);
    }
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if (in_array(auth()->user()->id, [1, 2])) {
            $query = WarrantyClaim::with('customer', 'branch', 'user', 'product');
        } else {
            $query = WarrantyClaim::with('customer', 'branch', 'user', 'product')->where('branch_id', $userBranchId);
        }

        if ($userBranchId == 1 && $filterBranchId) {
            $query->where('branch_id', $filterBranchId);
        }

        $barcode = $request->barcode ?? $request->claim_no;
        $data['barcode'] = $barcode;
        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->where(function($q) use ($barcodeVal) {
                $q->where('claim_no', 'like', "%{$barcodeVal}%")
                  ->orWhere('serial_no', 'like', "%{$barcodeVal}%")
                  ->orWhereHas('product', function ($subQ) use ($barcodeVal) {
                      $subQ->where('barcode', $barcodeVal)
                           ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                           ->orWhere('name', 'like', "%{$barcodeVal}%");
                  });
            });
        }

        if ($request->serial_no) {
            $query->where('serial_no', 'like', '%' . $request->serial_no . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $data['claims'] = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->all());
        
        return view('backend.pages.warranty.claim.index', $data);
    }

    public function create(Request $request)
    {
        $data['invoice_item'] = null;
        if ($request->serial_no) {
            // Search for serial in InvoiceItems
            $data['invoice_item'] = InvoiceItem::with('invoice.customer', 'product')
                ->where('imei', $request->serial_no)
                ->first();
            
            if (!$data['invoice_item']) {
                session()->flash('error', __('Serial Number not found in sales records.'));
            }
        }

        $data['service_centers'] = ServiceCenter::where('status', 1)->get();
        $data['branches'] = Branch::get();
        $data['products'] = Product::where('status', 1)->where('is_service', 0)->orderBy('name', 'asc')->get();
        
        return view('backend.pages.warranty.claim.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'received_date' => 'required|date',
        ]);

        $branchId = auth()->user()->branch_id == 1 ? $request->branch_id : auth()->user()->branch_id;

        $last_claim = WarrantyClaim::where('branch_id', $branchId)
            ->orderBy('id', 'desc')
            ->first();

        if ($last_claim == null) {
            $claim_no = "WCL-0000001";
        } else {
            $numberPart = (int) substr($last_claim->claim_no, strlen("WCL-"));
            $newNumber = str_pad($numberPart + 1, 7, '0', STR_PAD_LEFT);
            $claim_no = "WCL-" . $newNumber;
        }

        WarrantyClaim::create([
            'branch_id' => $branchId,
            'customer_id' => $request->customer_id,
            'invoice_id' => $request->invoice_id,
            'claim_no' => $claim_no,
            'serial_no' => $request->serial_no,
            'product_id' => $request->product_id,
            'product_name' => $request->product_name,
            'received_condition' => $request->received_condition,
            'place_for' => $request->place_for,
            'received_date' => $request->received_date,
            'status' => 'Pending',
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', __('Warranty Claim Recorded Successfully'));
        return redirect()->route('warranty-claim.index');
    }

    public function check($id)
    {
        $data['claim'] = WarrantyClaim::with('customer', 'product')->findOrFail($id);
        return view('backend.pages.warranty.claim.check', $data);
    }

    public function updateChecking(Request $request, $id)
    {
        $claim = WarrantyClaim::findOrFail($id);
        
        $claim->update([
            'checking_note' => $request->checking_note,
            'status' => $request->status,
        ]);

        session()->flash('success', __('Claim Status Updated Successfully'));
        return redirect()->route('warranty-claim.index');
    }

    public function destroy($id)
    {
        $claim = WarrantyClaim::findOrFail($id);
        $claim->delete();
        session()->flash('success', __('Claim Deleted Successfully'));
        return back();
    }
}
