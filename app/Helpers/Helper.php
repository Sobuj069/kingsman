<?php

use App\Models\Damage;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\UsedItem;
use App\Models\DamageItem;
use App\Models\ReturnItem;
use App\Models\AdjustStock;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\PurchaseItem;
use App\Models\TransferItem;
use App\Models\AdjustStockItem;
use App\Models\BankTransaction;
use App\Models\BusinessSetting;
use App\Models\UsedPurchaseItem;
use App\Models\ReturnPurchaseItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Picqer\Barcode\BarcodeGeneratorSVG;

function slugify($text)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'utf-8//IGNORE', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

//get all route list
function get_route_list()
{
    //get all routes
    $routes = Route::getRoutes();
    $routeList = [];
    foreach ($routes as $route) {
        $routeName = explode('.', $route->getName());
        if (isset($routeName[1])) {
            $routeList[$routeName[0]][] = $routeName[1];
        }
    }
    //remove duplicate routes
    foreach ($routeList as $key => $value) {
        $routeList[$key] = array_unique($value);
    }
    // return response()->json($routeList);

    //remove unnecessary routes
    unset($routeList['login']);
    unset($routeList['logout']);
    unset($routeList['register']);
    unset($routeList['password']);
    unset($routeList['verification']);
    unset($routeList['password']);
    unset($routeList['user-profile-information']);
    unset($routeList['user-password']);
    unset($routeList['two-factor']);
    unset($routeList['profile']);
    unset($routeList['sanctum']);
    unset($routeList['livewire']);
    unset($routeList['ignition']);
    unset($routeList['store']);
    unset($routeList['get']);
    unset($routeList['expense-category']);
    unset($routeList['user-role']);
    unset($routeList['branch']);
    unset($routeList['switch']);
    //sort ascending
    ksort($routeList);


    //set all routes to false
    foreach ($routeList as $key => $value) {
        $routeList[$key] = array_fill_keys($value, false);
    }

    return $routeList;
}

function check_permission($routeName)
{
    if (!auth()->check()) {
        return false;
    }

    $user = auth()->user();

    // If branch switching is globally disabled, block switch.branch for all users
    if ($routeName === 'switch.branch' || $routeName === 'switch') {
        if (!is_branch_switch_enabled()) {
            return false;
        }
    }

    // Super Admin or Admin role always has full permissions
    if ($user->isSuperAdmin() || ($user->role && in_array(strtolower(trim($user->role->name)), ['admin', 'super admin', 'superadmin']))) {
        return true;
    }

    if (!$user->role) {
        return false;
    }

    $parts = is_array($routeName) ? $routeName : explode('.', $routeName);
    if (!isset($parts[1])) return true;

    $module = $parts[0];
    $action = $parts[1];

    // Public/basic modules and utilities that do not require explicit role permissions
    $publicModules = [
        'login', 'logout', 'register', 'password', 'verification',
        'user-profile-information', 'user-password', 'two-factor',
        'profile', 'sanctum', 'livewire', 'ignition', 'store', 'get',
        'expense-category', 'user-role', 'otp', 'branch', 'switch',
        'customer_quick_history', 'product-search', 'search-product-id',
        'sc-product-search', 'sc-search-product-id', 'sc-pos-product-id',
        'posProducts', 'barcode'
    ];

    if (in_array($module, $publicModules)) {
        if ($module === 'switch' && !is_branch_switch_enabled()) {
            return false;
        }
        return true;
    }

    $authUserPermissions = $user->role->permission;
    if (is_string($authUserPermissions)) {
        $authUserPermissions = json_decode($authUserPermissions, true);
    }

    if (!is_array($authUserPermissions)) {
        return false;
    }

    // 1. Check direct [module][action]
    if (isset($authUserPermissions[$module][$action])) {
        $val = $authUserPermissions[$module][$action];
        if ($val === true || $val === 'true' || $val === 1 || $val === '1') {
            return true;
        }
    }

    // 2. Check full sub-action if route has 3 or more parts (e.g. invoice.hold.store -> hold or store)
    if (isset($parts[2])) {
        $subAction = $parts[2];
        if (isset($authUserPermissions[$module][$subAction])) {
            $val = $authUserPermissions[$module][$subAction];
            if ($val === true || $val === 'true' || $val === 1 || $val === '1') {
                return true;
            }
        }
    }

    // 3. Fallback for related invoice sub-routes (hold, offline-data, barcode, pay, exchange)
    if ($module === 'invoice' && in_array($action, ['hold', 'offline-data', 'barcode', 'pay', 'exchange'])) {
        if (!empty($authUserPermissions['invoice']['create']) || !empty($authUserPermissions['invoice']['index']) || !empty($authUserPermissions['invoice']['store'])) {
            return true;
        }
    }

    return false;
}

function main_menu_permission($menuName)
{
    if (!auth()->check()) {
        return false;
    }

    $user = auth()->user();
    if ($user->isSuperAdmin() || ($user->role && in_array(strtolower(trim($user->role->name)), ['admin', 'super admin', 'superadmin']))) {
        return true;
    }

    if (!$user->role) {
        return false;
    }

    $authUserPermissions = $user->role->permission;
    if (is_string($authUserPermissions)) {
        $authUserPermissions = json_decode($authUserPermissions, true);
    }

    if (isset($authUserPermissions[$menuName]) && is_array($authUserPermissions[$menuName])) {
        foreach ($authUserPermissions[$menuName] as $key => $value) {
            if ($value === true || $value === 'true' || $value === 1 || $value === '1') {
                return true;
            }
        }
        return false;
    }

    return false;
}


if (!function_exists('get_setting')) {
    function get_setting($key, $default = "")
    {
        try {
            $settings = Cache::remember('business_settings', 86400, function () {
                return BusinessSetting::all();
            });

            $setting = $settings->where('type', $key)->first();

            return $setting?->value ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}

if (!function_exists('get_hotline_phone')) {
    function get_hotline_phone()
    {
        return get_setting('com_phone') ?: '01987258406';
    }
}

if (!function_exists('get_whatsapp_phone')) {
    function get_whatsapp_phone()
    {
        return get_setting('com_whatsapp') ?: get_setting('com_phone') ?: '01987258406';
    }
}

if (!function_exists('format_wa_link')) {
    function format_wa_link($phone = null, $customMsg = '')
    {
        $raw = !empty($phone) ? $phone : get_whatsapp_phone();
        $digits = preg_replace('/[^0-9]/', '', $raw);
        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = '88' . $digits;
        } elseif (!str_starts_with($digits, '88') && !empty($digits) && strlen($digits) <= 11) {
            $digits = '88' . $digits;
        }
        $msg = !empty($customMsg) ? '?text=' . urlencode($customMsg) : '';
        return 'https://wa.me/' . $digits . $msg;
    }
}

if (!function_exists('get_frontend_showrooms')) {
    function get_frontend_showrooms()
    {
        $raw = get_setting('showrooms_list');
        if (!empty($raw)) {
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
            if (is_array($decoded) && count($decoded) > 0) {
                $cleaned = array_values(array_filter($decoded, function ($item) {
                    return !empty($item['name']) || !empty($item['address']);
                }));
                if (!empty($cleaned)) {
                    return collect($cleaned)->map(function ($item) {
                        return (object) [
                            'name' => $item['name'] ?? '',
                            'address' => $item['address'] ?? '',
                            'phone' => $item['phone'] ?? '',
                            'shop_name' => $item['shop_name'] ?? ($item['tag'] ?? ''),
                        ];
                    });
                }
            }
        }

        // Default initial showroom data
        $defaults = [
            [
                'name' => 'Mirpur 2 Showroom',
                'address' => 'Shop #115, 1st Floor, Mirpur 2 Shopping Complex, Dhaka',
                'phone' => '01987258406',
                'shop_name' => 'Mirpur 2',
            ],
            [
                'name' => 'Paltan Showroom',
                'address' => 'Shop #21-22, 1st Floor, Polwel Carnation, VIP Road, Dhaka',
                'phone' => '01987258405',
                'shop_name' => 'Polwel Carnation',
            ],
            [
                'name' => 'Bashundhara City Showroom',
                'address' => 'Shop #41, Block B, Level 3, Panthapath, Dhaka',
                'phone' => '01987258406',
                'shop_name' => 'Bashundhara City',
            ],
            [
                'name' => 'Chittagong Showroom',
                'address' => 'Sanmar Ocean City, GEC Circle, Chattogram',
                'phone' => '01987258406',
                'shop_name' => 'Sanmar Ocean City',
            ],
            [
                'name' => 'Jamuna Future Park Showroom',
                'address' => 'Shop #2C-010, Block C, Level 2, Dhaka',
                'phone' => '01987258405',
                'shop_name' => 'Jamuna Future Park',
            ],
            [
                'name' => 'Gazipur Showroom',
                'address' => 'Joydebpur Chowrasta, Gazipur',
                'phone' => '01987258406',
                'shop_name' => 'Joydebpur Chowrasta',
            ],
        ];

        return collect($defaults)->map(fn($item) => (object) $item);
    }
}



//invoiced_qty
if (!function_exists('invoiced_qty')) {
    function invoiced_qty($product)
    {
        if ($product->is_service == 0) {
            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
            if ($product->unit->related_unit  == null) {
                $query = InvoiceItem::with('invoice')
                    ->where('product_id', $product->id);

                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $query->where('branch_id', $filterBranchId);
                    }
                } else {
                    $query->where('branch_id', $userBranchId);
                }
                
                $inv_stock = InvoiceItem::filterByFakeSale($query)->sum('actual_main');
                $total_stock = $inv_stock;
                $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
            } else {
                //invoice
                $query = InvoiceItem::with('invoice')
                    ->where('product_id', $product->id);

                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $query->where('branch_id', $filterBranchId);
                    }
                } else {
                    $query->where('branch_id', $userBranchId);
                }

                $filteredItems = InvoiceItem::filterByFakeSale($query);
                $inv_stock_main = $filteredItems->sum('actual_main');
                $inv_total_sub = $filteredItems->sum('actual_sub');
                
                $inv_total_main = (float)($inv_stock_main * $product->unit->related_value);

                $inv_total_stock = (float)($inv_total_main + $inv_total_sub);
                $total_stock = $inv_total_stock;
                $check = $total_stock / $product->unit->related_value;
                if (is_integer($check)) {
                    $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
                } else {
                    $main_value = floor($check) * $product->unit->related_value;
                    $sub_value = $total_stock - $main_value;
                    $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
                }
            }
            return $data['stock_qty'];
        }
    }
}
//used product qty
if (!function_exists('used_qty')) {
    function used_qty($product)
    {
        if ($product->unit->related_unit  == null) {
            $inv_stock = UsedItem::where('product_id', $product->id)
                ->sum('main_qty');
            $total_stock = $inv_stock;
            $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
        } else {
            //invoice
            $inv_stock_main = UsedItem::with('invoice')
                ->where('product_id', $product->id)
                ->sum('main_qty');
            $inv_total_main = (float)($inv_stock_main * $product->unit->related_value);
            $inv_total_sub = UsedItem::with('invoice')
                ->where('product_id', $product->id)
                ->sum('sub_qty');
            $inv_total_stock = (float)($inv_total_main + $inv_total_sub);
            $total_stock = $inv_total_stock;
            $check = $total_stock / $product->unit->related_value;
            if (is_integer($check)) {
                $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
            } else {
                $main_value = floor($check) * $product->unit->related_value;
                $sub_value = $total_stock - $main_value;
                $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
            }
        }
        return $data['stock_qty'];
    }
}

//returned_qty
if (!function_exists('returned_qty')) {
    function returned_qty($product)
    {
        if ($product->is_service == 0) {
            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

            $query = ReturnItem::with('return.invoice')
                ->where('product_id', $product->id);

            if ($userBranchId == 1) {
                if ($filterBranchId) {
                    $query->where('branch_id', $filterBranchId);
                }
            } else {
                $query->where('branch_id', $userBranchId);
            }

            if ($product->unit->related_unit  == null) {
                $inv_stock = ReturnItem::filterByFakeSale($query)->sum('main_qty');
                $total_stock = $inv_stock;
                $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
            } else {
                //return
                $filteredItems = ReturnItem::filterByFakeSale($query);
                $inv_stock_main = $filteredItems->sum('main_qty');
                $inv_total_sub = $filteredItems->sum('sub_qty');

                $inv_total_main = (float)($inv_stock_main * $product->unit->related_value);
                $inv_total_stock = (float)($inv_total_main + $inv_total_sub);
                $total_stock = $inv_total_stock;
                $check = $total_stock / $product->unit->related_value;
                if (is_integer($check)) {
                    $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
                } else {
                    $main_value = floor($check) * $product->unit->related_value;
                    $sub_value = $total_stock - $main_value;
                    $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
                }
            }
            return $data['stock_qty'];
        }
    }
}

