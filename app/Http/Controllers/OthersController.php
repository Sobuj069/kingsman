<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Brand;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\InvoiceItem;
use App\Models\Supplier;
use App\Models\Variation;
use App\Models\BankAccount;
use App\Models\UsedProduct;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use App\Models\BranchProduct;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorSVG;

class OthersController extends Controller
{
    
     public function customerPrintInvoice($id)
    {

        $invoice = Invoice::with('customer', 'user', 'invoiceItems')
                            ->where('unique_id', $id)->first();
        return view('backend.pages.invoice.online-invoice',compact('invoice'));
    }
    
    public function statusUpdate(Request $request)
    {
        // validation
        // dd($request->all());
        $validated = $request->validate([
            'id' => 'required',
            'status' => 'required',
            'model' => 'required',
        ]);

        if ($validated) {
            $model = "\App\Models\\" . $request->model;
            $data = $model::find($request->id);
            $data->status = $request->status;
            $data->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ]);
        }
    }

    public function getProductBySupplier($supplier_id)
    {
        $products = Product::where('supplier_id', $supplier_id)
            ->with('unit.related_unit')
            ->when(env('APP_IMEI') != 'yes', function ($query) {
                return $query->where(function($q) {
                    $q->where('imei', '!=', 1)->orWhereNull('imei');
                });
            })
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($products);
    }

    public function productSearch(Request $request)
    {
        $userBranchId   = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $query          = trim(request('req'));

        // --- Variation barcode resolution ---
        // If the scanned code matches a concatenated variation barcode (product_barcode + variation_id)
        // return only that parent product with matched_variation_id set.
        $resolved = resolveProductAndVariationFromBarcode($query);
        if ($resolved['product_id']) {
            $product = Product::where('id', $resolved['product_id'])
                ->with('unit.related_unit')
                ->where('status', 1)
                ->where('is_service', 0)
                ->first();
            if ($product) {
                $product->stock_qty_text    = product_stock($product);
                $product->stock_qty         = (float) product_fake_stock_val($product);
                $product->matched_variation_id = $resolved['variation_id'];
                return response()->json([$product]);
            }
        }

        $matchingProductIds = [];
        if (!empty($query) && strlen($query) >= 2) {
            $cleanQuery = trim($query);
            $serialProductIds = SerialNumber::where('serial', 'LIKE', "%{$cleanQuery}%")
                ->pluck('product_id')->toArray();

            $purchaseProductIds = [];
            $invoiceProductIds  = [];
            if (preg_match('/[0-9]{3,}/', $cleanQuery)) {
                $purchaseProductIds = PurchaseItem::where('imei', 'LIKE', "%{$cleanQuery}%")
                    ->pluck('product_id')->toArray();
                $invoiceProductIds  = InvoiceItem::where('imei', 'LIKE', "%{$cleanQuery}%")
                    ->pluck('product_id')->toArray();
            }

            $productImeiIds = Product::where('imei', 'LIKE', "%{$cleanQuery}%")
                ->pluck('id')->toArray();

            $matchingProductIds = array_unique(array_merge($serialProductIds, $purchaseProductIds, $invoiceProductIds, $productImeiIds));
        }

        $activeBranchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;
        $branchProductIds = BranchProduct::where('branch_id', $activeBranchId)->pluck('product_id');

        if (strtolower($query) == 'out of stock') {
            $products = Product::where('is_service', 0)
                ->whereIn('id', $branchProductIds)
                ->with('unit.related_unit')
                ->where('status', 1)
                ->get()
                ->filter(fn ($p) => (float) product_fake_stock_val($p) <= 0)
                ->take(10)
                ->map(function ($product) {
                    $product->stock_qty_text = product_stock($product);
                    $product->stock_qty      = (float) product_fake_stock_val($product);
                    return $product;
                });
        } else {
            $products = Product::where(function($q) use ($branchProductIds) {
                    $q->whereIn('id', $branchProductIds)
                      ->orWhere('is_service', 1);
                })
                ->with('unit.related_unit')
                ->where(function ($q) use ($query, $matchingProductIds) {
                    $q->where('name', 'LIKE', "%$query%")
                        ->orWhere('barcode', 'LIKE', "$query%");
                    if (!empty($matchingProductIds)) {
                        $q->orWhereIn('id', $matchingProductIds);
                    }
                })
                ->when(env('APP_IMEI') != 'yes', function ($q) {
                    return $q->where(function ($qq) {
                        $qq->where('imei', '!=', 1)->orWhereNull('imei');
                    });
                })
                ->where('status', 1)
                ->limit(10)
                ->get()
                ->map(function ($product) {
                    $product->stock_qty_text = ($product->is_service == 1) ? __('Service Item') : product_stock($product);
                    $product->stock_qty      = ($product->is_service == 1) ? 999999 : (float) product_fake_stock_val($product);
                    return $product;
                });
        }

        return response()->json($products);
    }
    
    public function transProductSearchDetails($my_id, $branch_id)
    {
        $data['product'] = Product::where('id', $my_id)->with('unit.related_unit')->where('status', 1)->limit(10)->first();
        $product = Product::with('unit.related_unit')->where('status', 1)->where('is_service', 0)->where('id', $my_id)->first();
        $data['variations'] = Variation::where('product_id', $my_id)->get();
        $data['imeis'] = SerialNumber::where('product_id', $my_id)->where('status', 1)->pluck('serial');
        $data['brand'] = Brand::find($data['product']->brand_id);

        return response()->json($data);
    }

    public function scProductSearch(Request $request)
    {
        $userBranchId   = auth()->user()->branch_id;
        $filterBranchId = $request->branch_id ?? session('branch_filter_id', auth()->user()->branch_id);
        $query          = trim(request('req'));

        // --- Variation barcode resolution ---
        $resolved = resolveProductAndVariationFromBarcode($query);
        if ($resolved['product_id']) {
            $product = Product::where('id', $resolved['product_id'])
                ->with('unit.related_unit')
                ->where('status', 1)
                ->first();
            if ($product) {
                $product->stock_qty_text       = ($product->is_service == 1) ? __('Service Item') : product_stock($product);
                $product->stock_qty            = ($product->is_service == 1) ? 999999 : (float) product_fake_stock_val($product);
                $product->matched_variation_id = $resolved['variation_id'];
                return response()->json([$product]);
            }
        }

        $matchingProductIds = [];
        if (!empty($query) && strlen($query) >= 2) {
            $cleanQuery = trim($query);
            $serialProductIds = SerialNumber::where('serial', 'LIKE', "%{$cleanQuery}%")
                ->pluck('product_id')->toArray();

            $purchaseProductIds = [];
            $invoiceProductIds  = [];
            if (preg_match('/[0-9]{3,}/', $cleanQuery)) {
                $purchaseProductIds = PurchaseItem::where('imei', 'LIKE', "%{$cleanQuery}%")
                    ->pluck('product_id')->toArray();
                $invoiceProductIds  = InvoiceItem::where('imei', 'LIKE', "%{$cleanQuery}%")
                    ->pluck('product_id')->toArray();
            }

            $productImeiIds = Product::where('imei', 'LIKE', "%{$cleanQuery}%")
                ->pluck('id')->toArray();

            $matchingProductIds = array_unique(array_merge($serialProductIds, $purchaseProductIds, $invoiceProductIds, $productImeiIds));
        }

        $activeBranchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;
        $branchProductIds = BranchProduct::where('branch_id', $activeBranchId)->pluck('product_id');

        $productQuery = Product::where(function($q) use ($branchProductIds) {
                $q->whereIn('id', $branchProductIds)
                  ->orWhere('is_service', 1);
            })
            ->with('unit.related_unit')
            ->where('status', 1);

        if (strtolower($query) == 'out of stock') {
            $products = $productQuery->where('is_service', 0)->get()
                ->filter(function ($product) {
                    return (float) product_fake_stock_val($product) <= 0;
                })
                ->take(10)
                ->map(function ($product) {
                    $product->stock_qty_text = product_stock($product);
                    $product->stock_qty = (float) product_fake_stock_val($product);
                    return $product;
                });
        } else {
            $products = $productQuery
                ->where(function ($q) use ($query, $matchingProductIds) {
                    $q->where('name', 'LIKE', "%$query%")
                        ->orWhere('barcode', 'LIKE', "$query%");
                    if (!empty($matchingProductIds)) {
                        $q->orWhereIn('id', $matchingProductIds);
                    }
                })
                ->when(env('APP_IMEI') != 'yes', function ($query) {
                    return $query->where(function ($q) {
                        $q->where('imei', '!=', 1)->orWhereNull('imei');
                    });
                })
                ->limit(10)
                ->get()
                ->map(function ($product) {
                    $product->stock_qty_text = ($product->is_service == 1) ? __('Service Item') : product_stock($product);
                    $product->stock_qty = ($product->is_service == 1) ? 999999 : (float) product_fake_stock_val($product);
                    return $product;
                });
        }
        return response()->json($products);
    }

    public function serialSearch(Request $request)
    {
        $query = request('req');
        $query = trim($query);
        $imeis = SerialNumber::where('status', 1)->where('serial', 'LIKE', "%$query%")->get();
        return response()->json($imeis);
    }


    public function serialSearchDetails($my_id)
    {
        $imeis_first = SerialNumber::where('serial', 'LIKE', "%$my_id%")->orderBy('status', 'DESC')->orderBy('id', 'DESC')->first();
        if (!$imeis_first) {
            $pi = PurchaseItem::where('imei', 'LIKE', "%$my_id%")->first();
            if ($pi) {
                $data['product'] = Product::where('id', $pi->product_id)->with('unit.related_unit')->first();
            }
        } else {
            $data['product'] = Product::where('is_service', 0)->where('id', $imeis_first->product_id)->with('unit.related_unit')->first();
        }

        if (isset($data['product'])) {
            $data['imeis'] = $this->getProductImeis($data['product']->id);
            $data['stock_qty'] = product_stock_check($data['product']);
            
            // Set specific serial warranty if found
            if ($imeis_first) {
                $data['product']->warranty_value = $imeis_first->warranty_value;
                $data['product']->warranty_unit = $imeis_first->warranty_unit;
            }

            return response()->json($data);
        }
        
        return response()->json(['error' => 'Product not found'], 404);
    }

    private function getProductImeis($product_id)
    {
        $userBranchId = auth()->user() ? auth()->user()->branch_id : 1;
        $filterBranchId = session('branch_filter_id', $userBranchId);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        // 1. Query available IMEIs directly from SerialNumber table (status = 1)
        $serialQuery = SerialNumber::where('product_id', $product_id)
            ->where('status', 1);

        if ($branchId) {
            $serialQuery->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        $serialRecords = $serialQuery->get();
        $resultList = [];
        $seenSerials = [];

        foreach ($serialRecords as $sr) {
            $serialStr = trim((string) $sr->serial);
            if ($serialStr === '') continue;

            if (!isset($seenSerials[$serialStr])) {
                $seenSerials[$serialStr] = true;
                $resultList[] = (object) [
                    'serial' => $serialStr,
                    'warranty_value' => $sr->warranty_value ?? '',
                    'warranty_unit'  => $sr->warranty_unit ?? 'Month',
                ];
            }
        }

        // 2. Fallback to PurchaseItem only if SerialNumber table has NO records for this product
        if (empty($resultList) && !SerialNumber::where('product_id', $product_id)->exists()) {
            $soldImeisRaw = InvoiceItem::where('product_id', $product_id)
                ->whereHas('invoice', function ($q) {
                    $q->where('status', '!=', 2);
                })
                ->pluck('imei')
                ->toArray();

            $soldMap = [];
            foreach ($soldImeisRaw as $raw) {
                if (empty($raw)) continue;
                $parts = preg_split('/[,\s\r\n\/]+/', (string) $raw);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if ($p !== '') {
                        $soldMap[$p] = true;
                    }
                }
            }

            $purchaseItemsQuery = PurchaseItem::where('product_id', $product_id)
                ->whereNotNull('imei');

            if ($branchId) {
                $purchaseItemsQuery->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)->orWhereNull('branch_id');
                });
            }

            $purchaseItems = $purchaseItemsQuery->get();

            foreach ($purchaseItems as $pi) {
                $lines = preg_split('/[\r\n]+/', (string) $pi->imei);
                foreach ($lines as $line) {
                    $serialStr = trim($line);
                    if ($serialStr === '') continue;

                    $subParts = preg_split('/[,\s\r\n\/]+/', $serialStr);
                    $isSold = false;
                    foreach ($subParts as $sp) {
                        $sp = trim($sp);
                        if ($sp !== '' && isset($soldMap[$sp])) {
                            $isSold = true;
                            break;
                        }
                    }

                    if (!$isSold && !isset($seenSerials[$serialStr])) {
                        $seenSerials[$serialStr] = true;
                        $resultList[] = (object) [
                            'serial' => $serialStr,
                            'warranty_value' => $pi->warranty_value ?? '',
                            'warranty_unit'  => $pi->warranty_unit ?? 'Month',
                        ];
                    }
                }
            }
        }

        return collect($resultList);
    }

    public function productExchange(Request $request)
    {
        $query = request('req');
        $products = Product::where('name', 'LIKE', "%$query%")->orWhere('barcode', 'LIKE', "%$query%")->get();
        return response()->json($products);
    }

    public function usedProductSearch(Request $request)
    {
        $query = request('req');
        $products = UsedProduct::where('name', 'LIKE', "%$query%")->orWhere('barcode', 'LIKE', "%$query%")->where('status', 1)->limit(10)->get();
        return response()->json($products);
    }

    public function productSearchDetails($my_id)
    {
        $data['product'] = Product::where('id', $my_id)->with('unit.related_unit')->where('status', 1)->limit(10)->first();
        $data['stock_qty'] = product_stock_check($data['product']);
        $data['pure_stock'] = product_fake_stock_val($data['product']);
        $data['brand'] = Brand::find($data['product']->brand_id);

        $data['imeis'] = $this->getProductImeis($my_id);

        // Warranty default: Product's set warranty first, or latest purchase warranty
        if (!empty($data['product']->warranty_value)) {
            $data['warranty_value'] = $data['product']->warranty_value;
            $data['warranty_unit'] = $data['product']->warranty_unit ?? 'Month';
        } else {
            $latestPurchase = PurchaseItem::where('product_id', $my_id)
                ->orderBy('id', 'desc')
                ->first();
            
            if ($latestPurchase && !empty($latestPurchase->warranty_value)) {
                $data['warranty_value'] = $latestPurchase->warranty_value;
                $data['warranty_unit'] = $latestPurchase->warranty_unit;
                $data['product']->warranty_value = $latestPurchase->warranty_value;
                $data['product']->warranty_unit = $latestPurchase->warranty_unit;
            }
        }

        return response()->json($data);
    }

    // public function scproductSearchDetails($my_id)
    // {
    //     $data['product'] = Product::where('id', $my_id)->with('unit.related_unit')->first();
    //     $data['stock_qty'] = product_stock_check($data['product']);
    //     // $data['stock_qty'] = product_stock_check_sub($data['product']);

    //     $variations = $data['product']->product_variations()->orderBy('variation_id')->get();
    //     $dataa = [];

    //     foreach ($variations as $variation) {
    //         // dd($variation);
    //         $dataa[] = [
    //             'id' => $variation->id,
    //             'name' => $variation->product->name,
    //             'size' => $variation->size->size ?? '',
    //             'color' => $variation->color->color ?? '',
    //             'stock' => variation_stock($variation->id)
    //         ];
    //     }
    //     $data['variations'] = $dataa;
    //     return response()->json($data);
    // }
    
