<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Brand;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Customer;
use App\Models\Supplier;
use Carbon\CarbonPeriod;
use App\Models\OwnerShip;
use App\Models\DamageItem;
use App\Models\ReturnItem;
use App\Models\BankAccount;
use App\Models\BranchBrand;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use App\Models\UsedProduct;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\TransferItem;
use Illuminate\Http\Request;
use App\Models\BranchProduct;
use App\Models\BranchCategory;
use App\Models\Rack;
use App\Models\BranchRack;
use Illuminate\Support\Carbon;
use App\Models\BankTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    public function stock(Request $request)
    {
        $data['product_id'] = $request->product_id;
        $data['keyword'] = $request->search_keyword;
        $data['category_id'] = $request->category_id;
        $data['sub_category_id'] = $request->sub_category_id;
        $data['brand_id'] = $request->brand_id;
        $data['subCategories'] = $request->category_id ? SubCategory::where('category_id', $request->category_id)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $query = Product::with(['unit.related_unit', 'subCategory', 'racks'])->where('is_service', 0);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $brandIds = BranchBrand::where('branch_id', $filterBranchId)->pluck('brand_id');
                $rackIds = BranchRack::where('branch_id', $filterBranchId)->pluck('rack_id');
                $data['produc'] = Product::whereIn('id', $productIds)->with(['unit.related_unit', 'racks'])
                    ->where('is_service', 0)
                    ->orderBy('created_at', 'DESC')
                    ->get();
                $data['categories'] = Category::whereIn('id', $categoryIds)->orderBy('id', 'desc')->get();
                $data['brands'] = Brand::whereIn('id', $brandIds)->orderBy('id', 'desc')->get();
                $data['racks'] = Rack::whereIn('id', $rackIds)->orderBy('name', 'asc')->get();
                $query->whereIn('id', $productIds);
            } else {
                $data['produc'] = Product::with(['unit.related_unit', 'racks'])
                    ->where('is_service', 0)
                    ->orderBy('created_at', 'DESC')
                    ->get();
                $data['categories'] = Category::orderBy('id', 'desc')->get();
                $data['brands'] = Brand::orderBy('id', 'desc')->get();
                $data['racks'] = Rack::orderBy('name', 'asc')->get();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $brandIds = BranchBrand::where('branch_id', $userBranchId)->pluck('brand_id');
            $rackIds = BranchRack::where('branch_id', $userBranchId)->pluck('rack_id');
            $data['produc'] = Product::whereIn('id', $productIds)->with(['unit.related_unit', 'racks'])
                ->where('is_service', 0)
                ->orderBy('created_at', 'DESC')
                ->get();
            $data['categories'] = Category::whereIn('id', $categoryIds)->orderBy('id', 'desc')->get();
            $data['brands'] = Brand::whereIn('id', $brandIds)->orderBy('id', 'desc')->get();
            $data['racks'] = Rack::whereIn('id', $rackIds)->orderBy('name', 'asc')->get();
            $query->whereIn('id', $productIds);
        }

        if ($request->product_id != null) {
            $query->where('id', $request->product_id);
        }
        if ($request->category_id != null) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->sub_category_id != null) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if ($request->brand_id != null) {
            $query->where('brand_id', $request->brand_id);
        }

        $keyword = $request->search_keyword ?? $request->barcode;
        if ($keyword != null) {
            $keyword = trim($keyword);
            $resolved = function_exists('resolveProductAndVariationFromBarcode') ? resolveProductAndVariationFromBarcode($keyword) : ['product_id' => null];
            if ($resolved['product_id']) {
                // Variation barcode scanned — filter directly by the resolved product
                $query->where('id', $resolved['product_id']);
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                      ->orWhere('barcode', 'like', '%' . $keyword . '%')
                      ->orWhereHas('product_variations', function($vq) use ($keyword) {
                          $vq->where('barcode', 'like', "%{$keyword}%");
                      });
                });
            }
        }

        $allProductsQuery = clone $query;
        $allProducts = $allProductsQuery->with('product_variations')->get();

        $data['products'] = $query
            ->orderBy('created_at', 'DESC')
            ->paginate(20)->appends($request->all());

        // grand totals
        $grand_purchase   = 0;
        $grand_sale       = 0;
        $grand_transfer   = 0;
        $grand_receive    = 0;
        $grand_sale_ret   = 0;
        $grand_pur_ret    = 0;
        $grand_damage     = 0;
        $grand_stock_qty  = 0;
        $grand_adjust_in_total = 0;
        $grand_adjust_out_total = 0;
        $grand_purchase_value = 0;
        $grand_mrp_value = 0;

        $branchIdForStock = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : ($userBranchId == 1 ? null : $userBranchId);

        foreach ($allProducts as $product) {
            $purchase = (float) purchased_qty($product);
            $sale     = (float) invoiced_qty($product);
            $transfer = (float) stock_transfer_qty($product);
            $receive  = (float) stock_receive_qty($product);
            $sale_ret = (float) returned_qty($product);
            $pur_ret  = (float) return_pur_qty($product);
            $damage   = (float) damaged_qty($product);
            $adjust_in_total = (float) adjust_in($product);
            $adjust_out_total = (float) adjust_out($product);

            $factor = ($product->unit && $product->unit->related_value) ? (float)$product->unit->related_value : 1;
            if ($factor <= 0) $factor = 1;
            $fakeStockQty = product_fake_stock_val($product, $branchIdForStock);
            $stock = $fakeStockQty / $factor;

            $unitCost = product_purchase_unit_cost($product, $branchIdForStock);
            $sellingPriceUnit = ((float) ($product->selling_price ?: $product->dis_selling_price)) / $factor;

            $grand_purchase  += $purchase;
            $grand_sale      += $sale;
            $grand_transfer  += $transfer;
            $grand_receive   += $receive;
            $grand_sale_ret  += $sale_ret;
            $grand_pur_ret   += $pur_ret;
            $grand_damage    += $damage;
            $grand_stock_qty += $stock;
            $grand_adjust_in_total += $adjust_in_total;
            $grand_adjust_out_total += $adjust_out_total;

            $grand_purchase_value += ($fakeStockQty * $unitCost);
            $grand_mrp_value += ($fakeStockQty * $sellingPriceUnit);
        }

        return view('backend.pages.report.sc_stock', $data, compact(
            'grand_purchase',
            'grand_sale',
            'grand_transfer',
            'grand_receive',
            'grand_sale_ret',
            'grand_pur_ret',
            'grand_damage',
            'grand_adjust_in_total',
            'grand_adjust_out_total',
            'grand_stock_qty',
            'grand_purchase_value',
            'grand_mrp_value'
        ));
    }

    public function supplierLedger(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $data['suppliers'] = Supplier::where('branch_id', $filterBranchId)->orderBy('created_at', 'desc')->get();
            } else {
                $data['suppliers'] = Supplier::orderBy('created_at', 'desc')->get();
            }
        } else {
            $data['suppliers'] = Supplier::where('branch_id', $userBranchId)->orderBy('created_at', 'desc')->get();
        }

        if ($request->all() != NULL) {
            $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;
            $data['oneSupplier'] = Supplier::where('id', $request->supplier_id)->where('status', 1)->first();
            $data['sdate'] = Carbon::createFromDate($request->start_date)->format('Y-m-d');
            $data['edate'] = Carbon::createFromDate($request->end_date)->format('Y-m-d');

            $data['transaction'] = Transaction::with('invoice', 'purchase', 'supplier')
                ->where('supplier_id', $request->supplier_id)
                ->whereBetween('date', [$data['sdate'], $data['edate']])
                ->when($branchId, function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get();


            // Calculate Opening Balance before start date
            $before_trans = Transaction::where('supplier_id', $request->supplier_id)
                ->where('date', '<', $data['sdate'])
                ->when($branchId, function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->get();
            $previous_balance = $before_trans->sum('debit') - $before_trans->sum('credit');
            $data['previous_balance'] = $previous_balance;

            $data['closeing_balance'] = $previous_balance + $data['transaction']->sum('debit') - $data['transaction']->sum('credit');
        }
        return view('backend.pages.report.supplier_ledger', $data);
    }

    public function customerLedger(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        if ($branchId) {
            $data['customers'] = Customer::where('branch_id', $branchId)->orderBy('created_at', 'desc')->get();
        } else {
            $data['customers'] = Customer::orderBy('created_at', 'desc')->get();
        }

        if ($request->all() != NULL) {
            $data['sdate'] = Carbon::createFromDate($request->start_date)->format('Y-m-d');
            $data['edate'] = Carbon::createFromDate($request->end_date)->format('Y-m-d');

            if ($branchId) {
                $data['oneCustomer'] = Customer::where('branch_id', $branchId)->where('id', $request->customer_id)->where('status', 1)->first();
            } else {
                $data['oneCustomer'] = Customer::where('id', $request->customer_id)->where('status', 1)->first();
            }

            // Get transactions within period (filtered by active branch)
            $transQuery = Transaction::with('invoice', 'purchase', 'customer')
                ->where('customer_id', $request->customer_id)
                ->whereBetween('date', [$data['sdate'], $data['edate']]);
            
            if ($branchId) {
                $transQuery->where('branch_id', $branchId);
            }
            $transactions = Transaction::filterByFakeSale($transQuery, 'asc');
            $data['transaction'] = $transactions->sort(function ($a, $b) {
                $isOpeningA = ($a->transaction_type === 'Opening Due Amount' || $a->transaction_type === 'Opening Balance');
                $isOpeningB = ($b->transaction_type === 'Opening Due Amount' || $b->transaction_type === 'Opening Balance');

                if ($isOpeningA && !$isOpeningB) return -1;
                if (!$isOpeningA && $isOpeningB) return 1;

                if ($a->date === $b->date) {
                    return $a->id <=> $b->id;
                }
                return strcmp($a->date, $b->date);
            })->values();


            // Calculate Opening Balance before start date
            $beforeDebitQuery = Transaction::where('customer_id', $request->customer_id)->where('date', '<', $data['sdate']);
            $beforeCreditQuery = Transaction::where('customer_id', $request->customer_id)->where('date', '<', $data['sdate']);
            if ($branchId) {
                $beforeDebitQuery->where('branch_id', $branchId);
                $beforeCreditQuery->where('branch_id', $branchId);
            }
            $previous_balance = Transaction::getFakeSum($beforeDebitQuery, 'debit') - Transaction::getFakeSum($beforeCreditQuery, 'credit');
            $data['previous_balance'] = $previous_balance;

            // Calculate Closing Balance (Opening Balance + period total)
            $periodDebitQuery = Transaction::where('customer_id', $request->customer_id)->whereBetween('date', [$data['sdate'], $data['edate']]);
            $periodCreditQuery = Transaction::where('customer_id', $request->customer_id)->whereBetween('date', [$data['sdate'], $data['edate']]);
            if ($branchId) {
                $periodDebitQuery->where('branch_id', $branchId);
                $periodCreditQuery->where('branch_id', $branchId);
            }
            
            $data['closeing_balance'] = $previous_balance 
                + Transaction::getFakeSum($periodDebitQuery, 'debit') 
                - Transaction::getFakeSum($periodCreditQuery, 'credit');
        }
        return view('backend.pages.report.customer_ledger', $data);
    }

    public function customerFullReport(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        if ($branchId) {
            $data['customers'] = Customer::where('branch_id', $branchId)->orderBy('name')->get();
        } else {
            $data['customers'] = Customer::orderBy('name')->get();
        }

        $data['customer_id'] = $request->customer_id;
        $data['customer'] = null;
        $data['invoices'] = collect();
        $data['transactions'] = collect();

        if ($request->filled('customer_id')) {
            $customer = Customer::with('vehicles')->find($request->customer_id);
            $data['customer'] = $customer;

            // All invoices for this customer
            $data['invoices'] = Invoice::with('invoiceItems.product')
                ->where('customer_id', $request->customer_id)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->orderBy('date', 'asc')
                ->get();

            // All transactions for this customer
            $data['transactions'] = Transaction::with('invoice')
                ->where('customer_id', $request->customer_id)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->orderBy('date', 'asc')
                ->get();

            // Summary totals
            $data['total_invoice_amount'] = $data['invoices']->sum('total_amount');
            $data['total_paid']           = $data['invoices']->sum('total_paid');
            $data['total_due']            = $data['invoices']->sum('total_due');
            $data['opening_due']          = $customer->due_amount ?? 0;
            $data['grand_due']            = $data['total_due'] + $data['opening_due'];
        }

        return view('backend.pages.report.customer_full_report', $data);
    }

    public function customerDue(Request $request)
    {

        $data['customer_id'] = $request->customer_id;
        $data['phone_no'] = $request->phone_no;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $customersQuery = Customer::query();

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $data['custommer'] = Customer::where('branch_id', $filterBranchId)->orderBy('created_at', 'desc')->get();
                $customersQuery->where('branch_id', $filterBranchId);
            } else {
                $data['custommer'] = Customer::orderBy('created_at', 'desc')->get();
            }
        } else {
            $data['custommer'] = Customer::where('branch_id', $userBranchId)->orderBy('created_at', 'desc')->get();
            $customersQuery->where('branch_id', $userBranchId);
        }

        if ($request->customer_id != null) {
            $customersQuery->where('id', $request->customer_id);
        }
        if ($request->phone_no != null) {
            $customersQuery->where('phone', $request->phone_no);
        }
        $filteredCustomers = $customersQuery->get()->filter(function ($customer) {
            $inv_due = Invoice::getFakeSum(Invoice::where('customer_id', $customer->id), 'total_due');
            $open_balance = open_balance_customer($customer->id, $customer->due_amount);
            return ($inv_due + $open_balance) > 0;
        })->values();

        $page = request()->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredCustomers->slice($offset, $perPage),
            $filteredCustomers->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        $data['customers'] = $paginated;

        return view('backend.pages.report.customer_due', $data);
    }

    public function supplierDue(Request $request)
    {
        $data['supplier_id'] = $request->supplier_id;
        $data['phone_no'] = $request->phone_no;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $suppliersQuery = Supplier::query();
        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $data['supliers'] = Supplier::where('branch_id', $filterBranchId)->get();
                $suppliersQuery->where('branch_id', $filterBranchId);
            } else {
                $data['supliers'] = Supplier::get();
            }
        } else {
            $data['supliers'] = Supplier::where('branch_id', $userBranchId)->get();
            $suppliersQuery->where('branch_id', $userBranchId);
        }
        if ($request->supplier_id != null) {
            $suppliersQuery->where('id', $request->supplier_id);
        }

        if ($request->phone_no != null) {
            $suppliersQuery->where('phone', $request->phone_no);
        }
        $filteredSuppliers = $suppliersQuery->get()->filter(function ($supplier) {
            $total_payable = \App\Models\Purchase::where('supplier_id', $supplier->id)->sum('total_amount');
            $total_paid = \App\Models\Purchase::where('supplier_id', $supplier->id)->sum('total_paid');
            $opening_balance = $supplier->opening_balance ?? 0;

            $total_due = ($total_payable - $total_paid) + $opening_balance;

            return $total_due > 0;
        })->values();

        $page = request()->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredSuppliers->slice($offset, $perPage),
            $filteredSuppliers->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $data['suppliers'] = $paginated;

        return view('backend.pages.report.supplier_due', $data);
    }

    public function daily(Request $request)
    {
        if ($request->all() != NULL) {
            $data['sdate'] = Carbon::createFromDate($request->start_date)->toDateString();
            $ssdate = Carbon::createFromDate('2023-09-01')->toDateString();
            $sdate = Carbon::createFromDate($request->start_date)->toDateString();
            $data['bankAcc'] = BankAccount::where('status', 1)->orderBy('id', 'asc')->first();

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', null);

            if ($userBranchId == 1) {
                if ($filterBranchId) {
                    $data['invoices'] = Invoice::filterByFakeSale(Invoice::with('invoiceItems')->where('branch_id', $filterBranchId)->where(function($q) use ($sdate) {
                        $q->where('date', $sdate)
                          ->orWhereHas('invoiceItems', fn($iq) => $iq->where('date', $sdate))
                          ->orWhereHas('bankTransactions', fn($bq) => $bq->where('date', $sdate));
                    })->get());
                    $data['bankTrans'] = BankTransaction::filterByFakeSale(BankTransaction::where('date', $sdate)->where('branch_id', $filterBranchId)->where('pay_type', 'duepay'));
                    $data['bankTransdue'] = BankTransaction::getFakeSum(BankTransaction::where('date', $sdate)->where('branch_id', $filterBranchId)->where('pay_type', 'duepay'), 'amount');
                    $previous_total_sale = max(0, InvoiceItem::getFakeSum(InvoiceItem::whereBetween('date', [$ssdate, $sdate])->where('branch_id', $filterBranchId), 'inv_subtotal') - ReturnItem::where('branch_id', $filterBranchId)->whereBetween('date', [$ssdate, $sdate])->sum('subtotal'));
                    $today_total_sale = max(0, InvoiceItem::getFakeSum(InvoiceItem::where('date', $sdate)->where('branch_id', $filterBranchId), 'inv_subtotal') - ReturnItem::where('branch_id', $filterBranchId)->where('date', $sdate)->sum('subtotal'));
                    $privious_sale = ($previous_total_sale - $today_total_sale);
                    $today_sale_amount = $today_total_sale;
                    $total_expense = Expense::where('date', $sdate)->where('branch_id', $filterBranchId)->get();
                } else {
                    $data['invoices'] = Invoice::filterByFakeSale(Invoice::with('invoiceItems')->where(function($q) use ($sdate) {
                        $q->where('date', $sdate)
                          ->orWhereHas('invoiceItems', fn($iq) => $iq->where('date', $sdate))
                          ->orWhereHas('bankTransactions', fn($bq) => $bq->where('date', $sdate));
                    })->get());
                    $data['bankTrans'] = BankTransaction::filterByFakeSale(BankTransaction::where('date', $sdate)->where('pay_type', 'duepay'));
                    $data['bankTransdue'] = BankTransaction::getFakeSum(BankTransaction::where('date', $sdate)->where('pay_type', 'duepay'), 'amount');
                    $previous_total_sale = max(0, InvoiceItem::getFakeSum(InvoiceItem::whereBetween('date', [$ssdate, $sdate]), 'inv_subtotal') - ReturnItem::whereBetween('date', [$ssdate, $sdate])->sum('subtotal'));
                    $today_total_sale = max(0, InvoiceItem::getFakeSum(InvoiceItem::where('date', $sdate), 'inv_subtotal') - ReturnItem::where('date', $sdate)->sum('subtotal'));
                    $privious_sale = ($previous_total_sale - $today_total_sale);
                    $today_sale_amount = $today_total_sale;
                    $total_expense = Expense::where('date', $sdate)->get();
                }
            } else {
                $invoiceQuery = Invoice::with('invoiceItems')->where('branch_id', $userBranchId)->where(function($q) use ($sdate) {
                    $q->where('date', $sdate)
                      ->orWhereHas('invoiceItems', fn($iq) => $iq->where('date', $sdate))
                      ->orWhereHas('bankTransactions', fn($bq) => $bq->where('date', $sdate));
                });
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $invoiceQuery->where('branch_id', auth()->user()->branch_id);
                }
                $data['invoices'] = Invoice::filterByFakeSale($invoiceQuery->get());

                $bankTransQuery = BankTransaction::where('date', $sdate)->where('branch_id', $userBranchId)->where('pay_type', 'duepay');
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $bankTransQuery->where('branch_id', auth()->user()->branch_id);
                }
                $data['bankTrans'] = BankTransaction::filterByFakeSale($bankTransQuery);

                $bankTransDueQuery = BankTransaction::where('date', $sdate)->where('branch_id', $userBranchId)->where('pay_type', 'duepay');
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $bankTransDueQuery->where('branch_id', auth()->user()->branch_id);
                }
                $data['bankTransdue'] = BankTransaction::getFakeSum($bankTransDueQuery, 'amount');

                $prevSaleQuery = InvoiceItem::whereBetween('date', [$ssdate, $sdate])->where('branch_id', $userBranchId);
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $prevSaleQuery->where('branch_id', auth()->user()->branch_id);
                }
                $previous_total_sale = max(0, InvoiceItem::getFakeSum($prevSaleQuery, 'inv_subtotal') - ReturnItem::where('branch_id', $userBranchId)->whereBetween('date', [$ssdate, $sdate])->sum('subtotal'));

                $todayTotalSaleQuery = InvoiceItem::where('date', $sdate)->where('branch_id', $userBranchId);
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $todayTotalSaleQuery->where('branch_id', auth()->user()->branch_id);
                }
                $today_total_sale = max(0, InvoiceItem::getFakeSum($todayTotalSaleQuery, 'inv_subtotal') - ReturnItem::where('branch_id', $userBranchId)->where('date', $sdate)->sum('subtotal'));

                $privious_sale = ($previous_total_sale - $today_total_sale);

                $today_sale_amount = $today_total_sale;

                $total_expense = Expense::where('date', $sdate)->where('branch_id', $userBranchId)->get();
            }

            return view('backend.pages.report.daily', $data, compact('today_sale_amount', 'total_expense', 'privious_sale', 'previous_total_sale'));
        } else {
            return view('backend.pages.report.daily');
        }
    }




    public function accountLedger(Request $request)
    {
        // dd($request->all());
        $data['accounts'] = \App\Models\BankAccount::all();

        if ($request->filled('bank_id') && $request->filled('start_date') && $request->filled('end_date')) {

            $data['oneAccount'] = BankAccount::find($request->bank_id);
            $bank_name = BankAccount::where('id', $request->bank_id)->first();

            // рждрж╛рж░рж┐ржЦ ржарж┐ржХ ржХрж░рж╛
            $sdate = Carbon::parse($request->start_date)->startOfDay();
            $edate = Carbon::parse($request->end_date)->endOfDay();

            // Start Date ржПрж░ ржЖржЧрзЗрж░ рж╕ржорж╕рзНржд ржЯрзНрж░рж╛ржиржЬрзНржпрж╛ржХрж╢ржи ржерзЗржХрзЗ Opening Balance ржмрзЗрж░ ржХрж░рж╛
            $before_deposit = BankTransaction::where('bank_id', $bank_name->id)
                ->where('trans_type', 'deposit')
                ->where('date', '<', $sdate)
                ->sum('amount');

            $before_withdraw = BankTransaction::where('bank_id', $bank_name->id)
                ->where('trans_type', 'withdraw')
                ->where('date', '<', $sdate)
                ->sum('amount');

            $before_transfer_out = BankTransaction::where('from_bank_id', $bank_name->id)
                ->where('trans_type', 'transfer')
                ->where('date', '<', $sdate)
                ->sum('amount');

            $before_transfer_in = BankTransaction::where('to_bank_id', $bank_name->id)
                ->where('trans_type', 'transfer')
                ->where('date', '<', $sdate)
                ->sum('amount');

            $previous_balance = $bank_name->opening_balance + $before_deposit - $before_withdraw - $before_transfer_out + $before_transfer_in;

            $data['previous_balance_closing'] = $previous_balance;

            // ржлрж┐рж▓рзНржЯрж╛рж░ ржХрж░рж╛ ржЯрзНрж░рж╛ржиржЬрзНржпрж╛ржХрж╢ржи
            $data['bank_transaction'] = BankTransaction::where(function ($q) use ($request) {
                $q->where('bank_id', $request->bank_id)
                    ->orWhere('from_bank_id', $request->bank_id)
                    ->orWhere('to_bank_id', $request->bank_id);
            })
                ->whereBetween('date', [$sdate, $edate])
                ->orderBy('date', 'asc')
                ->get();
            $bank_transaction_closing_form = BankTransaction::where('from_bank_id', $request->bank_id)->where('trans_type', 'transfer')
                ->sum('amount');
            $bank_transaction_closing_to = BankTransaction::where('to_bank_id', $request->bank_id)->where('trans_type', 'transfer')
                ->sum('amount');
            $bank_transaction_closing_depo = BankTransaction::where('bank_id', $request->bank_id)->where('trans_type', 'deposit')
                ->sum('amount');
            $bank_transaction_closing_with = BankTransaction::where('bank_id', $request->bank_id)
                ->where('trans_type', 'withdraw')
                ->sum('amount');
            $balance_check = $bank_transaction_closing_depo - $bank_transaction_closing_with - $bank_transaction_closing_form + $bank_transaction_closing_to + $bank_name->opening_balance;

            $data['balance_closing'] = $balance_check;
        } else {
            $data['bank_transaction'] = collect(); // ржЦрж╛рж▓рж┐ ржХрж╛рж▓рзЗржХрж╢ржи

            $data['balance_closing'] = 0;
        }


        return view('backend.pages.report.account_ledger', $data);
    }




    public function dailyStock(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $data['sdate'] = Carbon::parse($request->start_date)->toDateString();
        $data['edate'] = Carbon::parse($request->end_date)->toDateString();

        $sdate = $data['sdate'];
        $edate = $data['edate'];

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $purchaseItems = PurchaseItem::where('branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate])->get();
                $transferItems = TransferItem::where('from_branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate])->get();
                $receiveItems = TransferItem::where('status', 1)->where('to_branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate])->get();
                $invoiceItems  = InvoiceItem::filterByFakeSale(InvoiceItem::where('branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate]));
                $returnItems   = ReturnItem::filterByFakeSale(ReturnItem::where('branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate]));
                $damageItems   = DamageItem::where('branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate])->get();
            } else {
                $purchaseItems = PurchaseItem::whereBetween('date', [$sdate, $edate])->get();
                $transferItems = TransferItem::whereBetween('date', [$sdate, $edate])->get();
                $receiveItems = TransferItem::where('status', 1)->whereBetween('date', [$sdate, $edate])->get();
                $invoiceItems  = InvoiceItem::filterByFakeSale(InvoiceItem::whereBetween('date', [$sdate, $edate]));
                $returnItems   = ReturnItem::filterByFakeSale(ReturnItem::whereBetween('date', [$sdate, $edate]));
                $damageItems   = DamageItem::whereBetween('date', [$sdate, $edate])->get();
            }
        } else {
            $purchaseItems = PurchaseItem::where('branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate])->get();
            $transferItems = TransferItem::where('from_branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate])->get();
            $receiveItems = TransferItem::where('status', 1)->where('to_branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate])->get();
            $invoiceItems  = InvoiceItem::filterByFakeSale(InvoiceItem::where('branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate]));
            $returnItems   = ReturnItem::filterByFakeSale(ReturnItem::where('branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate]));
            $damageItems   = DamageItem::where('branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate])->get();
        }



        $products = [];
        $productQuantities = []; // [product_id => [...qtys...]]

        $initProduct = function ($product_id) use (&$products, &$productQuantities) {
            if (!isset($products[$product_id])) {
                $product = Product::find($product_id);
                if ($product) {
                    $products[$product_id] = $product;
                }
            }
            if (!isset($productQuantities[$product_id])) {
                $productQuantities[$product_id] = [
                    'purchase' => 0,
                    'transfer' => 0,
                    'receive' => 0,
                    'invoice'  => 0,
                    'return'   => 0,
                    'damage'   => 0,
                ];
            }
        };

        // Loop and collect data
        foreach ($purchaseItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['purchase'] += $item->main_qty;
        }

        foreach ($transferItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['transfer'] += $item->main_qty;
        }
        foreach ($receiveItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['receive'] += $item->main_qty;
        }

        foreach ($invoiceItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['invoice'] += $item->main_qty;
        }

        foreach ($returnItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['return'] += $item->main_qty;
        }

        foreach ($damageItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['damage'] += $item->main_qty;
        }

        $barcode = $request->barcode ?? $request->search;
        $data['barcode'] = $barcode;
        if (!empty($barcode)) {
            $barcodeVal = strtolower(trim($barcode));
            $filteredProducts = [];
            $filteredQuantities = [];
            foreach ($products as $id => $p) {
                if (str_contains(strtolower($p->barcode ?? ''), $barcodeVal) || str_contains(strtolower($p->name ?? ''), $barcodeVal)) {
                    $filteredProducts[$id] = $p;
                    $filteredQuantities[$id] = $productQuantities[$id];
                }
            }
            $products = $filteredProducts;
            $productQuantities = $filteredQuantities;
        }

        // Pass to view
        $data['products'] = array_values($products);
        $data['productQuantities'] = $productQuantities;

        return view('backend.pages.report.daily_stock', $data);
    }

    public function sale(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        // Default data
        $data['products'] = Product::orderBy('created_at', 'DESC')->get();
        $data['categories'] = Category::orderBy('created_at', 'DESC')->get();
        $data['subCategories'] = $request->category_id ? SubCategory::where('category_id', $request->category_id)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();

        if ($request->filled('start_date') && $request->filled('end_date')) {

            $data['sdate'] = Carbon::parse($request->start_date)->toDateString();
            $data['edate'] = Carbon::parse($request->end_date)->toDateString();

            $sdate = $data['sdate'];
            $edate = $data['edate'];

            // Branch Logic
            if ($userBranchId == 1) {
                $branchId = $filterBranchId;
            } else {
                $branchId = $userBranchId;
            }

            // Product + Category filter by branch
            if ($branchId) {
                $productIds = BranchProduct::where('branch_id', $branchId)->pluck('product_id');
                $categoryIds = BranchCategory::where('branch_id', $branchId)->pluck('category_id');

                $data['products'] = Product::whereIn('id', $productIds)->latest()->get();
                $data['categories'] = Category::whereIn('id', $categoryIds)->latest()->get();
            }

            // Main Query (IMPORTANT FIX)
            $invoiceQuery = Invoice::with(['invoiceItems' => function ($q) use ($request, $sdate, $edate) {

                $q->whereBetween('date', [$sdate, $edate]);

                // Product filter
                if ($request->filled('product_id')) {
                    $q->where('product_id', $request->product_id);
                }

                // Category filter
                if ($request->filled('category_id')) {
                    $q->whereHas('product', function ($sub) use ($request) {
                        $sub->where('category_id', $request->category_id);
                    });
                }

                // Sub Category filter
                if ($request->filled('sub_category_id')) {
                    $q->whereHas('product', function ($sub) use ($request) {
                        $sub->where('sub_category_id', $request->sub_category_id);
                    });
                }
            }, 'invoiceItems.product.unit.related_unit'])
                ->where(function($mainQ) use ($sdate, $edate) {
                    $mainQ->whereBetween('date', [$sdate, $edate])
                          ->orWhereHas('invoiceItems', fn($iq) => $iq->whereBetween('date', [$sdate, $edate]));
                });

            // Add role-based filter to match InvoiceController
            if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                $invoiceQuery->where('branch_id', auth()->user()->branch_id);
            }

            // Branch condition
            if ($branchId) {
                $invoiceQuery->where('branch_id', $branchId);
            }

            // Filter only invoices that have matching items
            $invoiceQuery->whereHas('invoiceItems', function ($q) use ($request, $sdate, $edate) {
                $q->whereBetween('date', [$sdate, $edate]);

                if ($request->filled('product_id')) {
                    $q->where('product_id', $request->product_id);
                }

                if ($request->filled('category_id')) {
                    $q->whereHas('product', function ($sub) use ($request) {
                        $sub->where('category_id', $request->category_id);
                    });
                }

                if ($request->filled('sub_category_id')) {
                    $q->whereHas('product', function ($sub) use ($request) {
                        $sub->where('sub_category_id', $request->sub_category_id);
                    });
                }
            });

            $data['invoiceItem'] = Invoice::filterByFakeSale($invoiceQuery);
            $data['total_return_amount'] = ReturnItem::whereBetween('date', [$sdate, $edate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('subtotal');
        }

        return view('backend.pages.report.sale', $data);
    }

    public function itemSale(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $data['subCategories'] = $request->category_id ? SubCategory::where('category_id', $request->category_id)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $data['products'] = Product::whereIn('id', $productIds)->latest()->get();
                $data['categories'] = Category::whereIn('id', $categoryIds)->latest()->get();
            } else {
                $data['products'] = Product::latest()->get();
                $data['categories'] = Category::latest()->get();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $data['products'] = Product::whereIn('id', $productIds)->latest()->get();
            $data['categories'] = Category::whereIn('id', $categoryIds)->latest()->get();
        }

        if ($request->all() != null) {

            $data['sdate'] = Carbon::parse($request->start_date)->toDateString();
            $data['edate'] = Carbon::parse($request->end_date)->toDateString();

            $sdate = $data['sdate'];
            $edate = $data['edate'];

            $invoiceItemQuery = InvoiceItem::with(['invoice', 'product'])
                ->whereBetween('date', [$sdate, $edate])
                ->whereHas('invoice', function ($q) use ($userBranchId, $filterBranchId) {
                    // Add role-based filter to match InvoiceController
                    if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                        $q->where('branch_id', auth()->user()->branch_id);
                    }

                    if ($userBranchId == 1) {
                        if ($filterBranchId) {
                            $q->where('branch_id', $filterBranchId);
                        }
                    } else {
                        $q->where('branch_id', $userBranchId);
                    }
                });

            // Barcode / Product Keyword filter
            if (!empty($request->barcode)) {
                $barcode = trim($request->barcode);
                $invoiceItemQuery->whereHas('product', function ($q) use ($barcode) {
                    $q->where('barcode', $barcode)
                      ->orWhere('barcode', 'like', "%{$barcode}%")
                      ->orWhere('name', 'like', "%{$barcode}%")
                      ->orWhereHas('product_variations', function($vq) use ($barcode) {
                          $vq->where('barcode', 'like', "%{$barcode}%");
                      });
                });
            }

            // Product filter
            if (!empty($request->product_id)) {
                $invoiceItemQuery->where('product_id', $request->product_id);
            }

            // Category filter
            if (!empty($request->category_id)) {
                $invoiceItemQuery->whereHas('product', function ($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            }

            // Sub Category filter
            if (!empty($request->sub_category_id)) {
                $invoiceItemQuery->whereHas('product', function ($q) use ($request) {
                    $q->where('sub_category_id', $request->sub_category_id);
                });
            }

            $data['invoiceItem'] = InvoiceItem::filterByFakeSale($invoiceItemQuery);

            $returnItemQuery = ReturnItem::with(['invoice', 'product.category', 'product.unit.related_unit', 'return'])
                ->whereBetween('date', [$sdate, $edate])
                ->where(function ($q) use ($userBranchId, $filterBranchId) {
                    if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                        $q->where('branch_id', auth()->user()->branch_id);
                    }
                    if ($userBranchId == 1) {
                        if ($filterBranchId) {
                            $q->where('branch_id', $filterBranchId);
                        }
                    } else {
                        $q->where('branch_id', $userBranchId);
                    }
                });

            if (!empty($request->barcode)) {
                $barcode = trim($request->barcode);
                $returnItemQuery->whereHas('product', function ($q) use ($barcode) {
                    $q->where('barcode', $barcode)
                      ->orWhere('barcode', 'like', "%{$barcode}%")
                      ->orWhere('name', 'like', "%{$barcode}%")
                      ->orWhereHas('product_variations', function($vq) use ($barcode) {
                          $vq->where('barcode', 'like', "%{$barcode}%");
                      });
                });
            }

            if (!empty($request->product_id)) {
                $returnItemQuery->where('product_id', $request->product_id);
            }

            if (!empty($request->category_id)) {
                $returnItemQuery->whereHas('product', function ($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            }

            if (!empty($request->sub_category_id)) {
                $returnItemQuery->whereHas('product', function ($q) use ($request) {
                    $q->where('sub_category_id', $request->sub_category_id);
                });
            }

            $data['returnItems'] = ReturnItem::filterByFakeSale($returnItemQuery);
        }

        return view('backend.pages.report.item_sale', $data);
    }

    public function service(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        // Only load service products
        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $data['products'] = Product::whereIn('id', $productIds)->where('is_service', 1)->orderBy('name', 'asc')->get();
            } else {
                $data['products'] = Product::where('is_service', 1)->orderBy('name', 'asc')->get();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $data['products'] = Product::whereIn('id', $productIds)->where('is_service', 1)->orderBy('name', 'asc')->get();
        }

        // Initialize variables
        $data['invoiceItem'] = null;

        // If filters applied
        if ($request->all() != null) {
            $data['oneProduct'] = Product::find($request->product_id);
            $data['sdate'] = Carbon::parse($request->start_date)->toDateString();
            $data['edate'] = Carbon::parse($request->end_date)->toDateString();

            $sdate = $data['sdate'];
            $edate = $data['edate'];

            // Build base query on ServiceInvoice
            $invoiceQuery = \App\Models\ServiceInvoice::query()
                ->whereBetween('date', [$sdate, $edate]);

            // Add role-based filter
            if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                $invoiceQuery->where('branch_id', auth()->user()->branch_id);
            }

            // Branch handling for Super Admin
            if ($userBranchId == 1 && $filterBranchId) {
                $invoiceQuery->where('branch_id', $filterBranchId);
            }

            // Filter by product if selected
            if (!empty($request->product_id)) {
                $invoiceQuery->whereHas('items', function ($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                });
            }

            // Get filtered data
            $data['invoiceItem'] = $invoiceQuery->orderBy('date', 'desc')->get();
        }

        return view('backend.pages.report.service_report', $data);
    }
    public function productSale(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        // Base product and category lists
        $data['products'] = Product::orderBy('created_at', 'DESC')->get();
        $data['categories'] = Category::orderBy('created_at', 'DESC')->get();

        // If filters applied
        if ($request->all() != null) {

            $data['oneProduct'] = Product::find($request->product_id);
            $data['sdate'] = Carbon::parse($request->start_date)->toDateString();
            $data['edate'] = Carbon::parse($request->end_date)->toDateString();

            $sdate = $data['sdate'];
            $edate = $data['edate'];

            // Build base query
            $invoiceQuery = Invoice::query()
                ->where('status', '!=', 2)
                ->where(function($q) use ($sdate, $edate) {
                    $q->whereBetween('date', [$sdate, $edate])
                      ->orWhereHas('invoiceItems', fn($iq) => $iq->whereBetween('date', [$sdate, $edate]));
                });

            // Add role-based filter to match InvoiceController
            if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                $invoiceQuery->where('branch_id', auth()->user()->branch_id);
            }

            // Branch handling
            if ($userBranchId == 1) {
                if ($filterBranchId) {
                    // Filtered branch (for super admin)
                    $invoiceQuery->where('branch_id', $filterBranchId);

                    $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                    $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                    $data['products'] = Product::whereIn('id', $productIds)->orderBy('created_at', 'DESC')->get();
                    $data['categories'] = Category::whereIn('id', $categoryIds)->orderBy('created_at', 'DESC')->get();
                }
            } else {
                // Normal branch user
                $invoiceQuery->where('branch_id', $userBranchId);

                $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
                $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
                $data['products'] = Product::whereIn('id', $productIds)->orderBy('created_at', 'DESC')->get();
                $data['categories'] = Category::whereIn('id', $categoryIds)->orderBy('created_at', 'DESC')->get();
            }

            // --- SERVICE FILTER SECTION ---
            $invoiceQuery->whereHas('invoiceItems', function ($q) use ($request, $sdate, $edate) {
                $q->whereBetween('date', [$sdate, $edate]);
                // Only include items that are service products
                $q->whereHas('product', function ($p) use ($request) {
                    $p->where('is_service', 0);

                    // Optional: category filter
                    if (!empty($request->category_id)) {
                        $p->where('category_id', $request->category_id);
                    }
                });

                // Optional: product filter
                if (!empty($request->product_id)) {
                    $q->where('product_id', $request->product_id);
                }
            });

            // Get filtered data
            $data['invoiceItem'] = Invoice::filterByFakeSale($invoiceQuery);
        }

        return view('backend.pages.report.product_report', $data);
    }


    public function purchase(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1) ? $filterBranchId : $userBranchId;

        if ($branchId) {
            $productIds = BranchProduct::where('branch_id', $branchId)->pluck('product_id');
            $data['products'] = Product::whereIn('id', $productIds)->orderBy('name', 'asc')->get();
            $data['suppliers'] = Supplier::where('branch_id', $branchId)->orWhereNull('branch_id')->orderBy('name', 'asc')->get();
        } else {
            $data['products'] = Product::orderBy('name', 'asc')->get();
            $data['suppliers'] = Supplier::orderBy('name', 'asc')->get();
        }

        $data['product_id'] = $request->product_id;
        $data['supplier_id'] = $request->supplier_id;
        $data['sdate'] = $request->filled('start_date') ? Carbon::parse($request->start_date)->toDateString() : Carbon::now()->startOfMonth()->toDateString();
        $data['edate'] = $request->filled('end_date') ? Carbon::parse($request->end_date)->toDateString() : Carbon::now()->toDateString();

        $sdate = $data['sdate'];
        $edate = $data['edate'];

        $purchaseItemQuery = PurchaseItem::with(['purchase.supplier', 'product.unit.related_unit'])
            ->when($branchId, function ($q) use ($branchId) {
                return $q->where('branch_id', $branchId)
                         ->whereHas('purchase', function ($pQuery) use ($branchId) {
                             $pQuery->where('branch_id', $branchId);
                         });
            })
            ->whereHas('purchase', function ($query) use ($sdate, $edate) {
                $query->whereBetween('date', [$sdate, $edate]);
            });

        if ($request->filled('product_id')) {
            $purchaseItemQuery->where('product_id', $request->product_id);
        }

        if ($request->filled('supplier_id')) {
            $supplierId = $request->supplier_id;
            $purchaseItemQuery->whereHas('purchase', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            });
        }

        $barcode = $request->barcode ?? $request->search;
        $data['barcode'] = $barcode;
        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $purchaseItemQuery->where(function ($q) use ($barcodeVal) {
                $q->whereHas('purchase', function ($subQ) use ($barcodeVal) {
                    $subQ->where('purchase_no', 'like', "%{$barcodeVal}%");
                })
                ->orWhereHas('product', function ($subQ) use ($barcodeVal) {
                    $subQ->where('barcode', $barcodeVal)
                         ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                         ->orWhere('name', 'like', "%{$barcodeVal}%")
                         ->orWhereHas('product_variations', function($vq) use ($barcodeVal) {
                             $vq->where('barcode', 'like', "%{$barcodeVal}%");
                         });
                });
            });
        }

        $data['purchaseItem'] = $purchaseItemQuery->orderBy('id', 'desc')->get();

        return view('backend.pages.report.purchase', $data);
    }

    public function profitLoss(Request $request)
    {
        $startMonth = $request->filled('start_month') ? $request->start_month : Carbon::now()->startOfYear()->format('Y-m');
        $endMonth = $request->filled('end_month') ? $request->end_month : Carbon::now()->format('Y-m');

        $data['sdate'] = Carbon::parse($startMonth . '-01')->startOfMonth()->toDateString();
        $data['edate'] = Carbon::parse($endMonth . '-01')->endOfMonth()->toDateString();
        $dateRange = CarbonPeriod::create($data['sdate'], $data['edate']);

        $data['groupedDates'] = [];
        foreach ($dateRange as $date) {
            $data['groupedDates'][$date->format('Y-m')][] = $date->toDateString();
        }
        return view('backend.pages.report.profit_loss', $data);
    }

    public function low_stock(Request $request)
    {
        $data['product_id'] = $request->product_id;
        $data['keyword'] = $request->search_keyword;
        $data['category_id'] = $request->category_id;
        $data['brand_id'] = $request->brand_id;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $query = Product::with(['unit.related_unit', 'category', 'brand'])->where('is_service', 0);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $brandIds = BranchBrand::where('branch_id', $filterBranchId)->pluck('brand_id');
                $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $data['produc'] = Product::whereIn('id', $productIds)->where('is_service', 0)->get();
                $data['brands'] = Brand::whereIn('id', $brandIds)->get();
                $data['categories'] = Category::whereIn('id', $categoryIds)->get();
                $query->whereIn('id', $productIds);
            } else {
                $data['produc'] = Product::where('is_service', 0)->get();
                $data['brands'] = Brand::get();
                $data['categories'] = Category::get();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $brandIds = BranchBrand::where('branch_id', $filterBranchId ?: $userBranchId)->pluck('brand_id');
            $categoryIds = BranchCategory::where('branch_id', $filterBranchId ?: $userBranchId)->pluck('category_id');
            $data['produc'] = Product::whereIn('id', $productIds)->where('is_service', 0)->get();
            $data['brands'] = Brand::whereIn('id', $brandIds)->get();
            $data['categories'] = Category::whereIn('id', $categoryIds)->get();
            $query->whereIn('id', $productIds);
        }

        if ($request->product_id != null) {
            $query->where('id', $request->product_id);
        }

        if ($request->category_id != null) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->brand_id != null) {
            $query->where('brand_id', $request->brand_id);
        }

        $keyword = $request->search_keyword ?? $request->barcode;
        if ($keyword != null) {
            $keyword = trim($keyword);
            $resolved = function_exists('resolveProductAndVariationFromBarcode') ? resolveProductAndVariationFromBarcode($keyword) : ['product_id' => null];
            if ($resolved['product_id']) {
                $query->where('id', $resolved['product_id']);
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                      ->orWhere('barcode', 'like', '%' . $keyword . '%')
                      ->orWhereHas('product_variations', function($vq) use ($keyword) {
                          $vq->where('barcode', 'like', "%{$keyword}%");
                      });
                });
            }
        }

        $data['products'] = $query->orderBy('main_qty', 'asc')->get();

        return view('backend.pages.report.low_stock_report', $data);
    }

    public function user_sell(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $data['users'] = User::where('branch_id', $filterBranchId)->orderBy('created_at', 'desc')->get();
            } else {
                $data['users'] = User::orderBy('created_at', 'desc')->get();
            }
        } else {
            $data['users'] = User::where('branch_id', $userBranchId)->orderBy('created_at', 'desc')->get();
        }

        if ($request->all() != NULL) {
            $data['sdate'] = Carbon::createFromDate($request->start_date)->toDateString();
            $data['edate'] = Carbon::createFromDate($request->end_date)->toDateString();
            $sdate = $data['sdate'];
            $edate = $data['edate'];
            $data['oneUser'] = $request->user_id;

            $invoiceItem = Invoice::where('created_by', $request->user_id)->where(function($q) use ($sdate, $edate) {
                $q->whereBetween('date', [$sdate, $edate])
                  ->orWhereHas('invoiceItems', fn($iq) => $iq->whereBetween('date', [$sdate, $edate]));
            });

            $data['invoiceItem'] = Invoice::filterByFakeSale($invoiceItem->get());
        }
        return view('backend.pages.report.user_sell', $data);
    }

    public function usedStock(Request $request)
    {
        $data['product_id'] = $request->product_id;
        $data['keyword'] = $request->search_keyword;
        $data['category_id'] = $request->category_id;
        $data['brand_id'] = $request->brand_id;
        //get all stock with supplier and user
        $data['products'] = UsedProduct::with('unit.related_unit')
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
        $data['totalPrice'] = UsedProduct::all()->sum(function ($product) {
            // dd(product_stock_balance($product));
            return product_stock_balance($product) * $product->purchase_price;
        });

        if ($request->product_id != null) {
            $data['products'] = UsedProduct::with('unit.related_unit')->where('id', $request->product_id)
                ->orderBy('created_at', 'DESC')
                ->paginate(20)
                ->appends([
                    'product_id' => $request->product_id,
                ]);
        }

        if ($request->category_id != null) {
            $data['products'] = UsedProduct::with('unit.related_unit')->where('category_id', $request->category_id)
                ->orderBy('created_at', 'DESC')
                ->paginate(20)
                ->appends([
                    'category_id' => $request->category_id,
                ]);
        }

        if ($request->brand_id != null) {
            $data['products'] = UsedProduct::with('unit.related_unit')->where('brand_id', $request->brand_id)
                ->orderBy('created_at', 'DESC')
                ->paginate(20)
                ->appends([
                    'brand_id' => $request->brand_id,
                ]);
        }

        if ($request->search_keyword != null) {
            $data['products'] = UsedProduct::with('unit.related_unit')->where('name', 'like', '%' . $request->search_keyword . '%')
                ->orWhere('barcode', $request->search_keyword)
                ->orderBy('created_at', 'DESC')
                ->paginate(20)
                ->appends([
                    'search_keyword' => $request->search_keyword,
                ]);
        }
        return view('backend.pages.report.usedStock', $data);
    }

    public function inventoryRegister(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchId = ($userBranchId == 1) ? $filterBranchId : $userBranchId;

        // Default to current date if not provided
        $sdate = $request->start_date ? Carbon::parse($request->start_date)->toDateString() : Carbon::now()->startOfMonth()->toDateString();
        $edate = $request->end_date ? Carbon::parse($request->end_date)->toDateString() : Carbon::now()->toDateString();
        
        $group_by = $request->group_by ?? 'date_wise';
        $productId = $request->product_id;
        $categoryId = $request->category_id;
        $brandId = $request->brand_id;

        // Fetch filter dropdown options
        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $prods = Product::whereIn('id', BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id'));
                $cats = Category::whereIn('id', BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id'));
                $brnds = Brand::whereIn('id', BranchBrand::where('branch_id', $filterBranchId)->pluck('brand_id'));
            } else {
                $prods = Product::query();
                $cats = Category::query();
                $brnds = Brand::query();
            }
        } else {
            $prods = Product::whereIn('id', BranchProduct::where('branch_id', $userBranchId)->pluck('product_id'));
            $cats = Category::whereIn('id', BranchCategory::where('branch_id', $userBranchId)->pluck('category_id'));
            $brnds = Brand::whereIn('id', BranchBrand::where('branch_id', $userBranchId)->pluck('brand_id'));
        }

        $data['allProducts'] = $prods->where('is_service', 0)->orderBy('name', 'asc')->get();
        $data['categories'] = $cats->orderBy('name', 'asc')->get();
        $data['brands'] = $brnds->orderBy('name', 'asc')->get();

        // Build product query for report rows
        $productQuery = Product::with('unit')->where('is_service', 0);
        if ($productId) {
            $productQuery->where('id', $productId);
        }
        if ($categoryId) {
            $productQuery->where('category_id', $categoryId);
        }
        if ($brandId) {
            $productQuery->where('brand_id', $brandId);
        }
        if ($branchId) {
            $productQuery->whereIn('id', BranchProduct::where('branch_id', $branchId)->pluck('product_id'));
        }
        $reportProducts = $productQuery->get();

        $rows = [];

        if ($group_by === 'date_wise') {
            // Generate all dates in range
            $period = new \DatePeriod(
                new \DateTime($sdate),
                new \DateInterval('P1D'),
                (new \DateTime($edate))->modify('+1 day')
            );

            foreach ($period as $dateObj) {
                $currentDate = $dateObj->format('Y-m-d');

                foreach ($reportProducts as $product) {
                    $hasTransactions = PurchaseItem::where('product_id', $product->id)->where('date', $currentDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->exists() ||
                        InvoiceItem::where('product_id', $product->id)->where('date', $currentDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->exists() ||
                        TransferItem::where('product_id', $product->id)->where('date', $currentDate)->when($branchId, fn($q) => $q->where(function($qi) use ($branchId) { $qi->where('from_branch_id', $branchId)->orWhere('to_branch_id', $branchId); }))->exists() ||
                        DamageItem::where('product_id', $product->id)->where('date', $currentDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->exists() ||
                        ReturnItem::where('product_id', $product->id)->where('date', $currentDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->exists() ||
                        \App\Models\ReturnPurchaseItem::where('product_id', $product->id)->where('date', $currentDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->exists() ||
                        \App\Models\AdjustStockItem::where('product_id', $product->id)->where('date', $currentDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->exists();

                    if (!$hasTransactions && $productId == null) {
                        continue;
                    }

                    $row = $this->calculateStockRow($product, $branchId, $currentDate, $currentDate);
                    $row['date'] = $currentDate;
                    $row['product_name'] = $product->name;
                    $rows[] = $row;
                }
            }
        } else {
            // Product Wise grouping
            foreach ($reportProducts as $product) {
                $row = $this->calculateStockRow($product, $branchId, $sdate, $edate);
                $row['product_name'] = $product->name;
                $rows[] = $row;
            }
        }

        $data['rows'] = $rows;
        $data['sdate'] = $sdate;
        $data['edate'] = $edate;
        $data['group_by'] = $group_by;
        $data['product_id'] = $productId;
        $data['category_id'] = $categoryId;
        $data['brand_id'] = $brandId;

        return view('backend.pages.report.inventory_register', $data);
    }

    private function calculateStockRow($product, $branchId, $startDate, $endDate)
    {
        // 1. Opening Balance (prior to $startDate)
        $purchasedBefore = PurchaseItem::where('product_id', $product->id)->where('date', '<', $startDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $transfersInBefore = TransferItem::where('product_id', $product->id)->where('status', 1)->where('date', '<', $startDate)->when($branchId, fn($q) => $q->where('to_branch_id', $branchId))->sum('main_qty');
        $returnsBefore = ReturnItem::where('product_id', $product->id)->where('date', '<', $startDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $adjustInBefore = \App\Models\AdjustStockItem::where('product_id', $product->id)->where('stock_status', 1)->where('date', '<', $startDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');

        $salesBefore = InvoiceItem::where('product_id', $product->id)->where('is_return', 0)->where('date', '<', $startDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $transfersOutBefore = TransferItem::where('product_id', $product->id)->where('date', '<', $startDate)->when($branchId, fn($q) => $q->where('from_branch_id', $branchId))->sum('main_qty');
        $purReturnsBefore = \App\Models\ReturnPurchaseItem::where('product_id', $product->id)->where('date', '<', $startDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $damagesBefore = DamageItem::where('product_id', $product->id)->where('date', '<', $startDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $adjustOutBefore = \App\Models\AdjustStockItem::where('product_id', $product->id)->where('stock_status', 0)->where('date', '<', $startDate)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');

        $openingQty = ($purchasedBefore + $transfersInBefore + $returnsBefore + $adjustInBefore) - ($salesBefore + $transfersOutBefore + $purReturnsBefore + $damagesBefore + $adjustOutBefore);
        $openingValue = $openingQty * $product->purchase_price;

        // 2. Period Transactions (between $startDate and $endDate)
        $purchaseQty = PurchaseItem::where('product_id', $product->id)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $purchaseVal = PurchaseItem::where('product_id', $product->id)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('subtotal');
        
        $purchaseReturnQty = \App\Models\ReturnPurchaseItem::where('product_id', $product->id)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $purchaseReturnVal = \App\Models\ReturnPurchaseItem::where('product_id', $product->id)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('subtotal');

        $damageQty = DamageItem::where('product_id', $product->id)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $damageVal = DamageItem::where('product_id', $product->id)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('subtotal');

        $transferOutQty = TransferItem::where('product_id', $product->id)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('from_branch_id', $branchId))->sum('main_qty');
        $transferInQty = TransferItem::where('product_id', $product->id)->where('status', 1)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('to_branch_id', $branchId))->sum('main_qty');

        $salesOutQty = InvoiceItem::where('product_id', $product->id)->where('is_return', 0)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $salesOutVal = InvoiceItem::where('product_id', $product->id)->where('is_return', 0)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('subtotal');

        $salesReturnQty = ReturnItem::where('product_id', $product->id)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $salesReturnVal = ReturnItem::where('product_id', $product->id)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('subtotal');

        $adjustInQty = \App\Models\AdjustStockItem::where('product_id', $product->id)->where('stock_status', 1)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');
        $adjustOutQty = \App\Models\AdjustStockItem::where('product_id', $product->id)->where('stock_status', 0)->whereBetween('date', [$startDate, $endDate])->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('main_qty');

        // 3. Available Stock
        $availableQty = $openingQty + $purchaseQty + $adjustInQty - $damageQty - $transferOutQty - $adjustOutQty;
        $availableValue = $availableQty * $product->purchase_price;

        // 4. Ending Balance
        $endingQty = $openingQty + $purchaseQty + $transferInQty + $adjustInQty - $transferOutQty - $salesOutQty + $salesReturnQty - $purchaseReturnQty - $damageQty - $adjustOutQty;
        $endingValue = $endingQty * $product->purchase_price;

        return [
            'product' => $product,
            'opening_qty' => $openingQty,
            'opening_value' => $openingValue,
            'purchase_qty' => $purchaseQty,
            'purchase_val' => $purchaseVal,
            'purchase_return_qty' => $purchaseReturnQty,
            'purchase_return_val' => $purchaseReturnVal,
            'damage_qty' => $damageQty,
            'damage_val' => $damageVal,
            'available_qty' => $availableQty,
            'available_value' => $availableValue,
            'transfer_out_qty' => $transferOutQty,
            'transfer_in_qty' => $transferInQty,
            'sales_out_qty' => $salesOutQty,
            'sales_out_val' => $salesOutVal,
            'sales_return_qty' => $salesReturnQty,
            'sales_return_val' => $salesReturnVal,
            'adjust_in_qty' => $adjustInQty,
            'adjust_out_qty' => $adjustOutQty,
            'ending_qty' => $endingQty,
            'ending_value' => $endingValue,
        ];
    }

    public function vat(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : ($userBranchId == 1 ? null : $userBranchId);

        $sdate = $request->filled('start_date') ? Carbon::parse($request->start_date)->toDateString() : Carbon::now()->startOfMonth()->toDateString();
        $edate = $request->filled('end_date') ? Carbon::parse($request->end_date)->toDateString() : Carbon::now()->toDateString();

        $data['sdate'] = $sdate;
        $data['edate'] = $edate;
        $data['type'] = $request->type ?? 'all';
        $data['customer_id'] = $request->customer_id;
        $data['supplier_id'] = $request->supplier_id;

        if ($branchId) {
            $data['customers'] = Customer::where('branch_id', $branchId)->orderBy('name')->get();
            $data['suppliers'] = Supplier::where('branch_id', $branchId)->orderBy('name')->get();
        } else {
            $data['customers'] = Customer::orderBy('name')->get();
            $data['suppliers'] = Supplier::orderBy('name')->get();
        }

        $records = collect();
        $totalSaleVat = 0;
        $totalPurchaseVat = 0;
        $totalTaxableSales = 0;
        $totalTaxablePurchases = 0;

        // 1. Fetch Sales Invoices with VAT
        if ($data['type'] === 'all' || $data['type'] === 'sale') {
            $saleQuery = Invoice::with('customer')
                ->whereBetween('date', [$sdate, $edate])
                ->where(function($q) {
                    $q->where('vat_amount', '>', 0)
                      ->orWhere('vat', '>', 0);
                });

            if ($branchId) {
                $saleQuery->where('branch_id', $branchId);
            }
            if ($request->filled('customer_id')) {
                $saleQuery->where('customer_id', $request->customer_id);
            }

            $invoices = $saleQuery->orderBy('date', 'desc')->get();
            foreach ($invoices as $inv) {
                $vatAmt = (float) ($inv->vat_amount > 0 ? $inv->vat_amount : 0);
                $grossAmt = (float) ($inv->estimated_amount > 0 ? $inv->estimated_amount : ($inv->total_amount - $vatAmt + ($inv->discount_amount ?? 0)));
                $totalSaleVat += $vatAmt;
                $totalTaxableSales += $grossAmt;

                $records->push([
                    'type' => 'Sale',
                    'date' => $inv->date,
                    'reference' => $inv->invoice_no,
                    'url' => route('invoice.show', $inv->id),
                    'party_name' => $inv->customer ? $inv->customer->name : 'Walking Customer',
                    'party_phone' => $inv->customer ? $inv->customer->phone : '',
                    'party_type' => 'Customer',
                    'gross_amount' => $grossAmt,
                    'vat_rate' => $inv->vat,
                    'vat_amount' => $vatAmt,
                    'total_amount' => (float) $inv->total_amount,
                    'raw_date' => $inv->date,
                    'id' => $inv->id,
                ]);
            }
        }

        // 2. Fetch Purchases with VAT
        if ($data['type'] === 'all' || $data['type'] === 'purchase') {
            $purchaseQuery = Purchase::with('supplier')
                ->whereBetween('date', [$sdate, $edate])
                ->where(function($q) {
                    $q->where('vat_amount', '>', 0)
                      ->orWhere('vat', '>', 0);
                });

            if ($branchId) {
                $purchaseQuery->where('branch_id', $branchId);
            }
            if ($request->filled('supplier_id')) {
                $purchaseQuery->where('supplier_id', $request->supplier_id);
            }

            $purchases = $purchaseQuery->orderBy('date', 'desc')->get();
            foreach ($purchases as $pur) {
                $vatAmt = (float) ($pur->vat_amount > 0 ? $pur->vat_amount : 0);
                $grossAmt = (float) ($pur->estimated_amount > 0 ? $pur->estimated_amount : ($pur->total_amount - $vatAmt + ($pur->discount_amount ?? 0)));
                $totalPurchaseVat += $vatAmt;
                $totalTaxablePurchases += $grossAmt;

                $records->push([
                    'type' => 'Purchase',
                    'date' => $pur->date,
                    'reference' => $pur->purchase_no,
                    'url' => route('purchase.show', $pur->id),
                    'party_name' => $pur->supplier ? $pur->supplier->name : 'N/A',
                    'party_phone' => $pur->supplier ? $pur->supplier->phone : '',
                    'party_type' => 'Supplier',
                    'gross_amount' => $grossAmt,
                    'vat_rate' => $pur->vat,
                    'vat_amount' => $vatAmt,
                    'total_amount' => (float) $pur->total_amount,
                    'raw_date' => $pur->date,
                    'id' => $pur->id,
                ]);
            }
        }

        $data['records'] = $records->sortByDesc('raw_date')->values();
        $data['total_sale_vat'] = $totalSaleVat;
        $data['total_purchase_vat'] = $totalPurchaseVat;
        $data['net_vat_payable'] = $totalSaleVat - $totalPurchaseVat;
        $data['total_taxable_sales'] = $totalTaxableSales;
        $data['total_taxable_purchases'] = $totalTaxablePurchases;
        $data['total_transactions'] = $records->count();

        return view('backend.pages.report.vat', $data);
    }

    public function discount(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : ($userBranchId == 1 ? null : $userBranchId);

        $sdate = $request->filled('start_date') ? Carbon::parse($request->start_date)->toDateString() : Carbon::now()->startOfMonth()->toDateString();
        $edate = $request->filled('end_date') ? Carbon::parse($request->end_date)->toDateString() : Carbon::now()->toDateString();

        $data['sdate'] = $sdate;
        $data['edate'] = $edate;
        $data['type'] = $request->type ?? 'all';
        $data['customer_id'] = $request->customer_id;
        $data['supplier_id'] = $request->supplier_id;

        if ($branchId) {
            $data['customers'] = Customer::where('branch_id', $branchId)->orderBy('name')->get();
            $data['suppliers'] = Supplier::where('branch_id', $branchId)->orderBy('name')->get();
        } else {
            $data['customers'] = Customer::orderBy('name')->get();
            $data['suppliers'] = Supplier::orderBy('name')->get();
        }

        $records = collect();
        $totalSaleDiscount = 0;
        $totalPurchaseDiscount = 0;
        $totalSalesGross = 0;
        $totalPurchaseGross = 0;

        // 1. Fetch Sales Invoices with Discount
        if ($data['type'] === 'all' || $data['type'] === 'sale') {
            $saleQuery = Invoice::with(['customer', 'invoiceItems'])
                ->whereBetween('date', [$sdate, $edate])
                ->where(function($q) {
                    $q->where('discount_amount', '>', 0)
                      ->orWhere('discount', '>', 0)
                      ->orWhereHas('invoiceItems', function($itemQuery) {
                          $itemQuery->where('product_discount', '>', 0);
                      });
                });

            if ($branchId) {
                $saleQuery->where('branch_id', $branchId);
            }
            if ($request->filled('customer_id')) {
                $saleQuery->where('customer_id', $request->customer_id);
            }

            $invoices = $saleQuery->orderBy('date', 'desc')->get();
            foreach ($invoices as $inv) {
                $itemDiscount = $inv->invoiceItems->sum('product_discount');
                $invoiceDiscount = (float) ($inv->discount_amount > 0 ? $inv->discount_amount : 0);
                $totalDiscount = $invoiceDiscount + $itemDiscount;
                $grossAmt = (float) ($inv->estimated_amount > 0 ? $inv->estimated_amount : ($inv->total_amount + $totalDiscount - ($inv->vat_amount ?? 0)));

                $totalSaleDiscount += $totalDiscount;
                $totalSalesGross += $grossAmt;

                $records->push([
                    'type' => 'Sale',
                    'date' => $inv->date,
                    'reference' => $inv->invoice_no,
                    'url' => route('invoice.show', $inv->id),
                    'party_name' => $inv->customer ? $inv->customer->name : 'Walking Customer',
                    'party_phone' => $inv->customer ? $inv->customer->phone : '',
                    'party_type' => 'Customer',
                    'gross_amount' => $grossAmt,
                    'discount_rate' => $inv->discount,
                    'discount_amount' => $totalDiscount,
                    'total_amount' => (float) $inv->total_amount,
                    'raw_date' => $inv->date,
                    'id' => $inv->id,
                ]);
            }
        }

        // 2. Fetch Purchases with Discount
        if ($data['type'] === 'all' || $data['type'] === 'purchase') {
            $purchaseQuery = Purchase::with('supplier')
                ->whereBetween('date', [$sdate, $edate])
                ->where(function($q) {
                    $q->where('discount_amount', '>', 0)
                      ->orWhere('discount', '>', 0);
                });

            if ($branchId) {
                $purchaseQuery->where('branch_id', $branchId);
            }
            if ($request->filled('supplier_id')) {
                $purchaseQuery->where('supplier_id', $request->supplier_id);
            }

            $purchases = $purchaseQuery->orderBy('date', 'desc')->get();
            foreach ($purchases as $pur) {
                $discountAmt = (float) ($pur->discount_amount > 0 ? $pur->discount_amount : 0);
                $grossAmt = (float) ($pur->estimated_amount > 0 ? $pur->estimated_amount : ($pur->total_amount + $discountAmt - ($pur->vat_amount ?? 0)));

                $totalPurchaseDiscount += $discountAmt;
                $totalPurchaseGross += $grossAmt;

                $records->push([
                    'type' => 'Purchase',
                    'date' => $pur->date,
                    'reference' => $pur->purchase_no,
                    'url' => route('purchase.show', $pur->id),
                    'party_name' => $pur->supplier ? $pur->supplier->name : 'N/A',
                    'party_phone' => $pur->supplier ? $pur->supplier->phone : '',
                    'party_type' => 'Supplier',
                    'gross_amount' => $grossAmt,
                    'discount_rate' => $pur->discount,
                    'discount_amount' => $discountAmt,
                    'total_amount' => (float) $pur->total_amount,
                    'raw_date' => $pur->date,
                    'id' => $pur->id,
                ]);
            }
        }

        $data['records'] = $records->sortByDesc('raw_date')->values();
        $data['total_sale_discount'] = $totalSaleDiscount;
        $data['total_purchase_discount'] = $totalPurchaseDiscount;
        $data['net_discount'] = $totalSaleDiscount - $totalPurchaseDiscount;
        $data['total_sales_gross'] = $totalSalesGross;
        $data['total_purchase_gross'] = $totalPurchaseGross;
        $data['total_transactions'] = $records->count();

        return view('backend.pages.report.discount', $data);
    }

    public function topSelling(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $activeBranchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : ($userBranchId == 1 ? null : $userBranchId);

        $sdate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : Carbon::now()->startOfMonth()->format('Y-m-d');
        $edate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $limit = $request->filled('limit') ? (int)$request->limit : 20;
        $sortBy = $request->sort_by ?? 'qty'; // 'qty', 'amount', 'profit'
        $categoryId = $request->category_id;
        $subCategoryId = $request->sub_category_id;
        $brandId = $request->brand_id;

        // Query InvoiceItem within date range and branch
        $invQuery = InvoiceItem::query()
            ->whereBetween('date', [$sdate, $edate])
            ->whereHas('product', function($q) use ($categoryId, $subCategoryId, $brandId) {
                $q->where('is_service', 0);
                if ($categoryId) $q->where('category_id', $categoryId);
                if ($subCategoryId) $q->where('sub_category_id', $subCategoryId);
                if ($brandId) $q->where('brand_id', $brandId);
            });

        if ($activeBranchId) {
            $invQuery->where('branch_id', $activeBranchId);
        }

        $invoiceItems = $invQuery->with(['product.unit.related_unit', 'product.category', 'product.brand', 'product.subCategory'])->get();

        // Query ReturnItem within date range and branch
        $rtnQuery = ReturnItem::query()
            ->whereBetween('date', [$sdate, $edate])
            ->whereHas('product', function($q) use ($categoryId, $subCategoryId, $brandId) {
                $q->where('is_service', 0);
                if ($categoryId) $q->where('category_id', $categoryId);
                if ($subCategoryId) $q->where('sub_category_id', $subCategoryId);
                if ($brandId) $q->where('brand_id', $brandId);
            });

        if ($activeBranchId) {
            $rtnQuery->where('branch_id', $activeBranchId);
        }

        $returnItems = $rtnQuery->get();

        // Aggregate by Product
        $productStats = [];

        foreach ($invoiceItems as $item) {
            if (!$item->product) continue;
            $pId = $item->product_id;
            if (!isset($productStats[$pId])) {
                $productStats[$pId] = [
                    'product'       => $item->product,
                    'sold_qty'      => 0,
                    'return_qty'    => 0,
                    'sold_amount'   => 0,
                    'return_amount' => 0,
                    'pur_cost'      => 0,
                ];
            }
            $productStats[$pId]['sold_qty']    += (float)$item->main_qty;
            $productStats[$pId]['sold_amount'] += (float)$item->inv_subtotal;
            $productStats[$pId]['pur_cost']    += (float)$item->pur_subtotal;
        }

        foreach ($returnItems as $rtn) {
            $pId = $rtn->product_id;
            if (isset($productStats[$pId])) {
                $productStats[$pId]['return_qty']    += (float)$rtn->main_qty;
                $productStats[$pId]['return_amount'] += (float)$rtn->subtotal;
                $productStats[$pId]['pur_cost']      = max(0, $productStats[$pId]['pur_cost'] - (float)$rtn->pur_subtotal);
            }
        }

        // Calculate Net and Profit
        $reportList = collect();
        $totalSoldQty = 0;
        $totalSalesAmount = 0;
        $totalPurchaseCost = 0;
        $totalProfit = 0;

        foreach ($productStats as $pId => $stats) {
            $netQty = max(0, $stats['sold_qty'] - $stats['return_qty']);
            $netAmount = max(0, $stats['sold_amount'] - $stats['return_amount']);
            $netProfit = max(0, $netAmount - $stats['pur_cost']);
            $margin = $netAmount > 0 ? ($netProfit / $netAmount) * 100 : 0;

            $totalSoldQty += $netQty;
            $totalSalesAmount += $netAmount;
            $totalPurchaseCost += $stats['pur_cost'];
            $totalProfit += $netProfit;

            $reportList->push((object)[
                'product'       => $stats['product'],
                'sold_qty'      => $stats['sold_qty'],
                'return_qty'    => $stats['return_qty'],
                'net_qty'       => $netQty,
                'sold_amount'   => $stats['sold_amount'],
                'return_amount' => $stats['return_amount'],
                'net_amount'    => $netAmount,
                'pur_cost'      => $stats['pur_cost'],
                'profit'        => $netProfit,
                'margin'        => $margin,
            ]);
        }

        // Sort based on sort_by
        if ($sortBy === 'amount') {
            $sortedList = $reportList->sortByDesc('net_amount')->values();
        } elseif ($sortBy === 'profit') {
            $sortedList = $reportList->sortByDesc('profit')->values();
        } else {
            $sortedList = $reportList->sortByDesc('net_qty')->values();
        }

        if ($limit > 0) {
            $paginatedList = $sortedList->take($limit);
        } else {
            $paginatedList = $sortedList;
        }

        // Filter dropdowns
        if ($userBranchId == 1 && !$filterBranchId) {
            $categories = Category::orderBy('name', 'asc')->get();
            $brands = Brand::orderBy('name', 'asc')->get();
        } else {
            $categoryIds = BranchCategory::where('branch_id', $activeBranchId)->pluck('category_id');
            $brandIds = BranchBrand::where('branch_id', $activeBranchId)->pluck('brand_id');
            $categories = Category::whereIn('id', $categoryIds)->orderBy('name', 'asc')->get();
            $brands = Brand::whereIn('id', $brandIds)->orderBy('name', 'asc')->get();
        }
        $subCategories = $categoryId ? SubCategory::where('category_id', $categoryId)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();

        return view('backend.pages.report.top_selling', compact(
            'paginatedList',
            'sdate',
            'edate',
            'limit',
            'sortBy',
            'categories',
            'brands',
            'subCategories',
            'categoryId',
            'subCategoryId',
            'brandId',
            'totalSoldQty',
            'totalSalesAmount',
            'totalPurchaseCost',
            'totalProfit'
        ));
    }
}