if (!function_exists('return_pur_qty')) {
    function return_pur_qty($product)
    {
        if ($product->is_service == 0) {

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
            if ($product->unit->related_unit  == null) {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $pur_stock = ReturnPurchaseItem::where('branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('main_qty');
                    } else {
                        $pur_stock = ReturnPurchaseItem::where('product_id', $product->id)
                            ->sum('main_qty');
                    }
                } else {
                    $pur_stock = ReturnPurchaseItem::where('branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('main_qty');
                }
                $total_stock = $pur_stock;
                $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
            } else {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $inv_stock_main = ReturnPurchaseItem::where('branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('main_qty');
                        $inv_total_sub = ReturnPurchaseItem::where('branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('sub_qty');
                    } else {
                        $inv_stock_main = ReturnPurchaseItem::where('product_id', $product->id)
                            ->sum('main_qty');
                        $inv_total_sub = ReturnPurchaseItem::where('product_id', $product->id)
                            ->sum('sub_qty');
                    }
                } else {
                    $inv_stock_main = ReturnPurchaseItem::where('branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('main_qty');
                    $inv_total_sub = ReturnPurchaseItem::where('branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('sub_qty');
                }
                //return

                $inv_total_main = (float)($inv_stock_main * $product->unit->related_value);
                $inv_total_stock = (float)($inv_total_main + $inv_total_sub);
                $total_stock = $inv_total_stock;
                $check = $total_stock / $product->unit->related_value;
                if (is_integer($check)) {
                    $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
                } else {
                    $main_value = floor($check) * $product->unit->related_value;
                    $sub_value = $total_stock - $main_value;
                    $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
                }
            }
            return $data['stock_qty'];
        }
    }
}

// transfer quantity
if (!function_exists('stock_transfer_qty')) {
    function stock_transfer_qty($product)
    {
        if ($product->is_service == 0) {

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

            if ($product->unit->related_unit  == null) {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $transfer_stock = TransferItem::where('from_branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('main_qty');
                    } else {
                        $transfer_stock = TransferItem::where('product_id', $product->id)
                            ->sum('main_qty');
                    }
                } else {
                    $transfer_stock = TransferItem::where('from_branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('main_qty');
                }
                $total_stock = $transfer_stock;
                $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
            } else {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $inv_stock_main = TransferItem::where('from_branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('main_qty');
                        $inv_total_sub = TransferItem::where('from_branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('sub_qty');
                    } else {
                        $inv_stock_main = TransferItem::where('product_id', $product->id)
                            ->sum('main_qty');
                        $inv_total_sub = TransferItem::where('product_id', $product->id)
                            ->sum('sub_qty');
                    }
                } else {
                    $inv_stock_main = TransferItem::where('from_branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('main_qty');
                    $inv_total_sub = TransferItem::where('from_branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('sub_qty');
                }
                //return

                $inv_total_main = (float)($inv_stock_main * $product->unit->related_value);
                $inv_total_stock = (float)($inv_total_main + $inv_total_sub);
                $total_stock = $inv_total_stock;
                $check = $total_stock / $product->unit->related_value;
                if (is_integer($check)) {
                    $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
                } else {
                    $main_value = floor($check) * $product->unit->related_value;
                    $sub_value = $total_stock - $main_value;
                    $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
                }
            }
            return $data['stock_qty'];
        }
    }
}

// transfer receive quantity
if (!function_exists('stock_receive_qty')) {
    function stock_receive_qty($product)
    {
        if ($product->is_service == 0) {

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

            if ($product->unit->related_unit  == null) {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $transfer_stock = TransferItem::where('status', 1)->where('to_branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('main_qty');
                    } else {
                        $transfer_stock = TransferItem::where('status', 1)->where('product_id', $product->id)
                            ->sum('main_qty');
                    }
                } else {
                    $transfer_stock = TransferItem::where('status', 1)->where('to_branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('main_qty');
                }
                $total_stock = $transfer_stock;
                $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
            } else {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $inv_stock_main = TransferItem::where('status', 1)->where('to_branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('main_qty');
                        $inv_total_sub = TransferItem::where('status', 1)->where('to_branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('sub_qty');
                    } else {
                        $inv_stock_main = TransferItem::where('status', 1)->where('product_id', $product->id)
                            ->sum('main_qty');
                        $inv_total_sub = TransferItem::where('status', 1)->where('product_id', $product->id)
                            ->sum('sub_qty');
                    }
                } else {
                    $inv_stock_main = TransferItem::where('status', 1)->where('to_branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('main_qty');
                    $inv_total_sub = TransferItem::where('status', 1)->where('to_branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('sub_qty');
                }
                //return

                $inv_total_main = (float)($inv_stock_main * $product->unit->related_value);
                $inv_total_stock = (float)($inv_total_main + $inv_total_sub);
                $total_stock = $inv_total_stock;
                $check = $total_stock / $product->unit->related_value;
                if (is_integer($check)) {
                    $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
                } else {
                    $main_value = floor($check) * $product->unit->related_value;
                    $sub_value = $total_stock - $main_value;
                    $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
                }
            }
            return $data['stock_qty'];
        }
    }
}

//damaged_qty
if (!function_exists('damaged_qty')) {
    function damaged_qty($product)
    {
        if ($product->is_service == 0) {

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

            if ($product->unit->related_unit  == null) {

                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $damage_stock = DamageItem::where('branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('main_qty');
                    } else {
                        $damage_stock = DamageItem::where('product_id', $product->id)
                            ->sum('main_qty');
                    }
                } else {
                    $damage_stock = DamageItem::where('branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('main_qty');
                }


                $total_stock = $damage_stock;
                $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
            } else {

                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $damage_stock_main = DamageItem::where('branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('main_qty');
                        $inv_total_sub = DamageItem::where('branch_id', $filterBranchId)->where('product_id', $product->id)
                            ->sum('sub_qty');
                    } else {
                        $damage_stock_main = DamageItem::where('product_id', $product->id)
                            ->sum('main_qty');
                        $inv_total_sub = DamageItem::where('product_id', $product->id)
                            ->sum('sub_qty');
                    }
                } else {
                    $damage_stock_main = DamageItem::where('branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('main_qty');
                    $inv_total_sub = DamageItem::where('branch_id', $userBranchId)->where('product_id', $product->id)
                        ->sum('sub_qty');
                }

                $inv_total_main = (float)($damage_stock_main * $product->unit->related_value);
                $inv_total_stock = (float)($inv_total_main + $inv_total_sub);
                $total_stock = $inv_total_stock;
                $check = $total_stock / $product->unit->related_value;
                if (is_integer($check)) {
                    $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
                } else {
                    $main_value = floor($check) * $product->unit->related_value;
                    $sub_value = $total_stock - $main_value;
                    $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
                }
            }
            return $data['stock_qty'];
        }
    }
}
if (!function_exists('adjust_in')) {
    function adjust_in($product)
    {
        if ($product->is_service == 0) {

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

            // Stock query builder
            $query = AdjustStockItem::where('product_id', $product->id)
                ->where('stock_status', 1); // ✅ শুধু stock_status=1

            // Branch filter
            if ($userBranchId != 1) {
                $query->where('branch_id', $userBranchId);
            } elseif ($filterBranchId) {
                $query->where('branch_id', $filterBranchId);
            }

            if ($product->unit->related_unit == null) {
                // Sub unit নেই → normal
                $total_stock = $query->sum('main_qty');
                $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
            } else {
                // Sub unit আছে → main + sub convert
                $damage_stock_main = $query->sum('main_qty');
                $damage_stock_sub = $query->sum('sub_qty');

                $total_main_qty = $damage_stock_main * $product->unit->related_value;
                $total_qty = $total_main_qty + $damage_stock_sub;

                $check = $total_qty / $product->unit->related_value;

                if (is_integer($check)) {
                    $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
                } else {
                    $main_value = floor($check) * $product->unit->related_value;
                    $sub_value = $total_qty - $main_value;
                    $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
                }
            }

            return $data['stock_qty'];
        }
    }
}

if (!function_exists('adjust_out')) {
    function adjust_out($product)
    {
        if ($product->is_service == 0) {

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

            // Stock query builder
            $query = AdjustStockItem::where('product_id', $product->id)
                ->where('stock_status', 0); // ✅ শুধু stock_status=1

            // Branch filter
            if ($userBranchId != 1) {
                $query->where('branch_id', $userBranchId);
            } elseif ($filterBranchId) {
                $query->where('branch_id', $filterBranchId);
            }

            if ($product->unit->related_unit == null) {
                // Sub unit নেই → normal
                $total_stock_out = $query->sum('main_qty');
                $data['stock_qty'] = $total_stock_out . ' ' . $product->unit->name;
            } else {
                // Sub unit আছে → main + sub convert
                $total_main = $query->sum('main_qty') * $product->unit->related_value;
                $total_sub = $query->sum('sub_qty');
                $total_qty = $total_main + $total_sub;

                $check = $total_qty / $product->unit->related_value;

                if (is_integer($check)) {
                    $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
                } else {
                    $main_value = floor($check) * $product->unit->related_value;
                    $sub_value = $total_qty - $main_value;
                    $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
                }
            }

            return $data['stock_qty'];
        }
    }
}

//purchased_qty
if (!function_exists('purchased_qty')) {
    function purchased_qty($product)
    {
        if ($product->is_service == 0) {

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

            if ($product->unit->related_unit  == null) {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $pur_stock = PurchaseItem::with('purchase')
                            ->where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)
                            ->sum('main_qty');
                        $total_stock = (float)($pur_stock);
                        $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
                    } else {
                        $pur_stock = PurchaseItem::with('purchase')
                            ->where('product_id', $product->id)
                            ->sum('main_qty');
                        $total_stock = (float)($pur_stock);
                        $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
                    }
                } else {
                    $pur_stock = PurchaseItem::with('purchase')
                        ->where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)
                        ->sum('main_qty');
                    $total_stock = (float)($pur_stock);
                    $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
                }
            } else {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $pur_stock_main = PurchaseItem::with('purchase')
                            ->where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)
                            ->sum('main_qty');
                        $pur_stock_sub = PurchaseItem::with('purchase')
                            ->where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)
                            ->sum('sub_qty');
                    } else {
                        $pur_stock_main = PurchaseItem::with('purchase')
                            ->where('product_id', $product->id)
                            ->sum('main_qty');
                        $pur_stock_sub = PurchaseItem::with('purchase')
                            ->where('product_id', $product->id)
                            ->sum('sub_qty');
                    }
                } else {
                    $pur_stock_main = PurchaseItem::with('purchase')
                        ->where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)
                        ->sum('main_qty');
                    $pur_stock_sub = PurchaseItem::with('purchase')
                        ->where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)
                        ->sum('sub_qty');
                }

                $main_pro =  $pur_stock_main;
                $pur_total_main = (float)($main_pro * $product->unit->related_value);

                $pur_total_stock = (float)($pur_total_main  + $pur_stock_sub);
                $total_stock = $pur_total_stock;
                $check = $total_stock / $product->unit->related_value;
                if (is_integer($check)) {
                    $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
                } else {
                    $main_value = floor($check) * $product->unit->related_value;
                    $sub_value = $total_stock - $main_value;
                    $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
                }
            }
            return $data['stock_qty'];
        }
        // if ($product->is_service == 0) {
        //     if ($product->unit->related_unit  == null) {
        //         $pur_stock = PurchaseItem::with('purchase')
        //             ->where('product_id', $product->id)
        //             ->sum('main_qty');
        //         // $open_pro_stock = Product::where('id', $product->id)->first()->main_qty;
        //         $total_stock = $pur_stock;
        //         $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
        //     } else {
        //         //purchase
        //         $pur_stock_main = PurchaseItem::with('purchase')
        //             ->where('product_id', $product->id)
        //             ->sum('main_qty');
        //         // $open_main_pro_stock = Product::where('id', $product->id)->first()->main_qty;
        //         $main_pro =  $pur_stock_main;
        //         $pur_total_main = (float)($main_pro * $product->unit->related_value);
        //         $pur_stock_sub = PurchaseItem::with('purchase')
        //             ->where('product_id', $product->id)
        //             ->sum('sub_qty');
        //         // $open_main_pro_stock = Product::where('id', $product->id)->first()->sub_qty;
        //         $pur_total_stock = (float)($pur_total_main  + $pur_stock_sub);
        //         $total_stock = $pur_total_stock;
        //         $check = $total_stock / $product->unit->related_value;
        //         if (is_integer($check)) {
        //             $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
        //         } else {
        //             $main_value = floor($check) * $product->unit->related_value;
        //             $sub_value = $total_stock - $main_value;
        //             $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
        //         }
        //     }
        //     return $data['stock_qty'];
        // }
    }
}

