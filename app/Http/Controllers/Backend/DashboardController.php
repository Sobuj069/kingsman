<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BranchProduct;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\ReturnItem;
use App\Models\ReturnPurchase;
use App\Models\BankTransaction;
use App\Models\Customer;
use App\Models\ReturnTbl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $data['total_pur_due'] = Purchase::where('branch_id', $filterBranchId)->sum('total_due');
                $total_pur = Purchase::where('branch_id', $filterBranchId)->sum('total_amount');
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $data['total_product'] = Product::whereIn('id', $productIds)->count();
                $data['total_sale_due'] = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId), 'total_due');
                $total_inv = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId), 'total_amount');
                $data['total_customer'] = Customer::where('branch_id', $filterBranchId)->count();
                $data['total_return_amount'] = ReturnTbl::where('branch_id', $filterBranchId)->sum('total_return');
                $data['inv_due'] = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId)->where('date', date('Y-m-d')), 'total_due');
                $saleCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $filterBranchId), 'inv_subtotal');
                $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $filterBranchId), 'pur_subtotal');
                $returns = ReturnItem::where('branch_id', $filterBranchId)->get();
                $discount = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId), 'discount_amount');
                $returnDis = ReturnTbl::where('branch_id', $filterBranchId)->sum('discount_amount');
                $returnAmu = ReturnTbl::where('branch_id', $filterBranchId)->sum('total_return');
                $data['total_invoice_count'] = Invoice::where('branch_id', $filterBranchId)->count();
            } else {
                $data['total_pur_due'] = Purchase::sum('total_due');
                $total_pur = Purchase::sum('total_amount');
                $data['total_pur_amount'] = $total_pur;
                $data['total_product'] = Product::count();
                $data['total_sale_due'] = Invoice::getFakeSum(Invoice::query(), 'total_due');
                $total_inv = Invoice::getFakeSum(Invoice::query(), 'total_amount');
                $data['total_customer'] = Customer::count();
                $data['total_return_amount'] = ReturnTbl::sum('total_return');
                $data['inv_due'] = Invoice::getFakeSum(Invoice::where('date', date('Y-m-d')), 'total_due');
                $saleCost = InvoiceItem::getFakeSum(InvoiceItem::query(), 'inv_subtotal');
                $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::query(), 'pur_subtotal');
                $returns = ReturnItem::get();
                $returnAmu = ReturnTbl::sum('total_return');
                $discount = Invoice::getFakeSum(Invoice::query(), 'discount_amount');
                $returnDis = ReturnTbl::sum('discount_amount');
                $data['total_invoice_count'] = Invoice::count();
            }
        } else {
            $data['total_pur_due'] = Purchase::where('branch_id', $userBranchId)->sum('total_due');
            $total_pur = Purchase::where('branch_id', $userBranchId)->sum('total_amount');
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $data['total_product'] = Product::whereIn('id', $productIds)->count();
            $data['total_sale_due'] = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId), 'total_due');
            $total_inv = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId), 'total_amount');
            $data['total_customer'] = Customer::where('branch_id', $userBranchId)->count();
            $data['total_return_amount'] = ReturnTbl::where('branch_id', $userBranchId)->sum('total_return');
            $data['inv_due'] = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId)->where('date', date('Y-m-d')), 'total_due');
            $saleCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $userBranchId), 'inv_subtotal');
            $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $userBranchId), 'pur_subtotal');
            $returns = ReturnItem::where('branch_id', $userBranchId)->get();
            $discount = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId), 'discount_amount');
            $returnDis = ReturnTbl::where('branch_id', $userBranchId)->sum('discount_amount');
            $returnAmu = ReturnTbl::where('branch_id', $userBranchId)->sum('total_return');
            $data['total_invoice_count'] = Invoice::where('branch_id', $userBranchId)->count();
        }

        $data['total_pur_amount'] = $total_pur;
        $data['total_sale'] = $total_inv;
        $returnSale = ($userBranchId == 1 && $filterBranchId) 
            ? ReturnItem::where('branch_id', $filterBranchId)->sum('subtotal')
            : ($userBranchId == 1 ? ReturnItem::sum('subtotal') : ReturnItem::where('branch_id', $userBranchId)->sum('subtotal'));

        $returnPur = ($userBranchId == 1 && $filterBranchId)
            ? ReturnItem::where('branch_id', $filterBranchId)->sum('pur_subtotal')
            : ($userBranchId == 1 ? ReturnItem::sum('pur_subtotal') : ReturnItem::where('branch_id', $userBranchId)->sum('pur_subtotal'));

        $dis = $discount - $returnDis;
        $netSale = $saleCost - $returnSale;
        $netPur = $purchaseCost - $returnPur;
        $totalProfit = $netSale - $netPur;
        $profit = $totalProfit - $dis;
        $sale_amu = $total_inv - $profit;
        $branchIdForStock = ($userBranchId == 1) ? $filterBranchId : $userBranchId;
        if ($branchIdForStock) {
            $productIds = BranchProduct::where('branch_id', $branchIdForStock)->pluck('product_id');
            $stockProducts = Product::whereIn('id', $productIds)->where('is_service', 0)->with('unit')->get();
        } else {
            $stockProducts = Product::where('is_service', 0)->with('unit')->get();
        }

        $avail_stock = 0;
        $avail_stock_sale = 0;
        foreach ($stockProducts as $product) {
            $fakeStockQty = product_fake_stock_val($product, $branchIdForStock);
            $factor = ($product->unit && $product->unit->related_value) ? (float)$product->unit->related_value : 1;
            if ($factor <= 0) $factor = 1;

            $unitCost = product_purchase_unit_cost($product, $branchIdForStock);
            $sellingPriceUnit = ((float) $product->selling_price) / $factor;

            $avail_stock += $fakeStockQty * $unitCost;
            $avail_stock_sale += $fakeStockQty * $sellingPriceUnit;
        }

        // When no sales have occurred yet, sync stock purchase valuation to total purchase amount
        if (($total_inv ?? 0) <= 0 && ($total_pur ?? 0) > 0) {
            $avail_stock = $total_pur;
        }

        $data['totalPrice'] = $avail_stock;
        $data['available_stock_sale_val'] = $avail_stock_sale;
        $items = $this->getDashboardData('Today');
        $branch_id = session('branch_id');

        return view('backend.pages.dashboard.index', $data, ['data' => $items, 'filter' => 'Today']);
    }

    public function filterData(Request $request)
    {
        $filter = $request->input('filter');
        $data = $this->getDashboardData($filter);

        return response()->json($data);
    }

    // ReturnItem মডেলে
