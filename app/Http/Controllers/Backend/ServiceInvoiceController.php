<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ServiceInvoice;
use App\Models\ServiceInvoiceItem;
use App\Models\ServiceReceive;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Product;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ServiceInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if (in_array(auth()->user()->id, [1, 2])) {
            $query = ServiceInvoice::with('customer', 'branch', 'user');
        } else {
            $query = ServiceInvoice::with('customer', 'branch', 'user')->where('branch_id', $userBranchId);
        }

        if ($userBranchId == 1 && $filterBranchId) {
            $query->where('branch_id', $filterBranchId);
        }

        if ($request->invoice_no) {
            $query->where('invoice_no', 'like', '%' . $request->invoice_no . '%');
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->product_id) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        $data['invoices'] = $query->orderBy('created_at', 'desc')->paginate(20);
        $data['allCustomer'] = Customer::get();
        $data['allProducts'] = Product::where('is_service', 1)->orderBy('name', 'asc')->get();
        
        return view('backend.pages.service.invoice.index', $data);
    }

    public function create(Request $request)
    {
        $data['customers'] = Customer::get();
        $data['branches'] = Branch::get();
        $data['service_receives'] = ServiceReceive::where('status', '!=', 'Delivered')->get();
        $data['products'] = Product::where('is_service', 1)->get();
        $data['bank_accounts'] = BankAccount::where('status', 1)->get();
        
        $data['selected_receive'] = null;
        if ($request->receive_id) {
            $data['selected_receive'] = ServiceReceive::find($request->receive_id);
        }

        return view('backend.pages.service.invoice.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'date' => 'required|date',
            'product_id' => 'required|array',
        ]);

        $branchId = auth()->user()->branch_id == 1 ? $request->branch_id : auth()->user()->branch_id;

        $last_invoice = ServiceInvoice::where('branch_id', $branchId)
            ->orderBy('id', 'desc')
            ->first();

        if ($last_invoice == null) {
            $invoice_no = "SINV-0000001";
        } else {
            $numberPart = (int) substr($last_invoice->invoice_no, strlen("SINV-"));
            $newNumber = str_pad($numberPart + 1, 7, '0', STR_PAD_LEFT);
            $invoice_no = "SINV-" . $newNumber;
        }

        DB::transaction(function () use ($request, $invoice_no, $branchId) {
            $invoice = ServiceInvoice::create([
                'branch_id' => $branchId,
                'customer_id' => $request->customer_id,
                'service_receive_id' => $request->service_receive_id,
                'invoice_no' => $invoice_no,
                'total_amount' => $request->total_amount,
                'discount' => $request->discount ?? 0,
                'vat' => $request->vat ?? 0,
                'net_amount' => $request->net_amount,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->due_amount,
                'date' => $request->date,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->product_id as $key => $product_id) {
                ServiceInvoiceItem::create([
                    'service_invoice_id' => $invoice->id,
                    'product_id' => $product_id,
                    'cost_price' => $request->cost_price[$key] ?? 0.00,
                    'quantity' => $request->quantity[$key],
                    'price' => $request->price[$key],
                    'subtotal' => $request->subtotal[$key],
                ]);
            }

            // Update status of Service Receive if linked
            if ($request->service_receive_id) {
                ServiceReceive::where('id', $request->service_receive_id)->update(['status' => 'Delivered']);
            }

            // Create Transaction
            Transaction::create([
                'transaction_type' => 'Service Invoice',
                'branch_id' => $branchId,
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'debit' => $request->net_amount,
                'credit' => $request->paid_amount, // For simplicity, we treat it as immediate credit if paid
                'created_by' => auth()->id(),
            ]);
        });

        session()->flash('success', __('Service Invoice Created Successfully'));
        return redirect()->route('service-invoice.index');
    }

    public function show($id)
    {
        $data['invoice'] = ServiceInvoice::with('customer', 'branch', 'items.product', 'receive')->findOrFail($id);
        return view('backend.pages.service.invoice.show', $data);
    }

    public function destroy($id)
    {
        $invoice = ServiceInvoice::findOrFail($id);

        DB::transaction(function () use ($invoice) {
            // Delete related items
            ServiceInvoiceItem::where('service_invoice_id', $invoice->id)->delete();

            // Restore status of Service Receive if linked
            if ($invoice->service_receive_id) {
                ServiceReceive::where('id', $invoice->service_receive_id)->update(['status' => 'Completed']);
            }

            // Delete transaction
            Transaction::where('transaction_type', 'Service Invoice')
                ->where('branch_id', $invoice->branch_id)
                ->where('date', $invoice->date)
                ->where('customer_id', $invoice->customer_id)
                ->where('debit', $invoice->net_amount)
                ->where('credit', $invoice->paid_amount)
                ->delete();

            // Delete invoice
            $invoice->delete();
        });

        session()->flash('success', __('Service Invoice Deleted Successfully'));
        return back();
    }
}