//used product purchased_qty
if (!function_exists('used_purchased_qty')) {
    function used_purchased_qty($product)
    {
        if ($product->unit->related_unit  == null) {
            $pur_stock = UsedPurchaseItem::where('product_id', $product->id)
                ->sum('main_qty');
            $total_stock = $pur_stock;
            $data['stock_qty'] = $total_stock . ' ' . $product->unit->name;
        } else {
            $pur_stock_main = UsedPurchaseItem::where('product_id', $product->id)
                ->sum('main_qty');
            $main_pro =  $pur_stock_main;
            $pur_total_main = (float)($main_pro * $product->unit->related_value);
            $pur_stock_sub = UsedPurchaseItem::where('product_id', $product->id)
                ->sum('sub_qty');
            $pur_total_stock = (float)($pur_total_main  + $pur_stock_sub);
            $total_stock = $pur_total_stock;
            $check = $total_stock / $product->unit->related_value;
            if (is_integer($check)) {
                $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
            } else {
                $main_value = floor($check) * $product->unit->related_value;
                $sub_value = $total_stock - $main_value;
                $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
            }
        }
        return $data['stock_qty'];
    }
}

if (!function_exists('product_stock')) {
    function product_stock($product)
    {
        if (!$product || $product->is_service == 1) {
            return 'Service Product';
        }

        $fakeStock = product_fake_stock_val($product);
        $factor = ($product->unit && $product->unit->related_value) ? (float)$product->unit->related_value : 1;

        // Case 1: No sub-unit (just unit)
        if (!$product->unit || $product->unit->related_unit == null) {
            return (float) $fakeStock . ' ' . ($product->unit->name ?? 'pcs');
        }

        // Case 2: Has sub-unit
        $main_qty = floor($fakeStock / $factor);
        $sub_qty  = fmod($fakeStock, $factor);

        if ($sub_qty == 0) {
            return $main_qty . ' ' . $product->unit->name;
        } else {
            return $main_qty . ' ' . $product->unit->name . ' ' .
                $sub_qty . ' ' . $product->unit->related_unit->name;
        }
    }
}

if (!function_exists('product_fake_stock_val')) {
    function product_fake_stock_val($product, $branchId = null)
    {
        if (!$product || $product->is_service == 1) {
            return 0;
        }

        if ($branchId === null) {
            $userBranchId   = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', null);
            if ($userBranchId == 1) {
                $branchId = $filterBranchId;
            } else {
                $branchId = $userBranchId;
            }
        }

        $query = PurchaseItem::where('product_id', $product->id);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        $actualStock = $query->sum('stock_qty');

        $invQuery = InvoiceItem::with('invoice')->where('product_id', $product->id);
        $retQuery = ReturnItem::with('return.invoice')->where('product_id', $product->id);
        if ($branchId) {
            $invQuery->where('branch_id', $branchId);
            $retQuery->where('branch_id', $branchId);
        }

        $factor = ($product->unit && $product->unit->related_value) ? (float)$product->unit->related_value : 1;

        $allInvoices = $invQuery->get();
        $actualInvoiced = $allInvoices->sum(function ($item) use ($factor) {
            return ($item->main_qty * $factor) + ($item->sub_qty ?? 0);
        });

        $fakeInvoices = InvoiceItem::filterByFakeSale($allInvoices);
        $fakeInvoiced = $fakeInvoices->sum(function ($item) use ($factor) {
            return ($item->main_qty * $factor) + ($item->sub_qty ?? 0);
        });

        $allReturns = $retQuery->get();
        $actualReturned = $allReturns->sum(function ($item) use ($factor) {
            return ($item->main_qty * $factor) + ($item->sub_qty ?? 0);
        });

        $fakeReturns = ReturnItem::filterByFakeSale($allReturns);
        $fakeReturned = $fakeReturns->sum(function ($item) use ($factor) {
            return ($item->main_qty * $factor) + ($item->sub_qty ?? 0);
        });

        $fakeStock = $actualStock + ($actualInvoiced - $fakeInvoiced) - ($actualReturned - $fakeReturned);
        return (float) $fakeStock;
    }
}

if (!function_exists('product_purchase_unit_cost')) {
    function product_purchase_unit_cost($product, $branchId = null)
    {
        if (!$product || $product->is_service == 1) {
            return 0;
        }

        $factor = ($product->unit && $product->unit->related_value) ? (float)$product->unit->related_value : 1;
        if ($factor <= 0) $factor = 1;

        // 1. Check active remaining stock batches in PurchaseItem (FIFO / remaining stock batches)
        $activeBatches = \App\Models\PurchaseItem::where('product_id', $product->id)
            ->when($branchId, function($q) use ($branchId) {
                return $q->where('branch_id', $branchId);
            })
            ->where('stock_qty', '>', 0)
            ->get();

        $activeStockSum = $activeBatches->sum('stock_qty');
        if ($activeStockSum > 0) {
            $activeVal = $activeBatches->sum(function($b) use ($factor) {
                return (float)$b->stock_qty * ((float)$b->rate / $factor);
            });
            return $activeVal / $activeStockSum;
        }

        // 2. If no active batches remaining, check weighted average of all purchases
        $allPurchases = \App\Models\PurchaseItem::where('product_id', $product->id)
            ->when($branchId, function($q) use ($branchId) {
                return $q->where('branch_id', $branchId);
            })
            ->get();

        $totalPurQty = $allPurchases->sum(function($item) use ($factor) {
            return (float)$item->main_qty + (((float)($item->sub_qty ?? 0)) / $factor);
        });
        $totalPurSubtotal = $allPurchases->sum('subtotal');

        if ($totalPurQty > 0 && $totalPurSubtotal > 0) {
            return ($totalPurSubtotal / $totalPurQty) / $factor;
        }

        // 3. Fallback to product table purchase_price
        return ((float)$product->purchase_price) / $factor;
    }
}

// //gets stock check
// if (!function_exists('product_stock_check')) {
//     function product_stock_check($product)
//     {
//         $product = App\Models\Product::where('id', $product->id)->first();
//         if ($product->is_service == 0) {

//             $userBranchId = auth()->user()->branch_id;
//             $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

//             if ($product->unit->related_unit  == null) {
//                 if ($userBranchId == 1) {
//                     if ($filterBranchId) {
//                         $pur_stock = App\Models\PurchaseItem::with('purchase')->where('branch_id', $filterBranchId)
//                             ->where('product_id', $product->id)
//                             ->sum('stock_qty');
//                         $transfer_from_stock = TransferItem::where('from_branch_id', $filterBranchId)->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $transfer_to_stock = TransferItem::where('status', 1)->where('to_branch_id', $filterBranchId)->where('product_id', $product->id)
//                             ->sum('main_qty');
//                     } else {
//                         $pur_stock = App\Models\PurchaseItem::with('purchase')
//                             ->where('product_id', $product->id)
//                             ->sum('stock_qty');
//                         $transfer_from_stock = TransferItem::where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $transfer_to_stock = TransferItem::where('status', 1)->where('product_id', $product->id)
//                             ->sum('main_qty');
//                     }
//                 } else {
//                     $pur_stock = App\Models\PurchaseItem::with('purchase')->where('branch_id', $userBranchId)
//                         ->where('product_id', $product->id)
//                         ->sum('stock_qty');
//                     $transfer_from_stock = TransferItem::where('from_branch_id', $userBranchId)->where('product_id', $product->id)
//                         ->sum('main_qty');
//                     $transfer_to_stock = TransferItem::where('status', 1)->where('to_branch_id', $userBranchId)->where('product_id', $product->id)
//                         ->sum('main_qty');
//                 }
//                 $total_stock =
//                     (float) ($pur_stock);
//                 $data['stock_qty'] = $total_stock;
//             } else {
//                 if ($userBranchId == 1) {
//                     if ($filterBranchId) {
//                         $inv_stock_main = InvoiceItem::with('invoice')
//                             ->where('branch_id', $filterBranchId)
//                             ->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $inv_total_sub = InvoiceItem::with('invoice')
//                             ->where('branch_id', $filterBranchId)
//                             ->where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         $rtn_stock_main = ReturnItem::with('return')
//                             ->where('branch_id', $filterBranchId)
//                             ->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $rtn_total_sub = ReturnItem::with('return')
//                             ->where('branch_id', $filterBranchId)
//                             ->where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         $rtn_pur_main = ReturnPurchaseItem::where('product_id', $product->id)
//                             ->where('branch_id', $filterBranchId)
//                             ->sum('main_qty');
//                         $rtn_pur_total_sub = ReturnPurchaseItem::where('product_id', $product->id)
//                             ->where('branch_id', $filterBranchId)
//                             ->sum('sub_qty');
//                         $damage_stock_main = DamageItem::where('product_id', $product->id)
//                             ->where('branch_id', $filterBranchId)
//                             ->sum('main_qty');
//                         $damage_total_sub = DamageItem::where('product_id', $product->id)
//                             ->where('branch_id', $filterBranchId)
//                             ->sum('sub_qty');
//                         $pur_stock_main = PurchaseItem::with('purchase')
//                             ->where('branch_id', $filterBranchId)
//                             ->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $pur_stock_sub = PurchaseItem::with('purchase')
//                             ->where('branch_id', $filterBranchId)
//                             ->where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         //transfer 
//                         $transfer_from_main = TransferItem::where('from_branch_id', $filterBranchId)->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $transfer_from_sub = TransferItem::where('from_branch_id', $filterBranchId)->where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         //transfer receive
//                         $transfer_to_main = TransferItem::where('status', 1)->where('to_branch_id', $filterBranchId)->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $transfer_to_sub = TransferItem::where('status', 1)->where('to_branch_id', $filterBranchId)->where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         // $transfer_return_to_main = TransferItem::where('status', 2)->where('from_branch_id', $filterBranchId)->where('product_id', $product->id)
//                         //     ->sum('main_qty');
//                         // $transfer_return_to_sub = TransferItem::where('status', 2)->where('from_branch_id', $filterBranchId)->where('product_id', $product->id)
//                         //     ->sum('sub_qty');
//                     } else {
//                         $inv_stock_main = InvoiceItem::with('invoice')
//                             ->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $inv_total_sub = InvoiceItem::with('invoice')
//                             ->where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         $rtn_stock_main = ReturnItem::with('return')
//                             ->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $rtn_total_sub = ReturnItem::with('return')
//                             ->where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         $rtn_pur_main = ReturnPurchaseItem::where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $rtn_pur_total_sub = ReturnPurchaseItem::where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         $damage_stock_main = DamageItem::where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $damage_total_sub = DamageItem::where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         $pur_stock_main = PurchaseItem::with('purchase')
//                             ->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $pur_stock_sub = PurchaseItem::with('purchase')
//                             ->where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         //transfer 
//                         $transfer_from_main = TransferItem::where('status', 1)->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $transfer_from_sub = TransferItem::where('status', 1)->where('product_id', $product->id)
//                             ->sum('sub_qty');
//                         //transfer receive
//                         $transfer_to_main = TransferItem::where('status', 1)->where('product_id', $product->id)
//                             ->sum('main_qty');
//                         $transfer_to_sub = TransferItem::where('status', 1)->where('product_id', $product->id)
//                             ->sum('sub_qty');
//                     }
//                 } else {
//                     $inv_stock_main = InvoiceItem::with('invoice')
//                         ->where('branch_id', $userBranchId)
//                         ->where('product_id', $product->id)
//                         ->sum('main_qty');
//                     $inv_total_sub = InvoiceItem::with('invoice')
//                         ->where('branch_id', $userBranchId)
//                         ->where('product_id', $product->id)
//                         ->sum('sub_qty');
//                     $rtn_stock_main = ReturnItem::with('return')
//                         ->where('branch_id', $userBranchId)
//                         ->where('product_id', $product->id)
//                         ->sum('main_qty');
//                     $rtn_total_sub = ReturnItem::with('return')
//                         ->where('branch_id', $userBranchId)
//                         ->where('product_id', $product->id)
//                         ->sum('sub_qty');
//                     $rtn_pur_main = ReturnPurchaseItem::where('product_id', $product->id)
//                         ->where('branch_id', $userBranchId)
//                         ->sum('main_qty');
//                     $rtn_pur_total_sub = ReturnPurchaseItem::where('product_id', $product->id)
//                         ->where('branch_id', $userBranchId)
//                         ->sum('sub_qty');
//                     $damage_stock_main = DamageItem::where('product_id', $product->id)
//                         ->where('branch_id', $userBranchId)
//                         ->sum('main_qty');
//                     $damage_total_sub = DamageItem::where('product_id', $product->id)
//                         ->where('branch_id', $userBranchId)
//                         ->sum('sub_qty');
//                     $pur_stock_main = PurchaseItem::with('purchase')
//                         ->where('branch_id', $userBranchId)
//                         ->where('product_id', $product->id)
//                         ->sum('main_qty');
//                     $pur_stock_sub = PurchaseItem::with('purchase')
//                         ->where('branch_id', $userBranchId)
//                         ->where('product_id', $product->id)
//                         ->sum('sub_qty');
//                     //transfer 
//                     //transfer return
//                     // $transfer_return_to_main = TransferItem::where('status', 2)->where('from_branch_id', $userBranchId)->where('product_id', $product->id)
//                     //     ->sum('main_qty');
//                     // $transfer_return_to_sub = TransferItem::where('status', 2)->where('from_branch_id', $userBranchId)->where('product_id', $product->id)
//                     //     ->sum('sub_qty');
//                 }
//                 //invoice
//                 $inv_total_main = (float)($inv_stock_main * $product->unit->related_value);
//                 $inv_total_stock = (float)($inv_total_main + $inv_total_sub);
//                 //return sale
//                 $rtn_total_main = (float)($rtn_stock_main * $product->unit->related_value);
//                 $rtn_total_stock = (float)($rtn_total_main + $rtn_total_sub);
//                 //return purchase
//                 $rtn_pur_total_main = (float)($rtn_pur_main * $product->unit->related_value);
//                 $rtn_total_pur = (float)($rtn_pur_total_main + $rtn_pur_total_sub);
//                 //damage
//                 $damage_total_main = (float)($damage_stock_main * $product->unit->related_value);
//                 $damage_total_stock = (float)($damage_total_main + $damage_total_sub);
//                 //transfer
//                 //transfer return
//                 // $transfer_return_to_main = (float)($transfer_return_to_main * $product->unit->related_value);
//                 // $transfer_return_total_stock = (float)($transfer_return_to_main + $transfer_return_to_sub);