public static function getRealSum($query, $column)
{
    $items = $query->get();
    $realItems = $items->filter(function($item) {
        return $item->invoice && !$item->invoice->is_fake; // যদি is_fake ফিল্ড থাকে
        // অথবা ইনভয়েস মডেলের filterRealInvoices ব্যবহার করতে চাইলে:
        // return Invoice::filterRealInvoices(collect([$item->invoice]))->isNotEmpty();
    });
    return $realItems->sum($column);
}

    private function getDashboardData($filter)
    {
        switch ($filter) {
            case 'Today':
            case 'today':
                $sdate = Carbon::today()->toDateString();
                $edate = Carbon::today()->toDateString();
                break;
            case 'Yesterday':
            case 'yesterday':
                $sdate = Carbon::yesterday()->toDateString();
                $edate = Carbon::yesterday()->toDateString();
                break;
            case 'This-week':
            case 'This Week':
            case 'this-week':
                $sdate = Carbon::now()->startOfWeek()->toDateString();
                $edate = Carbon::now()->endOfWeek()->toDateString();
                break;
            case 'This-month':
            case 'This Month':
            case 'this-month':
                $sdate = Carbon::now()->startOfMonth()->toDateString();
                $edate = Carbon::now()->endOfMonth()->toDateString();
                break;
            case 'This-year':
            case 'This Year':
            case 'this-year':
                $sdate = Carbon::now()->startOfYear()->toDateString();
                $edate = Carbon::now()->endOfYear()->toDateString();
                break;
            default:
                $sdate = Carbon::today()->toDateString();
                $edate = Carbon::today()->toDateString();
                break;
        }

        $dateRange = [$sdate, $edate];
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $total_sale_today = 0;
        $totalpurchase = 0;
        $totalExpense = 0;

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $grossSale = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange), 'inv_subtotal');
                $returnSale = ReturnItem::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange)->sum('subtotal');
                $discount = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange), 'discount_amount');
                $total_sale_today = max(0, $grossSale - $returnSale - $discount);
                $totalpurchase = Purchase::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange)->sum('total_amount');
                $totalExpense = Expense::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange)->sum('amount');
            } else {
                $grossSale = InvoiceItem::getFakeSum(InvoiceItem::whereBetween('date', $dateRange), 'inv_subtotal');
                $returnSale = ReturnItem::whereBetween('date', $dateRange)->sum('subtotal');
                $discount = Invoice::getFakeSum(Invoice::query()->whereBetween('date', $dateRange), 'discount_amount');
                $total_sale_today = max(0, $grossSale - $returnSale - $discount);
                $totalpurchase = Purchase::whereBetween('date', $dateRange)->sum('total_amount');
                $totalExpense = Expense::whereBetween('date', $dateRange)->sum('amount');
            }
        } else {
            $grossSale = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $userBranchId)->whereBetween('date', $dateRange), 'inv_subtotal');
            $returnSale = ReturnItem::where('branch_id', $userBranchId)->whereBetween('date', $dateRange)->sum('subtotal');
            $discount = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId)->whereBetween('date', $dateRange), 'discount_amount');
            $total_sale_today = max(0, $grossSale - $returnSale - $discount);
            $totalpurchase = Purchase::where('branch_id', $userBranchId)->whereBetween('date', $dateRange)->sum('total_amount');
            $totalExpense = Expense::where('branch_id', $userBranchId)->whereBetween('date', $dateRange)->sum('amount');
        }

        return [
            'sale'       => $total_sale_today,
            'purchase'   => $totalpurchase,
            'expense'    => $totalExpense,
            'profit'     => $this->calculateProfit($dateRange),
        ];
    }

    private function calculateProfit($dateRange)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $saleCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange), 'inv_subtotal');
                $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange), 'pur_subtotal');
                $returnSale = ReturnItem::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange)->sum('subtotal');
                $returnPur = ReturnItem::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange)->sum('pur_subtotal');
                $discount = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange), 'discount_amount');
                $returnDis = ReturnTbl::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange)->sum('discount_amount');
            } else {
                $saleCost = InvoiceItem::getFakeSum(InvoiceItem::whereBetween('date', $dateRange), 'inv_subtotal');
                $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::whereBetween('date', $dateRange), 'pur_subtotal');
                $returnSale = ReturnItem::whereBetween('date', $dateRange)->sum('subtotal');
                $returnPur = ReturnItem::whereBetween('date', $dateRange)->sum('pur_subtotal');
                $discount = Invoice::getFakeSum(Invoice::query()->whereBetween('date', $dateRange), 'discount_amount');
                $returnDis = ReturnTbl::whereBetween('date', $dateRange)->sum('discount_amount');
            }
        } else {
            $saleCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $userBranchId)->whereBetween('date', $dateRange), 'inv_subtotal');
            $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $userBranchId)->whereBetween('date', $dateRange), 'pur_subtotal');
            $returnSale = ReturnItem::where('branch_id', $userBranchId)->whereBetween('date', $dateRange)->sum('subtotal');
            $returnPur = ReturnItem::where('branch_id', $userBranchId)->whereBetween('date', $dateRange)->sum('pur_subtotal');
            $discount = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId)->whereBetween('date', $dateRange), 'discount_amount');
            $returnDis = ReturnTbl::where('branch_id', $userBranchId)->whereBetween('date', $dateRange)->sum('discount_amount');
        }

        $netSale = $saleCost - $returnSale;
        $netPur = $purchaseCost - $returnPur;
        $dis = $discount - $returnDis;
        $totalProfit = $netSale - $netPur;
        $profit = $totalProfit - $dis;
        return $profit;
    }



    public function downloadPDF(Request $request)
    {
        $note = $request->input('note');

        $pdf = Pdf::loadView('pdf.note', compact('note'));
        return $pdf->download('note.pdf');
    }

    /**
     * Product sales and profit summary data for date filter
     */
    public function productSalesSummary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filter = $request->input('filter');

        if ($filter) {
            switch ($filter) {
                case 'Today':
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
                case 'Yesterday':
                    $sdate = Carbon::yesterday()->toDateString();
                    $edate = Carbon::yesterday()->toDateString();
                    break;
                case 'This-week':
                    $sdate = Carbon::now()->startOfWeek()->toDateString();
                    $edate = Carbon::now()->endOfWeek()->toDateString();
                    break;
                case 'This-month':
                    $sdate = Carbon::now()->startOfMonth()->toDateString();
                    $edate = Carbon::now()->endOfMonth()->toDateString();
                    break;
                case 'This-year':
                    $sdate = Carbon::now()->startOfYear()->toDateString();
                    $edate = Carbon::now()->endOfYear()->toDateString();
                    break;
                default:
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
            }
        } else {
            $sdate = $startDate ? Carbon::parse($startDate)->toDateString() : Carbon::today()->toDateString();
            $edate = $endDate ? Carbon::parse($endDate)->toDateString() : Carbon::today()->toDateString();
        }

        $authUser = auth()->user();
        $isSalesman = $authUser->role && in_array(strtolower(trim($authUser->role->slug)), ['salesman', 'sales-person', 'sales_man', 'staff']);

        $userBranchId = $authUser->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : ($userBranchId == 1 ? null : $userBranchId);

        $itemsQuery = InvoiceItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        $returnsQuery = ReturnItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        $invDiscountQuery = Invoice::query()->whereBetween('date', [$sdate, $edate]);

        if ($isSalesman) {
            $itemsQuery->whereHas('invoice', fn($q) => $q->where('created_by', $authUser->id));
            $returnsQuery->whereHas('invoice', fn($q) => $q->where('created_by', $authUser->id));
            $invDiscountQuery->where('created_by', $authUser->id);
        } else {
            if ($branchId) {
                $itemsQuery->where('branch_id', $branchId);
                $returnsQuery->where('branch_id', $branchId);
                $invDiscountQuery->where('branch_id', $branchId);
            }
        }

        $allInvoiceItems = InvoiceItem::filterByFakeSale($itemsQuery);
        $allReturnItems = ReturnItem::filterByFakeSale($returnsQuery);
        $grandTotalDiscount = (float) Invoice::getFakeSum($invDiscountQuery, 'discount_amount');

        $salesGrouped = $allInvoiceItems->groupBy('product_id');
        $returnsGrouped = $allReturnItems->groupBy('product_id');
        $allProductIds = $salesGrouped->keys()->concat($returnsGrouped->keys())->unique();

        $summary = [];
        $grandTotalQty = 0;
        $grandTotalReturnQty = 0;
        $grandTotalReturnAmount = 0;
        $grandTotalSaleAmount = 0;
        $grandTotalProductDiscount = 0;
        $grandTotalCostAmount = 0;
        $grandTotalProfit = 0;

        foreach ($allProductIds as $productId) {
            $items = $salesGrouped->get($productId, collect());
            $retItems = $returnsGrouped->get($productId, collect());

            $product = $items->first()?->product ?? $retItems->first()?->product;
            if (!$product) continue;

            $totalQty = (float)$items->sum('main_qty');
            $returnQty = (float)$retItems->sum('main_qty');
            $totalSale = (float)$items->sum('inv_subtotal');
            $returnSale = (float)$retItems->sum('subtotal');
            $productDiscount = (float)$items->sum('product_discount');
            $totalCost = (float)$items->sum('pur_subtotal');
            $returnCost = (float)$retItems->sum('pur_subtotal');

            $netSale = $totalSale - $returnSale;
            $netCost = $totalCost - $returnCost;
            $totalProfit = $netSale - $netCost;

            $summaryItem = [
                'product_id' => $productId,
                'name' => $product->name,
                'code' => $product->barcode ?: $product->id,
                'return_qty' => $returnQty,
                'return_amount' => $returnSale,
                'discount' => $productDiscount,
                'total_qty' => $totalQty,
                'total_sale' => $totalSale,
            ];

            if (!$isSalesman) {
                $summaryItem['total_cost'] = $totalCost;
                $summaryItem['total_profit'] = $totalProfit;
            }

            $summary[] = $summaryItem;

            $grandTotalQty += $totalQty;
            $grandTotalReturnQty += $returnQty;
            $grandTotalReturnAmount += $returnSale;
            $grandTotalSaleAmount += $totalSale;
            $grandTotalProductDiscount += $productDiscount;
            $grandTotalCostAmount += $totalCost;
            $grandTotalProfit += $totalProfit;
        }

        usort($summary, function($a, $b) {
            return $b['total_sale'] <=> $a['total_sale'];
        });

        $grandTotalNetSale = max(0, $grandTotalSaleAmount - $grandTotalReturnAmount - $grandTotalDiscount);

        $responseData = [
            'success' => true,
            'sdate' => $sdate,
            'edate' => $edate,
            'summary' => $summary,
            'grand_qty' => $grandTotalQty,
            'grand_return_qty' => $grandTotalReturnQty,
            'grand_return_amount' => $grandTotalReturnAmount,
            'grand_sale' => $grandTotalSaleAmount,
            'grand_net_sale' => $grandTotalNetSale,
            'grand_discount' => $grandTotalDiscount,
            'grand_product_discount' => $grandTotalProductDiscount,
            'is_salesman' => $isSalesman,
        ];

        if (!$isSalesman) {
            $responseData['grand_cost'] = $grandTotalCostAmount;
            $responseData['grand_profit'] = $grandTotalProfit;
        }

        return response()->json($responseData);
    }

    /**
     * Printable view for product sales and profit summary
     */
    public function printProductSalesSummary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filter = $request->input('filter');

        if ($filter) {
            switch ($filter) {
                case 'Today':
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
                case 'Yesterday':
                    $sdate = Carbon::yesterday()->toDateString();
                    $edate = Carbon::yesterday()->toDateString();
                    break;
                case 'This-week':
                    $sdate = Carbon::now()->startOfWeek()->toDateString();
                    $edate = Carbon::now()->endOfWeek()->toDateString();
                    break;
                case 'This-month':
                    $sdate = Carbon::now()->startOfMonth()->toDateString();
                    $edate = Carbon::now()->endOfMonth()->toDateString();
                    break;
                case 'This-year':
                    $sdate = Carbon::now()->startOfYear()->toDateString();
                    $edate = Carbon::now()->endOfYear()->toDateString();
                    break;
                default:
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
            }
        } else {
            $sdate = $startDate ? Carbon::parse($startDate)->toDateString() : Carbon::today()->toDateString();
            $edate = $endDate ? Carbon::parse($endDate)->toDateString() : Carbon::today()->toDateString();
        }

        $authUser = auth()->user();
        $isSalesman = $authUser->role && in_array(strtolower(trim($authUser->role->slug)), ['salesman', 'sales-person', 'sales_man', 'staff']);

        $userBranchId = $authUser->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : ($userBranchId == 1 ? null : $userBranchId);

        $itemsQuery = InvoiceItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        $returnsQuery = ReturnItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        $invDiscountQuery = Invoice::query()->whereBetween('date', [$sdate, $edate]);

        if ($isSalesman) {
            $itemsQuery->whereHas('invoice', fn($q) => $q->where('created_by', $authUser->id));
            $returnsQuery->whereHas('invoice', fn($q) => $q->where('created_by', $authUser->id));
            $invDiscountQuery->where('created_by', $authUser->id);
        } else {
            if ($branchId) {
                $itemsQuery->where('branch_id', $branchId);
                $returnsQuery->where('branch_id', $branchId);
                $invDiscountQuery->where('branch_id', $branchId);
            }
        }

        $allInvoiceItems = InvoiceItem::filterByFakeSale($itemsQuery);
        $allReturnItems = ReturnItem::filterByFakeSale($returnsQuery);
        $grandTotalDiscount = (float) Invoice::getFakeSum($invDiscountQuery, 'discount_amount');

        $salesGrouped = $allInvoiceItems->groupBy('product_id');
        $returnsGrouped = $allReturnItems->groupBy('product_id');
        $allProductIds = $salesGrouped->keys()->concat($returnsGrouped->keys())->unique();

        $summary = [];
        $grandTotalQty = 0;
        $grandTotalReturnQty = 0;
        $grandTotalReturnAmount = 0;
        $grandTotalSaleAmount = 0;
        $grandTotalProductDiscount = 0;
        $grandTotalCostAmount = 0;
        $grandTotalProfit = 0;

        foreach ($allProductIds as $productId) {
            $items = $salesGrouped->get($productId, collect());
            $retItems = $returnsGrouped->get($productId, collect());

            $product = $items->first()?->product ?? $retItems->first()?->product;
            if (!$product) continue;

            $totalQty = (float)$items->sum('main_qty');
            $returnQty = (float)$retItems->sum('main_qty');
            $totalSale = (float)$items->sum('inv_subtotal');
            $returnSale = (float)$retItems->sum('subtotal');
            $productDiscount = (float)$items->sum('product_discount');
            $totalCost = (float)$items->sum('pur_subtotal');
            $returnCost = (float)$retItems->sum('pur_subtotal');

            $netSale = $totalSale - $returnSale;
            $netCost = $totalCost - $returnCost;
            $totalProfit = $netSale - $netCost;

            $summary[] = [
                'product_id' => $productId,
                'name' => $product->name,
                'code' => $product->barcode ?: $product->id,
                'return_qty' => $returnQty,
                'return_amount' => $returnSale,
                'discount' => $productDiscount,
                'total_qty' => $totalQty,
                'total_sale' => $totalSale,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
            ];

            $grandTotalQty += $totalQty;
            $grandTotalReturnQty += $returnQty;
            $grandTotalReturnAmount += $returnSale;
            $grandTotalSaleAmount += $totalSale;
            $grandTotalProductDiscount += $productDiscount;
            $grandTotalCostAmount += $totalCost;
            $grandTotalProfit += $totalProfit;
        }

        usort($summary, function($a, $b) {
            return $b['total_sale'] <=> $a['total_sale'];
        });

        $grandTotalNetSale = max(0, $grandTotalSaleAmount - $grandTotalReturnAmount - $grandTotalDiscount);
        $branch = $branchId ? \App\Models\Branch::find($branchId) : null;

        return view('backend.pages.dashboard.summary_print', compact(
            'summary', 'sdate', 'edate', 
            'grandTotalQty', 'grandTotalReturnQty', 'grandTotalReturnAmount',
            'grandTotalSaleAmount', 'grandTotalNetSale', 'grandTotalProductDiscount', 'grandTotalCostAmount', 'grandTotalProfit', 
            'grandTotalDiscount', 'branch', 'isSalesman', 'authUser'
        ));
    }

    /**
     * Download or stream PDF for product sales and profit summary
     */
    public function downloadProductSalesSummaryPDF(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filter = $request->input('filter');

        if ($filter) {
            switch ($filter) {
                case 'Today':
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
                case 'Yesterday':
                    $sdate = Carbon::yesterday()->toDateString();
                    $edate = Carbon::yesterday()->toDateString();
                    break;
                case 'This-week':
                    $sdate = Carbon::now()->startOfWeek()->toDateString();
                    $edate = Carbon::now()->endOfWeek()->toDateString();
                    break;
                case 'This-month':
                    $sdate = Carbon::now()->startOfMonth()->toDateString();
                    $edate = Carbon::now()->endOfMonth()->toDateString();
                    break;
                case 'This-year':
                    $sdate = Carbon::now()->startOfYear()->toDateString();
                    $edate = Carbon::now()->endOfYear()->toDateString();
                    break;
                default:
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
            }
        } else {
            $sdate = $startDate ? Carbon::parse($startDate)->toDateString() : Carbon::today()->toDateString();
            $edate = $endDate ? Carbon::parse($endDate)->toDateString() : Carbon::today()->toDateString();
        }

        $authUser = auth()->user();
        $isSalesman = $authUser->role && in_array(strtolower(trim($authUser->role->slug)), ['salesman', 'sales-person', 'sales_man', 'staff']);

        $userBranchId = $authUser->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : ($userBranchId == 1 ? null : $userBranchId);

        $itemsQuery = InvoiceItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        $returnsQuery = ReturnItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        $invDiscountQuery = Invoice::query()->whereBetween('date', [$sdate, $edate]);

        if ($isSalesman) {
            $itemsQuery->whereHas('invoice', fn($q) => $q->where('created_by', $authUser->id));
            $returnsQuery->whereHas('invoice', fn($q) => $q->where('created_by', $authUser->id));
            $invDiscountQuery->where('created_by', $authUser->id);
        } else {
            if ($branchId) {
                $itemsQuery->where('branch_id', $branchId);
                $returnsQuery->where('branch_id', $branchId);
                $invDiscountQuery->where('branch_id', $branchId);
            }
        }

        $allInvoiceItems = InvoiceItem::filterByFakeSale($itemsQuery);
        $allReturnItems = ReturnItem::filterByFakeSale($returnsQuery);
        $grandTotalDiscount = (float) Invoice::getFakeSum($invDiscountQuery, 'discount_amount');

        $salesGrouped = $allInvoiceItems->groupBy('product_id');
        $returnsGrouped = $allReturnItems->groupBy('product_id');
        $allProductIds = $salesGrouped->keys()->concat($returnsGrouped->keys())->unique();

        $summary = [];
        $grandTotalQty = 0;
        $grandTotalReturnQty = 0;
        $grandTotalReturnAmount = 0;
        $grandTotalSaleAmount = 0;
        $grandTotalProductDiscount = 0;
        $grandTotalCostAmount = 0;
        $grandTotalProfit = 0;

        foreach ($allProductIds as $productId) {
            $items = $salesGrouped->get($productId, collect());
            $retItems = $returnsGrouped->get($productId, collect());

            $product = $items->first()?->product ?? $retItems->first()?->product;
            if (!$product) continue;

            $totalQty = (float)$items->sum('main_qty');
            $returnQty = (float)$retItems->sum('main_qty');
            $totalSale = (float)$items->sum('inv_subtotal');
            $returnSale = (float)$retItems->sum('subtotal');
            $productDiscount = (float)$items->sum('product_discount');
            $totalCost = (float)$items->sum('pur_subtotal');
            $returnCost = (float)$retItems->sum('pur_subtotal');

            $netSale = $totalSale - $returnSale;
            $netCost = $totalCost - $returnCost;
            $totalProfit = $netSale - $netCost;

            $summary[] = [
                'product_id' => $productId,
                'name' => $product->name,
                'code' => $product->barcode ?: $product->id,
                'return_qty' => $returnQty,
                'return_amount' => $returnSale,
                'discount' => $productDiscount,
                'total_qty' => $totalQty,
                'total_sale' => $totalSale,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
            ];

            $grandTotalQty += $totalQty;
            $grandTotalReturnQty += $returnQty;
            $grandTotalReturnAmount += $returnSale;
            $grandTotalSaleAmount += $totalSale;
            $grandTotalProductDiscount += $productDiscount;
            $grandTotalCostAmount += $totalCost;
            $grandTotalProfit += $totalProfit;
        }

        usort($summary, function($a, $b) {
            return $b['total_sale'] <=> $a['total_sale'];
        });

        $grandTotalNetSale = max(0, $grandTotalSaleAmount - $grandTotalReturnAmount - $grandTotalDiscount);
        $branch = $branchId ? \App\Models\Branch::find($branchId) : null;

        $pdf = Pdf::loadView('backend.pages.dashboard.summary_pdf', compact(
            'summary', 'sdate', 'edate', 
            'grandTotalQty', 'grandTotalReturnQty', 'grandTotalReturnAmount',
            'grandTotalSaleAmount', 'grandTotalNetSale', 'grandTotalProductDiscount', 'grandTotalCostAmount', 'grandTotalProfit', 
            'grandTotalDiscount', 'branch', 'isSalesman', 'authUser'
        ))->setPaper('a4', 'portrait');

        $fileName = 'sales_summary_' . $sdate . '_to_' . $edate . '.pdf';

        if ($request->has('download')) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }
}
