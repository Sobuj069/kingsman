<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockAudit;
use App\Models\StockAuditItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StockAuditController extends Controller
{
    /**
     * Display a listing of stock audit history.
     */
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        $query = StockAudit::with(['branch', 'auditor', 'items'])->orderBy('id', 'desc');

        if ($userBranchId != 1) {
            $query->where('branch_id', $userBranchId);
        } elseif ($filterBranchId) {
            $query->where('branch_id', $filterBranchId);
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $sdate = Carbon::createFromDate($request->startDate)->startOfDay();
            $edate = Carbon::createFromDate($request->endDate)->endOfDay();
            $query->whereBetween('date', [$sdate, $edate]);
        }

        if ($request->filled('audit_no')) {
            $query->where('audit_no', 'like', '%' . $request->audit_no . '%');
        }

        $audits = $query->paginate(20);

        return view('backend.pages.stock-audit.index', compact('audits'));
    }

    /**
     * Show the audit scan page.
     */
    public function create()
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $activeBranchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        $branches = Branch::all();
        $categories = Category::orderBy('name', 'ASC')->get();

        return view('backend.pages.stock-audit.create', compact('branches', 'categories', 'userBranchId', 'activeBranchId'));
    }

    /**
     * Search/Scan product via AJAX.
     */
    public function productScan(Request $request)
    {
        $searchTerm = trim($request->input('query'));
        if (empty($searchTerm)) {
            return response()->json(['success' => false, 'message' => 'Query term required'], 400);
        }

        $variationId = null;
        $product = null;

        // 1. Try resolving variation barcode via helper function
        if (function_exists('resolveProductAndVariationFromBarcode')) {
            $resolved = resolveProductAndVariationFromBarcode($searchTerm);
            if (!empty($resolved['product_id'])) {
                $product = Product::with(['unit', 'category', 'brand'])
                    ->where('id', $resolved['product_id'])
                    ->first();
                $variationId = $resolved['variation_id'];
            }
        }

        // 2. Delimited variation barcode like "BARCODE-VARID" or "BARCODE_VARID"
        if (!$product && (str_contains($searchTerm, '-') || str_contains($searchTerm, '_'))) {
            $parts = preg_split('/[-_]/', $searchTerm);
            if (count($parts) >= 2 && is_numeric(end($parts))) {
                $varIdCandidate = (int)end($parts);
                $pvCandidate = \App\Models\ProductVariation::with('product')
                    ->where('id', $varIdCandidate)
                    ->first();
                if ($pvCandidate && $pvCandidate->product) {
                    $product = $pvCandidate->product;
                    $variationId = $pvCandidate->id;
                }
            }
        }

        // 3. Direct ID match in ProductVariation table if numeric
        if (!$product && is_numeric($searchTerm)) {
            $pvCandidate = \App\Models\ProductVariation::with('product')->find((int)$searchTerm);
            if ($pvCandidate && $pvCandidate->product) {
                $product = $pvCandidate->product;
                $variationId = $pvCandidate->id;
            }
        }

        // 4. Exact match on Product Barcode or Product ID
        if (!$product) {
            $product = Product::with(['unit', 'category', 'brand'])
                ->where(function ($q) use ($searchTerm) {
                    $q->where('barcode', $searchTerm)
                      ->orWhere('id', $searchTerm);
                })
                ->first();
        }

        // 5. Search by IMEI or Serial Number (Product imei, SerialNumber table, PurchaseItem imei, InvoiceItem imei)
        if (!$product) {
            $product = Product::with(['unit', 'category', 'brand'])
                ->where('imei', 'like', "%{$searchTerm}%")
                ->first();
        }

        if (!$product) {
            $productBySerialId = \App\Models\SerialNumber::where('serial', $searchTerm)
                ->orWhere('serial', 'like', "%{$searchTerm}%")
                ->value('product_id');

            if (!$productBySerialId) {
                $productBySerialId = \App\Models\PurchaseItem::where('imei', $searchTerm)
                    ->orWhere('imei', 'like', "%{$searchTerm}%")
                    ->value('product_id');
            }

            if (!$productBySerialId) {
                $productBySerialId = \App\Models\InvoiceItem::where('imei', $searchTerm)
                    ->orWhere('imei', 'like', "%{$searchTerm}%")
                    ->value('product_id');
            }

            if ($productBySerialId) {
                $product = Product::with(['unit', 'category', 'brand'])->find($productBySerialId);
            }
        }

        // 6. Search by Size or Color attribute name attached to ProductVariation
        if (!$product) {
            $pvByAttr = \App\Models\ProductVariation::with('product')
                ->whereHas('size', function($q) use ($searchTerm) {
                    $q->where('size', $searchTerm);
                })
                ->orWhereHas('color', function($q) use ($searchTerm) {
                    $q->where('color', $searchTerm);
                })
                ->first();

            if ($pvByAttr && $pvByAttr->product) {
                $product = $pvByAttr->product;
                $variationId = $pvByAttr->id;
            }
        }

        // 7. Search by Product Name (Exact or LIKE search)
        if (!$product) {
            $product = Product::with(['unit', 'category', 'brand'])
                ->where('name', $searchTerm)
                ->orWhere('name', 'like', "%{$searchTerm}%")
                ->first();
        }

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found (' . $searchTerm . ')'], 404);
        }

        // Build variation label (Size / Color) if a specific variation was identified
        $variationText = '';
        if ($variationId) {
            $pv = \App\Models\ProductVariation::with(['size', 'color'])->find($variationId);
            if ($pv) {
                $varDetails = [];
                if ($pv->size && !empty($pv->size->size)) {
                    $varDetails[] = 'Size: ' . $pv->size->size;
                }
                if ($pv->color && !empty($pv->color->color)) {
                    $varDetails[] = 'Color: ' . $pv->color->color;
                }
                if (!empty($varDetails)) {
                    $variationText = ' (' . implode(', ', $varDetails) . ')';
                }
            }
        }

        // Set active branch filter in session
        $userBranchId = auth()->user()->branch_id;
        $branchId = $request->input('branch_id');
        if ($branchId) {
            session(['branch_filter_id' => $branchId]);
        } else {
            $branchId = session('branch_filter_id', $userBranchId);
        }

        // Get system stock for target branch exactly matching stock report
        $cacheKey = "product_stock_{$product->id}_{$userBranchId}_{$branchId}";
        cache()->forget($cacheKey);

        $systemStock = 0;
        $systemStockFormatted = '';

        if ($product->product_variations && $product->product_variations->count() > 0) {
            if ($variationId) {
                $varStock = (float)variation_stock($variationId);
                $systemStock = $varStock;
                $systemStockFormatted = $varStock . ' ' . ($product->unit->name ?? 'pcs');
            } else {
                $varTotal = 0;
                foreach ($product->product_variations as $v) {
                    $varTotal += (float)variation_stock($v->id);
                }
                $systemStock = $varTotal;
                $systemStockFormatted = $varTotal . ' ' . ($product->unit->name ?? 'pcs');
            }
        } else {
            $factor = ($product->unit && $product->unit->related_value) ? (float)$product->unit->related_value : 1;
            if ($factor <= 0) $factor = 1;

            $fakeVal = product_fake_stock_val($product, $branchId);
            $systemStock = (float)($fakeVal / $factor);
            $systemStockFormatted = product_stock($product);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name . $variationText,
                'code' => $product->barcode ?: $product->id,
                'barcode' => $product->barcode ?: $product->id,
                'unit_name' => $product->unit->name ?? '',
                'category_name' => $product->category->name ?? '',
                'brand_name' => $product->brand->name ?? '',
                'system_qty' => $systemStock,
                'system_qty_formatted' => $systemStockFormatted,
                'variation_id' => $variationId
            ]
        ]);
    }

    /**
     * Search product suggestions via AJAX as user types.
     */
    public function productSuggestions(Request $request)
    {
        $searchTerm = trim($request->input('query'));
        if (empty($searchTerm) || strlen($searchTerm) < 1) {
            return response()->json(['success' => true, 'suggestions' => []]);
        }

        $userBranchId = auth()->user()->branch_id;
        $branchId = $request->input('branch_id');
        if ($branchId) {
            session(['branch_filter_id' => $branchId]);
        } else {
            $branchId = session('branch_filter_id', $userBranchId);
        }

        $query = Product::with(['unit', 'category', 'brand', 'product_variations.size', 'product_variations.color'])
            ->where('is_service', 0)
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('barcode', 'like', "%{$searchTerm}%")
                  ->orWhere('id', $searchTerm)
                  ->orWhere('imei', 'like', "%{$searchTerm}%")
                  ->orWhereHas('product_variations', function ($pvQ) use ($searchTerm) {
                      $pvQ->where('barcode', 'like', "%{$searchTerm}%")
                          ->orWhereHas('size', fn($sq) => $sq->where('size', 'like', "%{$searchTerm}%"))
                          ->orWhereHas('color', fn($cq) => $cq->where('color', 'like', "%{$searchTerm}%"));
                  });
            });

        $products = $query->take(15)->get();
        $suggestions = [];

        foreach ($products as $product) {
            $cacheKey = "product_stock_{$product->id}_{$userBranchId}_{$branchId}";
            cache()->forget($cacheKey);

            if ($product->product_variations && $product->product_variations->count() > 0) {
                // If product has variations, provide individual variations
                foreach ($product->product_variations as $pv) {
                    $varDetails = [];
                    if ($pv->size && !empty($pv->size->size)) {
                        $varDetails[] = 'Size: ' . $pv->size->size;
                    }
                    if ($pv->color && !empty($pv->color->color)) {
                        $varDetails[] = 'Color: ' . $pv->color->color;
                    }
                    $variationLabel = !empty($varDetails) ? ' (' . implode(', ', $varDetails) . ')' : '';

                    $varStock = (float)variation_stock($pv->id);
                    $suggestions[] = [
                        'id' => $product->id,
                        'variation_id' => $pv->id,
                        'name' => $product->name . $variationLabel,
                        'code' => $pv->barcode ?: ($product->barcode ?: $product->id),
                        'barcode' => $pv->barcode ?: ($product->barcode ?: $product->id),
                        'unit_name' => $product->unit->name ?? '',
                        'category_name' => $product->category->name ?? '',
                        'brand_name' => $product->brand->name ?? '',
                        'system_qty' => $varStock,
                        'system_qty_formatted' => $varStock . ' ' . ($product->unit->name ?? 'pcs')
                    ];
                }
            } else {
                $factor = ($product->unit && $product->unit->related_value) ? (float)$product->unit->related_value : 1;
                if ($factor <= 0) $factor = 1;

                $fakeVal = product_fake_stock_val($product, $branchId);
                $systemStock = (float)($fakeVal / $factor);
                $systemStockFormatted = product_stock($product);

                $suggestions[] = [
                    'id' => $product->id,
                    'variation_id' => null,
                    'name' => $product->name,
                    'code' => $product->barcode ?: $product->id,
                    'barcode' => $product->barcode ?: $product->id,
                    'unit_name' => $product->unit->name ?? '',
                    'category_name' => $product->category->name ?? '',
                    'brand_name' => $product->brand->name ?? '',
                    'system_qty' => $systemStock,
                    'system_qty_formatted' => $systemStockFormatted
                ];
            }
        }

        return response()->json(['success' => true, 'suggestions' => $suggestions]);
    }

    /**
     * Load products by category via AJAX for bulk audit.
     */
    public function loadCategoryProducts(Request $request)
    {
        $categoryId = $request->input('category_id');
        $userBranchId = auth()->user()->branch_id;
        $branchId = $request->input('branch_id');
        if ($branchId) {
            session(['branch_filter_id' => $branchId]);
        } else {
            $branchId = session('branch_filter_id', $userBranchId);
        }
        
        $query = Product::with(['unit', 'category', 'brand', 'product_variations'])->where('is_service', 0);
        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name', 'ASC')->get();
        $data = [];

        foreach ($products as $product) {
            $cacheKey = "product_stock_{$product->id}_{$userBranchId}_{$branchId}";
            cache()->forget($cacheKey);

            $systemStock = 0;
            $systemStockFormatted = '';

            if ($product->product_variations && $product->product_variations->count() > 0) {
                $varTotal = 0;
                foreach ($product->product_variations as $v) {
                    $varTotal += (float)variation_stock($v->id);
                }
                $systemStock = $varTotal;
                $systemStockFormatted = $varTotal . ' ' . ($product->unit->name ?? 'pcs');
            } else {
                $factor = ($product->unit && $product->unit->related_value) ? (float)$product->unit->related_value : 1;
                if ($factor <= 0) $factor = 1;

                $fakeVal = product_fake_stock_val($product, $branchId);
                $systemStock = (float)($fakeVal / $factor);
                $systemStockFormatted = product_stock($product);
            }

            $data[] = [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->barcode ?: $product->id,
                'barcode' => $product->barcode ?: $product->id,
                'unit_name' => $product->unit->name ?? '',
                'category_name' => $product->category->name ?? '',
                'brand_name' => $product->brand->name ?? '',
                'system_qty' => $systemStock,
                'system_qty_formatted' => $systemStockFormatted
            ];
        }

        return response()->json(['success' => true, 'products' => $data]);
    }

    /**
     * Store audit session.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'branch_id' => 'required',
        ]);

        $lastAudit = StockAudit::orderBy('id', 'DESC')->select('audit_no')->first();
        if (!$lastAudit) {
            $auditNo = "SA-00001";
        } else {
            $auditNo = $lastAudit->audit_no;
            $auditNo++;
        }

        DB::beginTransaction();
        try {
            $audit = new StockAudit();
            $audit->audit_no = $auditNo;
            $audit->date = $request->date;
            $audit->branch_id = $request->branch_id;
            $audit->audited_by = auth()->user()->id;
            $audit->note = $request->note;
            $audit->status = 'completed';

            $audit->save();

            $totalItems = 0;
            $matchedItems = 0;
            $discrepancyItems = 0;
            $totalDeficit = 0;
            $totalSurplus = 0;

            foreach ($request->product_id as $key => $productId) {
                $systemQty = (float)($request->system_qty[$key] ?? 0);
                $scannedQty = (float)($request->scanned_qty[$key] ?? 0);
                $physicalQty = (float)($request->physical_qty[$key] ?? 0);
                $diffQty = $physicalQty - $systemQty;

                $totalItems++;
                if (abs($diffQty) < 0.001) {
                    $matchedItems++;
                } else {
                    $discrepancyItems++;
                    if ($diffQty < 0) {
                        $totalDeficit += abs($diffQty);
                    } else {
                        $totalSurplus += $diffQty;
                    }
                }

                $item = new StockAuditItem();
                $item->stock_audit_id = $audit->id;
                $item->product_id = $productId;
                $item->product_variation_id = $request->variation_id[$key] ?? null;
                $item->system_qty = $systemQty;
                $item->scanned_qty = $scannedQty;
                $item->physical_qty = $physicalQty;
                $item->diff_qty = $diffQty;
                $item->save();
            }

            $audit->total_items = $totalItems;
            $audit->matched_items = $matchedItems;
            $audit->discrepancy_items = $discrepancyItems;
            $audit->total_deficit_qty = $totalDeficit;
            $audit->total_surplus_qty = $totalSurplus;
            $audit->save();

            DB::commit();

            session()->flash('success', 'Stock audit record created successfully!');
            return redirect()->route('stock-audit.show', $audit->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving stock audit: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * View audit record.
     */
    public function show(string $id)
    {
        $audit = StockAudit::with(['branch', 'auditor', 'items.product.unit'])->findOrFail($id);
        return view('backend.pages.stock-audit.show', compact('audit'));
    }

    /**
     * Print stock audit report.
     */
    public function print(string $id)
    {
        $audit = StockAudit::with(['branch', 'auditor', 'items.product.unit'])->findOrFail($id);
        return view('backend.pages.stock-audit.print', compact('audit'));
    }

    /**
     * Remove the audit log.
     */
    public function destroy(string $id)
    {
        $audit = StockAudit::findOrFail($id);
        $audit->delete();

        session()->flash('success', 'Stock audit record deleted successfully');
        return back();
    }
}
