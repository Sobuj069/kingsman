<?php

namespace App\Http\Controllers\Backend;

use AdnSms\AdnSms;
use App\Http\Controllers\Controller;
use App\Models\ActualPayment;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Branch;
use App\Models\BranchCategory;
use App\Models\BranchProduct;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Customer;
use App\Models\DamageItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\SaleSerialNumber;
use App\Models\SerialNumber;
use App\Models\Transaction;
use App\Models\TransferItem;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $data['invoice_no'] = $request->invoice_no;
        $data['customer_id'] = $request->customer_id;
        $data['supplier_id'] = $request->supplier_id;
        $data['category_id'] = $request->category_id;
        $data['sub_category_id'] = $request->sub_category_id;
        $data['imei'] = $request->imei;
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;
        $data['bank_id'] = $request->bank_id;
        $data['product_id'] = $request->product_id;
        $sdate = Carbon::createFromDate($request->startDate)->toDateString();
        $edate = Carbon::createFromDate($request->endDate)->toDateString();
        $data['bankAccount'] = BankAccount::Where('status', 1)->orderBy('id', 'asc')->get();
        $data['allCategory'] = Category::orderBy('name', 'asc')->get();
        $data['allSubCategory'] = $request->category_id ? SubCategory::where('category_id', $request->category_id)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();

        $userBranchId = auth()->user()->branch_id ?? null;
        $filterBranchId = session('branch_filter_id', null);

        $query = Invoice::with('customer', 'user');
        $data['allCustomer'] = Customer::get();
        $data['allSupplier'] = \App\Models\Supplier::get();

        if ($filterBranchId) {
            $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
            $data['allProduct'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->get();
            $query->where('branch_id', $filterBranchId);
        } else {
            $data['allProduct'] = Product::orderBy('id', 'desc')->get();
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Bank filter
        if ($request->bank_id) {
            $bankId = $request->bank_id;
            $query->whereHas('transactions', function ($q) use ($bankId) {
                $q->where('bank_id', $bankId);
            });
        }
        if ($request->startDate != null && $request->endDate != null && $request->customer_id != null) {
            $query->whereBetween('date', [$sdate, $edate])->where('customer_id', $request->customer_id);
        }
        if ($request->startDate != null && $request->endDate != null) {
            $query->whereBetween('date', [$sdate, $edate]);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $barcode = $request->barcode ?? $request->invoice_no;
        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->where(function($q) use ($barcodeVal) {
                $q->where('invoice_no', 'like', "%{$barcodeVal}%")
                  ->orWhereHas('invoiceItems.product', function ($subQ) use ($barcodeVal) {
                      $subQ->where('barcode', $barcodeVal)
                           ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                           ->orWhere('name', 'like', "%{$barcodeVal}%");
                  })
                  ->orWhereHas('invoiceItems', function ($subQ) use ($barcodeVal) {
                      $subQ->where('imei', 'like', "%{$barcodeVal}%");
                  });
            });
        }

        if ($request->product_id) {
            $keyword = $request->product_id;
            $query->whereHas('invoiceItems.product', function ($q) use ($keyword) {
                $q->where('id', $keyword);
            });
        }

        if ($request->category_id) {
            $categoryId = $request->category_id;
            $query->whereHas('invoiceItems.product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($request->sub_category_id) {
            $subCategoryId = $request->sub_category_id;
            $query->whereHas('invoiceItems.product', function ($q) use ($subCategoryId) {
                $q->where('sub_category_id', $subCategoryId);
            });
        }

        if ($request->supplier_id) {
            $supplierId = $request->supplier_id;
            $query->whereHas('invoiceItems.product', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            });
        }

        if ($request->imei) {
            $imei = $request->imei;
            $query->whereHas('invoiceItems', function ($q) use ($imei) {
                $q->where('imei', 'LIKE', "%$imei%");
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();
        $filtered = Invoice::filterByFakeSale($invoices);

        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 20;
        $currentPageItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $data['invoices'] = new \Illuminate\Pagination\LengthAwarePaginator($currentPageItems, count($filtered), $perPage);
        $data['invoices']->setPath($request->url());

        $data['invoices']->appends([
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'customer_id' => $request->customer_id,
            'supplier_id' => $request->supplier_id,
            'barcode' => $barcode,
            'invoice_no' => $request->invoice_no,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'imei' => $request->imei,
            'invoice_no' => $request->invoice_no,
            'product_id' => $request->product_id,
        ]);
        $invoicessss = Invoice::orderBy('created_at', 'desc')->get();  // Get all invoices
        $latestInvoice = $invoicessss->first();


        return view('backend.pages.invoice.index', $data, compact('latestInvoice'));
    }

    public function onlineSale(Request $request)
    {
        $barcode = $request->barcode ?? $request->invoice_no;
        $data['barcode'] = $barcode;
        $data['invoice_no'] = $request->invoice_no;
        $data['customer_id'] = $request->customer_id;
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;
        $data['product_id'] = $request->product_id;
        $sdate = $request->startDate ? Carbon::createFromDate($request->startDate)->toDateString() : null;
        $edate = $request->endDate ? Carbon::createFromDate($request->endDate)->toDateString() : null;

        $query = Invoice::with(['customer', 'user', 'invoiceItems.product', 'invoiceItems.product_variation.size', 'invoiceItems.product_variation.color'])
            ->where('sale_type', 'Online')
            ->where('status', '!=', 2);

        // Show orders that have been dispatched to courier or confirmed
        if (!$request->filled('view_all')) {
            $query->where(function($q) {
                $q->where(function($sq) {
                    $sq->whereNotNull('consignment_id')->where('consignment_id', '!=', '');
                })->orWhere('order_status', '!=', 'Pending');
            });
        }

        if ($sdate && $edate) {
            $query->whereBetween('date', [$sdate, $edate]);
        }

        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->where(function($q) use ($barcodeVal) {
                $q->where('invoice_no', 'like', "%{$barcodeVal}%")
                  ->orWhereHas('invoiceItems.product', function ($subQ) use ($barcodeVal) {
                      $subQ->where('barcode', $barcodeVal)
                           ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                           ->orWhere('name', 'like', "%{$barcodeVal}%");
                  })
                  ->orWhereHas('invoiceItems', function ($subQ) use ($barcodeVal) {
                      $subQ->where('imei', 'like', "%{$barcodeVal}%");
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

        if ($request->courier != null) {
            $query->where('courier_type', $request->courier);
        }

        $data['invoices'] = $query->orderBy('created_at', 'DESC')->paginate(20)->appends($request->all());
        $data['bankAccounts'] = BankAccount::where('status', 1)->orderBy('id', 'asc')->get();

        $invoices = Invoice::orderBy('created_at', 'desc')->get();  // Get all invoices
        $latestInvoice = $invoices->first();

        return view('backend.pages.invoice.online-index', $data, compact('invoices'));
    }

    public function clearSelectedDues(Request $request)
    {
        $rawIds = $request->input('invoice_ids');
        $bankId = $request->input('bank_id');
        $paymentDate = $request->input('payment_date') ?? date('Y-m-d');

        $ids = [];
        if (is_array($rawIds)) {
            $ids = $rawIds;
        } elseif (is_string($rawIds)) {
            $ids = json_decode($rawIds, true) ?? array_filter(explode(',', $rawIds));
        }

        if (empty($ids)) {
            session()->flash('error', __('Please select at least one invoice.'));
            return redirect()->back();
        }

        $invoices = Invoice::whereIn('id', $ids)
            ->where('total_due', '>', 0)
            ->where('status', '!=', 2)
            ->get();

        $clearedCount = 0;
        $totalPaidAmount = 0;

        foreach ($invoices as $inv) {
            $dueAmount = (float) $inv->total_due;
            if (autoPayInvoiceDue($inv, $bankId, $paymentDate)) {
                $clearedCount++;
                $totalPaidAmount += $dueAmount;
            }
        }

        if ($clearedCount > 0) {
            $bankName = BankAccount::find($bankId)?->bank_name ?? __('Bank Account');
            session()->flash('success', __(":count selected invoice due(s) cleared into :bank! Total Paid: TK :amount", [
                'count' => $clearedCount,
                'bank' => $bankName,
                'amount' => number_format($totalPaidAmount, 2)
            ]));
        } else {
            session()->flash('info', __('No pending due found for the selected invoice(s).'));
        }

        return redirect()->back();
    }

    public function create(Request $request)
    {
        $quotation = null;
        if ($request->has('quotation_id')) {
            $quotation = \App\Models\Quotation::with('quotationItems.product', 'customer')->find($request->quotation_id);
        }
        $preOrder = null;
        if ($request->has('pre_order_id')) {
            $preOrder = \App\Models\PreOrder::with('items.product', 'customer')->find($request->pre_order_id);

            if ($preOrder && $request->filled('convert_mode')) {
                if ($preOrder->status != 'pending') {
                    session()->flash('error', __('This Pre-Order has already been processed or cancelled.'));
                    return redirect()->route('pre-orders.index');
                }

                $insufficientItems = [];
                foreach ($preOrder->items as $item) {
                    $product = $item->product;
                    if (!$product || $product->is_service == 1) {
                        continue;
                    }

                    $availableStock = 0;
                    if ($item->product_variation_id) {
                        $availableStock = (float) variation_stock($item->product_variation_id);
                    } else {
                        $availableStock = (float) product_fake_stock_val($product);
                    }

                    if ($availableStock < $item->quantity) {
                        $varName = '';
                        if ($item->product_variation_id) {
                            $var = \App\Models\ProductVariation::with('size', 'color')->find($item->product_variation_id);
                            if ($var) {
                                $varName = ' (' . trim(($var->size->size ?? '') . ' ' . ($var->color->color ?? '')) . ')';
                            }
                        }
                        $insufficientItems[] = [
                            'name'      => $product->name . $varName,
                            'required'  => $item->quantity,
                            'available' => max(0, (int)$availableStock),
                        ];
                    }
                }

                if (!empty($insufficientItems)) {
                    $errMsg = __('Stock is INSUFFICIENT to convert this Pre-Order into a Sale. Please Restock first!') . "<br><ul class='mt-2 mb-0 pl-3'>";
                    foreach ($insufficientItems as $err) {
                        $errMsg .= "<li><b>" . e($err['name']) . "</b> — Required: " . $err['required'] . ", Available Stock: " . $err['available'] . "</li>";
                    }
                    $errMsg .= "</ul>";

                    session()->flash('error', $errMsg);
                    return redirect()->route('pre-orders.index');
                }
            }
        }
        $data['preOrder'] = $preOrder;
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $query = Customer::orderBy('id', 'asc');

        $activeBranchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        $productsQuery = Product::where('status', 1)
            ->where('is_service', 0)
            ->with('unit.related_unit')
            ->when(env('APP_IMEI') != 'yes', function ($query) {
                return $query->where(function($q) {
                    $q->where('imei', '!=', 1)->orWhereNull('imei');
                });
            });

        $servicesQuery = Product::where('status', 1)
            ->where('is_service', 1)
            ->with('unit.related_unit');

        if ($userBranchId == 1 && !$filterBranchId) {
            $data['categories'] = Category::orderBy('name', 'ASC')->get();
        } else {
            $productId = BranchProduct::where('branch_id', $activeBranchId)->pluck('product_id');
            $categoryId = BranchCategory::where('branch_id', $activeBranchId)->pluck('category_id');

            $allAssignedServiceIds = BranchProduct::pluck('product_id')->toArray();
            $productsQuery->whereIn('id', $productId);
            $servicesQuery->where(function($q) use ($productId, $allAssignedServiceIds) {
                $q->whereIn('id', $productId)
                  ->orWhereNotIn('id', $allAssignedServiceIds);
            });
            $data['categories'] = Category::whereIn('id', $categoryId)->orderBy('name', 'ASC')->get();
        }

        $data['products'] = $productsQuery->orderBy('name', 'ASC')->paginate(12);
        $data['services'] = $servicesQuery->orderBy('name', 'ASC')->get();

        $query->where(function ($q) use ($activeBranchId) {
            $q->where('branch_id', $activeBranchId)
                ->orWhereNull('branch_id');
        });

        // $token = env('PATHAO_SECRET_TOKEN');
        // $cityList = getCityList($token);
        // $data['citits'] =  $cityList['data']['data'];
        // $data['shops'] = getStoreList($token);

        // dd($data['citits']);
        // $data['products'] = Product::with('unit.related_unit')->orderBy('name', 'ASC')->paginate(8);

        // dd(ProductVariation::where('product_id',$data['products']->id)   ->get());
        // $data['categories'] = Category::orderBy('name', 'ASC')->get();
        $data['customers'] = Customer::orderByRaw("CASE WHEN id = 1 THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->with('vehicles', 'discountGroup')
            ->get();
        $data['discountGroups'] = \App\Models\DiscountGroup::where('status', 1)->orderBy('name', 'asc')->get();

        $data['allBranch'] = Branch::get();
        $data['bank_accounts'] = BankAccount::where('status', 1)->get();
        $data['platforms'] = \App\Models\Platform::where('status', 1)->orderBy('name', 'asc')->get();
        $data['hold_count'] = \App\Models\HoldInvoice::where('branch_id', $activeBranchId)->count();

        return view('backend.pages.invoice.sc-create', $data, compact('filterBranchId', 'userBranchId', 'quotation'));
    }

    public function store(Request $request)
    {
        // Safety guard: if this form was submitted from Pre-Order EDIT mode (no convert_mode), block sale creation
        if ($request->filled('pre_order_id') && !$request->filled('convert_mode')) {
            return redirect()->route('pre-orders.index')
                ->with('error', __('Cannot create a Sale from Pre-Order edit page. Use the Update Pre-Order button.'));
        }

        // Capture convert_mode flag and pre_order_id before main logic
        $convertMode   = $request->filled('convert_mode') && $request->filled('pre_order_id');
        $preOrderId    = $request->pre_order_id;

        // dd($request->all());
        $this->validateImeiSelections($request);

        $last_invoice = Invoice::where('invoice_no', 'LIKE', 'INV-%')
            ->orderBy('id', 'desc')
            ->select('invoice_no')
            ->first();

        if ($last_invoice == null) {
            $nextNumber = 1;
        } else {
            $digits = preg_replace('/\D/', '', $last_invoice->invoice_no);
            $nextNumber = ((int) $digits) + 1;
        }

        $invoice_no = "INV-" . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

        while (Invoice::where('invoice_no', $invoice_no)->exists()) {
            $nextNumber++;
            $invoice_no = "INV-" . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
        }

        $invoice = new Invoice();
        $invoice->date = $request->date;
        $invoice->unique_id = uniqid();
        $invoice->invoice_no = $invoice_no;
        $invoice->customer_id = $request->customer_id;
        $invoice->estimated_amount = $request->estimated_amount;
        $invoice->discount = ($request->discount_amount == null) ? '0.00' : $request->discount_amount;
        $invoice->discount_amount = $request->discount;

        $invoice->vat = ($request->vat_amount == null) ? '0.00' : $request->vat_amount;
        $invoice->vat_amount = $request->vat;

        $invoice->total_amount = $request->payable_amount;
        $invoice->delivery_charge = $request->delivery_charge ?? 0;
        $invoice->courier_type = $request->courier_type;
        $previousDue = (float)($request->previous_due ?? 0);
        if ($previousDue <= 0 && $request->customer_id && $request->customer_id != 1) {
            $priorInvDue = (float) Invoice::where('customer_id', $request->customer_id)->sum('total_due');
            $custObj = Customer::find($request->customer_id);
            $openBal = $custObj ? open_balance_customer($request->customer_id, $custObj->due_amount) : 0;
            $previousDue = $priorInvDue + $openBal;
        }
        $invoice->previous_due = $previousDue;
        $invoice->note = $request->note;
        $invoice->ref_no = $request->ref_no;
        $invoice->change_amount = $request->balance;
        $invoice->created_by = auth()->user()->id;
        $invoice->sale_type = $request->sale_type ?? 'Outlet';
        if ($request->has('quotation_id')) {
            $invoice->quotation_id = $request->quotation_id;
        }
        if ($request->filled('vehicle_reg_no')) {
            $invoice->vehicle_reg_no = $request->vehicle_reg_no;
        }

        $pay_point = (float)($request->pay_point ?? 0);
        $pay_point_amount = round($pay_point * 0.75, 2);

        if (env('APP_LOYALTY') == 'yes' && $request->customer_id != 1) {
            $customer = Customer::where('id', $request->customer_id)->first();
            if ($customer && $customer->id != 1) {
                $earned_point = floor((float)($request->payable_amount ?? 0) / 100);
                $new_total_point = max(0, (float)$customer->total_point - $pay_point + $earned_point);

                Customer::where('id', $request->customer_id)->update([
                    'total_point' => $new_total_point,
                ]);

                $invoice->inv_point = $earned_point;
                $invoice->pay_point = $pay_point;
                $invoice->total_point = $new_total_point;
            }
        }

        if (auth()->user()->branch_id == 1) {
            $invoice->branch_id = $request->branch_id;
        } else {
            $invoice->branch_id = auth()->user()->branch_id;
        }

        $customer_info = Customer::where('id', $request->customer_id)->first();
        $courierType = strtolower(trim($request->courier_type ?? ''));
        $isCourierOrder = ($request->sale_type == 'Online' || !empty($request->courier_type));

        if ($isCourierOrder) {
            // Prevent courier orders for Walk-in Customer (ID == 1)
            if ($request->customer_id == 1 || ($customer_info && $customer_info->id == 1) || strtolower(trim($customer_info?->name ?? '')) == 'walk-in customer') {
                session()->flash('error', __('Courier orders cannot be placed for Walk-in Customer. Please select or create a registered customer with a valid name, phone, and delivery address.'));
                return redirect()->back()->withInput();
            }

            // Validate phone number
            $cleanPhone = preg_replace('/[^0-9]/', '', $customer_info?->phone ?? '');
            if (empty($cleanPhone) || strlen($cleanPhone) < 11) {
                session()->flash('error', __('Courier order requires a registered customer with a valid 11-digit mobile number.'));
                return redirect()->back()->withInput();
            }

            // Validate delivery address
            if (empty(trim($customer_info?->address ?? ''))) {
                session()->flash('error', __('Courier order requires a registered customer with a valid delivery address.'));
                return redirect()->back()->withInput();
            }
        }

        if ($isCourierOrder && in_array($courierType, ['stead fast', 'steadfast', 'stead_fast', 'stead-fast'])) {
            $recipientName = $customer_info->name;
            $recipientPhone = $customer_info->phone;
            $recipientAddress = $customer_info->address;
            $codAmount = (int)($request->due_amount ?? $request->payable_amount ?? 0);

            $ahmad = placeOrder($invoice_no, $recipientName, $recipientPhone, $recipientAddress, $codAmount, $request->note);
            if (isset($ahmad['status']) && $ahmad['status'] == 200 && !empty($ahmad['consignment'])) {
                $invoice->consignment_id = $ahmad['consignment']['consignment_id'] ?? '';
                $invoice->order_status = $ahmad['consignment']['status'] ?? 'pending';
            } else {
                \Log::warning('Steadfast API Order Placement Response', ['response' => $ahmad, 'invoice' => $invoice_no]);
                $errMsg = $ahmad['message'] ?? __('Steadfast Courier API order creation failed.');
                session()->flash('error', __('Courier placement failed: ') . $errMsg . __(' — Invoice was NOT created. Please fix the issue and try again.'));
                return redirect()->back()->withInput();
            }
        } elseif ($isCourierOrder && $courierType == 'pathao') {
            $token = getPathaoAccessToken();
            if (!$token) {
                session()->flash('error', __('Pathao order could not be sent because Pathao API credentials (Client Secret, Username/Email, Password) are missing or invalid in Settings > Operational. — Invoice was NOT created.'));
                return redirect()->back()->withInput();
            }

            $recipientName = $customer_info->name;
            $recipientPhone = $customer_info->phone;
            $recipientAddress = $customer_info->address;
            $codAmount = (int)($request->due_amount ?? $request->payable_amount ?? 0);

            $pathaoRes = placePathaoOrder(
                $request->store_id ?? 1,
                $invoice_no,
                $recipientName,
                $recipientPhone,
                $recipientAddress,
                $request->recipient_city ?? 1,
                $request->recipient_zone ?? 1,
                $request->recipient_area ?? 1,
                $request->delivery_type ?? 48,
                $request->item_type ?? 2,
                $request->special_instruction ?? $request->note ?? '',
                $request->item_quantity ?? 1,
                $request->item_weight ?? 0.5,
                $codAmount
            );

            if (isset($pathaoRes['data']['consignment_id'])) {
                $invoice->consignment_id = $pathaoRes['data']['consignment_id'];
                $invoice->order_status = $pathaoRes['data']['order_status'] ?? 'pending';
            } elseif (isset($pathaoRes['consignment_id'])) {
                $invoice->consignment_id = $pathaoRes['consignment_id'];
                $invoice->order_status = $pathaoRes['order_status'] ?? 'pending';
            } else {
                \Log::warning('Pathao API Order Placement Response', ['response' => $pathaoRes, 'invoice' => $invoice_no]);
                $errMsg = $pathaoRes['message'] ?? 'Pathao API Error';
                if (!empty($pathaoRes['errors']) && is_array($pathaoRes['errors'])) {
                    $fieldErrs = [];
                    foreach ($pathaoRes['errors'] as $fld => $msgs) {
                        if (is_array($msgs)) {
                            $fieldErrs[] = implode(', ', $msgs);
                        } else {
                            $fieldErrs[] = $msgs;
                        }
                    }
                    if (!empty($fieldErrs)) {
                        $errMsg = implode(' | ', $fieldErrs);
                    }
                }
                session()->flash('error', __('Courier placement failed: ') . $errMsg . __(' — Invoice was NOT created. Please fix the customer details and try again.'));
                return redirect()->back()->withInput();
            }
        } else {
            $invoice->consignment_id = '';
        }

        if (!$request->has('product_id') || !is_array($request->product_id) || count($request->product_id) === 0) {
            session()->flash('error', __('Please select at least one product to create an invoice.'));
            return redirect()->back()->withInput();
        }

        if ((float)($request->payable_amount ?? 0) < 0) {
            session()->flash('error', __('Payable amount cannot be negative.'));
            return redirect()->back()->withInput();
        }

        // ===== Server-side Stock & Variation Stock Validation =====
        if ($request->has('product_id') && is_array($request->product_id)) {
            foreach ($request->product_id as $key => $productId) {
                $product = Product::find($productId);
                if (!$product || $product->is_service == 1) {
                    continue;
                }

                $variationId = $request->variation_id[$key] ?? null;
                $mainQty = (float)($request->main_qty[$key] ?? 1);
                $subQty  = (float)($request->sub_qty[$key] ?? 0);
                $relatedValue = ($product->unit && $product->unit->related_value) ? (float)$product->unit->related_value : 1;
                $saleQty = ($product->unit && $product->unit->related_unit != null) ? ($mainQty * $relatedValue) + $subQty : $mainQty;

                if ($saleQty <= 0) {
                    session()->flash('error', __('Quantity for product ":prod" must be greater than zero.', ['prod' => $product->name]));
                    return redirect()->back()->withInput();
                }

                if ($variationId) {
                    $varStock = variation_stock($variationId);
                    if ($varStock < $saleQty) {
                        $variation = \App\Models\ProductVariation::with('size', 'color')->find($variationId);
                        $varName = trim(($variation?->size?->size ?? '') . ' ' . ($variation?->color?->color ?? ''));
                        session()->flash('error', __('Stock Out Error: Variation ":var" of product ":prod" is out of stock! Available: :avail, Requested: :req.', [
                            'var' => $varName ?: "#{$variationId}",
                            'prod' => $product->name,
                            'avail' => $varStock,
                            'req' => $saleQty
                        ]));
                        return redirect()->back()->withInput();
                    }
                } else {
                    $prodStock = (float) product_fake_stock_val($product);
                    if ($prodStock < $saleQty) {
                        session()->flash('error', __('Stock Out Error: Product ":prod" is out of stock! Available: :avail, Requested: :req.', [
                            'prod' => $product->name,
                            'avail' => product_stock($product),
                            'req' => $saleQty
                        ]));
                        return redirect()->back()->withInput();
                    }
                }
            }
        }

        DB::transaction(function () use ($request, $invoice) {
            if ($invoice->save()) {
                if ($invoice->quotation_id) {
                    $quotation = \App\Models\Quotation::find($invoice->quotation_id);
                    if ($quotation) {
                        $quotation->status = 1;
                        $quotation->save();
                    }
                }

                foreach ($request->product_id as $key => $product_id) {
                    $invoice_item = new InvoiceItem();
                    $find_unit_id = Product::where('id', $product_id)->first();
                    $selectedImeis = [];
                    if ($find_unit_id && (int) $find_unit_id->imei === 1) {
                        $selectedImeis = $this->parseImeis($request->imei[$key] ?? null);
                        $invoice_item->imei = implode("\n", $selectedImeis);
                    } else {
                        $invoice_item->imei = $request->imei[$key] ?? null;
                    }

                    $invoice_item->branch_id = auth()->user()->branch_id == 1
                        ? $request->branch_id
                        : auth()->user()->branch_id;

                    $invoice_item->invoice_id = $invoice->id;
                    $invoice_item->product_id = $product_id;
                    $invoice_item->rate = $request->rate[$key];
                    $invoice_item->product_discount = $request->product_discount[$key];

                    // Variation handle
                    $variation_id = (!empty($request->variation_id[$key])) ? $request->variation_id[$key] : null;
                    $invoice_item->product_variation_id = $variation_id;

                    // Warranty handle
                    $invoice_item->warranty_value = (!empty($request->warranty_value[$key])) ? $request->warranty_value[$key] : null;
                    $invoice_item->warranty_unit = (!empty($request->warranty_unit[$key])) ? $request->warranty_unit[$key] : null;

                    if ($find_unit_id->is_service == 0) {
                        if ((int) $find_unit_id->imei === 1) {
                            $qty = count($selectedImeis);
                            $invoice_item->main_qty = $qty;
                            $invoice_item->sub_qty = 0;
                            $invoice_item->actual_main = $qty;
                            $invoice_item->actual_sub = 0;
                            $saleQty = $qty;

                            $rate = (float) ($request->rate[$key] ?? 0);
                            $discount = (float) ($request->product_discount[$key] ?? 0);
                            $lineSubtotal = ($rate * $qty) - $discount;

                            $invoice_item->subtotal = $lineSubtotal;
                            $invoice_item->actual_total = $lineSubtotal;
                            $invoice_item->inv_subtotal = $lineSubtotal;
                        } elseif ($find_unit_id->unit->related_unit == null) {
                            $invoice_item->main_qty = $request->main_qty[$key];
                            $invoice_item->actual_main = $request->main_qty[$key];
                            $saleQty = $request->main_qty[$key];

                            $invoice_item->subtotal = $request->sub_total[$key];
                            $invoice_item->actual_total = $request->sub_total[$key];
                            $invoice_item->inv_subtotal = $request->sub_total[$key];
                        } else {
                            $invoice_item->main_qty = $request->main_qty[$key];
                            $invoice_item->sub_qty = $request->sub_qty[$key];
                            $invoice_item->actual_main = $request->main_qty[$key];
                            $invoice_item->actual_sub = $request->sub_qty[$key];
                            $main = $request->main_qty[$key] * $find_unit_id->unit->related_value;
                            $sub = $request->sub_qty[$key] ?? 0;
                            $saleQty = $main + $sub;

                            $invoice_item->subtotal = $request->sub_total[$key];
                            $invoice_item->actual_total = $request->sub_total[$key];
                            $invoice_item->inv_subtotal = $request->sub_total[$key];
                        }
                        $invoice_item->pur_subtotal = calculateUnitPriceUsingFIFO($product_id, $saleQty, $variation_id, $invoice_item->branch_id);
                    } else {
                        $invoice_item->main_qty = $request->main_qty[$key];
                        $invoice_item->subtotal = $request->sub_total[$key];
                        $invoice_item->inv_subtotal = $request->sub_total[$key];
                        $invoice_item->pur_subtotal = ($find_unit_id->purchase_price ?? 0.00) * $request->main_qty[$key];
                    }
                    $invoice_item->date = $request->date;
                    $invoice_item->save();

                    // Mark IMEI as sold
                    if ($invoice_item->imei) {
                        $imeis = $this->parseImeis($invoice_item->imei);
                        SerialNumber::where('product_id', $product_id)
                                    ->where('status', 1)
                                    ->whereIn('serial', array_map('trim', $imeis))
                                    ->update(['status' => 0]);
                    }
                }


                //create purchase log call createPurchaseLog function
                $id = $invoice->id;
                $type = 'Invoice';

                // Transaction
                $transaction = new Transaction();
                $transaction->transaction_type = $type;
                if (auth()->user()->branch_id == 1) {
                    $transaction->branch_id = $request->branch_id;
                } else {
                    $transaction->branch_id = auth()->user()->branch_id;
                }
                $transaction->date = $request->date;
                $transaction->invoice_id = $id;
                $transaction->customer_id = $request->customer_id;
                $transaction->debit = $request->payable_amount;
                $transaction->credit = NULL;
                $transaction->created_by = auth()->user()->id;
                $transaction->save();
                $this->createInvoiceInv($request, $id);

                if ($request->is_installment == 1) {
                    $installment = \App\Models\Installment::create([
                        'invoice_id' => $invoice->id,
                        'customer_id' => $invoice->customer_id,
                        'branch_id' => $invoice->branch_id,
                        'advance_pay' => $request->inst_advance_pay ?? 0.00,
                        'remaining_amount' => $request->inst_remaining ?? 0.00,
                        'total_installments' => $request->inst_total_installments ?? 1,
                        'interval_days' => $request->inst_interval_days ?? 30,
                        'interest_percentage' => $request->inst_interest_percent ?? 0.00,
                        'interest_amount' => $request->inst_interest_amount ?? 0.00,
                        'per_installment_amount' => $request->inst_per_installment ?? 0.00,
                        'total_with_interest' => $request->inst_total_with_interest ?? 0.00,
                        'first_due_date' => $request->inst_first_due_date ?? $request->date,
                        'last_due_date' => Carbon::parse($request->inst_first_due_date ?? $request->date)->addDays((($request->inst_total_installments ?? 1) - 1) * ($request->inst_interval_days ?? 30))->toDateString(),
                        'status' => 'pending',
                        'created_by' => auth()->user()->id,
                    ]);

                    $totalInstallments = (int) ($request->inst_total_installments ?? 1);
                    $intervalDays = (int) ($request->inst_interval_days ?? 30);
                    $totalWithInterest = (double) ($request->inst_total_with_interest ?? 0.00);
                    $firstDueDate = Carbon::parse($request->inst_first_due_date ?? $request->date);
                    
                    $perInstallment = round($totalWithInterest / $totalInstallments, 2);
                    $sumSchedules = 0;

                    for ($i = 1; $i <= $totalInstallments; $i++) {
                        $dueDate = $firstDueDate->copy()->addDays(($i - 1) * $intervalDays)->toDateString();
                        
                        if ($i === $totalInstallments) {
                            $amount = $totalWithInterest - $sumSchedules;
                        } else {
                            $amount = $perInstallment;
                            $sumSchedules += $amount;
                        }

                        \App\Models\InstallmentSchedule::create([
                            'installment_id' => $installment->id,
                            'installment_no' => $i,
                            'due_date' => $dueDate,
                            'amount' => $amount,
                            'paid_amount' => 0.00,
                            'status' => 'pending',
                        ]);
                    }
                }
            }
        });
        // invoiceSMS($request->customer_id, $invoice->id);
        $invoice->load('customer', 'invoiceItems.product');
        session()->flash('success', __('Invoice Created Successfully'));
        logActivity('Create Invoice', "Invoice #{$invoice->invoice_no} created for customer: {$invoice->customer?->name}", $invoice);

        // If this was a Pre-Order → Sale conversion, mark pre-order as converted and go to Sale List
        if ($convertMode && $preOrderId) {
            $preOrder = \App\Models\PreOrder::find($preOrderId);
            if ($preOrder && $preOrder->status === 'pending') {
                $preOrder->status = 'converted';
                $preOrder->save();
            }
            session()->flash('success', __('Pre-Order converted to Sale successfully! Invoice #:no created.', ['no' => $invoice->invoice_no]));
            return redirect()->route('invoice.index');
        }

        return redirect()->route('invoice.print', $invoice->id);
    }

    public function show($id)
    {
        $invoice = Invoice::with(['customer', 'user', 'branch', 'installment.schedules'])->findOrFail($id);
        $inv_items = InvoiceItem::where('invoice_id', $id)
            ->with(['product.unit.related_unit', 'product_variation.size', 'product_variation.color'])
            ->get();
        $payments = BankTransaction::where('invoice_id', $id)
            ->with('bank_account')
            ->orderBy('date', 'asc')
            ->get();

        return view('backend.pages.invoice.show', compact('invoice', 'inv_items', 'payments'));
    }

    private function validateImeiSelections(Request $request): void
    {
        $productIds = $request->input('product_id', []);
        $imeiValues = $request->input('imei', []);
        $qtyValues = $request->input('main_qty', []);

        $branchId = auth()->user()->branch_id == 1
            ? (int) ($request->branch_id ?? auth()->user()->branch_id)
            : auth()->user()->branch_id;

        $used = [];

        foreach ($productIds as $key => $productId) {
            $product = Product::find($productId);
            if (!$product || (int) $product->imei !== 1) {
                continue;
            }

            $imeis = $this->parseImeis($imeiValues[$key] ?? null);
            if (count($imeis) === 0) {
                throw ValidationException::withMessages([
                    "imei.$key" => __('Please select IMEI for IMEI products.'),
                ]);
            }

            if (count($imeis) !== count(array_unique($imeis))) {
                throw ValidationException::withMessages([
                    "imei.$key" => __('Duplicate IMEI selected.'),
                ]);
            }

            $qty = isset($qtyValues[$key]) ? (int) $qtyValues[$key] : 0;
            if ($qty > 0 && $qty !== count($imeis)) {
                throw ValidationException::withMessages([
                    "imei.$key" => __('Selected IMEI count must match quantity.'),
                ]);
            }

            foreach ($imeis as $imei) {
                if (isset($used[$imei])) {
                    throw ValidationException::withMessages([
                        "imei.$key" => __('Same IMEI cannot be used multiple times in one sale.'),
                    ]);
                }
                $used[$imei] = true;
            }

            $available = $this->availableImeisForProduct((int) $productId, $branchId);
            $missing = array_values(array_diff($imeis, $available));
            if (count($missing) > 0) {
                throw ValidationException::withMessages([
                    "imei.$key" => __('Some selected IMEI are not available.'),
                ]);
            }
        }
    }

    private function parseImeis(?string $value): array
    {
        $value = trim((string) $value);
        if ($value === '') {
            return [];
        }

        $parts = preg_split('/[\r\n,]+/', $value) ?: [];
        $parts = array_values(array_filter(array_map(static fn ($v) => trim((string) $v), $parts), static fn ($v) => $v !== ''));
        return $parts;
    }

    private function availableImeisForProduct(int $productId, int $branchId): array
    {
        // 1. Query available in-stock IMEIs from SerialNumber table (status = 1)
        $serialQuery = SerialNumber::where('product_id', $productId)
            ->where('status', 1);

        if ($branchId) {
            $serialQuery->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        $serials = $serialQuery->pluck('serial')
            ->map(static fn ($s) => trim((string) $s))
            ->filter()
            ->values();

        if ($serials->isNotEmpty()) {
            $result = [];
            foreach ($serials as $serialStr) {
                $result[] = $serialStr;
                $subParts = preg_split('/[,\s\r\n\/]+/', $serialStr);
                foreach ($subParts as $sp) {
                    $sp = trim($sp);
                    if ($sp !== '') $result[] = $sp;
                }
            }
            return array_values(array_unique($result));
        }

        // 2. Fallback to PurchaseItems ONLY if no SerialNumber records exist for this product at all
        if (!SerialNumber::where('product_id', $productId)->exists()) {
            $soldImeisRaw = InvoiceItem::where('product_id', $productId)
                ->whereNotNull('imei')
                ->whereHas('invoice', function ($q) {
                    $q->where('status', '!=', 2);
                })
                ->pluck('imei')
                ->toArray();

            $allSold = [];
            foreach ($soldImeisRaw as $si) {
                $allSold = array_merge($allSold, $this->parseImeis((string) $si));
            }

            $damagedImeisRaw = DamageItem::where('product_id', $productId)
                ->whereNotNull('imei')
                ->pluck('imei')
                ->toArray();
            foreach ($damagedImeisRaw as $di) {
                $allSold = array_merge($allSold, $this->parseImeis((string) $di));
            }

            $transferredImeisRaw = TransferItem::where('product_id', $productId)
                ->whereNotNull('imei')
                ->where('from_branch_id', $branchId)
                ->pluck('imei')
                ->toArray();
            foreach ($transferredImeisRaw as $ti) {
                $allSold = array_merge($allSold, $this->parseImeis((string) $ti));
            }

            $allSold = array_values(array_unique($allSold));

            $purchaseItems = PurchaseItem::where('product_id', $productId)
                ->where('branch_id', $branchId)
                ->whereNotNull('imei')
                ->get(['imei']);
            $allImeis = [];

            foreach ($purchaseItems as $pi) {
                $allImeis = array_merge($allImeis, $this->parseImeis((string) $pi->imei));
            }

            return array_values(array_diff(array_unique($allImeis), $allSold));
        }

        return [];
    }

    public function invoiceEdit($id)
    {
        $invoice = Invoice::where('id', $id)->first();
        if (!$invoice) {
            session()->flash('error', __('Invoice not found'));
            return redirect()->back();
        }

        $repurchasedImeis = invoiceHasRepurchasedImeis($invoice);
        if (!empty($repurchasedImeis)) {
            $imeiList = implode(', ', $repurchasedImeis);
            session()->flash('error', __("Cannot edit invoice #:no because IMEI(s) :imeis have already been repurchased into active stock.", [
                'no' => $invoice->invoice_no,
                'imeis' => $imeiList
            ]));
            return redirect()->back();
        }

        $rawItems = InvoiceItem::where('invoice_id', $id)->with(['product.unit.related_unit', 'product.product_variations'])->get();
        $groupedItems = collect();

        foreach ($rawItems->groupBy(fn($i) => $i->product_id . '_' . ($i->product_variation_id ?? 0)) as $group) {
            $first = clone $group->first();
            $first->main_qty = $group->sum('main_qty');
            $first->sub_qty = $group->sum('sub_qty');
            $first->product_discount = $group->sum('product_discount');
            $first->subtotal = $group->sum('subtotal');
            $first->inv_subtotal = $group->sum('inv_subtotal');
            $first->pur_subtotal = $group->sum('pur_subtotal');
            
            $allImeis = $group->pluck('imei')->filter()->map(fn($im) => trim($im))->filter()->values();
            if ($allImeis->isNotEmpty()) {
                $first->imei = implode("\n", $allImeis->toArray());
            }

            $groupedItems->push($first);
        }

        $invoiceItem = $groupedItems;

        $previousDue = 0;
        $totalCustomerDue = 0;

        if ($invoice && $invoice->customer_id) {
            $previousDue = Invoice::where('customer_id', $invoice->customer_id)
                ->where('id', '!=', $id)
                ->sum('total_due');

            $totalCustomerDue = Invoice::where('customer_id', $invoice->customer_id)
                ->sum('total_due');
        }

        return view('backend.pages.invoice.edit', compact('invoice', 'invoiceItem', 'previousDue', 'totalCustomerDue'));
    }
    public function invoiceUpdate($id, Request $request)
    {
        $invoice = Invoice::findOrFail($id);

        $repurchasedImeis = invoiceHasRepurchasedImeis($invoice);
        if (!empty($repurchasedImeis)) {
            $imeiList = implode(', ', $repurchasedImeis);
            session()->flash('error', __("Cannot update invoice #:no because IMEI(s) :imeis have already been repurchased into active stock.", [
                'no' => $invoice->invoice_no,
                'imeis' => $imeiList
            ]));
            return redirect()->back()->withInput();
        }

        $editDate = date('Y-m-d');
        $branchId = $invoice->branch_id;

        if (!$request->has('product_id') || !is_array($request->product_id) || count($request->product_id) === 0) {
            session()->flash('error', __('Please select at least one product to update the invoice.'));
            return redirect()->back()->withInput();
        }

        // ===== 1. SERVER-SIDE STOCK VALIDATION ON SALE EDIT =====
        $oldItems = InvoiceItem::where('invoice_id', $invoice->id)->get();
        $oldQtyMap = [];
        $oldItemsMap = [];

        foreach ($oldItems as $oldItem) {
            $product = Product::find($oldItem->product_id);
            $varId = $oldItem->product_variation_id ?? 0;
            $key = $oldItem->product_id . '_' . $varId;

            if ($product && $product->unit && $product->unit->related_unit != null) {
                $oldUnits = ($oldItem->main_qty * $product->unit->related_value) + ($oldItem->sub_qty ?? 0);
            } else {
                $oldUnits = (float) $oldItem->main_qty;
            }
            $oldQtyMap[$key] = ($oldQtyMap[$key] ?? 0) + $oldUnits;

            $varName = '';
            if ($oldItem->product_variation_id) {
                $var = \App\Models\ProductVariation::with('size', 'color')->find($oldItem->product_variation_id);
                if ($var) {
                    $varName = " (" . ($var->size?->size ?? '') . "-" . ($var->color?->color ?? '') . ")";
                }
            }
            $fullName = ($product?->name ?? 'Product') . $varName;

            if (isset($oldItemsMap[$key])) {
                $oldItemsMap[$key]['qty'] += $oldUnits;
                $oldItemsMap[$key]['main_qty'] += $oldItem->main_qty;
                $oldItemsMap[$key]['sub_qty'] += ($oldItem->sub_qty ?? 0);
                $oldItemsMap[$key]['subtotal'] += $oldItem->subtotal;
            } else {
                $oldItemsMap[$key] = [
                    'product_id'   => $oldItem->product_id,
                    'name'         => $fullName,
                    'qty'          => $oldUnits,
                    'main_qty'     => $oldItem->main_qty,
                    'sub_qty'      => $oldItem->sub_qty ?? 0,
                    'rate'         => $oldItem->rate,
                    'discount'     => $oldItem->product_discount ?? 0,
                    'subtotal'     => $oldItem->subtotal,
                    'date'         => $oldItem->date ?: $invoice->date,
                    'imei'         => $oldItem->imei,
                    'warranty_value' => $oldItem->warranty_value,
                    'warranty_unit'  => $oldItem->warranty_unit,
                ];
            }
        }

        $newProductIds = $request->input('product_id', []);
        $newQtyMap = [];
        $newItemsMap = [];

        foreach ($newProductIds as $k => $pId) {
            $product = Product::find($pId);
            if (!$product || $product->is_service == 1) {
                continue;
            }

            $varId = $request->variation_id[$k] ?? null;
            $mainQty = (float)($request->main_qty[$k] ?? 1);
            $subQty  = (float)($request->sub_qty[$k] ?? 0);

            if ($product->unit && $product->unit->related_unit != null) {
                $newUnits = ($mainQty * $product->unit->related_value) + $subQty;
            } else {
                $newUnits = $mainQty;
            }

            $mapKey = $pId . '_' . ($varId ?? 0);
            $newQtyMap[$mapKey] = ($newQtyMap[$mapKey] ?? 0) + $newUnits;

            $varName = '';
            if ($varId) {
                $var = \App\Models\ProductVariation::with('size', 'color')->find($varId);
                if ($var) {
                    $varName = " (" . ($var->size?->size ?? '') . "-" . ($var->color?->color ?? '') . ")";
                }
            }
            $fullName = $product->name . $varName;

            $newItemsMap[$mapKey] = [
                'product_id'   => $pId,
                'name'         => $fullName,
                'qty'          => $newUnits,
                'main_qty'     => $mainQty,
                'sub_qty'      => $subQty,
                'rate'         => (float)($request->rate[$k] ?? 0),
                'discount'     => (float)($request->product_discount[$k] ?? 0),
                'subtotal'     => (float)($request->sub_total[$k] ?? 0),
            ];
        }

        // Validate stock for any added or increased quantity
        foreach ($newQtyMap as $key => $reqQty) {
            list($pId, $vId) = explode('_', $key);
            $vId = ($vId == 0) ? null : $vId;
            $product = Product::find($pId);
            if (!$product || $product->is_service == 1) continue;

            $alreadyInInvoice = $oldQtyMap[$key] ?? 0;
            $additionalNeeded = $reqQty - $alreadyInInvoice;

            if ($additionalNeeded > 0) {
                $purQuery = PurchaseItem::where('product_id', $pId)->where('branch_id', $branchId);
                if ($vId) {
                    $purQuery->where('product_variation_id', $vId);
                }
                $availableInStock = (float) $purQuery->sum('stock_qty');

                if ($availableInStock < $additionalNeeded) {
                    $prodName = $newItemsMap[$key]['name'] ?? $product->name;
                    session()->flash('error', __("Insufficient stock for ':prod'. Stock available: :stock, Additional needed: :req", [
                        'prod'  => $prodName,
                        'stock' => $availableInStock,
                        'req'   => $additionalNeeded
                    ]));
                    return redirect()->back()->withInput();
                }
            }
        }

        // Track old total paid before update
        $oldTotalPaid = (float) $invoice->total_paid;

        // Perform Update in DB Transaction
        DB::transaction(function () use ($request, $invoice, $oldItems, $oldItemsMap, $newItemsMap, $editDate, $oldTotalPaid) {
            
            // Update Invoice fields
            $totalAmount = (float)$request->total_amount;
            $totalPaid = (float)$request->total_paid;

            $due = max(0, $totalAmount - $totalPaid);
            $change = max(0, $totalPaid - $totalAmount);

            $invoice->date = $request->date ?? $invoice->date;
            $invoice->estimated_amount = $request->estimated_amount;
            $invoice->discount = ($request->discount_amount == null) ? '0.00' : $request->discount_amount;
            $invoice->discount_amount = $request->discount;
            $invoice->total_amount = $totalAmount;
            $invoice->total_paid = $totalPaid;
            $invoice->total_due = $due;
            $invoice->change_amount = $change;
            $invoice->status = ($due > 0) ? 0 : 1;
            $invoice->is_edited = 1;
            $invoice->edit_status = 'edited';
            $invoice->updated_by = auth()->id();
            $previousDue = (float)($request->previous_due ?? 0);
            if ($previousDue <= 0 && $request->customer_id && $request->customer_id != 1) {
                $priorInvDue = (float) Invoice::where('customer_id', $request->customer_id)->where('id', '<', $invoice->id)->sum('total_due');
                $custObj = Customer::find($request->customer_id);
                $openBal = $custObj ? open_balance_customer($request->customer_id, $custObj->due_amount) : 0;
                $previousDue = $priorInvDue + $openBal;
            }
            $invoice->previous_due = $previousDue;
            if ($request->has('note')) {
                $invoice->note = $request->note;
            }
            $invoice->save();

            // STEP 1: Restore stock from old items
            foreach ($oldItems as $oldItem) {
                if ($oldItem->imei) {
                    $oldImeis = $this->parseImeis($oldItem->imei);
                    SerialNumber::where('product_id', $oldItem->product_id)
                                ->whereIn('serial', array_map('trim', $oldImeis))
                                ->update(['status' => 1]);
                }
                
                $product = Product::find($oldItem->product_id);
                if ($product && $product->is_service == 0) {
                    if ($product->unit && $product->unit->related_unit != null) {
                        $oldQty = ($oldItem->main_qty * $product->unit->related_value) + ($oldItem->sub_qty ?? 0);
                    } else {
                        $oldQty = $oldItem->main_qty;
                    }
                    restoreToFIFO($product->id, $oldQty, $oldItem->product_variation_id, $invoice->branch_id);
                }
                
                $oldItem->delete();
            }

            // STEP 2: Save new items and deduct stock
            $productIds = $request->input('product_id', []);
            foreach ($productIds as $key => $product_id) {
                $find_unit_id = Product::where('id', $product_id)->first();
                $variation_id = $request->variation_id[$key] ?? null;
                $mapKey = $product_id . '_' . ($variation_id ?? 0);

                $origDate = isset($oldItemsMap[$mapKey]) ? ($oldItemsMap[$mapKey]['date'] ?? $invoice->date) : $editDate;
                $prevMainQty = isset($oldItemsMap[$mapKey]) ? (float)($oldItemsMap[$mapKey]['main_qty']) : 0;
                $reqMainQty = (float)($request->main_qty[$key] ?? 1);
                $reqSubQty = (float)($request->sub_qty[$key] ?? 0);

                $selectedImeis = [];
                if ($find_unit_id && (int) $find_unit_id->imei === 1) {
                    $selectedImeis = $this->parseImeis($request->imei[$key] ?? null);
                }

                // If quantity was increased on an existing item:
                if (isset($oldItemsMap[$mapKey]) && $reqMainQty > $prevMainQty && $prevMainQty > 0 && empty($selectedImeis)) {
                    // Row 1: previous qty with original date
                    $this->createInvoiceItemRow($invoice, $find_unit_id, $product_id, $variation_id, $prevMainQty, $reqSubQty, $request->rate[$key], $request->product_discount[$key] ?? 0, $origDate, null, $request->warranty_value[$key] ?? null, $request->warranty_unit[$key] ?? null);
                    // Row 2: additional qty with today's edit date
                    $this->createInvoiceItemRow($invoice, $find_unit_id, $product_id, $variation_id, $reqMainQty - $prevMainQty, 0, $request->rate[$key], 0, $editDate, null, $request->warranty_value[$key] ?? null, $request->warranty_unit[$key] ?? null);
                } else {
                    $itemDate = isset($oldItemsMap[$mapKey]) ? $origDate : $editDate;
                    $this->createInvoiceItemRow($invoice, $find_unit_id, $product_id, $variation_id, $reqMainQty, $reqSubQty, $request->rate[$key], $request->product_discount[$key] ?? 0, $itemDate, $selectedImeis, $request->warranty_value[$key] ?? null, $request->warranty_unit[$key] ?? null);
                }

                if (!empty($selectedImeis)) {
                    SerialNumber::where('product_id', $product_id)
                                ->whereIn('serial', array_map('trim', $selectedImeis))
                                ->update(['status' => 0]);
                }
            }

            // ===== STEP 3: DATE-WISE CASH & BANK DIFFERENTIAL TRANSACTION =====
            $existingBankTx = BankTransaction::where('invoice_id', $invoice->id)->first();
            $bankId = $existingBankTx ? $existingBankTx->bank_id : 1;

            // Notice: Historical payment records maintain their original collection dates.
            // Any additional or reduced payment difference is recorded on today's edit date ($editDate).
            $existingNetPaid = (float) BankTransaction::where('invoice_id', $invoice->id)
                ->selectRaw("SUM(CASE WHEN trans_type = 'deposit' THEN amount WHEN trans_type = 'withdraw' THEN -amount ELSE 0 END) as total")
                ->value('total');

            $txCount = BankTransaction::where('invoice_id', $invoice->id)->count();

            if ($txCount === 0 && $totalPaid > 0) {
                // Initial payment entry if none existed before
                BankTransaction::create([
                    'trans_type' => 'deposit',
                    'pay_type'   => 'invpay',
                    'branch_id'  => $invoice->branch_id,
                    'date'       => $request->date,
                    'bank_id'    => $bankId,
                    'invoice_id' => $invoice->id,
                    'amount'     => $totalPaid,
                    'created_by' => auth()->id(),
                ]);

                Transaction::create([
                    'transaction_type' => 'Received from Customer',
                    'branch_id'        => $invoice->branch_id,
                    'date'             => $request->date,
                    'invoice_id'       => $invoice->id,
                    'customer_id'      => $request->customer_id,
                    'debit'            => null,
                    'credit'           => $totalPaid,
                    'bank_id'          => $bankId,
                    'created_by'       => auth()->id(),
                ]);
            } else {
                $diffPaid = $totalPaid - $existingNetPaid;

                if ($diffPaid > 0) {
                    // Additional payment collected on TODAY'S edit date
                    BankTransaction::create([
                        'trans_type' => 'deposit',
                        'pay_type'   => 'invpay_edit',
                        'branch_id'  => $invoice->branch_id,
                        'date'       => $editDate, // Hit today's cash date!
                        'bank_id'    => $bankId,
                        'invoice_id' => $invoice->id,
                        'amount'     => $diffPaid,
                        'created_by' => auth()->id(),
                    ]);

                    Transaction::create([
                        'transaction_type' => 'Received from Customer (Invoice Edit)',
                        'branch_id'        => $invoice->branch_id,
                        'date'             => $editDate,
                        'invoice_id'       => $invoice->id,
                        'customer_id'      => $request->customer_id,
                        'debit'            => null,
                        'credit'           => $diffPaid,
                        'bank_id'          => $bankId,
                        'created_by'       => auth()->id(),
                    ]);
                } elseif ($diffPaid < 0) {
                    // Paid amount decreased: log cash refund on TODAY'S edit date
                    $refundVal = abs($diffPaid);
                    BankTransaction::create([
                        'trans_type' => 'withdraw',
                        'pay_type'   => 'rtn_pay_edit',
                        'branch_id'  => $invoice->branch_id,
                        'date'       => $editDate,
                        'bank_id'    => $bankId,
                        'invoice_id' => $invoice->id,
                        'amount'     => $refundVal,
                        'created_by' => auth()->id(),
                    ]);

                    Transaction::create([
                        'transaction_type' => 'Return Money to Customer (Invoice Edit)',
                        'branch_id'        => $invoice->branch_id,
                        'date'             => $editDate,
                        'invoice_id'       => $invoice->id,
                        'customer_id'      => $request->customer_id,
                        'debit'            => $refundVal,
                        'credit'           => null,
                        'bank_id'          => $bankId,
                        'created_by'       => auth()->id(),
                    ]);
                }
            }

            // ===== STEP 4: DATE-WISE CUSTOMER LEDGER DEBIT TRANSACTIONS =====
            Transaction::where('invoice_id', $invoice->id)->update([
                'customer_id' => $request->customer_id,
                'branch_id'   => $invoice->branch_id,
            ]);

            $initialInvoiceTx = Transaction::where('invoice_id', $invoice->id)
                ->where('transaction_type', 'Invoice')
                ->first();

            if (!$initialInvoiceTx) {
                Transaction::create([
                    'transaction_type' => 'Invoice',
                    'branch_id'        => $invoice->branch_id,
                    'date'             => $request->date,
                    'invoice_id'       => $invoice->id,
                    'customer_id'      => $request->customer_id,
                    'debit'            => $totalAmount,
                    'credit'           => null,
                    'created_by'       => auth()->id(),
                ]);
            } else {
                // Update date of initial invoice transaction if user changed invoice date
                $initialInvoiceTx->update([
                    'date' => $request->date,
                ]);

                $existingNetDebit = (float) Transaction::where('invoice_id', $invoice->id)
                    ->where(function ($q) {
                        $q->where('transaction_type', 'Invoice')
                          ->orWhere('transaction_type', 'like', '%Invoice Product%');
                    })
                    ->selectRaw("SUM(CASE WHEN debit IS NOT NULL THEN debit ELSE 0 END) - SUM(CASE WHEN credit IS NOT NULL THEN credit ELSE 0 END) as total")
                    ->value('total');

                $diffDebit = $totalAmount - $existingNetDebit;

                if ($diffDebit > 0) {
                    Transaction::create([
                        'transaction_type' => 'Invoice Product Added (Sale Edit)',
                        'branch_id'        => $invoice->branch_id,
                        'date'             => $editDate,
                        'invoice_id'       => $invoice->id,
                        'customer_id'      => $request->customer_id,
                        'debit'            => $diffDebit,
                        'credit'           => null,
                        'created_by'       => auth()->id(),
                    ]);
                } elseif ($diffDebit < 0) {
                    Transaction::create([
                        'transaction_type' => 'Invoice Product Removed (Sale Edit)',
                        'branch_id'        => $invoice->branch_id,
                        'date'             => $editDate,
                        'invoice_id'       => $invoice->id,
                        'customer_id'      => $request->customer_id,
                        'debit'            => null,
                        'credit'           => abs($diffDebit),
                        'created_by'       => auth()->id(),
                    ]);
                }
            }

            // ===== STEP 4: PRODUCT EDIT LOGS AUDIT TRAIL =====
            foreach ($newItemsMap as $k => $newItem) {
                if (!isset($oldItemsMap[$k])) {
                    // Added Product
                    \App\Models\InvoiceEditLog::create([
                        'invoice_id'   => $invoice->id,
                        'user_id'      => auth()->id(),
                        'edit_date'    => $editDate,
                        'product_id'   => $newItem['product_id'],
                        'product_name' => $newItem['name'],
                        'action'       => 'Added Product',
                        'details'      => "Added {$newItem['qty']} Qty @ {$newItem['rate']} Tk (Subtotal: {$newItem['subtotal']} Tk) on {$editDate}",
                        'new_value'    => "Qty: {$newItem['qty']}, Subtotal: {$newItem['subtotal']}",
                    ]);
                } else {
                    $oldItem = $oldItemsMap[$k];
                    if ($newItem['qty'] != $oldItem['qty'] || $newItem['rate'] != $oldItem['rate'] || $newItem['subtotal'] != $oldItem['subtotal']) {
                        // Modified Product
                        \App\Models\InvoiceEditLog::create([
                            'invoice_id'   => $invoice->id,
                            'user_id'      => auth()->id(),
                            'edit_date'    => $editDate,
                            'product_id'   => $newItem['product_id'],
                            'product_name' => $newItem['name'],
                            'action'       => 'Modified Product',
                            'details'      => "Updated {$newItem['name']}: Qty ({$oldItem['qty']} → {$newItem['qty']}), Rate ({$oldItem['rate']} → {$newItem['rate']} Tk), Subtotal ({$oldItem['subtotal']} Tk) on {$editDate}",
                            'old_value'    => "Qty: {$oldItem['qty']}, Rate: {$oldItem['rate']}, Subtotal: {$oldItem['subtotal']}",
                            'new_value'    => "Qty: {$newItem['qty']}, Rate: {$newItem['rate']}, Subtotal: {$newItem['subtotal']}",
                        ]);
                    }
                }
            }

            foreach ($oldItemsMap as $k => $oldItem) {
                if (!isset($newItemsMap[$k])) {
                    // Removed Product
                    \App\Models\InvoiceEditLog::create([
                        'invoice_id'   => $invoice->id,
                        'user_id'      => auth()->id(),
                        'edit_date'    => $editDate,
                        'product_id'   => $oldItem['product_id'],
                        'product_name' => $oldItem['name'],
                        'action'       => 'Removed Product',
                        'details'      => "Removed {$oldItem['name']} (Was {$oldItem['qty']} Qty @ {$oldItem['rate']} Tk) on {$editDate}",
                        'old_value'    => "Qty: {$oldItem['qty']}, Subtotal: {$oldItem['subtotal']}",
                    ]);
                }
            }
        });

        session()->flash('success', __('Invoice Updated Successfully'));
        logActivity('Update Invoice', "Invoice #{$invoice->invoice_no} updated", $invoice);
        return redirect()->route('invoice.print', $invoice->id);
    }

    public function invoiceExchange($id)
    {
        if (env('APP_SC') == 'yes') {
            $data['invoice'] = Invoice::where('id', $id)->first();
            $data['categories'] = Category::orderBy('name', 'ASC')->get();
            $data['customers'] = Customer::get();
            $data['bank_accounts'] = BankAccount::where('status', 1)->get();
            return view('backend.pages.invoice.sc-exchange', $data);
        } else {
            $data['invoice'] = Invoice::where('id', $id)->first();
            $data['categories'] = Category::orderBy('name', 'ASC')->get();
            $data['customers'] = Customer::get();
            $data['bank_accounts'] = BankAccount::where('status', 1)->get();
            return view('backend.pages.invoice.exchange', $data);
        }
    }

    public function returnInvoice($id, Request $request)
    {
        $item = InvoiceItem::where('id', $id)->first();

        DB::transaction(function () use ($request, $item) {
            $item->delete();

            $main_qty = $request->rtn_main;
            $rate = $request->rate;
            $total = $request->totalAmount;
            $esti_amount = $request->estimateAmount;
            $due = $request->dueAmount;
            $paid = $request->paidAmount;

            if ($due == 0.00) {
                $esti_amount = $esti_amount - ($rate * $main_qty);
                $due = $esti_amount - $paid;
            }

            Invoice::where('id', $request->invoice)->update([
                'estimated_amount' => $esti_amount,
                'total_amount' => $esti_amount,
                'total_due' => $due,
                'return_amount' => $request->return_amount,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        });
        $invoice = Invoice::with('customer')->find($request->invoice);
        session()->flash('success', __('Successfully return Product'));
        logActivity('Return Product', "Product returned for Invoice #{$invoice?->invoice_no}", $invoice);
        return redirect()->back();
    }

    public function updateExchangeInvoice(Request $request)
    {
        // dd($request->all());
        $invoice = Invoice::findOrFail($request->id);
        $today = date('Y-m-d');
        $invoice->estimated_amount = $request->estimated_amount;
        $invoice->discount = $request->discount_amount ?? 0.00;
        $invoice->discount_amount = $request->discount ?? 0.00;
        $invoice->total_amount = $request->payable_amount;

        $due = $request->payable_amount - $request->total_paid;
        $invoice->total_due = $due > 0 ? $due : 0.00;
        $invoice->change_amount = $due < 0 ? abs($due) : 0.00;
        $invoice->is_edited = 2;
        $invoice->edit_status = 'exchange';
        $invoice->status = ($due > 0) ? 0 : 1;
        $invoice->updated_by = auth()->id();

        if (!$request->has('product_id') || !is_array($request->product_id) || count($request->product_id) === 0) {
            session()->flash('error', __('Please select at least one product to exchange.'));
            return redirect()->back()->withInput();
        }

        DB::transaction(function () use ($request, $invoice, $today) {

            // STEP 1️⃣ — Restore stock from old invoice items
            $oldExchangeItems = InvoiceItem::where('invoice_id', $invoice->id)->get();
            foreach ($oldExchangeItems as $oldItem) {
                if ($oldItem->imei) {
                    $oldImeis = $this->parseImeis($oldItem->imei);
                    SerialNumber::where('product_id', $oldItem->product_id)
                                ->whereIn('serial', array_map('trim', $oldImeis))
                                ->update(['status' => 1]);
                }
            }
            
            foreach ($oldExchangeItems as $item) {
                $product = Product::find($item->product_id);
                $variationId = $item->product_variation_id;
                if ($product && $product->is_service == 0) {
                    // Convert to base qty
                    if ($product->unit && $product->unit->related_unit == null) {
                        $oldQty = $item->main_qty;
                    } else {
                        $oldQty = ($item->main_qty * ($product->unit->related_value ?? 1)) + ($item->sub_qty ?? 0);
                    }
                    restoreToFIFO($product->id, $oldQty, $variationId, $invoice->branch_id);
                }
                $item->delete();
            }

            // STEP 2️⃣ — Create updated items and deduct new stock
            $productIds = $request->input('product_id', []);
            foreach ($productIds as $key => $productId) {
                $product = Product::find($productId);
                $variationId = $request->variation[$key] ?? null;

                $invoiceItem = new InvoiceItem();
                $invoiceItem->date = $invoice->date;
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->branch_id = $invoice->branch_id;
                $invoiceItem->product_id = $productId;
                $invoiceItem->product_variation_id = $variationId;
                $invoiceItem->rate = $request->rate[$key];

                if ($product && $product->is_service == 0) {
                    if ($product->unit && $product->unit->related_unit == null) {
                        $invoiceItem->main_qty = $request->main_qty[$key];
                        $invoiceItem->actual_main = $request->main_qty[$key];
                        $saleQty = $request->main_qty[$key];
                    } else {
                        $invoiceItem->main_qty = $request->main_qty[$key];
                        $invoiceItem->sub_qty = $request->sub_qty[$key] ?? 0;
                        $invoiceItem->actual_main = $request->main_qty[$key];
                        $invoiceItem->actual_sub = $request->sub_qty[$key] ?? 0;
                        $saleQty = ($request->main_qty[$key] * ($product->unit->related_value ?? 1)) + ($request->sub_qty[$key] ?? 0);
                    }
                    $invoiceItem->pur_subtotal = calculateUnitPriceUsingFIFO($productId, $saleQty, $variationId, $invoice->branch_id);
                } else {
                    $invoiceItem->main_qty = $request->main_qty[$key];
                    $invoiceItem->actual_main = $request->main_qty[$key];
                    $invoiceItem->pur_subtotal = ($product->purchase_price ?? 0.00) * $request->main_qty[$key];
                }

                $selectedImeis = [];
                if ($product && (int) $product->imei === 1) {
                    $selectedImeis = $this->parseImeis($request->imei[$key] ?? null);
                    $invoiceItem->imei = !empty($selectedImeis) ? implode("\n", $selectedImeis) : null;
                } else {
                    $invoiceItem->imei = null;
                }
                $invoiceItem->subtotal = $request->sub_total[$key];
                $invoiceItem->actual_total = $request->sub_total[$key];
                $invoiceItem->inv_subtotal = $request->sub_total[$key];

                $invoiceItem->save();

                // Mark IMEI as sold
                if ($invoiceItem->imei) {
                    $imeis = $selectedImeis ?: $this->parseImeis($invoiceItem->imei);
                    SerialNumber::where('product_id', $productId)
                                ->whereIn('serial', array_map('trim', $imeis))
                                ->update(['status' => 0]);
                }
            }

            // STEP 3️⃣ — Update Transactions & Payments
            $existingBankTx = BankTransaction::where('invoice_id', $invoice->id)->first();
            $bankId = $existingBankTx ? $existingBankTx->bank_id : 1;

            $existingNetPaid = (float) BankTransaction::where('invoice_id', $invoice->id)
                ->selectRaw("SUM(CASE WHEN trans_type = 'deposit' THEN amount WHEN trans_type = 'withdraw' THEN -amount ELSE 0 END) as total")
                ->value('total');

            $newPaid = (float) $request->total_paid;
            $diffPaid = $newPaid - $existingNetPaid;

            if ($diffPaid > 0) {
                BankTransaction::create([
                    'trans_type' => 'deposit',
                    'pay_type'   => 'invpay_edit',
                    'branch_id'  => $invoice->branch_id,
                    'date'       => $today,
                    'bank_id'    => $bankId,
                    'invoice_id' => $invoice->id,
                    'amount'     => $diffPaid,
                    'created_by' => auth()->id(),
                ]);

                Transaction::create([
                    'transaction_type' => 'Received from Customer (Exchange)',
                    'branch_id'        => $invoice->branch_id,
                    'date'             => $today,
                    'invoice_id'       => $invoice->id,
                    'customer_id'      => $request->customer_id,
                    'debit'            => null,
                    'credit'           => $diffPaid,
                    'bank_id'          => $bankId,
                    'created_by'       => auth()->id(),
                ]);
            } elseif ($diffPaid < 0) {
                $refundVal = abs($diffPaid);
                BankTransaction::create([
                    'trans_type' => 'withdraw',
                    'pay_type'   => 'rtn_pay_edit',
                    'branch_id'  => $invoice->branch_id,
                    'date'       => $today,
                    'bank_id'    => $bankId,
                    'invoice_id' => $invoice->id,
                    'amount'     => $refundVal,
                    'created_by' => auth()->id(),
                ]);

                Transaction::create([
                    'transaction_type' => 'Return Money to Customer (Exchange)',
                    'branch_id'        => $invoice->branch_id,
                    'date'             => $today,
                    'invoice_id'       => $invoice->id,
                    'customer_id'      => $request->customer_id,
                    'debit'            => $refundVal,
                    'credit'           => null,
                    'bank_id'          => $bankId,
                    'created_by'       => auth()->id(),
                ]);
            }

            $invoice->total_paid = $newPaid;
            $invoice->save();
        });

        session()->flash('success', __('Successfully updated exchange invoice.'));
        logActivity('Exchange Invoice', "Invoice #{$invoice->invoice_no} exchanged", $invoice);
        return redirect()->route('invoice.index');
    }


    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $repurchasedImeis = invoiceHasRepurchasedImeis($invoice);
        if (!empty($repurchasedImeis)) {
            $imeiList = implode(', ', $repurchasedImeis);
            session()->flash('error', __("Cannot delete invoice #:no because IMEI(s) :imeis have already been repurchased into active stock.", [
                'no' => $invoice->invoice_no,
                'imeis' => $imeiList
            ]));
            return back();
        }

        $invoiceItems = InvoiceItem::where('invoice_id', $id)->get();

        if ($invoice->status != 2) {
            foreach ($invoiceItems as $item) {

                $product = Product::with('unit')->find($item->product_id);

                // ✅ Quantity calculation (main + sub)
                if ($product->unit->related_unit == null) {
                    $qty = $item->main_qty;
                } else {
                    $main = $item->main_qty * $product->unit->related_value;
                    $qty = $main + $item->sub_qty;
                }

                // ✅ restore stock to FIFO queue correctly (including branch constraint)
                if ($product->is_service == 0) {
                    restoreToFIFO(
                        $product->id,
                        $qty,
                        $item->product_variation_id,
                        $invoice->branch_id
                    );
                }

                // Restore IMEI status
                if ($item->imei) {
                    $imeis = $this->parseImeis($item->imei);
                    SerialNumber::where('product_id', $item->product_id)
                                ->whereIn('serial', array_map('trim', $imeis))
                                ->update(['status' => 1]);
                }

                $item->delete();
            }
        } else {
            foreach ($invoiceItems as $item) {
                $item->delete();
            }
        }

        // transactions rollback
        $transactions = Transaction::where('invoice_id', $invoice->id)->get();
        foreach ($transactions as $transaction) {
            if ($transaction->actual_pay_id) {
                $actualpay = ActualPayment::find($transaction->actual_pay_id);
                if ($actualpay) {
                    $actualpay->amount -= $transaction->credit;
                    $actualpay->amount <= 0 ? $actualpay->delete() : $actualpay->save();
                }
            }
            $transaction->delete();
        }

        // Capture info before delete
        $invoice->load('customer', 'invoiceItems.product');
        logActivity('Delete Invoice', "Invoice #{$invoice->invoice_no} for {$invoice->customer?->name} deleted", $invoice);

        $invoice->delete();
        session()->flash('success', __('Invoice deleted successfully'));
        return back();
    }

    public function invoicePay($id)
    {

        //get invoice with supplier and user by id
        $invoice = Invoice::with('customer', 'user', 'invoiceItems')
            ->where('id', $id)->first();

        if ($invoice->total_due <= 0) {
            return redirect()->back();
        }
        // return response()->json($invoice);
        //get all payment methods
        $bank_accounts = BankAccount::where('status', 1)->get();
        // return response()->json($bank_accounts);

        return view('backend.pages.invoice.pay', compact('invoice', 'bank_accounts'));
    }

    public function storeInvoiceLog(Request $request)
    {
        if ($request->paid_amount <= 0) {
            session()->flash('error', __('Please Enter Amount'));
            return redirect()->back();
        }

        $id = $request->invoice_id;
        $this->createInvoiceDue($request, $id);

        $invoice = Invoice::find($id);
        session()->flash('success', __('Due Paid successfully'));
        logActivity('Due Payment', "Due paid for invoice #{$id}", $invoice, $request->all());
        return redirect()->route('due.invoice.print', $id);
    }
    public function printInvoice($id)
    {

        if (env('APP_IMEI') == 'yes') {
            //get invoice with supplier and user by id
            $invoice = Invoice::with('customer', 'user', 'invoiceItems')
                ->where('id', $id)->first();

            if (get_setting('inv_design') == 'pos') {
                return view('backend.pages.invoice.p-print-imei', compact('invoice'));
            } else {
                return view('backend.pages.invoice.print-imei', compact('invoice'));
            }
        } else {
            //get invoice with supplier and user by id
            $invoice = Invoice::with('customer', 'user', 'invoiceItems')
                ->where('id', $id)->first();

            if (get_setting('inv_design') == 'pos') {
                return view('backend.pages.invoice.p-print', compact('invoice'));
            } elseif (get_setting('inv_design') == 'a5') {
                return view('backend.pages.invoice.afive-print', compact('invoice'));
            } else {
                return view('backend.pages.invoice.print', compact('invoice'));
            }
        }
    }

    public function dueInvoicePrint($id)
    {
        //get invoice with supplier and user by id
        $invoice = Invoice::with('customer', 'user', 'invoiceItems')
            ->where('id', $id)->first();

        return view('backend.pages.invoice.due-print', compact('invoice'));
    }

    function createInvoiceLog($request, $id)
    {
        //added total_paid and total_due in invoice table
        $invoice = Invoice::find($id);
        if ($request->type == 'Due Paid') {
            $invoice->total_paid = $invoice->total_paid + $request->paid_amount;
            $invoice->total_due = $invoice->total_due - $request->paid_amount;
            $invoice->due_pay = $invoice->due_pay + $request->paid_amount;
        } else {
            $invoice->total_paid = $request->paid_amount;
            $invoice->total_due = $request->due_amount;
            $invoice->due_pay = $request->paid_amount;
        }
        //change invoice status to 1 if paid amount is equal to total amount
        if ($request->due_amount == 0) {
            $invoice->status = 1;
        }

        DB::transaction(function () use ($request, $invoice, $id) {
            if ($invoice->save()) {
                //create bank transaction
                if ($request->paid_amount > 0) {
                    //create bank transaction
                    $bank_transaction = new BankTransaction();
                    $bank_transaction->trans_type = 'deposit';
                    if ($request->type == 'Due Paid') {
                        $bank_transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $bank_transaction->date = $request->date;
                    }
                    $bank_transaction->bank_id = $request->bank_id;
                    $bank_transaction->invoice_id = $id;
                    $bank_transaction->amount = $request->paid_amount - $request->balance;
                    $bank_transaction->created_by = auth()->user()->id;
                    $bank_transaction->save();

                    // Transaction
                    $transaction = new Transaction();
                    $transaction->transaction_type = 'Received from Customer';
                    if ($request->type == 'Due Paid') {
                        $transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $transaction->date = $request->date;
                    }
                    $transaction->bank_id = $request->bank_id;
                    $transaction->invoice_id = $id;
                    $transaction->customer_id = $request->customer_id;
                    $transaction->debit = NULL;
                    $transaction->credit = $request->paid_amount  - $request->balance;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });
    }

    function createInvoiceInv($request, $id)
    {
        //added total_paid and total_due in invoice table
        $invoice = Invoice::find($id);
        if ($request->type == 'Due Paid') {
            $invoice->total_paid = $invoice->total_paid + $request->paid_amount;
            $invoice->total_due = $invoice->total_due - $request->paid_amount;
            $invoice->due_pay = $invoice->due_pay + $request->due_amount;
        } else {
            $invoice->total_paid = $request->paid_amount;
            $invoice->total_due = $request->due_amount;
        }
        //change invoice status to 1 if paid amount is equal to total amount
        if ($request->due_amount == 0) {
            $invoice->status = 1;
        }

        DB::transaction(function () use ($request, $invoice, $id) {
            if ($invoice->save()) {
                $paymentType = $request->input('payment_type');
                if ($paymentType === 'pos') {
                    if ($request->paid_amount > 0) {
                        //create bank transaction
                        $bank_transaction = new BankTransaction();
                        $bank_transaction->trans_type = 'deposit';
                        $bank_transaction->pay_type = 'invpay';
                        if (auth()->user()->branch_id == 1) {
                            $bank_transaction->branch_id = $request->branch_id;
                        } else {
                            $bank_transaction->branch_id = auth()->user()->branch_id;
                        }
                        if ($request->type == 'Due Paid') {
                            $bank_transaction->date = Carbon::now()->format('Y-m-d');
                        } else {
                            $bank_transaction->date = $request->date;
                        }
                        $bank_transaction->bank_id = $request->bank_id;
                        $bank_transaction->invoice_id = $id;
                        $pay_point_amount = round(((float)($request->pay_point ?? 0)) * 0.75, 2);
                        $bank_transaction->amount = (float)$request->paid_amount - (float)($request->balance ?? 0) - $pay_point_amount;
                        $bank_transaction->created_by = auth()->user()->id;
                        $bank_transaction->save();

                        // Transaction
                        $transaction = new Transaction();
                        $transaction->transaction_type = 'Received from Customer';
                        if (auth()->user()->branch_id == 1) {
                            $transaction->branch_id = $request->branch_id;
                        } else {
                            $transaction->branch_id = auth()->user()->branch_id;
                        }
                        if ($request->type == 'Due Paid') {
                            $transaction->date = Carbon::now()->format('Y-m-d');
                        } else {
                            $transaction->date = $request->date;
                        }
                        $transaction->bank_id = $request->bank_id;
                        $transaction->invoice_id = $id;
                        $transaction->customer_id = $request->customer_id;
                        $transaction->debit = NULL;
                        $transaction->credit = (float)$request->paid_amount - (float)($request->balance ?? 0) + $pay_point_amount;
                        $transaction->created_by = auth()->user()->id;
                        $transaction->save();
                    }
                } elseif ($paymentType === 'checking') {
                    $amounts = $request->input('amounts', []);
                    if (is_array($amounts)) {
                        $totalAllocated = array_sum(array_map('floatval', $amounts));
                        $pay_point_amount = round(((float)($request->pay_point ?? 0)) * 0.75, 2);
                        $actualPayable = (float)$request->paid_amount - (float)($request->balance ?? 0) - $pay_point_amount;
                        $ratio = ($totalAllocated > 0 && abs($totalAllocated - $actualPayable) > 0.01) ? ($actualPayable / $totalAllocated) : 1;

                        foreach ($amounts as $bankId => $rawAmount) {
                            $amount = round((float)$rawAmount * $ratio, 2);
                            if ($amount > 0) {
                                // Create bank transaction
                                $bank_transaction = new BankTransaction();
                                $bank_transaction->trans_type = 'deposit';
                                $bank_transaction->pay_type = 'invpay';
                                if (auth()->user()->branch_id == 1) {
                                    $bank_transaction->branch_id = $request->branch_id;
                                } else {
                                    $bank_transaction->branch_id = auth()->user()->branch_id;
                                }
                                if ($request->type == 'Due Paid') {
                                    $bank_transaction->date = Carbon::now()->format('Y-m-d');
                                } else {
                                    $bank_transaction->date = $request->date;
                                }
                                $bank_transaction->bank_id = $bankId;
                                $bank_transaction->invoice_id = $id;
                                $bank_transaction->amount = $amount;
                                $bank_transaction->created_by = auth()->user()->id;
                                $bank_transaction->save();
                            }
                        }

                        foreach ($amounts as $bankId => $rawAmount) {
                            $amount = round((float)$rawAmount * $ratio, 2);
                            if ($amount > 0) {
                                $transaction = new Transaction();
                                $transaction->transaction_type = 'Received from Customer';
                                if (auth()->user()->branch_id == 1) {
                                    $transaction->branch_id = $request->branch_id;
                                } else {
                                    $transaction->branch_id = auth()->user()->branch_id;
                                }
                                if ($request->type == 'Due Paid') {
                                    $transaction->date = Carbon::now()->format('Y-m-d');
                                } else {
                                    $transaction->date = $request->date;
                                }
                                $transaction->bank_id = $bankId;
                                $transaction->invoice_id = $id;
                                $transaction->customer_id = $request->customer_id;
                                $transaction->debit = NULL;
                                $transaction->credit = $amount;
                                $transaction->created_by = auth()->user()->id;
                                $transaction->save();
                            }
                        }
                    }
                }
            }
        });
    }

    function createInvoiceDue($request, $id)
    {
        //added total_paid and total_due in invoice table
        $invoice = Invoice::find($id);
        if ($request->type == 'Due Paid') {
            $invoice->total_paid = $invoice->total_paid + $request->paid_amount;
            $invoice->total_due = $invoice->total_due - $request->paid_amount;
            $invoice->due_pay = $invoice->due_pay + $request->paid_amount;
        } else {
            $invoice->total_paid = $request->paid_amount;
            $invoice->total_due = $request->due_amount;
            $invoice->due_pay = $request->paid_amount;
        }
        //change invoice status to 1 if paid amount is equal to total amount
        if ($invoice->total_due == 0) {
            $invoice->status = 1;
        }

        DB::transaction(function () use ($request, $invoice, $id) {
            if ($invoice->save()) {
                //create bank transaction
                if ($request->paid_amount > 0) {
                    //create bank transaction
                    $bank_transaction = new BankTransaction();
                    $bank_transaction->trans_type = 'deposit';
                    $bank_transaction->pay_type = 'duepay';
                    $bank_transaction->branch_id = $invoice->branch_id;

                    if ($request->type == 'Due Paid') {
                        $bank_transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $bank_transaction->date = $request->date;
                    }
                    $bank_transaction->bank_id = $request->bank_id;
                    $bank_transaction->invoice_id = $id;
                    $bank_transaction->amount = $request->paid_amount - $request->balance;
                    $bank_transaction->created_by = auth()->user()->id;
                    $bank_transaction->save();

                    // Transaction
                    $transaction = new Transaction();
                    $transaction->transaction_type = 'Received from Customer';
                    $transaction->branch_id = $invoice->branch_id;

                    if ($request->type == 'Due Paid') {
                        $transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $transaction->date = $request->date;
                    }
                    $transaction->bank_id = $request->bank_id;
                    $transaction->invoice_id = $id;
                    $transaction->customer_id = $request->customer_id;
                    $transaction->debit = NULL;
                    $transaction->credit = $request->paid_amount  - $request->balance;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });
    }
    public function customerPrintInvoice($id)
    {

        $invoice = Invoice::with('customer', 'user', 'invoiceItems')
            ->where('unique_id', $id)->first();
        return view('backend.pages.invoice.online-invoice', compact('invoice'));
    }
    public function getOfflineData()
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        // Fetch Products for current active branch with stock and units
        $productIds = BranchProduct::where('branch_id', $branchId)->pluck('product_id');
        $productsQuery = Product::where('status', 1)->where('is_service', 0)->whereIn('id', $productIds)->with('unit', 'variations');
        $products = $productsQuery->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'selling_price' => $product->selling_price,
                'stock_qty' => product_stock($product),
                'unit' => $product->unit,
                'variations' => $product->variations,
                'imei' => $product->imei,
                'is_service' => $product->is_service
            ];
        });

        // Fetch Customers
        $customers = Customer::select('id', 'name', 'phone')->get();

        return response()->json([
            'products' => $products,
            'customers' => $customers
        ]);
    }

    public function holdStore(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        $hold = new \App\Models\HoldInvoice();
        $hold->hold_no = 'HOLD-' . strtoupper(\Illuminate\Support\Str::random(4)) . '-' . date('His');
        $hold->branch_id = $branchId;
        $hold->user_id = auth()->id();
        $hold->customer_id = $request->customer_id ?? 1;
        $hold->customer_name = $request->customer_name ?? 'Walking Customer';
        $hold->total_amount = $request->total_amount ?? 0;
        $hold->total_items = $request->total_items ?? 0;
        $hold->items_data = [
            'localData' => $request->localData ?? [],
            'items_data' => $request->items_data ?? [],
        ];
        $hold->note = $request->note ?? null;
        $hold->save();

        $count = \App\Models\HoldInvoice::where('branch_id', $branchId)->count();

        return response()->json([
            'success' => true,
            'message' => __('Invoice held successfully'),
            'hold' => $hold,
            'hold_count' => $count
        ]);
    }

    public function holdList(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        $holds = \App\Models\HoldInvoice::with('customer', 'user')
            ->where('branch_id', $branchId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'holds' => $holds,
            'hold_count' => $holds->count()
        ]);
    }

    public function holdDelete($id)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        $hold = \App\Models\HoldInvoice::where('id', $id)
            ->where('branch_id', $branchId)
            ->first();

        if ($hold) {
            $hold->delete();
        }

        $count = \App\Models\HoldInvoice::where('branch_id', $branchId)->count();

        return response()->json([
            'success' => true,
            'message' => __('Held invoice removed'),
            'hold_count' => $count
        ]);
    }

    public function holdGet($id)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        $hold = \App\Models\HoldInvoice::with('customer', 'user')
            ->where('id', $id)
            ->where('branch_id', $branchId)
            ->first();

        if (!$hold) {
            return response()->json(['success' => false, 'message' => __('Held invoice not found')], 404);
        }

        return response()->json([
            'success' => true,
            'hold' => $hold
        ]);
    }

    private function createInvoiceItemRow($invoice, $find_unit_id, $product_id, $variation_id, $mainQty, $subQty, $rateVal, $discountVal, $itemDate, $selectedImeis = null, $warrantyVal = null, $warrantyUnit = null)
    {
        $invoice_item = new InvoiceItem();
        $invoice_item->invoice_id = $invoice->id;
        $invoice_item->product_id = $product_id;
        $invoice_item->rate = $rateVal;
        $invoice_item->product_discount = $discountVal ?? 0.00;
        $invoice_item->product_variation_id = $variation_id;
        $invoice_item->warranty_value = $warrantyVal;
        $invoice_item->warranty_unit = $warrantyUnit;
        $invoice_item->branch_id = $invoice->branch_id;
        $invoice_item->imei = !empty($selectedImeis) ? implode("\n", $selectedImeis) : null;

        if ($find_unit_id && $find_unit_id->is_service == 0) {
            if ((int) $find_unit_id->imei === 1 && !empty($selectedImeis)) {
                $qty = count($selectedImeis);
                $invoice_item->main_qty = $qty;
                $invoice_item->sub_qty = 0;
                $invoice_item->actual_main = $qty;
                $invoice_item->actual_sub = 0;
                $saleQty = $qty;

                $rate = (float) $rateVal;
                $discount = (float) $discountVal;
                $lineSubtotal = ($rate * $qty) - $discount;

                $invoice_item->subtotal = $lineSubtotal;
                $invoice_item->actual_total = $lineSubtotal;
                $invoice_item->inv_subtotal = $lineSubtotal;
            } elseif ($find_unit_id->unit && $find_unit_id->unit->related_unit == null) {
                $invoice_item->main_qty = $mainQty;
                $invoice_item->actual_main = $mainQty;
                $saleQty = $mainQty;

                $lineSubtotal = ((float)$rateVal * (float)$mainQty) - (float)$discountVal;
                $invoice_item->subtotal = $lineSubtotal;
                $invoice_item->actual_total = $lineSubtotal;
                $invoice_item->inv_subtotal = $lineSubtotal;
            } else {
                $invoice_item->main_qty = $mainQty;
                $invoice_item->sub_qty = $subQty;
                $invoice_item->actual_main = $mainQty;
                $invoice_item->actual_sub = $subQty;
                $main = $mainQty * ($find_unit_id->unit ? $find_unit_id->unit->related_value : 1);
                $sub = $subQty ?? 0;
                $saleQty = $main + $sub;

                $lineSubtotal = ((float)$rateVal * (float)$mainQty) - (float)$discountVal;
                $invoice_item->subtotal = $lineSubtotal;
                $invoice_item->actual_total = $lineSubtotal;
                $invoice_item->inv_subtotal = $lineSubtotal;
            }

            $invoice_item->pur_subtotal = calculateUnitPriceUsingFIFO($product_id, $saleQty, $variation_id, $invoice->branch_id);
        } else {
            $invoice_item->main_qty = $mainQty;
            $lineSubtotal = ((float)$rateVal * (float)$mainQty) - (float)$discountVal;
            $invoice_item->subtotal = $lineSubtotal;
            $invoice_item->inv_subtotal = $lineSubtotal;
            $invoice_item->pur_subtotal = ($find_unit_id->purchase_price ?? 0.00) * $mainQty;
        }

        $invoice_item->date = $itemDate;
        $invoice_item->save();

        return $invoice_item;
    }
}
