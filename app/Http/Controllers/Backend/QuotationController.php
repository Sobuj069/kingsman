<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\BranchCategory;
use App\Models\BranchProduct;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $data['quotation_no'] = $request->quotation_no;
        $data['customer_id'] = $request->customer_id;
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;
        $data['product_id'] = $request->product_id;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if (in_array(auth()->user()->id, [1, 2])) {
            $query = Quotation::with('customer', 'user', 'updatedBy', 'branch', 'quotationItems.product');
        } else {
            $query = Quotation::with('customer', 'user', 'updatedBy', 'branch', 'quotationItems.product')->where('branch_id', auth()->user()->branch_id);
        }

        $data['allCustomer'] = Customer::get();

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $data['allProduct'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->get();
                $query->where('branch_id', $filterBranchId);
            } else {
                $data['allProduct'] = Product::orderBy('id', 'desc')->get();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $data['allProduct'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->get();
            $query->where('branch_id', $userBranchId);
        }

        if ($request->startDate != null && $request->endDate != null) {
            $sdate = Carbon::createFromDate($request->startDate)->toDateString();
            $edate = Carbon::createFromDate($request->endDate)->toDateString();
            $query->whereBetween('date', [$sdate, $edate]);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $barcode = $request->barcode ?? $request->quotation_no;
        $data['barcode'] = $barcode;
        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->where(function($q) use ($barcodeVal) {
                $q->where('quotation_no', 'like', "%{$barcodeVal}%")
                  ->orWhereHas('quotationItems.product', function ($subQ) use ($barcodeVal) {
                      $subQ->where('barcode', $barcodeVal)
                           ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                           ->orWhere('name', 'like', "%{$barcodeVal}%");
                  });
            });
        }

        if ($request->product_id) {
            $keyword = $request->product_id;
            $query->whereHas('quotationItems.product', function ($q) use ($keyword) {
                $q->where('id', $keyword);
            });
        }

        $data['quotations'] = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->all());

        return view('backend.pages.quotation.index', $data);
    }

    public function create()
    {
        $userBranchId   = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $showImei       = trim(strtolower(env('APP_IMEI'))) === 'yes';

        // --- Products & Categories ---
        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds  = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');

                $productsQuery = Product::whereIn('id', $productIds)
                    ->with('unit:id,name,related_unit_id,related_value', 'unit.related_unit:id,name');

                if (!$showImei) {
                    $productsQuery->where(function ($q) {
                        $q->where('imei', '!=', 1)->orWhereNull('imei');
                    });
                }

                $data['products']   = $productsQuery->orderBy('name', 'ASC')->paginate(12);
                $data['categories'] = Category::whereIn('id', $categoryIds)->orderBy('name', 'ASC')
                    ->select('id', 'name')->get();

                $customerQuery = Customer::orderByRaw("CASE WHEN id = 1 THEN 0 ELSE 1 END")
                    ->orderBy('id', 'desc')
                    ->where(function ($q) use ($filterBranchId) {
                        $q->where('branch_id', $filterBranchId)->orWhereNull('branch_id');
                    });
            } else {
                $productsQuery = Product::with('unit:id,name,related_unit_id,related_value', 'unit.related_unit:id,name');

                if (!$showImei) {
                    $productsQuery->where(function ($q) {
                        $q->where('imei', '!=', 1)->orWhereNull('imei');
                    });
                }

                $data['products']   = $productsQuery->orderBy('name', 'ASC')->paginate(12);
                $data['categories'] = Category::orderBy('name', 'ASC')->select('id', 'name')->get();

                $customerQuery = Customer::orderByRaw("CASE WHEN id = 1 THEN 0 ELSE 1 END")
                    ->orderBy('id', 'desc');
            }
        } else {
            $productIds  = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');

            $productsQuery = Product::whereIn('id', $productIds)
                ->with('unit:id,name,related_unit_id,related_value', 'unit.related_unit:id,name');

            if (!$showImei) {
                $productsQuery->where(function ($q) {
                    $q->where('imei', '!=', 1)->orWhereNull('imei');
                });
            }

            $data['products']   = $productsQuery->orderBy('name', 'ASC')->paginate(12);
            $data['categories'] = Category::whereIn('id', $categoryIds)->orderBy('name', 'ASC')
                ->select('id', 'name')->get();

            $customerQuery = Customer::orderByRaw("CASE WHEN id = 1 THEN 0 ELSE 1 END")
                ->orderBy('id', 'desc')
                ->where(function ($q) use ($userBranchId) {
                    $q->where('branch_id', $userBranchId)->orWhereNull('branch_id');
                });
        }

        $data['customers']     = $customerQuery->select('id', 'name', 'phone', 'branch_id')->get();
        $data['allBranch']     = Branch::select('id', 'name')->get();
        $data['bank_accounts'] = BankAccount::where('status', 1)->select('id', 'bank_name')->get();
        $data['platforms']     = \App\Models\Platform::where('status', 1)->orderBy('name', 'ASC')
            ->select('id', 'name')->get();

        return view('backend.pages.quotation.create', $data, compact('filterBranchId', 'userBranchId'));
    }

    public function store(Request $request)
    {
        $branchId = $request->branch_id;
        
        $last_quotation = Quotation::where('branch_id', $branchId)
            ->where('quotation_no', 'like', 'QT-%')
            ->orderBy('id', 'desc')
            ->select('quotation_no')
            ->first();
            
        if ($last_quotation == null) {
            $quotation_no = "QT-0000001";
        } else {
            $numberPart = (int) substr($last_quotation->quotation_no, strlen("QT-"));
            $newNumber = str_pad($numberPart + 1, 7, '0', STR_PAD_LEFT);
            $quotation_no = "QT-" . $newNumber;
        }

        $quotation = new Quotation();
        $quotation->date = $request->date;
        $quotation->quotation_no = $quotation_no;
        $quotation->customer_id = $request->customer_id;
        $quotation->vehicle_reg_no = $request->vehicle_reg_no ?? null;
        $quotation->estimated_amount = $request->estimated_amount ?? 0;

        // Discount parsing
        $discount = $request->discount_amount;
        $discount_amount = $request->discount;
        if (is_numeric($discount) && is_string($discount_amount) && str_contains($discount_amount, '%')) {
            $temp = $discount;
            $discount = $discount_amount;
            $discount_amount = $temp;
        }
        if (is_string($discount_amount)) {
            $discount_amount = str_replace(['%', ' '], '', $discount_amount);
        }
        $discount_amount = is_numeric($discount_amount) ? (float) $discount_amount : 0.00;

        $estimated = (float) ($request->estimated_amount ?? 0);
        if ($discount_amount > $estimated) {
            $discount_amount = $estimated;
        }

        $quotation->discount = ($discount == null) ? '0.00' : $discount;
        $quotation->discount_amount = $discount_amount;

        // VAT parsing
        $vat = $request->vat;
        $vat_amount = $request->vat_amount;
        if (is_numeric($vat) && is_string($vat_amount) && str_contains($vat_amount, '%')) {
            $temp = $vat;
            $vat = $vat_amount;
            $vat_amount = $temp;
        }
        if (is_string($vat_amount)) {
            $vat_amount = str_replace(['%', ' '], '', $vat_amount);
        }
        $vat_amount = is_numeric($vat_amount) ? (float) $vat_amount : 0.00;

        $quotation->vat = ($vat == null) ? '0.00' : $vat;
        $quotation->vat_amount = $vat_amount;

        $quotation->total_amount = $request->payable_amount ?? 0;
        $quotation->delivery_charge = $request->delivery_charge ?? 0;
        $quotation->note = $request->note ?? '';
        $quotation->created_by = auth()->user()->id;

        if (auth()->user()->branch_id == 1) {
            $quotation->branch_id = $request->branch_id;
        } else {
            $quotation->branch_id = auth()->user()->branch_id;
        }

        DB::transaction(function () use ($request, $quotation) {
            if ($quotation->save()) {
                foreach ($request->product_id as $key => $product_id) {
                    $quotation_item = new QuotationItem();
                    $quotation_item->quotation_id = $quotation->id;
                    $quotation_item->product_id = $product_id;
                    $quotation_item->rate = $request->rate[$key];
                    $quotation_item->product_discount = $request->product_discount[$key] ?? 0;

                    $variation_id = $request->variation_id[$key] ?? null;
                    $quotation_item->product_variation_id = $variation_id;

                    $quotation_item->warranty_value = $request->warranty_value[$key] ?? null;
                    $quotation_item->warranty_unit = $request->warranty_unit[$key] ?? null;

                    $quotation_item->branch_id = auth()->user()->branch_id == 1
                        ? $request->branch_id
                        : auth()->user()->branch_id;

                    $quotation_item->main_qty = $request->main_qty[$key];
                    $quotation_item->sub_qty = $request->sub_qty[$key] ?? 0;
                    $quotation_item->subtotal = $request->sub_total[$key];
                    $quotation_item->product_unit = $request->product_unit[$key] ?? null;
                    $quotation_item->imei = $request->imei[$key] ?? null;
                    
                    $quotation_item->save();
                }
            }
        });

        session()->flash('success', __('Quotation Created Successfully'));
        return redirect()->route('quotation.print', $quotation->id);
    }

    public function printQuotation($id)
    {
        $quotation = Quotation::with(['customer', 'user', 'quotationItems.product', 'quotationItems.product_variation'])
            ->where(function($query) use ($id) {
                $query->where('id', $id)
                      ->orWhere('quotation_no', $id);
            })->firstOrFail();

        return view('backend.pages.quotation.print', compact('quotation'));
    }

    public function edit($id)
    {
        $quotation = Quotation::findOrFail($id);
        if ($quotation->status == 1) {
            session()->flash('error', __('Converted quotation cannot be edited.'));
            return redirect()->route('quotation.index');
        }
        $quotationItems = QuotationItem::where('quotation_id', $id)->get();

        return view('backend.pages.quotation.edit', compact('quotation', 'quotationItems'));
    }

    public function update(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);
        if ($quotation->status == 1) {
            session()->flash('error', __('Converted quotation cannot be updated.'));
            return redirect()->route('quotation.index');
        }
        $quotation->date = $request->date;
        $quotation->estimated_amount = $request->estimated_amount ?? 0;

        // Discount parsing
        $discount = $request->discount_amount;
        $discount_amount = $request->discount;
        if (is_numeric($discount) && is_string($discount_amount) && str_contains($discount_amount, '%')) {
            $temp = $discount;
            $discount = $discount_amount;
            $discount_amount = $temp;
        }
        if (is_string($discount_amount)) {
            $discount_amount = str_replace(['%', ' '], '', $discount_amount);
        }
        $discount_amount = is_numeric($discount_amount) ? (float) $discount_amount : 0.00;

        $estimated = (float) ($request->estimated_amount ?? 0);
        if ($discount_amount > $estimated) {
            $discount_amount = $estimated;
        }

        $quotation->discount = ($discount == null) ? '0.00' : $discount;
        $quotation->discount_amount = $discount_amount;
        $quotation->total_amount = $request->total_amount ?? 0;
        $quotation->vehicle_reg_no = $request->vehicle_reg_no ?? null;
        $quotation->updated_by = auth()->user()->id;

        DB::transaction(function () use ($request, $quotation) {
            if ($quotation->save()) {
                // Delete old items
                QuotationItem::where('quotation_id', $quotation->id)->delete();

                // Add new items
                foreach ($request->product_id as $key => $product_id) {
                    $quotation_item = new QuotationItem();
                    $quotation_item->quotation_id = $quotation->id;
                    $quotation_item->product_id = $product_id;
                    $quotation_item->rate = $request->rate[$key];
                    $quotation_item->product_discount = $request->product_discount[$key] ?? 0;

                    $variation_id = $request->variation_id[$key] ?? null;
                    $quotation_item->product_variation_id = $variation_id;

                    $quotation_item->warranty_value = $request->warranty_value[$key] ?? null;
                    $quotation_item->warranty_unit = $request->warranty_unit[$key] ?? null;

                    $quotation_item->branch_id = $quotation->branch_id;

                    $quotation_item->main_qty = $request->main_qty[$key];
                    $quotation_item->sub_qty = $request->sub_qty[$key] ?? 0;
                    $quotation_item->subtotal = $request->sub_total[$key];
                    $quotation_item->product_unit = $request->product_unit[$key] ?? null;
                    $quotation_item->imei = $request->imei[$key] ?? null;
                    
                    $quotation_item->save();
                }
            }
        });

        session()->flash('success', __('Quotation Updated Successfully'));
        return redirect()->route('quotation.print', $quotation->id);
    }

    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->delete(); // Cascades deletes quotation_items as per DB migration.

        session()->flash('success', __('Quotation Deleted Successfully'));
        return redirect()->route('quotation.index');
    }
}
