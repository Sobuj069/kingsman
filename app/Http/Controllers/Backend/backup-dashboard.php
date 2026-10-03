<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\ReturnItem;
use App\Models\ReturnPurchase;
use App\Models\ReturnTbl;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter_keyword = $request->filter;
        $data['filter']  = 'Today';
        $sold = Invoice::where('date', date('Y-m-d'))->sum('total_amount');
        $return = ReturnTbl::where('date', date('Y-m-d'))->sum('total_return');
        $data['today_invoice'] = $sold - $return;
        $purchase = Purchase::where('date', date('Y-m-d'))->sum('total_amount');
        $return_pur = ReturnPurchase::where('date', date('Y-m-d'))->sum('total_return');
        $data['today_purchase'] = $purchase-$return_pur;
        $data['expence'] = Expense::where('date', date('Y-m-d'))->sum('amount');
        $data['pur_due'] = Purchase::where('date',date('Y-m-d'))->sum('total_due');
        $data['inv_due'] = Invoice::where('date',date('Y-m-d'))->sum('total_due');
        $invoices = InvoiceItem::whereDate('created_at', Carbon::today())->get();
        $returns = ReturnItem::whereDate('created_at', Carbon::today())->get();
        $discount = Invoice::whereDate('created_at',Carbon::today())->sum('discount_amount');
        $returnDis = ReturnTbl::where('date', date('Y-m-d'))->sum('discount_amount');
        $dis = $discount - $returnDis;
        $data['profit'] = profit($invoices) - rtn_profit($returns)- $dis ;
        switch ($filter_keyword) {
            case 'yesterday':
                $data['filter']  = 'Yesterday';
                $yesterday = Carbon::createFromDate($filter_keyword)->toDateString();
                $sold = Invoice::where('date', $yesterday)->sum('total_amount');
                $return = ReturnTbl::where('date', $yesterday)->sum('total_return');
                $data['today_invoice'] = $sold - $return;
                // dd($return); 

                $purchase = Purchase::where('date', $yesterday)->sum('total_amount');
                $return_pur = ReturnPurchase::where('date', $yesterday)->sum('total_return');
                $data['today_purchase'] = $purchase-$return_pur;

                $data['expence'] = Expense::where('date', $yesterday)->sum('amount');
                $data['pur_due'] = Purchase::where('date',$yesterday)->sum('total_due');
                $data['inv_due'] = Invoice::where('date',$yesterday)->sum('total_due');
                $discount = Invoice::whereDate('created_at',Carbon::yesterday())->sum('discount_amount');
                $invoices = InvoiceItem::whereDate('created_at', Carbon::yesterday())->get();
                $returns = ReturnItem::whereDate('created_at',Carbon::yesterday())->get();
                $returnDis = ReturnTbl::whereDate('created_at',Carbon::yesterday())->sum('discount_amount') ;
                $dis = $discount - $returnDis;
                $data['profit'] = profit($invoices) - rtn_profit($returns)- $dis ;
                break;
            case 'this_week':
                $data['filter']  = 'This Week';
                $sold = Invoice::whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');
                $return = ReturnTbl::whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_return');
                $data['today_invoice'] = $sold - $return;
                $purchase = Purchase::whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');
                $return_pur = ReturnPurchase::whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_return');
                $data['today_purchase'] = $purchase-$return_pur;
                $data['expence'] = Expense::whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
                $data['pur_due'] = Purchase::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_due');
                $data['inv_due'] = Invoice::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_due');
                $discount = Invoice::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('discount_amount');
                $invoices = InvoiceItem::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
                $returns = ReturnItem::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
                $returnDis = ReturnTbl::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('discount_amount');
                $dis = $discount - $returnDis;
                $data['profit'] = profit($invoices) - rtn_profit($returns)- $dis ;
                break;
            case 'last_week':
                $data['filter']  = 'Last Week';
                $sold = Invoice::whereBetween('date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('total_amount');
                $return = ReturnTbl::whereBetween('date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('total_return');
                $data['today_invoice'] = $sold - $return;
                $purchase = Purchase::whereBetween('date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('total_amount');
                $return_pur = ReturnPurchase::whereBetween('date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('total_return');
                $data['today_purchase'] = $purchase-$return_pur;
                $data['expence'] = Expense::whereBetween('date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('amount');
                $data['pur_due'] = Purchase::whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('total_due');
                $data['inv_due'] = Invoice::whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('total_due');
                $invoices = InvoiceItem::whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->get();
                $discount = Invoice::whereDate('created_at',[Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('discount_amount');
                $returns = ReturnItem::whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->get();
                $returnDis = ReturnTbl::whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('discount_amount');
                $dis = $discount - $returnDis;
                $data['profit'] = profit($invoices) - rtn_profit($returns)- $dis ;
                break;
            case 'this_month':
                $data['filter']  = 'This Month';
                $sold = Invoice::whereMonth('date', Carbon::now()->month)->sum('total_amount');
                $return = ReturnTbl::whereMonth('date', Carbon::now()->month)->sum('total_return');
                $data['today_invoice'] = $sold - $return;
                $purchase = Purchase::whereMonth('date', Carbon::now()->month)->sum('total_amount');
                $return_pur = ReturnPurchase::whereMonth('date', Carbon::now()->month)->sum('total_return');
                $data['today_purchase'] = $purchase-$return_pur;
                $data['expence'] = Expense::whereMonth('date', Carbon::now()->month)->sum('amount');
                $data['pur_due'] = Purchase::whereMonth('created_at', Carbon::now()->month)->sum('total_due');
                $data['inv_due'] = Invoice::whereMonth('created_at', Carbon::now()->month)->sum('total_due');
                $invoices = InvoiceItem::whereMonth('created_at', Carbon::now()->month)->get();
                $discount = Invoice::whereMonth('created_at',Carbon::now()->month)->sum('discount_amount');
                $returns = ReturnItem::whereMonth('created_at',Carbon::now()->month)->get();
                $returnDis = ReturnTbl::whereMonth('created_at',Carbon::now()->month)->sum('discount_amount');
                $dis = $discount - $returnDis;
                $data['profit'] = profit($invoices) - rtn_profit($returns)- $dis ;
                break;
            case 'last_month':
                $data['filter']  = 'Last Month';
                $sold = Invoice::whereMonth('date', Carbon::now()->subMonth()->month)->sum('total_amount');
                $return = ReturnTbl::whereMonth('date', Carbon::now()->subMonth()->month)->sum('total_return');
                $data['today_invoice'] = $sold - $return;
                $purchase = Purchase::whereMonth('date', Carbon::now()->subMonth()->month)->sum('total_amount');
                $return_pur = ReturnPurchase::whereMonth('date', Carbon::now()->subMonth()->month)->sum('total_return');
                $data['today_purchase'] = $purchase-$return_pur;
                $data['expence'] = Expense::whereMonth('date', Carbon::now()->subMonth()->month)->sum('amount');
                $data['pur_due'] = Purchase::whereMonth('created_at', Carbon::now()->subMonth()->month)->sum('total_due');
                $data['inv_due'] = Invoice::whereMonth('created_at', Carbon::now()->subMonth()->month)->sum('total_due');
                $invoices = InvoiceItem::whereMonth('created_at', Carbon::now()->subMonth()->month)->get();
                $discount = Invoice::whereMonth('created_at',Carbon::now()->subMonth()->month)->sum('discount_amount');
                $returns = ReturnItem::whereMonth('created_at',Carbon::now()->subMonth()->month)->get();
                $returnDis = ReturnTbl::whereMonth('created_at',Carbon::now()->subMonth()->month)->sum('discount_amount');
                $dis = $discount - $returnDis;
                $data['profit'] = profit($invoices) - rtn_profit($returns)- $dis ;
                break;
            case 'this_year':
                $data['filter']  = 'This Year';
                $sold = Invoice::whereYear('date', Carbon::now()->year)->sum('total_amount');
                $return = ReturnTbl::whereYear('date', Carbon::now()->year)->sum('total_return');
                $data['today_invoice'] = $sold - $return;
                $purchase = Purchase::whereYear('date', Carbon::now()->year)->sum('total_amount');
                $return_pur = ReturnPurchase::whereYear('date', Carbon::now()->year)->sum('total_return');
                $data['today_purchase'] = $purchase-$return_pur;
                $data['expence'] = Expense::whereYear('date', Carbon::now()->year)->sum('amount');
                $data['pur_due'] = Purchase::whereYear('created_at', Carbon::now()->year)->sum('total_due');
                $data['inv_due'] = Invoice::whereYear('created_at', Carbon::now()->year)->sum('total_due');
                $invoices = InvoiceItem::whereYear('created_at', Carbon::now()->year)->get();
                $discount = Invoice::whereYear('created_at',Carbon::now()->year)->sum('discount_amount');
                $returns = ReturnItem::whereYear('created_at',Carbon::now()->year)->get();
                $returnDis = ReturnTbl::whereYear('created_at',Carbon::now()->year)->sum('discount_amount');
                $dis = $discount - $returnDis;
                $data['profit'] = profit($invoices) - rtn_profit($returns)- $dis ;
                break;
            case 'last_year':
                $data['filter']  = 'Last Year';
                $sold = Invoice::whereYear('date', Carbon::now()->subYear()->year)->sum('total_amount');
                $return = ReturnTbl::whereYear('date', Carbon::now()->subYear()->year)->sum('total_return');
                $data['today_invoice'] = $sold - $return;
                $purchase = Purchase::whereYear('date', Carbon::now()->subYear()->year)->sum('total_amount');
                $return_pur = ReturnPurchase::whereYear('date', Carbon::now()->subYear()->year)->sum('total_return');
                $data['today_purchase'] = $purchase-$return_pur;
                $data['expence'] = Expense::whereYear('date', Carbon::now()->subYear()->year)->sum('amount');
                $data['pur_due'] = Purchase::whereYear('created_at', Carbon::now()->subYear()->year)->sum('total_due');
                $data['inv_due'] = Invoice::whereYear('created_at', Carbon::now()->subYear()->year)->sum('total_due');
                $invoices = InvoiceItem::whereYear('created_at', Carbon::now()->subYear()->year)->get();
                $discount = Invoice::whereYear('created_at',Carbon::now()->subYear()->year)->sum('discount_amount');
                $returns = ReturnItem::whereYear('created_at',Carbon::now()->subYear()->year)->get();
                $returnDis = ReturnTbl::whereYear('created_at',Carbon::now()->subYear()->year)->sum('discount_amount');
                $dis = $discount - $returnDis;
                $data['profit'] = profit($invoices) - rtn_profit($returns)- $dis ;
                break;
        }


        return view('backend.pages.dashboard.index', $data);
    }
    public function yesterday(){
        $data['filter']  = 'Yesterday';
        // $yesterday = Carbon::createFromDate($filter_keyword)->toDateString();
        $data['today_invoice'] = Invoice::where('date', Carbon::yesterday())->sum('total_amount');
        $data['today_purchase'] = Purchase::where('date', Carbon::yesterday())->sum('total_amount');
        $data['expence'] = Expense::where('date', Carbon::yesterday())->sum('amount');
        $invoices = InvoiceItem::whereDate('created_at', Carbon::yesterday())->get();
        $data['profit'] = profit($invoices);

        return view('backend.pages.dashboard.index', $data);
    }

}