//                 // $transfer_stock = StockTransferItem::where('product_id', $product->id)
//                 //     ->sum('main_qty');
//                 // $transfer_stock_sub = StockTransferItem::where('product_id', $product->id)
//                 //     ->sum('sub_qty');

//                 // $trans_total_stock = (float)($transfer_stock + $transfer_stock_sub);
//                 //purchase
//                 $main_pro =  $pur_stock_main;
//                 $pur_total_main = (float)($main_pro * $product->unit->related_value);
//                 // $open_main_pro_stock = Product::where('id', $product->id)->first()->sub_qty;
//                 $pur_total_stock = (float)($pur_total_main  + $pur_stock_sub + $rtn_total_stock);
//                 $total_stock = $pur_total_stock - ($inv_total_stock + $damage_total_stock + $rtn_total_pur);

//                 $check = $total_stock / $product->unit->related_value;
//                 if (is_integer($check)) {
//                     $data['stock_qty'] = $check . ' ' . $product->unit->name . ' 0 ' . $product->unit->related_unit->name;
//                 } else {
//                     $main_value = floor($check) * $product->unit->related_value;
//                     $sub_value = $total_stock - $main_value;
//                     $data['stock_qty'] = floor($check) . ' ' . $product->unit->name . ' ' . $sub_value . ' ' . $product->unit->related_unit->name;
//                 }
//             }

//             return $data['stock_qty'];
//         }
//     }
// }

// gets stock check
if (!function_exists('product_stock_check')) {
    function product_stock_check($product)
    {
        // যদি service product হয়, তাহলে সরাসরি 0 বা 'Service Product' রিটার্ন
        if ($product->is_service == 1) {
            return 'Service Product';
        }

        $userBranchId   = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        // --- স্টক কুয়েরি ---
        $query = App\Models\PurchaseItem::where('product_id', $product->id);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $query->where('branch_id', $filterBranchId);
            }
        } else {
            $query->where('branch_id', $userBranchId);
        }

        $totalStock = $query->sum('stock_qty'); // সবসময় stock_qty থেকে আসবে

        // --- Case 1: Sub-unit নাই ---
        if (!$product->unit || $product->unit->related_unit == null) {
            return $totalStock . ' ' . ($product->unit->name ?? 'pcs');
        }

        // --- Case 2: Sub-unit আছে ---
        $factor = $product->unit->related_value; // যেমন 1kg = 1000gm

        $main_qty = floor($totalStock / $factor);
        $sub_qty  = $totalStock % $factor;

        if ($sub_qty == 0) {
            return $main_qty . ' ' . $product->unit->name;
        } else {
            return $main_qty . ' ' . $product->unit->name . ' ' .
                $sub_qty . ' ' . $product->unit->related_unit->name;
        }
    }
}

if (!function_exists('product_stock_check_sub')) {
    function product_stock_check_sub($product)
    {
        $product = App\Models\Product::where('id', $product->id)->first();
        if ($product->is_service == 0) {

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

            // যদি sub unit না থাকে → normal হিসাব
            if ($product->unit->related_unit  == null) {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $pur_stock = App\Models\PurchaseItem::with('purchase')
                            ->where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)
                            ->sum('stock_qty');
                        $transfer_from_stock = TransferItem::where('from_branch_id', $filterBranchId)
                            ->where('product_id', $product->id)
                            ->sum('main_qty');
                        $transfer_to_stock = TransferItem::where('status', 1)
                            ->where('to_branch_id', $filterBranchId)
                            ->where('product_id', $product->id)
                            ->sum('main_qty');
                    } else {
                        $pur_stock = App\Models\PurchaseItem::with('purchase')
                            ->where('product_id', $product->id)
                            ->sum('stock_qty');
                        $transfer_from_stock = TransferItem::where('product_id', $product->id)
                            ->sum('main_qty');
                        $transfer_to_stock = TransferItem::where('status', 1)
                            ->where('product_id', $product->id)
                            ->sum('main_qty');
                    }
                } else {
                    $pur_stock = App\Models\PurchaseItem::with('purchase')
                        ->where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)
                        ->sum('stock_qty');
                    $transfer_from_stock = TransferItem::where('from_branch_id', $userBranchId)
                        ->where('product_id', $product->id)
                        ->sum('main_qty');
                    $transfer_to_stock = TransferItem::where('status', 1)
                        ->where('to_branch_id', $userBranchId)
                        ->where('product_id', $product->id)
                        ->sum('main_qty');
                }

                $total_stock = (float) ($pur_stock + $transfer_to_stock) - (float)($transfer_from_stock);
                $data['stock_qty'] = $total_stock;
            }
            // যদি sub unit থাকে → সব কিছু sub unit এ কনভার্ট হবে
            else {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $inv_stock_main = InvoiceItem::where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('main_qty');
                        $inv_total_sub = InvoiceItem::where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('sub_qty');
                        $rtn_stock_main = ReturnItem::where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('main_qty');
                        $rtn_total_sub = ReturnItem::where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('sub_qty');
                        $rtn_pur_main = ReturnPurchaseItem::where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('main_qty');
                        $rtn_pur_total_sub = ReturnPurchaseItem::where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('sub_qty');
                        $damage_stock_main = DamageItem::where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('main_qty');
                        $damage_total_sub = DamageItem::where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('sub_qty');
                        $pur_stock_main = PurchaseItem::where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('main_qty');
                        $pur_stock_sub = PurchaseItem::where('branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('sub_qty');
                        $transfer_from_main = TransferItem::where('from_branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('main_qty');
                        $transfer_from_sub = TransferItem::where('from_branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('sub_qty');
                        $transfer_to_main = TransferItem::where('status', 1)
                            ->where('to_branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('main_qty');
                        $transfer_to_sub = TransferItem::where('status', 1)
                            ->where('to_branch_id', $filterBranchId)
                            ->where('product_id', $product->id)->sum('sub_qty');
                        // $transfer_return_to_main = TransferItem::where('status', 2)
                        //     ->where('from_branch_id', $filterBranchId)
                        //     ->where('product_id', $product->id)->sum('main_qty');
                        // $transfer_return_to_sub = TransferItem::where('status', 2)
                        //     ->where('from_branch_id', $filterBranchId)
                        //     ->where('product_id', $product->id)->sum('sub_qty');
                    } else {
                        $inv_stock_main = InvoiceItem::where('product_id', $product->id)->sum('main_qty');
                        $inv_total_sub = InvoiceItem::where('product_id', $product->id)->sum('sub_qty');
                        $rtn_stock_main = ReturnItem::where('product_id', $product->id)->sum('main_qty');
                        $rtn_total_sub = ReturnItem::where('product_id', $product->id)->sum('sub_qty');
                        $rtn_pur_main = ReturnPurchaseItem::where('product_id', $product->id)->sum('main_qty');
                        $rtn_pur_total_sub = ReturnPurchaseItem::where('product_id', $product->id)->sum('sub_qty');
                        $damage_stock_main = DamageItem::where('product_id', $product->id)->sum('main_qty');
                        $damage_total_sub = DamageItem::where('product_id', $product->id)->sum('sub_qty');
                        $pur_stock_main = PurchaseItem::where('product_id', $product->id)->sum('main_qty');
                        $pur_stock_sub = PurchaseItem::where('product_id', $product->id)->sum('sub_qty');
                        $transfer_from_main = TransferItem::where('product_id', $product->id)->sum('main_qty');
                        $transfer_from_sub = TransferItem::where('product_id', $product->id)->sum('sub_qty');
                        $transfer_to_main = TransferItem::where('status', 1)->where('product_id', $product->id)->sum('main_qty');
                        $transfer_to_sub = TransferItem::where('status', 1)->where('product_id', $product->id)->sum('sub_qty');
                        // $transfer_return_to_main = TransferItem::where('status', 2)->where('product_id', $product->id)->sum('main_qty');
                        // $transfer_return_to_sub = TransferItem::where('status', 2)->where('product_id', $product->id)->sum('sub_qty');
                    }
                } else {
                    $inv_stock_main = InvoiceItem::where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('main_qty');
                    $inv_total_sub = InvoiceItem::where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('sub_qty');
                    $rtn_stock_main = ReturnItem::where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('main_qty');
                    $rtn_total_sub = ReturnItem::where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('sub_qty');
                    $rtn_pur_main = ReturnPurchaseItem::where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('main_qty');
                    $rtn_pur_total_sub = ReturnPurchaseItem::where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('sub_qty');
                    $damage_stock_main = DamageItem::where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('main_qty');
                    $damage_total_sub = DamageItem::where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('sub_qty');
                    $pur_stock_main = PurchaseItem::where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('main_qty');
                    $pur_stock_sub = PurchaseItem::where('branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('sub_qty');
                    $transfer_from_main = TransferItem::where('from_branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('main_qty');
                    $transfer_from_sub = TransferItem::where('from_branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('sub_qty');
                    $transfer_to_main = TransferItem::where('status', 1)
                        ->where('to_branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('main_qty');
                    $transfer_to_sub = TransferItem::where('status', 1)
                        ->where('to_branch_id', $userBranchId)
                        ->where('product_id', $product->id)->sum('sub_qty');
                    // $transfer_return_to_main = TransferItem::where('status', 2)
                    //     ->where('from_branch_id', $userBranchId)
                    //     ->where('product_id', $product->id)->sum('main_qty');
                    // $transfer_return_to_sub = TransferItem::where('status', 2)
                    //     ->where('from_branch_id', $userBranchId)
                    //     ->where('product_id', $product->id)->sum('sub_qty');
                }

                // সব কিছু sub unit এ কনভার্ট
                $inv_total_stock = ($inv_stock_main * $product->unit->related_value) + $inv_total_sub;
                $rtn_total_stock = ($rtn_stock_main * $product->unit->related_value) + $rtn_total_sub;
                $rtn_total_pur   = ($rtn_pur_main * $product->unit->related_value) + $rtn_pur_total_sub;
                $damage_total_stock = ($damage_stock_main * $product->unit->related_value) + $damage_total_sub;
                $transfer_total_stock = ($transfer_from_main * $product->unit->related_value) + $transfer_from_sub;
                $transfer_receive_total_stock = ($transfer_to_main * $product->unit->related_value) + $transfer_to_sub;
                // $transfer_return_total_stock = ($transfer_return_to_main * $product->unit->related_value) + $transfer_return_to_sub;

                $pur_total_stock = ($pur_stock_main * $product->unit->related_value) + $pur_stock_sub + $rtn_total_stock + $transfer_receive_total_stock;

                $total_stock = $pur_total_stock - ($inv_total_stock + $damage_total_stock + $rtn_total_pur + $transfer_total_stock);

                // Final → শুধু sub unit এ রিটার্ন
                $data['stock_qty'] = $total_stock . ' ' . $product->unit->related_unit->name;
            }

            return $data['stock_qty'];
        }
    }
}

//gets stock check
if (!function_exists('used_product_stock_check')) {
    function used_product_stock_check($product)
    {
        if ($product->unit->related_unit  == null) {
            $use_stock = App\Models\UsedItem::where('product_id', $product->id)
                ->sum('main_qty');
            $buy_stock = App\Models\UsedPurchaseItem::where('product_id', $product->id)
                ->sum('main_qty');
            $total_stock = (float) ($buy_stock) - ($use_stock);
            $data['stock_qty'] = $total_stock;
        } else {
            //invoice
            $inv_stock_main = App\Models\InvoiceItem::with('invoice')
                ->where('product_id', $product->id)
                ->sum('main_qty');
            $inv_total_main = (float) ($inv_stock_main * $product->unit->related_value);
            $inv_total_sub = App\Models\InvoiceItem::with('invoice')
                ->where('product_id', $product->id)
                ->sum('sub_qty');
            $inv_total_stock = (float) ($inv_total_main + $inv_total_sub);
            //return
            $rtn_stock_main = App\Models\ReturnItem::with('return')
                ->where('product_id', $product->id)
                ->sum('main_qty');
            $rtn_total_main = (float) ($rtn_stock_main * $product->unit->related_value);
            $rtn_total_sub = App\Models\ReturnItem::with('return')
                ->where('product_id', $product->id)
                ->sum('sub_qty');
            $rtn_total_stock = (float) ($rtn_total_main + $rtn_total_sub);
            //purchase
            $pur_stock_main = App\Models\PurchaseItem::with('purchase')
                ->where('product_id', $product->id)
                ->sum('main_qty');
            // $open_main_pro_stock = App\Models\Product::where('id', $product->id)->first()
            //     ->main_qty;
            $main_pro =  $pur_stock_main;
            $pur_total_main = (float) ($main_pro * $product->unit->related_value);
            $pur_stock_sub = App\Models\PurchaseItem::with('purchase')
                ->where('product_id', $product->id)
                ->sum('sub_qty');
            // $open_main_pro_stock = App\Models\Product::where('id', $product->id)->first()
            //     ->o_sub_qty;
            $pur_total_stock =
                (float) ($pur_total_main +  $pur_stock_sub);
            $total_stock = $pur_total_stock + $rtn_total_stock - $inv_total_stock;
            $p_value = $product->purchase_price / $product->unit->related_value;
            $s_value = $product->selling_price / $product->unit->related_value;
            $purchase_price = $total_stock * $p_value;
            $selling_price = $total_stock * $s_value;
            $data['stock_qty'] = (float)($total_stock);
        }
        return $data['stock_qty'];
    }
}

// stock amount
if (!function_exists('product_stock_balance')) {
    function product_stock_balance($product)
    {
        if ($product->is_service == 0) {
            if ($product->unit->related_unit  == null) {
                $pur_stock = App\Models\PurchaseItem::with('purchase')
                    ->where('product_id', $product->id)
                    ->sum('stock_qty');
                $total_stock =
                    (float) ($pur_stock);
                $data['stock_qty'] = $total_stock;
            } else {
                $pur_stock_main = App\Models\PurchaseItem::with('purchase')
                    ->where('product_id', $product->id)
                    ->sum('stock_qty');
                $pur_total_stock =
                    (float) ($pur_stock_main);
                $total_stock = $pur_total_stock;
                $total_stock = $total_stock / $product->unit->related_value;
                $data['stock_qty'] = (float)($total_stock);
            }
            return $data['stock_qty'];
        }
    }
}

//gets current_balance
if (!function_exists('current_balance')) {
    function current_balance($bank_id)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $open_balance = BankAccount::where('id', $bank_id)->first()->opening_balance;
                $deposit = BankTransaction::where('branch_id', $filterBranchId)->where('trans_type', 'deposit')->where('bank_id', $bank_id)->sum('amount');
                $withdraw = BankTransaction::where('branch_id', $filterBranchId)->where('trans_type', 'withdraw')->where('bank_id', $bank_id)->sum('amount');
                $from_transfer = BankTransaction::where('branch_id', $filterBranchId)->where('trans_type', 'transfer')->where('from_bank_id', $bank_id)->sum('amount');
                $to_transfer = BankTransaction::where('branch_id', $filterBranchId)->where('trans_type', 'transfer')->where('to_bank_id', $bank_id)->sum('amount');
            } else {
                $open_balance = BankAccount::where('id', $bank_id)->first()->opening_balance;
                $deposit = BankTransaction::where('trans_type', 'deposit')->where('bank_id', $bank_id)->sum('amount');
                $withdraw = BankTransaction::where('trans_type', 'withdraw')->where('bank_id', $bank_id)->sum('amount');
                $from_transfer = BankTransaction::where('trans_type', 'transfer')->where('from_bank_id', $bank_id)->sum('amount');
                $to_transfer = BankTransaction::where('trans_type', 'transfer')->where('to_bank_id', $bank_id)->sum('amount');
            }
        } else {
            $open_balance = BankAccount::where('id', $bank_id)->first()->opening_balance;
            $deposit = BankTransaction::where('branch_id', $userBranchId)->where('trans_type', 'deposit')->where('bank_id', $bank_id)->sum('amount');
            $withdraw = BankTransaction::where('branch_id', $userBranchId)->where('trans_type', 'withdraw')->where('bank_id', $bank_id)->sum('amount');
            $from_transfer = BankTransaction::where('branch_id', $userBranchId)->where('trans_type', 'transfer')->where('from_bank_id', $bank_id)->sum('amount');
            $to_transfer = BankTransaction::where('branch_id', $userBranchId)->where('trans_type', 'transfer')->where('to_bank_id', $bank_id)->sum('amount');
        }
        $current_balance = (float)($open_balance + $to_transfer + $deposit) - (float)($withdraw + $from_transfer);
        return $current_balance;
    }
}

