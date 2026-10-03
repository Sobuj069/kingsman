<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use App\Models\Invoice;
use App\Models\User;
use App\Models\InvoiceItem;
use App\Models\PurchaseItem;
use Carbon\CarbonPeriod;
use PDF;

class PDFController extends Controller
{
    public function stock_pdf(Request $request)
    {

        $data['product_id'] = $request->product_id;
        $data['keyword'] = $request->search_keyword;
        $data['category_id'] = $request->category_id;
        $data['brand_id'] = $request->brand_id;

        $query = Product::with('unit.related_unit')->orderBy('created_at', 'DESC');

        if ($request->product_id != null) {
            $query->where('id', $request->product_id);
        }

        if ($request->category_id != null) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->brand_id != null) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->search_keyword != null) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_keyword . '%')
                  ->orWhere('barcode', $request->search_keyword);
            });
        }

        $products = $query->get();
        $data['products'] = $products;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchId = ($userBranchId == 1) ? $filterBranchId : $userBranchId;

        $data['totalPrice'] = $products->sum(function ($product) use ($branchId) {
            if ($product->is_service == 1) {
                return 0;
            }
            return product_fake_stock_val($product, $branchId) * product_purchase_unit_cost($product, $branchId);
        });

        $pdf = PDF::loadView('backend.pages.report.pdf.stock_pdf', $data);
        return $pdf->download('stock_report.pdf');

    }

    public function daily_pdf(Request $request){
        // if ($request->all() != NULL) {
            $data['sdate'] = Carbon::createFromDate($request->start_date)->toDateString();
            $data['edate'] = Carbon::createFromDate($request->end_date)->toDateString();
            $dateRange = CarbonPeriod::create($data['sdate'], $data['edate']);

            $data['dates'] = array_map(fn ($date) => $date->format('Y-m-d'), iterator_to_array($dateRange));
            // return view('backend.pages.report.daily', $data);
        // }
        $pdf = PDF::loadView('backend.pages.report.daily',$data);

        return $pdf->download('user_sell.pdf');
    }

    public function pdf(Request $request)
    {
            $invoice = Invoice::orderBy('id', 'desc')->get();
        $pdf = PDF::loadView('backend.pages.report.user_sell_pdf',compact('invoice'));
        return $pdf->download('user_sell.pdf');
    }
}