public function scproductSearchDetails($my_id)
{
    $request = request();
    $originalFilter = session('branch_filter_id');
    if ($request->branch_id) {
        session(['branch_filter_id' => $request->branch_id]);
    }

    $data['product'] = Product::where('id', $my_id)
        ->with(['unit.related_unit', 'racks'])
        ->first();
    $data['rack_ids'] = $data['product'] ? $data['product']->racks->pluck('id')->toArray() : [];

    $data['stock_qty'] = product_stock_check($data['product']);
    $data['pure_stock'] = product_fake_stock_val($data['product']);

    // Variation গুলো size name অনুযায়ী sort হবে
    $variations = $data['product']->product_variations()
        ->with(['size', 'color'])
        ->leftJoin('product_sizes', 'product_variations.size_id', '=', 'product_sizes.id')
        ->select('product_variations.*')
        ->orderBy('product_sizes.size', 'asc')
        ->get();

    $dataa = [];

    foreach ($variations as $variation) {
        $dataa[] = [
            'id' => $variation->id,
            'name' => $variation->product->name,
            'size' => $variation->size->size ?? '',
            'color' => $variation->color->color ?? '',
            'stock' => variation_stock($variation->id),
        ];
    }

    $data['variations'] = $dataa;
    $data['imeis'] = $this->getProductImeis($my_id);

    // Warranty default: Product's set warranty first, or latest purchase warranty
    if (!empty($data['product']->warranty_value)) {
        $data['warranty_value'] = $data['product']->warranty_value;
        $data['warranty_unit'] = $data['product']->warranty_unit ?? 'Month';
    } else {
        $latestPurchase = PurchaseItem::where('product_id', $my_id)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($latestPurchase && !empty($latestPurchase->warranty_value)) {
            $data['warranty_value'] = $latestPurchase->warranty_value;
            $data['warranty_unit'] = $latestPurchase->warranty_unit;
            $data['product']->warranty_value = $latestPurchase->warranty_value;
            $data['product']->warranty_unit = $latestPurchase->warranty_unit;
        }
    }

    // Restore original filter
    session(['branch_filter_id' => $originalFilter]);

    return response()->json($data);
}




    public function usedProductSearchDetails($my_id)
    {
        $data['product'] = UsedProduct::where('id', $my_id)->with('unit.related_unit')->where('status', 1)->limit(10)->first();
        $product = Product::with('unit.related_unit')->where('status', 1)->where('is_service', 0)->where('id', $my_id)->first();
        $data['stock_qty'] = used_product_stock_check($data['product']);
        // $data['brand'] = Brand::find($data['product']->brand_id);

        return response()->json($data);
    }

    public function productBarcode($code)
    {
        if (empty($code)) {
            $code = '00000000';
        }
        try {
            $generatorSVG = new BarcodeGeneratorSVG();
            $barcode = $generatorSVG->getBarcode($code, $generatorSVG::TYPE_CODE_128, 1.3, 20);
            return response()->json($barcode);
        } catch (\Exception $e) {
            return response()->json('');
        }
    }

    public function productUnit($my_id)
    {
        $unit = Unit::where('id', $my_id)->with('related_unit')->first();
        if ($unit->related_unit != NULL) {
            return response()->json($unit);
        }
    }

    public function posProducts(Request $request)
    {
        $userBranchId   = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $activeBranchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;
        $showImei       = trim(strtolower(env('APP_IMEI'))) === 'yes';

        $productIds = \App\Models\BranchProduct::where('branch_id', $activeBranchId)->pluck('product_id');

        $query = Product::where('status', 1)
            ->where('is_service', 0)
            ->whereIn('id', $productIds)
            ->where('category_id', $request->cat_id)
            ->with('unit:id,name,related_unit_id,related_value', 'unit.related_unit:id,name');

        if (!$showImei) {
            $query->where(function ($q) {
                $q->where('imei', '!=', 1)->orWhereNull('imei');
            });
        }

        $data['products'] = $query->orderBy('name', 'ASC')->paginate(12);

        return view('backend.pages.invoice.scat-products', $data);
    }

    public function productPosDetails($my_id)
    {
        $data['product'] = Product::where('id', $my_id)->with('unit.related_unit')->first();
        $data['stock_qty'] = product_stock_check($data['product']);
        $data['brand'] = Brand::find($data['product']->brand_id);
        $data['imeis'] = $this->getProductImeis($my_id);
        
        // Warranty default: Product's set warranty first, or latest purchase warranty
        if (!empty($data['product']->warranty_value)) {
            $data['warranty_value'] = $data['product']->warranty_value;
            $data['warranty_unit'] = $data['product']->warranty_unit ?? 'Month';
        } else {
            $latestPurchase = PurchaseItem::where('product_id', $my_id)
                ->orderBy('id', 'desc')
                ->first();
            
            if ($latestPurchase && !empty($latestPurchase->warranty_value)) {
                $data['warranty_value'] = $latestPurchase->warranty_value;
                $data['warranty_unit'] = $latestPurchase->warranty_unit;
                $data['product']->warranty_value = $latestPurchase->warranty_value;
                $data['product']->warranty_unit = $latestPurchase->warranty_unit;
            }
        }

        return response()->json($data);
    }
    public function productScPosDetails($my_id)
    {
        try {
            $data['product'] = Product::where('id', $my_id)->with('unit.related_unit')->first();
            $data['stock_qty'] = product_stock($data['product']);
            $variations = $data['product']->product_variations()->with(['size', 'color'])->orderBy('variation_id')->get();
            $dataa = [];
     
            foreach ($variations as $variation) {
                $dataa[] = [
                    'id' => $variation->id,
                    'name' => $variation->product->name ?? $data['product']->name,
                    'size' => $variation->size->size ?? '',
                    'color' => $variation->color->color ?? '',
                    'stock' => variation_stock($variation->id)
                ];
            }
            $data['variations'] = $dataa;
            $data['imeis'] = $this->getProductImeis($my_id);
    
            // Warranty default: Product's set warranty first, or latest purchase warranty
            if (!empty($data['product']->warranty_value)) {
                $data['warranty_value'] = $data['product']->warranty_value;
                $data['warranty_unit'] = $data['product']->warranty_unit ?? 'Month';
            } else {
                $latestPurchase = PurchaseItem::where('product_id', $my_id)
                    ->orderBy('id', 'desc')
                    ->first();
                
                if ($latestPurchase && !empty($latestPurchase->warranty_value)) {
                    $data['warranty_value'] = $latestPurchase->warranty_value;
                    $data['warranty_unit'] = $latestPurchase->warranty_unit;
                    $data['product']->warranty_value = $latestPurchase->warranty_value;
                    $data['product']->warranty_unit = $latestPurchase->warranty_unit;
                }
            }
    
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getToAccount(Request $request)
    {
        $from_bank_id = $request->from_bank_id;
        $data = BankAccount::whereNot('id', $from_bank_id)->get();
        return response()->json($data);
    }

    public function getAccountBalance(Request $request)
    {
        $bank_id = $request->bank_id;
        $data['balance'] = current_balance($bank_id);
        return response()->json($data);
    }

    public function getCustomerAccountBalance($my_id)
    {
        $customer = Customer::where('id', $my_id)->first();
        $due_invoice = Invoice::where('customer_id', $my_id)->where('status', 0)->count('id');
        $invoice_due = Invoice::where('customer_id', $my_id)->where('status', 0)->sum('total_due');
        $data['customer_name'] = $customer->name;
        $data['due_invoice'] = $due_invoice;
        $data['invoice_due'] = $invoice_due;
        $data['walletBalance'] = open_balance_customer($my_id, $customer->due_amount);
        $data['total_due'] = $data['invoice_due'] + $data['walletBalance'];
        return response()->json($data);
    }

    public function getSupplierAccountBalance($my_id)
    {
        $supplier = Supplier::where('id', $my_id)->first();
        $due_purchase = Purchase::where('supplier_id', $my_id)->where('status', 0)->count('id');
        $purchase_due = Purchase::where('supplier_id', $my_id)->where('status', 0)->sum('total_due');
        $data['supplier_name'] = $supplier->name;
        $data['due_purchase'] = $due_purchase;
        $data['purchase_due'] = $purchase_due;
        $data['walletBalance'] = open_balance_supplier($my_id, $supplier->due_amount, $supplier->advance_amount);
        return response()->json($data);
    }
    public function findCustomerDue(Request $request)
    {
        $customerId = $request->customer_id;
        $customer = Customer::where('id', $customerId)->first();
        $openingDue =  open_balance_customer($customerId, $customer->open_receivable, $customer->open_payable);
        $invDue = Invoice::where('customer_id', $customerId)->where('status', 0)->sum('total_due');

        $totalDue = $openingDue + $invDue;

        return response()->json(['total_due' => $totalDue]);
    }

    public function getCustomerVehicles(Request $request)
    {
        $customerId = $request->customer_id;
        $vehicles = \App\Models\Vehicle::where('customer_id', $customerId)
            ->whereNotNull('reg_no')
            ->where('reg_no', '!=', '')
            ->select('id', 'vehicle_name', 'reg_no', 'model')
            ->get();
        return response()->json(['vehicles' => $vehicles]);
    }

    public function downloadBackup(Request $request)
    {
        if (env('APP_MODE') == 'demo') {
            session()->flash('error', 'This Feature is not available in Demo');
            return back();
        }
        $files = Storage::files(config('app.name'));
        foreach ($files as $file) {
            Storage::delete($file);
        }

        Artisan::call('backup:run', ['--only-db' => true]);
        $files = Storage::files(config('app.name'));
        if ($files != null) {
            logActivity('Download Backup', "Database backup file downloaded: " . $files[0]);
            return Storage::download($files[0]);
        } else {
            session()->flash('warning', 'Database not backed up');
            return back();
        }
    }

    public function checkImeiDuplicates(Request $request)
    {
        $imeis = $request->imeis;
        if (!is_array($imeis)) {
            $imeis = [];
        }
        $excludePurchaseId = $request->exclude_purchase_id;

        // Only check IMEIs that are currently active in stock (status = 1)
        // Sold IMEIs (status = 0) can be repurchased when customers sell back or exchange
        $query = \App\Models\SerialNumber::where('status', 1);
        if ($excludePurchaseId) {
            $query->where('purchase_id', '!=', $excludePurchaseId);
        }

        $allSerials = $query->pluck('serial')->toArray();
        $duplicates = [];

        foreach ($imeis as $token) {
            $token = trim($token);
            if (empty($token)) continue;
            foreach ($allSerials as $serialStr) {
                $existingTokens = array_filter(array_map('trim', preg_split('/[\s,\/]+/', $serialStr)));
                if (in_array($token, $existingTokens)) {
                    $duplicates[] = $token;
                    break;
                }
            }
        }

        return response()->json(['duplicates' => array_values(array_unique($duplicates))]);
    }

    public function getInvoiceItemCandidateImeis(Request $request)
    {
        $productId = $request->product_id;
        $invoiceId = $request->invoice_id;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        if ($invoiceId) {
            $invoice = \App\Models\Invoice::find($invoiceId);
            if ($invoice) {
                $branchId = $invoice->branch_id;
            }
        }

        // 1. Get currently selected IMEIs for this invoice and product
        $currentImeis = [];
        if ($invoiceId) {
            $invoiceItem = \App\Models\InvoiceItem::where('invoice_id', $invoiceId)
                ->where('product_id', $productId)
                ->first();
            if ($invoiceItem && !empty($invoiceItem->imei)) {
                $currentImeis = array_filter(array_map('trim', preg_split('/[\n,]+/', $invoiceItem->imei)));
            }
        }

        // 2. Get available IMEIs (status = 1) in the current branch
        $availableImeis = \App\Models\SerialNumber::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->pluck('serial')
            ->toArray();

        return response()->json([
            'current' => $currentImeis,
            'available' => $availableImeis
        ]);
    }

    public function getQuotationItemCandidateImeis(Request $request)
    {
        $productId = $request->product_id;
        $quotationId = $request->quotation_id;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        if ($quotationId) {
            $quotation = \App\Models\Quotation::find($quotationId);
            if ($quotation) {
                $branchId = $quotation->branch_id;
            }
        }

        // 1. Get currently selected IMEIs for this quotation and product
        $currentImeis = [];
        if ($quotationId) {
            $quotationItem = \App\Models\QuotationItem::where('quotation_id', $quotationId)
                ->where('product_id', $productId)
                ->first();
            if ($quotationItem && !empty($quotationItem->imei)) {
                $currentImeis = array_filter(array_map('trim', preg_split('/[\n,]+/', $quotationItem->imei)));
            }
        }

        // 2. Get available IMEIs (status = 1) in the current branch
        $availableImeis = \App\Models\SerialNumber::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->pluck('serial')
            ->toArray();

        return response()->json([
            'current' => $currentImeis,
            'available' => $availableImeis
        ]);
    }
}