if (!function_exists('date_current_balance')) {
    function date_current_balance($bank_id, $date)
    {
        $open_balance = BankAccount::where('id', $bank_id)->first()->opening_balance;
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $deposit = BankTransaction::where('trans_type', 'deposit')->where('branch_id', $filterBranchId)->where('bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
                $withdraw = BankTransaction::where('trans_type', 'withdraw')->where('branch_id', $filterBranchId)->where('bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
                $from_transfer = BankTransaction::where('trans_type', 'transfer')->where('branch_id', $filterBranchId)->where('from_bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
                $to_transfer = BankTransaction::where('trans_type', 'transfer')->where('branch_id', $filterBranchId)->where('to_bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
            } else {
                $deposit = BankTransaction::where('trans_type', 'deposit')->where('bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
                $withdraw = BankTransaction::where('trans_type', 'withdraw')->where('bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
                $from_transfer = BankTransaction::where('trans_type', 'transfer')->where('from_bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
                $to_transfer = BankTransaction::where('trans_type', 'transfer')->where('to_bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
            }
        } else {
            $deposit = BankTransaction::where('trans_type', 'deposit')->where('branch_id', $userBranchId)->where('bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
            $withdraw = BankTransaction::where('trans_type', 'withdraw')->where('branch_id', $userBranchId)->where('bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
            $from_transfer = BankTransaction::where('trans_type', 'transfer')->where('branch_id', $userBranchId)->where('from_bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
            $to_transfer = BankTransaction::where('trans_type', 'transfer')->where('branch_id', $userBranchId)->where('to_bank_id', $bank_id)->where('date', '=', $date)->sum('amount');
        }


        $date_current_balance = (float)($to_transfer + $deposit) - (float)($withdraw + $from_transfer);
        return $date_current_balance;
    }
}

if (!function_exists('previous_balance')) {
    function previous_balance($bank_id, $date)
    {
        $open_balance = 0;
        $open_balance = BankAccount::where('id', $bank_id)->first()->opening_balance ?? 0;
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $deposit = BankTransaction::where('trans_type', 'deposit')->where('branch_id', $filterBranchId)->where('bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
                $withdraw = BankTransaction::where('trans_type', 'withdraw')->where('branch_id', $filterBranchId)->where('bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
                $from_transfer = BankTransaction::where('trans_type', 'transfer')->where('branch_id', $filterBranchId)->where('from_bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
                $to_transfer = BankTransaction::where('trans_type', 'transfer')->where('branch_id', $filterBranchId)->where('to_bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
            } else {
                $deposit = BankTransaction::where('trans_type', 'deposit')->where('bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
                $withdraw = BankTransaction::where('trans_type', 'withdraw')->where('bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
                $from_transfer = BankTransaction::where('trans_type', 'transfer')->where('from_bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
                $to_transfer = BankTransaction::where('trans_type', 'transfer')->where('to_bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
            }
        } else {
            $deposit = BankTransaction::where('trans_type', 'deposit')->where('branch_id', $userBranchId)->where('bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
            $withdraw = BankTransaction::where('trans_type', 'withdraw')->where('branch_id', $userBranchId)->where('bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
            $from_transfer = BankTransaction::where('trans_type', 'transfer')->where('branch_id', $userBranchId)->where('from_bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
            $to_transfer = BankTransaction::where('trans_type', 'transfer')->where('branch_id', $userBranchId)->where('to_bank_id', $bank_id)->where('date', '<', $date)->sum('amount');
        }
        $previous_balance = (float)($open_balance + $to_transfer + $deposit) - (float)($withdraw + $from_transfer);
        return $previous_balance;
    }
}

//gets open_balance_customer
if (!function_exists('open_balance_customer')) {
    function open_balance_customer($id, $open_receivable)
    {
        $received = App\Models\ActualPayment::with('transaction')
            ->where('account_type', 'Customer')
            ->where('wallet_type', 'Balance Adjust')
            ->where('pay_type', 'Money Received')
            ->whereHas('transaction', function ($query) use ($id) {
                $query->where('customer_id', $id);
            })
            ->sum('amount');
        $payable = App\Models\ActualPayment::with('transaction')
            ->where('account_type', 'Customer')
            ->where('wallet_type', 'Balance Adjust')
            ->where('pay_type', 'Money Payment')
            ->whereHas('transaction', function ($query) use ($id) {
                $query->where('customer_id', $id);
            })
            ->sum('amount');
        $open_balance = (float)($open_receivable + $payable) - (float)(+$received);
        return $open_balance;
    }
}

//gets current_balance_supplier
if (!function_exists('open_balance_supplier')) {
    function open_balance_supplier($id, $open_receivable, $open_payable)
    {
        $received = App\Models\ActualPayment::with('transaction')
            ->where('account_type', 'Supplier')
            ->where('wallet_type', 'Balance Adjust')
            ->where('pay_type', 'Money Received')
            ->whereHas('transaction', function ($query) use ($id) {
                $query->where('supplier_id', $id);
            })
            ->sum('amount');
        // dd($received);    
        $payable = App\Models\ActualPayment::with('transaction')
            ->where('account_type', 'Supplier')
            ->where('wallet_type', 'Balance Adjust')
            ->where('pay_type', 'Money Payment')
            ->whereHas('transaction', function ($query) use ($id) {
                $query->where('supplier_id', $id);
            })
            ->sum('amount');
        $open_balance = (float)($open_receivable + $received) - (float)($open_payable + $payable);
        return $open_balance;
    }
}

function rtn_profit($returns)
{
    $sum = 0;
    foreach ($returns as $key => $returnItem) {
        $product = Product::with('unit.related_unit')
            ->where('id', $returnItem->product_id)
            ->first();
        $purchase = PurchaseItem::where('product_id', $product->id)->first();

        if ($product->unit->related_unit == null) {
            $inv_stock = $returnItem->main_qty;
            $purchase_price = $inv_stock * $purchase?->rate;
            $selling_price = $inv_stock * $returnItem->rate;
            $profits = $selling_price - $purchase_price;
        } else {
            //return
            $inv_stock_main = $returnItem->main_qty;
            $inv_total_main = (float) ($inv_stock_main * $product->unit->related_value);
            $inv_total_sub = $returnItem->sub_qty;
            $inv_total_stock = (float) ($inv_total_main + $inv_total_sub);
            $total_stock = $inv_total_stock;
            $p_value = $purchase?->rate / $product->unit->related_value;
            $s_value = $returnItem->rate / $product->unit->related_value;
            $purchase_price = $total_stock * $p_value;
            $selling_price = $total_stock * $s_value;

            $profits = $selling_price - $purchase_price;
        }
        $sum += $profits;
    }

    return $sum;
}

function sendPromotionalSMS($contacts, $message)
{
    $url = env('SMS_API_URL');
    $apiKey = env('SMS_API_KEY');
    $senderId = env('SMS_SENDER_ID');

    $response = Http::post($url, [
        'api_key' => $apiKey,
        'type' => 'text',  // specify your content type
        'contacts' => $contacts,  // e.g., '88017xxxxxxxx+88018xxxxxxxx'
        'senderid' => $senderId,
        'purpose' => 'promotional',
        'msg' => $message,
    ]);

    return $response->json();
}

function sendSSLPromotionalSMS($contacts, $message)
{
    $url = env('SMS_API_URL');
    $apiToken = env('SMS_API_TOKEN');
    $sid = env('SMS_SID');
    $msisdn = env('SMS_MSISDN');

    $response = Http::post($url, [
        'api_token' => $apiToken,
        'sid' => $sid,
        'msisdn' => $contacts,
        'sms' => $message,
        'csms_id' => uniqid(), // unique id for each message
    ]);

    return $response->json();
}



function invoiceSMS($customer_id, $invoice_id)
{
    $customer = Customer::find($customer_id);
    $invoice = Invoice::find($invoice_id);
    if ($customer->id != 1) {
        $contacts = $customer->phone;
        $formattedDate = date_format($invoice->created_at, "d-m-Y");
        $formattedTime = date_format($invoice->created_at, 'h:i:s A');
        $paid = $invoice->total_paid;
        $message = "Dear {$customer->name}, Thanks for shopping at Civvy Square at $formattedTime on $formattedDate. Total Paid: $paid, Invoice No: $invoice->invoice_no, Details: https://civvy-square.fastitbd.com/customer/invoice/copy/$invoice->unique_id";

        sendPromotionalSMS($contacts, $message);
    }
}

function calculateUnitPriceUsingFIFO($productId, $requiredQty, $variationId = null, $branchId = null)
{
    $product = Product::find($productId);

    if ($product->is_service == 0) {
        $userBranchId = auth()->check() ? auth()->user()->branch_id : 1;
        $filterBranchId = session('branch_filter_id', $userBranchId);

        // Base query for available stock
        $query = PurchaseItem::where('product_id', $productId)
            ->where('stock_qty', '>', 0)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc');

        if ($variationId !== null && $variationId !== '') {
            $query->where('product_variation_id', $variationId);
        } else {
            $query->whereNull('product_variation_id'); // for products without variation
        }

        // Branch filter
        if ($branchId) {
            $query->where('branch_id', $branchId);
        } else {
            if ($userBranchId == 1) {
                if ($filterBranchId) {
                    $query->where('branch_id', $filterBranchId);
                }
            } else {
                $query->where('branch_id', $userBranchId);
            }
        }

        $purchaseItems = $query->get();

        $remainingQty = $requiredQty;
        $totalCost = 0;

        if ($purchaseItems->isNotEmpty()) {
            // FIFO deduction
            foreach ($purchaseItems as $item) {
                if ($remainingQty <= 0) break;

                $availableQty = $item->stock_qty;
                $usedQty = min($remainingQty, $availableQty);

                $item->update(['stock_qty' => $availableQty - $usedQty]);

                if ($product->unit->related_unit == null) {
                    $totalCost += $usedQty * $item->rate;
                } else {
                    $totalCost += ($usedQty / $product->unit->related_value) * $item->rate;
                }

                $remainingQty -= $usedQty;
            }
        } else {
            // 🔹 No stock available, use last purchase rate
            $lastPurchaseItemQuery = PurchaseItem::where('product_id', $productId);

            if ($variationId !== null && $variationId !== '') {
                $lastPurchaseItemQuery->where('product_variation_id', $variationId);
            } else {
                $lastPurchaseItemQuery->whereNull('product_variation_id');
            }

            if ($branchId) {
                $lastPurchaseItemQuery->where('branch_id', $branchId);
            } else {
                if ($userBranchId == 1) {
                    if ($filterBranchId) {
                        $lastPurchaseItemQuery->where('branch_id', $filterBranchId);
                    }
                } else {
                    $lastPurchaseItemQuery->where('branch_id', $userBranchId);
                }
            }

            $lastPurchaseItem = $lastPurchaseItemQuery->orderBy('id', 'desc')->first();

            if ($lastPurchaseItem) {
                if ($product->unit->related_unit == null) {
                    $totalCost = $requiredQty * $lastPurchaseItem->rate;
                } else {
                    $totalCost = ($requiredQty / $product->unit->related_value) * $lastPurchaseItem->rate;
                }
            } else {
                $totalCost = 0; // fallback if no purchase exists at all
            }
        }
    } else {
        $totalCost = 0;
    }

    return $totalCost;
}



// function calculateUnitPriceUsingFIFO($productId, $requiredQty, $variationId = null)
// {
//     $product = Product::find($productId);

//     if ($product->is_service == 0) {
//         $userBranchId = auth()->user()->branch_id;
//         $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

//         // Base query
//         $query = PurchaseItem::where('product_id', $productId)
//             ->where('stock_qty', '>', 0)
//             ->orderBy('id', 'asc');

//         if ($variationId !== null && $variationId !== '') {
//             $query->where('product_variation_id', $variationId);
//         } else {
//             $query->whereNull('product_variation_id'); // 🔹 explicitly include products without variation
//         }

//         // ✅ Branch filter
//         if ($userBranchId == 1) {
//             if ($filterBranchId) {
//                 $query->where('branch_id', $filterBranchId);
//             }
//         } else {
//             $query->where('branch_id', $userBranchId);
//         }

//         $purchaseItems = $query->get();

//         $remainingQty = $requiredQty;
//         $totalCost = 0;
//         $totalQtyConsidered = 0;

//         foreach ($purchaseItems as $item) {
//             if ($remainingQty <= 0) {
//                 break;
//             }

//             $availableQty = $item->stock_qty;
//             $usedQty = min($remainingQty, $availableQty);

//             $item->update(['stock_qty' => $availableQty - $usedQty]);

//             // 🔹 Unit-wise conversion
//             if ($product->unit->related_unit == null) {
//                 $totalCost += $usedQty * $item->rate;
//             } else {
//                 $totalCost += ($usedQty / $product->unit->related_value) * $item->rate;
//             }

//             $totalQtyConsidered += $usedQty;
//             $remainingQty -= $usedQty;
//         }
//     } else {
//         $totalCost = 0;
//     }

//     return $totalCost;
// }


function restoreToFIFO($productId, $qtyToRestore, $variationId = null, $branchId = null)
{
    $product = Product::find($productId);
    if (!$product || $product->is_service == 1) return;

    $userBranchId = auth()->check() ? auth()->user()->branch_id : 1;
    $filterBranchId = session('branch_filter_id', $userBranchId);

    $query = PurchaseItem::where('product_id', $productId)
        ->orderBy('id', 'desc'); // reverse order (LIFO restore to most recent stock first)

    if ($variationId) {
        $query->where('product_variation_id', $variationId);
    }

    if ($branchId) {
        $query->where('branch_id', $branchId);
    } else {
        if ($userBranchId == 1 && $filterBranchId) {
            $query->where('branch_id', $filterBranchId);
        } else {
            $query->where('branch_id', $userBranchId);
        }
    }

    $purchaseItems = $query->get();

    $remaining = (float) $qtyToRestore;
    foreach ($purchaseItems as $item) {
        if ($remaining <= 0) break;

        // Calculate capacity of this purchase item
        $related_value = 1.0;
        if ($product->unit) {
            $related_value = ($product->unit->related_value > 0) ? (float) $product->unit->related_value : 1.0;
        }
        $actual_sub = (float) ($item->actual_sub ?? 0);
        $actual_main = (float) $item->actual_main;
        $item_capacity = ($actual_main * $related_value) + $actual_sub;

        // Calculate how much stock can be restored to this item
        $can_restore = max(0.0, $item_capacity - (float)$item->stock_qty);
        $restore_to_this_item = min($remaining, $can_restore);

        if ($restore_to_this_item > 0) {
            $item->update(['stock_qty' => $item->stock_qty + $restore_to_this_item]);
            $remaining -= $restore_to_this_item;
        }
    }

    // Fallback: If there's still remaining quantity, add it to the first item (the most recent purchase item)
    if ($remaining > 0 && $purchaseItems->isNotEmpty()) {
        $firstItem = $purchaseItems->first();
        $firstItem->update(['stock_qty' => $firstItem->stock_qty + $remaining]);
    }
}




// function calculateUnitPriceUsingFIFO($productId, $requiredQty, $variationId = null)
// {
//     $product = Product::where('id', $productId)->first();
//     if ($product->is_service == 0) {
//         // Purchase query (variation থাকলে filter)
//         $purchaseItemsQuery = PurchaseItem::where('product_id', $productId)
//             ->where('stock_qty', '>', 0);

//         if ($variationId) {
//             $purchaseItemsQuery->where('product_variation_id', $variationId);
//         }

//         $purchaseItems = $purchaseItemsQuery->orderBy('id', 'asc')->get();

//         $remainingQty = $requiredQty;
//         $totalCost = 0;

//         foreach ($purchaseItems as $item) {
//             if ($remainingQty <= 0) break;

//             $availableQty = $item->stock_qty;
//             $usedQty = min($remainingQty, $availableQty);
//             $quantity = $availableQty - $usedQty;

//             $item->update(['stock_qty' => $quantity]);

//             if ($product->unit->related_unit == null) {
//                 $totalCost += $usedQty * $item->rate;
//             } else {
//                 $totalCost += ($usedQty / $product->unit->related_value) * $item->rate;
//             }

//             $remainingQty -= $usedQty;
//         }
//     } else {
//         $totalCost = 0;
//     }

//     return $totalCost;
// }


function reduceStockFIFO($productId, $requiredQty, $variationId = null, $branchId = null)
{
    $product = Product::find($productId);

    if (!$product || $product->is_service) {
        return 0;
    }

    $purchaseItemsQuery = PurchaseItem::where('product_id', $productId)
        ->where('stock_qty', '>', 0);

    if ($variationId) {
        $purchaseItemsQuery->where('product_variation_id', $variationId);
    }

    if ($branchId) {
        $purchaseItemsQuery->where('branch_id', $branchId);
    }

    $purchaseItems = $purchaseItemsQuery->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

    $remainingQty = $requiredQty;
    $totalCost = 0;

    foreach ($purchaseItems as $item) {
        if ($remainingQty <= 0) break;

        $availableQty = $item->stock_qty;
        $usedQty = min($remainingQty, $availableQty);

        $item->stock_qty -= $usedQty;
        $item->save();

        if ($product->unit->related_unit == null) {
            $totalCost += $usedQty * $item->rate;
        } else {
            $totalCost += ($usedQty / $product->unit->related_value) * $item->rate;
        }

        $remainingQty -= $usedQty;
    }

    if ($remainingQty > 0) {
        throw new \Exception("Insufficient stock for product ID: {$productId}" . ($variationId ? " (Variation ID: $variationId)" : ""));
    }

    return $totalCost;
}



// function productStockUpdate($productId, $requiredQty)
// {
//     $product = Product::where('id', $productId)->first();
//     if ($product && $product->is_service == 0) {
//         $purchaseItems = PurchaseItem::where('product_id', $productId)
//             ->latest()
//             ->first();

//         if ($purchaseItems) {
//             $availableQty = $purchaseItems->stock_qty ?? 0;
//             $quantity = $availableQty + $requiredQty;
//             $purchaseItems->update(['stock_qty' => $quantity]);
//         } else {
//             // যদি কোনো purchase item না থাকে তাহলে নতুনভাবে যুক্ত করার চিন্তা করুন
//             PurchaseItem::create([
//                 'product_id' => $productId,
//                 'stock_qty' => $requiredQty,
//                 // অন্যান্য প্রয়োজনীয় ফিল্ড এখানে যুক্ত করুন
//             ]);
//         }
//     }
// }







function productStockUpdate($productId, $requiredQty, $variationId = null)
{
    $product = Product::find($productId);

    if (!$product || $product->is_service == 1) {
        return;
    }

    // 🔥 CASE 1: Product has variation
    if (!empty($variationId)) {

        $purchaseItem = PurchaseItem::where('product_id', $productId)
            ->where('product_variation_id', $variationId)
            ->latest()
            ->first();

        if ($purchaseItem) {
            $purchaseItem->update([
                'stock_qty' => $purchaseItem->stock_qty + $requiredQty
            ]);
        }
        // else {
        //     PurchaseItem::create([
        //         'product_id' => $productId,
        //         'product_variation_id' => $variationId,
        //         'stock_qty' => $requiredQty,
        //     ]);
        // }

    }
    // 🔥 CASE 2: Product without variation
    else {

        $purchaseItem = PurchaseItem::where('product_id', $productId)
            ->whereNull('product_variation_id')
            ->latest()
            ->first();

        if ($purchaseItem) {
            $purchaseItem->update([
                'stock_qty' => $purchaseItem->stock_qty + $requiredQty
            ]);
        }
        // else {
        // PurchaseItem::create([
        //     'product_id' => $productId,
        //     'stock_qty' => $requiredQty,
        // ]);
        // }
    }
}











function variation_stock($variation)
{
    $userBranchId = auth()->check() ? auth()->user()->branch_id : 1;
    $filterBranchId = session('branch_filter_id', $userBranchId);

    if ($userBranchId == 1) {
        // যদি super admin হয়
        if ($filterBranchId) {
            $stock = PurchaseItem::where('branch_id', $filterBranchId)
                ->where('product_variation_id', $variation)
                ->sum('stock_qty');
        } else {
            $stock = PurchaseItem::where('product_variation_id', $variation)
                ->sum('stock_qty');
        }
    } else {
        // normal user branch
        $stock = PurchaseItem::where('branch_id', $userBranchId)
            ->where('product_variation_id', $variation)
            ->sum('stock_qty');
    }

    return (float) $stock;
}



// stead fast courier
function placeOrder($invoice, $recipient_name, $recipient_phone, $recipient_address, $cod_amount, $note)
{
    try {
        $apiKey = get_setting('steadfast_api_key') ?: env('STEADFAST_API_KEY');
        $secretKey = get_setting('steadfast_secret_key') ?: env('STEADFAST_SECRET_KEY');
        $baseUrl = rtrim(env('STEADFAST_BASE_URL', 'https://portal.packzy.com/api/v1'), '/');
        if (str_contains($baseUrl, 'portal.steadfast.com.bd')) {
            $baseUrl = 'https://portal.packzy.com/api/v1';
        }

        $response = Http::withoutVerifying()->withHeaders([
            'Api-Key' => $apiKey,
            'Secret-Key' => $secretKey,
            'Content-Type' => 'application/json'
        ])->post($baseUrl . '/create_order', [
            'invoice' => $invoice,
            'recipient_name' => $recipient_name,
            'recipient_phone' => $recipient_phone,
            'recipient_address' => $recipient_address,
            'cod_amount' => $cod_amount,
            'note' => $note,
        ]);
        return $response->json();
    } catch (\Exception $e) {
        \Log::error('Steadfast Order Placement Error: ' . $e->getMessage());
        return [
            'status' => 500,
            'message' => $e->getMessage()
        ];
    }
}

// pathao courier
function getPathaoAccessToken()
{
    return Cache::remember('pathao_access_token', 3600, function () {
        try {
            $secretToken = get_setting('pathao_secret_token') ?: env('PATHAO_SECRET_TOKEN');
            if ($secretToken) {
                return $secretToken;
            }

            $clientId = get_setting('pathao_client_id') ?: env('PATHAO_CLIENT_ID');
            $clientSecret = get_setting('pathao_client_secret') ?: env('PATHAO_CLIENT_SECRET');
            $username = get_setting('pathao_username') ?: env('PATHAO_USERNAME');
            $password = get_setting('pathao_password') ?: env('PATHAO_PASSWORD');
            $baseUrl = rtrim(get_setting('pathao_base_url') ?: env('PATHAO_BASE_URL', 'https://api-hermes.pathao.com'), '/');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($baseUrl . '/aladdin/api/v1/issue-token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }
        } catch (\Exception $e) {
            \Log::error('Pathao Access Token Cache Error: ' . $e->getMessage());
        }
        return null;
    });
}

function getPathaoDefaultStoreId($token = null)
{
    $settingStoreId = get_setting('pathao_store_id') ?: env('PATHAO_STORE_ID');
    if (!empty($settingStoreId)) {
        return (int)$settingStoreId;
    }

    if (!$token) {
        $token = getPathaoAccessToken();
    }

    if ($token) {
        $stores = getStoreList($token);
        if (isset($stores['data'][0]['store_id'])) {
            return (int)$stores['data'][0]['store_id'];
        }
    }

    return 371574; // Fallback to merchant's main store ID
}

function placePathaoOrder($storeId, $merchantOrderId, $recipientName, $recipientPhone, $recipientAddress, $recipientCity, $recipientZone, $recipientArea, $deliveryType, $itemType, $specialInstruction, $itemQuantity, $itemWeight, $amountToCollect)
{
    try {
        $token = getPathaoAccessToken();
        if (!$token) {
            return [
                'status' => 500,
                'message' => 'Pathao access token could not be issued. Please check your Pathao API credentials.'
            ];
        }

        if (empty($storeId) || $storeId == 1 || $storeId == '1') {
            $storeId = getPathaoDefaultStoreId($token);
        }

        $baseUrl = rtrim(get_setting('pathao_base_url') ?: env('PATHAO_BASE_URL', 'https://api-hermes.pathao.com'), '/');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=UTF-8',
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->post($baseUrl . '/aladdin/api/v1/orders', [
            'store_id' => (int)$storeId,
            'merchant_order_id' => (string)$merchantOrderId,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'recipient_address' => $recipientAddress,
            'recipient_city' => (int)($recipientCity ?: 1),
            'recipient_zone' => (int)($recipientZone ?: 1),
            'recipient_area' => (int)($recipientArea ?: 1),
            'delivery_type' => (int)($deliveryType ?: 48),
            'item_type' => (int)($itemType ?: 2),
            'special_instruction' => $specialInstruction,
            'item_quantity' => (int)($itemQuantity ?: 1),
            'item_weight' => (float)($itemWeight ?: 0.5),
            'amount_to_collect' => (int)$amountToCollect
        ]);

        return $response->json();
    } catch (\Exception $e) {
        \Log::error('Pathao Order Placement Error: ' . $e->getMessage());
        return [
            'status' => 500,
            'message' => $e->getMessage()
        ];
    }
}

function getStoreList($accessToken)
{
    $baseUrl = rtrim(get_setting('pathao_base_url') ?: env('PATHAO_BASE_URL', 'https://api-hermes.pathao.com'), '/');
    $response = Http::withHeaders([
        'Content-Type' => 'application/json; charset=UTF-8',
        'Authorization' => 'Bearer ' . $accessToken,
        'Accept' => 'application/json'
    ])->get($baseUrl . '/aladdin/api/v1/store-list', []);

    if ($response->successful()) {
        return $response->json(); // Returns store list
    }
    return $response->json(); // Returns error message
}
function getCityList($accessToken)
{
    $response = Http::withHeaders([
        'Content-Type' => 'application/json; charset=UTF-8',
        'Authorization' => 'Bearer ' . $accessToken,
    ])->get(env('PATHAO_BASE_URL') . '/aladdin/api/v1/city-list', []);

    if ($response->successful()) {
        return $response->json(); // Returns the city list
    }
    return $response->json(); // Returns error message
}
function getZoneList($cityId, $accessToken)
{
    $response = Http::withHeaders([
        'Content-Type' => 'application/json; charset=UTF-8',
        'Authorization' => 'Bearer ' . $accessToken,
    ])->get(env('PATHAO_BASE_URL') . "/aladdin/api/v1/cities/{$cityId}/zone-list", []);

    if ($response->successful()) {
        return $response->json(); // Returns zone list
    }
    return $response->json(); // Returns error message
}
function getAreaList($zoneId, $accessToken)
{
    $response = Http::withHeaders([
        'Content-Type' => 'application/json; charset=UTF-8',
        'Authorization' => 'Bearer ' . $accessToken,
    ])->get(env('PATHAO_BASE_URL') . "/aladdin/api/v1/zones/{$zoneId}/area-list", []);
    if ($response->successful()) {
        return $response->json(); // Returns area list
    }
    return $response->json(); // Returns error message
}

function getOrderSummary($consignmentId, $accessToken)
{
    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=UTF-8',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get(env('PATHAO_BASE_URL') . "/aladdin/api/v1/orders/{$consignmentId}/info");

        if ($response->successful()) {
            return $response->json();
        }
        return $response->json() ?? [];
    } catch (\Exception $e) {
        \Log::error('Pathao Get Order Summary Error: ' . $e->getMessage());
        return [];
    }
}

function status($consignmentId)
{
    try {
        $apiKey = get_setting('steadfast_api_key') ?: env('STEADFAST_API_KEY');
        $secretKey = get_setting('steadfast_secret_key') ?: env('STEADFAST_SECRET_KEY');
        $baseUrl = rtrim(env('STEADFAST_BASE_URL', 'https://portal.packzy.com/api/v1'), '/');
        if (str_contains($baseUrl, 'portal.steadfast.com.bd')) {
            $baseUrl = 'https://portal.packzy.com/api/v1';
        }

        $response = Http::withoutVerifying()->withHeaders([
            'Api-Key' => $apiKey,
            'Secret-Key' => $secretKey,
            'Content-Type' => 'application/json'
        ])->get($baseUrl . "/status_by_cid/{$consignmentId}");

        if ($response->successful()) {
            return $response->json();
        }
        return $response->json() ?? [];
    } catch (\Exception $e) {
        \Log::error('Steadfast Status Error: ' . $e->getMessage());
        return [];
    }
}


function barcodeNew($code)
{
    // dd($code);
    $generatorSVG = new BarcodeGeneratorSVG();
    $code = $generatorSVG->getBarcode($code, $generatorSVG::TYPE_CODE_128, 1.3, 33);
    return $code;
}

function logActivity($action, $description = null, $model = null, $data = null)
{
    try {
        $log = new \App\Models\ActivityLog();
        $log->user_id = auth()->id();
        $log->branch_id = session('branch_filter_id') ?? (auth()->check() ? auth()->user()->branch_id : null);
        $log->action = $action;
        $log->description = $description;
        if ($model) {
            if (is_object($model)) {
                $log->model_type = get_class($model);
                $log->model_id = $model->id ?? null;
                
                // Auto capture model data if not provided
                if (!$data) {
                    $data = $model->toArray();
                }
            } else if (is_array($model)) {
                if (!$data) {
                    $data = $model;
                }
            }
        }

        if ($data) {
            $log->data = is_array($data) ? json_encode($data) : $data;
        }

        $log->ip_address = request()->ip();
        $log->save();
    } catch (\Throwable $e) {
        \Log::error("Activity Log Error: " . $e->getMessage());
    }
}

function recalculateInvoiceProfit($productId, $branchId)
{
    $product = \App\Models\Product::find($productId);
    if (!$product || $product->is_service == 1) {
        return;
    }

    $purchaseItems = \App\Models\PurchaseItem::where('product_id', $productId)
        ->where('branch_id', $branchId)
        ->orderBy('date', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    $factor = ($product->unit && $product->unit->related_value) ? $product->unit->related_value : 1;
    foreach ($purchaseItems as $pi) {
        if ($product->unit && $product->unit->related_unit == null) {
            $pi->temp_qty = $pi->main_qty;
        } else {
            $pi->temp_qty = ($pi->main_qty * $factor) + $pi->sub_qty;
        }
    }

    $invoiceItems = \App\Models\InvoiceItem::where('product_id', $productId)
        ->where('branch_id', $branchId)
        ->orderBy('date', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    foreach ($invoiceItems as $ii) {
        if ($product->unit && $product->unit->related_unit == null) {
            $saleQty = $ii->main_qty;
        } else {
            $saleQty = ($ii->main_qty * $factor) + ($ii->sub_qty ?? 0);
        }

        $remainingQty = $saleQty;
        $totalCost = 0;

        foreach ($purchaseItems as $pi) {
            if ($remainingQty <= 0) break;

            $availableQty = $pi->temp_qty;
            if ($availableQty <= 0) continue;

            $usedQty = min($remainingQty, $availableQty);
            
            if ($product->unit && $product->unit->related_unit == null) {
                $totalCost += $usedQty * $pi->rate;
            } else {
                $totalCost += ($usedQty / $factor) * $pi->rate;
            }

            $pi->temp_qty -= $usedQty;
            $remainingQty -= $usedQty;
        }

        if ($remainingQty > 0 && $purchaseItems->isNotEmpty()) {
            $lastPi = $purchaseItems->last();
            if ($product->unit && $product->unit->related_unit == null) {
                $totalCost += $remainingQty * $lastPi->rate;
            } else {
                $totalCost += ($remainingQty / $factor) * $lastPi->rate;
            }
        }

        $ii->pur_subtotal = $totalCost;
        $ii->save();
    }
}

/**
 * Resolve a scanned barcode (which may be a variation barcode = product_barcode + variation_id)
 * back to the parent product ID and optional variation ID.
 *
 * Returns: ['product_id' => int|null, 'variation_id' => int|null, 'product' => Product|null]
 */
if (!function_exists('resolveProductAndVariationFromBarcode')) {
    function resolveProductAndVariationFromBarcode($scannedCode)
    {
        $scannedCode = trim($scannedCode);

        // 1. First try exact match on product barcode
        $product = \App\Models\Product::where('barcode', $scannedCode)->first();
        if ($product) {
            return [
                'product_id'   => $product->id,
                'variation_id' => null,
                'product'      => $product,
            ];
        }

        // 2. Try to parse as concatenated barcode: product_barcode + variation_id
        if (is_numeric($scannedCode) && strlen($scannedCode) >= 3) {
            $candidates = \App\Models\Product::whereNotNull('barcode')
                ->where('status', 1)
                ->whereRaw('? LIKE CONCAT(barcode, "%")', [$scannedCode])
                ->get(['id', 'barcode']);

            foreach ($candidates as $candidate) {
                $base = (string) $candidate->barcode;
                if ($base === '' || strpos($scannedCode, $base) !== 0) {
                    continue;
                }
                $suffix = substr($scannedCode, strlen($base));
                if (!is_numeric($suffix) || $suffix === '') {
                    continue;
                }
                $variationId = (int) $suffix;
                $variationExists = \App\Models\ProductVariation::where('id', $variationId)
                    ->where('product_id', $candidate->id)
                    ->exists();
                if ($variationExists) {
                    return [
                        'product_id'   => $candidate->id,
                        'variation_id' => $variationId,
                        'product'      => $candidate,
                    ];
                }
            }
        }

        return ['product_id' => null, 'variation_id' => null, 'product' => null];
    }
}

if (!function_exists('zatca_qr_code')) {
    /**
     * Generate ZATCA Phase-1 Base64 TLV string for QR Code
     *
     * @param string $sellerName Business Name
     * @param string $vatNumber 15-digit VAT Number
     * @param string|\DateTimeInterface $timestamp Invoice Timestamp
     * @param float|string $totalAmount Invoice Total (including VAT)
     * @param float|string $vatAmount VAT/Tax Amount
     * @return string Base64 string
     */
    function zatca_qr_code($sellerName, $vatNumber, $timestamp, $totalAmount, $vatAmount)
    {
        return \App\Services\ZatcaService::generateBase64Qr(
            $sellerName,
            $vatNumber,
            $timestamp,
            $totalAmount,
            $vatAmount
        );
    }
}

if (!function_exists('autoPayInvoiceDue')) {
    /**
     * Auto clear due for an invoice when courier delivers the product or when user clears due manually
     *
     * @param \App\Models\Invoice $invoice
     * @param int|null $bankId
     * @param string|null $paymentDate
     * @return bool
     */
    function autoPayInvoiceDue($invoice, $bankId = null, $paymentDate = null)
    {
        if (!$invoice || $invoice->total_due <= 0 || $invoice->status == 2) {
            return false;
        }

        if (!$bankId) {
            $defaultBank = \App\Models\BankAccount::where('status', 1)->first();
            $bankId = $defaultBank ? $defaultBank->id : 1;
        }

        $transDate = $paymentDate ?: \Illuminate\Support\Carbon::now()->format('Y-m-d');
        $dueAmount = (float) $invoice->total_due;

        $invoice->total_paid = (float) $invoice->total_paid + $dueAmount;
        $invoice->due_pay = (float) $invoice->due_pay + $dueAmount;
        $invoice->total_due = 0.00;
        $invoice->status = 1; // Mark as paid

        \Illuminate\Support\Facades\DB::transaction(function () use ($invoice, $dueAmount, $bankId, $transDate) {
            $invoice->save();

            // 1. Create Bank Transaction (Deposit)
            $bank_transaction = new \App\Models\BankTransaction();
            $bank_transaction->trans_type = 'deposit';
            $bank_transaction->pay_type = 'duepay';
            $bank_transaction->branch_id = $invoice->branch_id;
            $bank_transaction->date = $transDate;
            $bank_transaction->bank_id = $bankId;
            $bank_transaction->invoice_id = $invoice->id;
            $bank_transaction->amount = $dueAmount;
            $bank_transaction->created_by = auth()->id() ?? $invoice->created_by ?? 1;
            $bank_transaction->save();

            // 2. Create Customer Transaction (Received from Customer)
            $transaction = new \App\Models\Transaction();
            $transaction->transaction_type = 'Received from Customer';
            $transaction->branch_id = $invoice->branch_id;
            $transaction->date = $transDate;
            $transaction->bank_id = $bankId;
            $transaction->invoice_id = $invoice->id;
            $transaction->customer_id = $invoice->customer_id;
            $transaction->debit = null;
            $transaction->credit = $dueAmount;
            $transaction->created_by = auth()->id() ?? $invoice->created_by ?? 1;
            $transaction->save();
        });

        logActivity('Courier Due Payment', "Due paid {$dueAmount} for invoice #{$invoice->invoice_no}", $invoice);
        return true;
    }
}

if (!function_exists('syncInvoiceCourierStatusAndAutoPay')) {
    /**
     * Sync courier status for an invoice and auto pay due if delivered
     *
     * @param \App\Models\Invoice $invoice
     * @return bool Returns true if due was auto paid
     */
    function syncInvoiceCourierStatusAndAutoPay($invoice)
    {
        if (!$invoice || $invoice->total_due <= 0 || $invoice->status == 2) {
            return false;
        }

        $deliveredStatuses = [
            'delivered',
            'delivered approval pending',
            'delivered_approval_pending',
            'delivery completed',
            'delivery_completed',
            'completed'
        ];

        // 1. Check if DB order_status is already delivered
        if (!empty($invoice->order_status)) {
            $normalizedDbStatus = strtolower(trim(str_replace(['_', '-'], ' ', $invoice->order_status)));
            if (in_array($normalizedDbStatus, $deliveredStatuses) || str_contains($normalizedDbStatus, 'delivered')) {
                return autoPayInvoiceDue($invoice);
            }
        }

        // 2. Fetch live status if consignment_id is present
        if (!empty($invoice->consignment_id)) {
            $cType = strtolower(trim($invoice->courier_type ?? ''));
            $fetchedStatus = null;

            if ($cType == 'pathao') {
                $token = getPathaoAccessToken();
                if ($token) {
                    $summary = getOrderSummary($invoice->consignment_id, $token);
                    $fetchedStatus = $summary['data']['order_status'] ?? null;
                }
            } else {
                // Steadfast or default
                $summary = status($invoice->consignment_id);
                $fetchedStatus = $summary['delivery_status'] ?? null;
            }

            if ($fetchedStatus) {
                $invoice->order_status = $fetchedStatus;
                $invoice->save();

                $normalizedStatus = strtolower(trim(str_replace(['_', '-'], ' ', $fetchedStatus)));

                if (in_array($normalizedStatus, $deliveredStatuses) || str_contains($normalizedStatus, 'delivered')) {
                    return autoPayInvoiceDue($invoice);
                }
            }
        }

        return false;
    }
}

if (!function_exists('is_vat_enabled')) {
    /**
     * Check if VAT / Tax is enabled in Settings (Business Settings or SuperAdmin .env)
     *
     * @return bool
     */
    function is_vat_enabled()
    {
        $setting = function_exists('get_setting') ? get_setting('vat_include') : null;
        if ($setting !== null && $setting !== '') {
            return in_array(strtolower(trim($setting)), ['yes', '1', 'include', 'true', 'on']);
        }
        return in_array(strtolower(trim(env('APP_VAT', 'no'))), ['yes', '1', 'include', 'true', 'on']);
    }
}

if (!function_exists('is_warranty_enabled')) {
    /**
     * Check if Warranty is enabled
     *
     * @return bool
     */
    function is_warranty_enabled()
    {
        return in_array(strtolower(trim(env('APP_WARRANTY', 'no'))), ['yes', '1', 'true', 'on']);
    }
}

if (!function_exists('is_hide_customer_dates')) {
    /**
     * Check if Customer Birth Date and Anniversary Date should be hidden
     *
     * @return bool
     */
    function is_hide_customer_dates()
    {
        return strtolower(trim(env('HIDE_CUSTOMER_DATES', 'no'))) === 'yes';
    }
}

if (!function_exists('is_branch_switch_enabled')) {
    /**
     * Check if Branch Switching is enabled globally
     *
     * @return bool
     */
    function is_branch_switch_enabled()
    {
        $val = env('APP_BRANCH_SWITCH', 'yes');
        return strtolower(trim((string)$val)) !== 'no';
    }
}

if (!function_exists('is_rack_enabled')) {
    /**
     * Check if Rack Management System is enabled globally
     *
     * @return bool
     */
    function is_rack_enabled()
    {
        return strtolower(trim((string)env('APP_RACK', 'no'))) === 'yes';
    }
}

if (!function_exists('is_unit_enabled')) {
    /**
     * Check if Unit management is enabled.
     * Defaults to true ('yes') unless explicitly set to 'no'.
     *
     * @return bool
     */
    function is_unit_enabled()
    {
        return strtolower(trim((string)env('APP_UNIT', 'yes'))) !== 'no';
    }
}

if (!function_exists('is_invoice_qr_enabled')) {
    /**
     * Check if QR code on invoice printouts is enabled.
     * Defaults to true ('yes') unless explicitly set to 'no' in env (APP_QR_CODE) or business settings (inv_qr_code).
     *
     * @return bool
     */
    function is_invoice_qr_enabled()
    {
        if (strtolower(trim((string)env('APP_QR_CODE', 'yes'))) === 'no') {
            return false;
        }
        if (get_setting('inv_qr_code') === 'no') {
            return false;
        }
        return true;
    }
}

if (!function_exists('isImeiRepurchased')) {
    /**
     * Check if a sold IMEI for a product has been repurchased.
     *
     * An IMEI is considered repurchased if:
     * 1. It is currently active in stock (status = 1 in serial_numbers).
     * 2. Or there exists a serial_numbers / purchase entry for this IMEI created after the invoice was made.
     *
     * @param int $productId
     * @param string $imei
     * @param \App\Models\Invoice|int|null $invoice
     * @return bool
     */
    function isImeiRepurchased($productId, $imei, $invoice = null)
    {
        $imei = trim((string) $imei);
        if ($imei === '') {
            return false;
        }

        // 1. Is the IMEI currently in stock? (status = 1)
        $inStock = \App\Models\SerialNumber::where('product_id', $productId)
            ->where('serial', $imei)
            ->where('status', 1)
            ->exists();
        if ($inStock) {
            return true;
        }

        // 2. If an invoice is provided, check if a newer purchase or serial was created after this invoice
        if ($invoice) {
            if (is_numeric($invoice)) {
                $invoice = \App\Models\Invoice::find($invoice);
            }

            if ($invoice && $invoice->created_at) {
                // Check if any SerialNumber was created after this invoice
                $newerSerial = \App\Models\SerialNumber::where('product_id', $productId)
                    ->where('serial', $imei)
                    ->where('created_at', '>', $invoice->created_at)
                    ->exists();
                if ($newerSerial) {
                    return true;
                }

                // Check if any PurchaseItem was created after this invoice with this IMEI
                $newerPurchase = \App\Models\PurchaseItem::where('product_id', $productId)
                    ->where('created_at', '>', $invoice->created_at)
                    ->where(function ($q) use ($imei) {
                        $q->where('imei', $imei)
                          ->orWhere('imei', 'like', "%{$imei}%");
                    })
                    ->exists();
                if ($newerPurchase) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('invoiceHasRepurchasedImeis')) {
    /**
     * Check if an invoice contains any IMEI that has been repurchased.
     *
     * @param \App\Models\Invoice|int $invoice
     * @return array Array of repurchased IMEI strings (empty if none)
     */
    function invoiceHasRepurchasedImeis($invoice)
    {
        if (is_numeric($invoice)) {
            $invoice = \App\Models\Invoice::with('invoiceItems.product')->find($invoice);
        }
        if (!$invoice) {
            return [];
        }

        $repurchased = [];
        $items = $invoice->invoiceItems ?? \App\Models\InvoiceItem::where('invoice_id', $invoice->id)->get();

        foreach ($items as $item) {
            if (empty($item->imei)) {
                continue;
            }
            $imeis = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $item->imei)));
            foreach ($imeis as $imei) {
                if (isImeiRepurchased($item->product_id, $imei, $invoice)) {
                    $repurchased[] = $imei;
                }
            }
        }

        return array_values(array_unique($repurchased));
    }
}



