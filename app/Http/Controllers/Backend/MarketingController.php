<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AdCost;
use App\Models\Platform;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    public function index()
    {
        $adCosts = AdCost::with(['platform', 'product'])->orderBy('date', 'desc')->paginate(20);
        return view('backend.pages.marketing.ad-cost.index', compact('adCosts'));
    }

    public function create()
    {
        $platforms = Platform::all();
        $products = Product::orderBy('name', 'asc')->get();
        $branches = Branch::all();
        return view('backend.pages.marketing.ad-cost.create', compact('platforms', 'products', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'platform_id' => 'nullable|exists:platforms,id',
            'product_id' => 'nullable|exists:products,id',
            'campaign_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        AdCost::create([
            'platform_id' => $request->platform_id,
            'product_id' => $request->product_id,
            'campaign_name' => $request->campaign_name,
            'amount' => $request->amount,
            'date' => $request->date,
            'notes' => $request->notes,
        ]);

        session()->flash('success', __('Ad cost added successfully'));
        return redirect()->route('ad-cost.index');
    }

    public function destroy($id)
    {
        $adCost = AdCost::findOrFail($id);
        $adCost->delete();

        session()->flash('success', __('Ad cost deleted successfully'));
        return redirect()->route('ad-cost.index');
    }

    public function roi(Request $request)
    {
        $products = Product::orderBy('name', 'asc')->get();
        $selectedProductId = $request->get('product_id');

        $roiData = [];
        $overallData = null;
        $selectedProduct = null;

        if ($selectedProductId) {
            $selectedProduct = Product::find($selectedProductId);
            
            if ($selectedProduct) {
                $platforms = Platform::all();
                foreach ($platforms as $platform) {
                    // Sales of this product on this platform
                    $totalSales = DB::table('invoice_items')
                        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                        ->where('invoice_items.product_id', $selectedProduct->id)
                        ->where('invoices.platform_id', $platform->id)
                        ->sum('invoice_items.subtotal');

                    // Ad cost of this product on this platform
                    $totalAdCost = DB::table('ad_costs')
                        ->where('product_id', $selectedProduct->id)
                        ->where('platform_id', $platform->id)
                        ->sum('amount');

                    $netProfit = $totalSales - $totalAdCost;
                    $roiPercentage = $totalAdCost > 0 ? ($netProfit / $totalAdCost) * 100 : 0;

                    $roiData[] = (object)[
                        'platform' => $platform,
                        'total_sales' => $totalSales,
                        'total_ad_cost' => $totalAdCost,
                        'net_profit' => $netProfit,
                        'roi_percentage' => $roiPercentage
                    ];
                }

                // Offline/No Platform sales for this product
                $noPlatformSales = DB::table('invoice_items')
                    ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->where('invoice_items.product_id', $selectedProduct->id)
                    ->whereNull('invoices.platform_id')
                    ->sum('invoice_items.subtotal');

                $noPlatformAdCost = DB::table('ad_costs')
                    ->where('product_id', $selectedProduct->id)
                    ->whereNull('platform_id')
                    ->sum('amount');

                $noPlatformNet = $noPlatformSales - $noPlatformAdCost;
                $noPlatformRoi = $noPlatformAdCost > 0 ? ($noPlatformNet / $noPlatformAdCost) * 100 : 0;

                $overallData = (object)[
                    'total_sales' => $noPlatformSales,
                    'total_ad_cost' => $noPlatformAdCost,
                    'net_profit' => $noPlatformNet,
                    'roi_percentage' => $noPlatformRoi
                ];
            }
        }

        return view('backend.pages.marketing.roi.index', compact('products', 'selectedProduct', 'roiData', 'overallData'));
    }

    public function courierTracking(Request $request)
    {
        $consignmentId = $request->get('consignment_id');
        $invoice = null;
        $courierStatus = null;

        if ($consignmentId) {
            $invoice = \App\Models\Invoice::with(['customer', 'invoice_items.product'])
                ->where('consignment_id', $consignmentId)
                ->first();

            // Fetch live status from Steadfast API using global helper
            $courierStatus = status($consignmentId);
        }

        return view('backend.pages.marketing.courier.tracking', compact('invoice', 'courierStatus', 'consignmentId'));
    }
}
