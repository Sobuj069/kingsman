<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Branch;
use App\Models\BranchCategory;
use App\Models\BranchBrand;
use App\Models\BranchProduct;

class MaitreSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maitre:sync 
                            {--direction=pull : Sync direction (pull from remote, or push to remote)}
                            {--show= : Show live data from remote API (options: categories, brands, customers, suppliers, products)}
                            {--dry-run : Perform comparison only without saving any changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch, display, and sync data (Categories, Brands, Customers, Suppliers, Products) with the remote Maitre software API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = env('MAITRE_API_URL', 'https://mait-re.fastitbd.com/api');
        // Normalize double slashes to single slash (except after http/https protocol)
        $baseUrl = preg_replace('/([^:])(\/{2,})/', '$1/', $baseUrl);

        $this->info("=== Maitre API Sync System ===");
        $this->info("Remote URL: $baseUrl");

        // 1. Show live data if requested
        $showOption = $this->option('show');
        if ($showOption) {
            $this->showRemoteData($baseUrl, $showOption);
            return 0;
        }

        // 2. Determine direction
        $direction = strtolower($this->option('direction'));
        if (!in_array($direction, ['pull', 'push'])) {
            $this->error("Invalid direction '$direction'. Supported directions: pull, push");
            return 1;
        }

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn("!!! DRY RUN MODE ACTIVE - No changes will be written !!!");
        }

        $this->info("Fetching remote data for sync...");
        $remoteData = $this->fetchRemoteData($baseUrl);
        if (!$remoteData) {
            $this->error("Failed to fetch remote data. Aborting sync.");
            return 1;
        }

        if ($direction === 'pull') {
            $this->info("Starting PULL sync (Remote -> Local)...");
            $this->syncPull($remoteData, $dryRun);
        } else {
            $this->info("Starting PUSH sync (Local -> Remote)...");
            $this->syncPush($remoteData, $baseUrl, $dryRun);
        }

        return 0;
    }

    /**
     * Helper to get configured HTTP client
     */
    protected function getClient()
    {
        $client = Http::asJson()->timeout(30);
        $token = env('MAITRE_API_TOKEN');
        if ($token) {
            $client = $client->withToken($token);
        }
        return $client;
    }

    /**
     * Fetch all relevant data from the remote server
     */
    protected function fetchRemoteData($baseUrl)
    {
        $data = [
            'categories' => [],
            'brands' => [],
            'customers' => [],
            'suppliers' => [],
            'products' => []
        ];

        foreach (array_keys($data) as $type) {
            $url = "$baseUrl/$type";
            try {
                $response = $this->getClient()->get($url);
                if ($response->successful()) {
                    $json = $response->json();
                    $data[$type] = $json['data'] ?? $json ?? [];
                } else {
                    $this->error("Failed to fetch $type. Status: " . $response->status());
                    return null;
                }
            } catch (\Exception $e) {
                $this->error("Error fetching $type: " . $e->getMessage());
                return null;
            }
        }

        return $data;
    }

    /**
     * Show live data in a table format
     */
    protected function showRemoteData($baseUrl, $type)
    {
        $validTypes = ['categories', 'brands', 'customers', 'suppliers', 'products'];
        if (!in_array($type, $validTypes)) {
            $this->error("Invalid type '$type'. Supported types: " . implode(', ', $validTypes));
            return;
        }

        $this->info("Fetching live $type data from remote server...");
        $url = "$baseUrl/$type";

        try {
            $response = $this->getClient()->get($url);
            if (!$response->successful()) {
                $this->error("Failed to fetch. Status: " . $response->status());
                return;
            }

            $json = $response->json();
            $items = $json['data'] ?? $json ?? [];

            if (empty($items)) {
                $this->warn("No records found on the remote server for $type.");
                return;
            }

            $this->info("Total remote records found: " . count($items));

            // Select only first 20 for preview to keep output readable
            $previewItems = array_slice($items, 0, 20);

            switch ($type) {
                case 'categories':
                    $headers = ['ID', 'Name', 'Created At'];
                    $rows = array_map(fn($c) => [$c['id'] ?? '', $c['name'] ?? '', $c['created_at'] ?? ''], $previewItems);
                    break;
                case 'brands':
                    $headers = ['ID', 'Name', 'Slug', 'Created At'];
                    $rows = array_map(fn($b) => [$b['id'] ?? '', $b['name'] ?? '', $b['slug'] ?? '', $b['created_at'] ?? ''], $previewItems);
                    break;
                case 'customers':
                    $headers = ['ID', 'Name', 'Phone', 'Email', 'Address'];
                    $rows = array_map(fn($c) => [$c['id'] ?? '', $c['name'] ?? '', $c['phone'] ?? '', $c['email'] ?? '', $c['address'] ?? ''], $previewItems);
                    break;
                case 'suppliers':
                    $headers = ['ID', 'Name', 'Phone', 'Email', 'Address'];
                    $rows = array_map(fn($s) => [$s['id'] ?? '', $s['name'] ?? '', $s['phone'] ?? '', $s['email'] ?? '', $s['address'] ?? ''], $previewItems);
                    break;
                case 'products':
                    $headers = ['ID', 'Name', 'Barcode', 'Category ID', 'Brand ID', 'Price'];
                    $rows = array_map(fn($p) => [$p['id'] ?? '', $p['name'] ?? '', $p['barcode'] ?? '', $p['category_id'] ?? '', $p['brand_id'] ?? '', $p['selling_price'] ?? ''], $previewItems);
                    break;
            }

            $this->table($headers, $rows);
            if (count($items) > 20) {
                $this->info("... showing first 20 records of " . count($items) . " total.");
            }
        } catch (\Exception $e) {
            $this->error("Error retrieving data: " . $e->getMessage());
        }
    }

    /**
     * PULL Sync: Remote -> Local
     */
    protected function syncPull($remote, $dryRun)
    {
        $branches = Branch::all();
        $branchIds = $branches->pluck('id')->toArray();
        $results = [];

        // 1. Categories
        $catUpdated = 0; $catCreated = 0; $catFailed = 0;
        $this->info("\n--- Importing Categories ---");
        foreach ($remote['categories'] as $item) {
            if (!isset($item['id']) || !isset($item['name'])) continue;

            // Resolve duplicate unique keys (name)
            if (!$dryRun) {
                $dup = Category::where('name', $item['name'])->first();
                if ($dup && $dup->id != $item['id']) {
                    $dup->delete();
                }
            }

            $exists = Category::find($item['id']);
            $this->comment(($exists ? "Updating" : "Creating") . " category: {$item['name']} (ID: {$item['id']})");

            if ($dryRun) {
                if ($exists) $catUpdated++; else $catCreated++;
                continue;
            }

            try {
                $category = Category::updateOrCreate(
                    ['id' => $item['id']],
                    ['name' => $item['name']]
                );

                // Link to all branches
                foreach ($branchIds as $branchId) {
                    BranchCategory::firstOrCreate([
                        'branch_id' => $branchId,
                        'category_id' => $category->id
                    ]);
                }

                if ($exists) $catUpdated++; else $catCreated++;
            } catch (\Exception $e) {
                $this->error("Error importing category '{$item['name']}': " . $e->getMessage());
                $catFailed++;
            }
        }
        $results['Categories'] = ['Total' => count($remote['categories']), 'Created' => $catCreated, 'Updated' => $catUpdated, 'Failed' => $catFailed];

        // 2. Brands
        $brandUpdated = 0; $brandCreated = 0; $brandFailed = 0;
        $this->info("\n--- Importing Brands ---");
        foreach ($remote['brands'] as $item) {
            if (!isset($item['id']) || !isset($item['name'])) continue;

            // Resolve duplicate unique keys (name)
            if (!$dryRun) {
                $dup = Brand::where('name', $item['name'])->first();
                if ($dup && $dup->id != $item['id']) {
                    $dup->delete();
                }
            }

            $exists = Brand::find($item['id']);
            $this->comment(($exists ? "Updating" : "Creating") . " brand: {$item['name']} (ID: {$item['id']})");

            if ($dryRun) {
                if ($exists) $brandUpdated++; else $brandCreated++;
                continue;
            }

            try {
                $brand = Brand::updateOrCreate(
                    ['id' => $item['id']],
                    [
                        'name' => $item['name'],
                        'slug' => $item['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $item['name'])))
                    ]
                );

                // Link to all branches
                foreach ($branchIds as $branchId) {
                    BranchBrand::firstOrCreate([
                        'branch_id' => $branchId,
                        'brand_id' => $brand->id
                    ]);
                }

                if ($exists) $brandUpdated++; else $brandCreated++;
            } catch (\Exception $e) {
                $this->error("Error importing brand '{$item['name']}': " . $e->getMessage());
                $brandFailed++;
            }
        }
        $results['Brands'] = ['Total' => count($remote['brands']), 'Created' => $brandCreated, 'Updated' => $brandUpdated, 'Failed' => $brandFailed];

        // 3. Customers
        $custUpdated = 0; $custCreated = 0; $custFailed = 0;
        $this->info("\n--- Importing Customers ---");
        foreach ($remote['customers'] as $item) {
            if (!isset($item['id']) || !isset($item['name'])) continue;

            $branchId = (isset($item['branch_id']) && in_array($item['branch_id'], $branchIds)) ? $item['branch_id'] : ($branchIds[0] ?? 1);

            // Resolve duplicate unique keys (phone per branch)
            if (!$dryRun && isset($item['phone']) && trim($item['phone']) !== '') {
                $dup = Customer::where('phone', $item['phone'])->where('branch_id', $branchId)->first();
                if ($dup && $dup->id != $item['id']) {
                    $dup->delete();
                }
            }

            $exists = Customer::find($item['id']);
            $this->comment(($exists ? "Updating" : "Creating") . " customer: {$item['name']} (ID: {$item['id']})");

            if ($dryRun) {
                if ($exists) $custUpdated++; else $custCreated++;
                continue;
            }

            try {
                Customer::updateOrCreate(
                    ['id' => $item['id']],
                    [
                        'name' => $item['name'],
                        'phone' => $item['phone'] ?? '',
                        'email' => $item['email'] ?? null,
                        'address' => $item['address'] ?? null,
                        'due_amount' => $item['due_amount'] ?? 0,
                        'member_id' => $item['member_id'] ?? rand(100000000, 999999999),
                        'branch_id' => $branchId,
                        'date' => $item['date'] ?? date('Y-m-d'),
                        'customer_type' => $item['customer_type'] ?? null,
                        'vehicle_name' => $item['vehicle_name'] ?? null,
                        'reg_no' => $item['reg_no'] ?? null,
                        'model' => $item['model'] ?? null,
                        'made_in' => $item['made_in'] ?? null,
                        'engine_no' => $item['engine_no'] ?? null,
                        'chassis_no' => $item['chassis_no'] ?? null,
                        'birth_date' => $item['birth_date'] ?? null,
                        'anni_date' => $item['anni_date'] ?? null,
                        'total_point' => $item['total_point'] ?? 0,
                        'status' => $item['status'] ?? 1,
                    ]
                );

                if ($exists) $custUpdated++; else $custCreated++;
            } catch (\Exception $e) {
                $this->error("Error importing customer '{$item['name']}': " . $e->getMessage());
                $custFailed++;
            }
        }
        $results['Customers'] = ['Total' => count($remote['customers']), 'Created' => $custCreated, 'Updated' => $custUpdated, 'Failed' => $custFailed];

        // 4. Suppliers
        $supUpdated = 0; $supCreated = 0; $supFailed = 0;
        $this->info("\n--- Importing Suppliers ---");
        foreach ($remote['suppliers'] as $item) {
            if (!isset($item['id']) || !isset($item['name'])) continue;

            $branchId = (isset($item['branch_id']) && in_array($item['branch_id'], $branchIds)) ? $item['branch_id'] : ($branchIds[0] ?? 1);

            // Resolve duplicate unique keys (phone per branch)
            if (!$dryRun && isset($item['phone']) && trim($item['phone']) !== '') {
                $dup = Supplier::where('phone', $item['phone'])->where('branch_id', $branchId)->first();
                if ($dup && $dup->id != $item['id']) {
                    $dup->delete();
                }
            }

            $exists = Supplier::find($item['id']);
            $this->comment(($exists ? "Updating" : "Creating") . " supplier: {$item['name']} (ID: {$item['id']})");

            if ($dryRun) {
                if ($exists) $supUpdated++; else $supCreated++;
                continue;
            }

            try {
                Supplier::updateOrCreate(
                    ['id' => $item['id']],
                    [
                        'name' => $item['name'],
                        'phone' => $item['phone'] ?? '',
                        'email' => $item['email'] ?? null,
                        'address' => $item['address'] ?? null,
                        'advance_amount' => $item['advance_amount'] ?? 0,
                        'due_amount' => $item['due_amount'] ?? 0,
                        'branch_id' => $branchId,
                        'date' => $item['date'] ?? date('Y-m-d'),
                        'status' => $item['status'] ?? 1,
                    ]
                );

                if ($exists) $supUpdated++; else $supCreated++;
            } catch (\Exception $e) {
                $this->error("Error importing supplier '{$item['name']}': " . $e->getMessage());
                $supFailed++;
            }
        }
        $results['Suppliers'] = ['Total' => count($remote['suppliers']), 'Created' => $supCreated, 'Updated' => $supUpdated, 'Failed' => $supFailed];

        // 5. Products
        $prodUpdated = 0; $prodCreated = 0; $prodFailed = 0;
        $this->info("\n--- Importing Products ---");
        foreach ($remote['products'] as $item) {
            if (!isset($item['id']) || !isset($item['name'])) continue;

            // Resolve duplicate unique keys (barcode or name)
            if (!$dryRun) {
                if (isset($item['barcode']) && trim($item['barcode']) !== '') {
                    $dup = Product::where('barcode', $item['barcode'])->first();
                    if ($dup && $dup->id != $item['id']) {
                        $dup->delete();
                    }
                }
                $dup2 = Product::where('name', $item['name'])->first();
                if ($dup2 && $dup2->id != $item['id']) {
                    $dup2->delete();
                }
            }

            $exists = Product::find($item['id']);
            $this->comment(($exists ? "Updating" : "Creating") . " product: {$item['name']} (ID: {$item['id']})");

            if ($dryRun) {
                if ($exists) $prodUpdated++; else $prodCreated++;
                continue;
            }

            try {
                $product = Product::updateOrCreate(
                    ['id' => $item['id']],
                    [
                        'name' => $item['name'],
                        'barcode' => $item['barcode'] ?? '',
                        'category_id' => $item['category_id'] ?? null,
                        'brand_id' => $item['brand_id'] ?? null,
                        'unit_id' => $item['unit_id'] ?? 1,
                        'main_qty' => $item['main_qty'] ?? null,
                        'sub_qty' => $item['sub_qty'] ?? null,
                        'purchase_price' => $item['purchase_price'] ?? 0,
                        'selling_price' => $item['selling_price'] ?? 0,
                        'dis_selling_price' => $item['dis_selling_price'] ?? 0,
                        'discount' => $item['discount'] ?? 0,
                        'is_service' => $item['is_service'] ?? 0,
                        'has_warranty' => $item['has_warranty'] ?? 0,
                        'warranty_value' => $item['warranty_value'] ?? null,
                        'warranty_unit' => $item['warranty_unit'] ?? null,
                        'description' => $item['description'] ?? null,
                        'has_serial' => $item['has_serial'] ?? null,
                        'images' => $item['images'] ?? null,
                        'status' => $item['status'] ?? 1,
                        'date' => $item['date'] ?? date('Y-m-d'),
                        'imei' => $item['imei'] ?? null,
                    ]
                );

                // Link to all branches
                foreach ($branchIds as $branchId) {
                    BranchProduct::firstOrCreate([
                        'branch_id' => $branchId,
                        'product_id' => $product->id
                    ]);
                }

                if ($exists) $prodUpdated++; else $prodCreated++;
            } catch (\Exception $e) {
                $this->error("Error importing product '{$item['name']}': " . $e->getMessage());
                $prodFailed++;
            }
        }
        $results['Products'] = ['Total' => count($remote['products']), 'Created' => $prodCreated, 'Updated' => $prodUpdated, 'Failed' => $prodFailed];

        // Print Summary Table
        $this->info("\n=== PULL Import Summary ===");
        $summaryHeaders = ['Entity Type', 'Remote Total', 'Locally Created', 'Locally Updated', 'Failed'];
        $summaryRows = [];
        foreach ($results as $type => $counts) {
            $summaryRows[] = [
                $type,
                $counts['Total'],
                $counts['Created'],
                $counts['Updated'],
                $counts['Failed']
            ];
        }
        $this->table($summaryHeaders, $summaryRows);
    }

    /**
     * PUSH Sync: Local -> Remote
     */
    protected function syncPush($remote, $baseUrl, $dryRun)
    {
        // Setup lookup maps from remote data
        $remoteCategories = [];
        foreach ($remote['categories'] as $c) {
            if (isset($c['name'])) {
                $remoteCategories[strtolower(trim($c['name']))] = $c['id'];
            }
        }

        $remoteBrands = [];
        foreach ($remote['brands'] as $b) {
            if (isset($b['name'])) {
                $remoteBrands[strtolower(trim($b['name']))] = $b['id'];
            }
        }

        $remoteCustomers = [];
        foreach ($remote['customers'] as $cust) {
            if (isset($cust['phone'])) {
                $remoteCustomers[trim($cust['phone'])] = $cust['id'];
            }
        }

        $remoteSuppliers = [];
        foreach ($remote['suppliers'] as $sup) {
            if (isset($sup['phone'])) {
                $remoteSuppliers[trim($sup['phone'])] = $sup['id'];
            }
        }

        $remoteProducts = [];
        foreach ($remote['products'] as $p) {
            if (isset($p['barcode']) && trim($p['barcode']) !== '') {
                $remoteProducts[trim($p['barcode'])] = $p['id'];
            }
            if (isset($p['name'])) {
                $remoteProducts[strtolower(trim($p['name']))] = $p['id'];
            }
        }

        $results = [];

        // 1. Sync Categories
        $localCategories = Category::all();
        $catSynced = 0; $catFailed = 0; $catSkipped = 0;
        $this->info("\n--- Syncing Categories ---");
        foreach ($localCategories as $cat) {
            $nameKey = strtolower(trim($cat->name));
            if (isset($remoteCategories[$nameKey])) {
                $catSkipped++;
                continue;
            }

            $this->comment("Syncing category: {$cat->name}");
            if ($dryRun) {
                $catSynced++;
                $remoteCategories[$nameKey] = 'dry-run-id';
                continue;
            }

            try {
                $response = $this->getClient()->post("$baseUrl/categories", [
                    'name' => $cat->name
                ]);

                if ($response->successful()) {
                    $resJson = $response->json();
                    $remoteId = $resJson['data']['id'] ?? $resJson['id'] ?? null;
                    if ($remoteId) {
                        $remoteCategories[$nameKey] = $remoteId;
                        $catSynced++;
                    } else {
                        $this->warn("Category synced but failed to parse returned ID.");
                        $catFailed++;
                    }
                } else {
                    $this->error("Failed to sync category '{$cat->name}'. Status: " . $response->status() . " Response: " . $response->body());
                    $catFailed++;
                }
            } catch (\Exception $e) {
                $this->error("Error syncing category '{$cat->name}': " . $e->getMessage());
                $catFailed++;
            }
        }
        $results['Categories'] = ['Total' => count($localCategories), 'RemoteExist' => count($remote['categories']), 'Synced' => $catSynced, 'Failed' => $catFailed, 'Skipped' => $catSkipped];

        // 2. Sync Brands
        $localBrands = Brand::all();
        $brandSynced = 0; $brandFailed = 0; $brandSkipped = 0;
        $this->info("\n--- Syncing Brands ---");
        foreach ($localBrands as $brand) {
            $nameKey = strtolower(trim($brand->name));
            if (isset($remoteBrands[$nameKey])) {
                $brandSkipped++;
                continue;
            }

            $this->comment("Syncing brand: {$brand->name}");
            if ($dryRun) {
                $brandSynced++;
                $remoteBrands[$nameKey] = 'dry-run-id';
                continue;
            }

            try {
                $response = $this->getClient()->post("$baseUrl/brands", [
                    'name' => $brand->name,
                    'slug' => $brand->slug ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $brand->name))),
                ]);

                if ($response->successful()) {
                    $resJson = $response->json();
                    $remoteId = $resJson['data']['id'] ?? $resJson['id'] ?? null;
                    if ($remoteId) {
                        $remoteBrands[$nameKey] = $remoteId;
                        $brandSynced++;
                    } else {
                        $this->warn("Brand synced but failed to parse ID.");
                        $brandFailed++;
                    }
                } else {
                    $this->error("Failed to sync brand '{$brand->name}'. Status: " . $response->status());
                    $brandFailed++;
                }
            } catch (\Exception $e) {
                $this->error("Error syncing brand '{$brand->name}': " . $e->getMessage());
                $brandFailed++;
            }
        }
        $results['Brands'] = ['Total' => count($localBrands), 'RemoteExist' => count($remote['brands']), 'Synced' => $brandSynced, 'Failed' => $brandFailed, 'Skipped' => $brandSkipped];

        // 3. Sync Customers
        $localCustomers = Customer::all();
        $custSynced = 0; $custFailed = 0; $custSkipped = 0;
        $this->info("\n--- Syncing Customers ---");
        foreach ($localCustomers as $cust) {
            $phoneKey = trim($cust->phone);
            if (empty($phoneKey)) {
                $custSkipped++;
                continue;
            }
            if (isset($remoteCustomers[$phoneKey])) {
                $custSkipped++;
                continue;
            }

            $this->comment("Syncing customer: {$cust->name} ({$cust->phone})");
            if ($dryRun) {
                $custSynced++;
                continue;
            }

            try {
                $response = $this->getClient()->post("$baseUrl/customers", [
                    'name' => $cust->name,
                    'phone' => $cust->phone,
                    'email' => $cust->email,
                    'address' => $cust->address,
                    'due_amount' => $cust->due_amount ?? 0,
                    'member_id' => $cust->member_id,
                    'vehicle_name' => $cust->vehicle_name,
                    'reg_no' => $cust->reg_no,
                    'model' => $cust->model,
                    'made_in' => $cust->made_in,
                    'engine_no' => $cust->engine_no,
                    'chassis_no' => $cust->chassis_no,
                    'customer_type' => $cust->customer_type,
                ]);

                if ($response->successful()) {
                    $custSynced++;
                } else {
                    $this->error("Failed to sync customer '{$cust->name}'. Status: " . $response->status() . " Body: " . $response->body());
                    $custFailed++;
                }
            } catch (\Exception $e) {
                $this->error("Error syncing customer '{$cust->name}': " . $e->getMessage());
                $custFailed++;
            }
        }
        $results['Customers'] = ['Total' => count($localCustomers), 'RemoteExist' => count($remote['customers']), 'Synced' => $custSynced, 'Failed' => $custFailed, 'Skipped' => $custSkipped];

        // 4. Sync Suppliers
        $localSuppliers = Supplier::all();
        $supSynced = 0; $supFailed = 0; $supSkipped = 0;
        $this->info("\n--- Syncing Suppliers ---");
        foreach ($localSuppliers as $sup) {
            $phoneKey = trim($sup->phone);
            if (empty($phoneKey)) {
                $supSkipped++;
                continue;
            }
            if (isset($remoteSuppliers[$phoneKey])) {
                $supSkipped++;
                continue;
            }

            $this->comment("Syncing supplier: {$sup->name} ({$sup->phone})");
            if ($dryRun) {
                $supSynced++;
                continue;
            }

            try {
                $response = $this->getClient()->post("$baseUrl/suppliers", [
                    'name' => $sup->name,
                    'phone' => $sup->phone,
                    'email' => $sup->email,
                    'address' => $sup->address,
                    'advance_amount' => $sup->advance_amount ?? 0,
                    'due_amount' => $sup->due_amount ?? 0,
                ]);

                if ($response->successful()) {
                    $supSynced++;
                } else {
                    $this->error("Failed to sync supplier '{$sup->name}'. Status: " . $response->status() . " Body: " . $response->body());
                    $supFailed++;
                }
            } catch (\Exception $e) {
                $this->error("Error syncing supplier '{$sup->name}': " . $e->getMessage());
                $supFailed++;
            }
        }
        $results['Suppliers'] = ['Total' => count($localSuppliers), 'RemoteExist' => count($remote['suppliers']), 'Synced' => $supSynced, 'Failed' => $supFailed, 'Skipped' => $supSkipped];

        // 5. Sync Products
        $localProducts = Product::with(['category', 'brand', 'unit'])->get();
        $prodSynced = 0; $prodFailed = 0; $prodSkipped = 0;
        $this->info("\n--- Syncing Products ---");
        foreach ($localProducts as $prod) {
            $barcodeKey = trim($prod->barcode);
            $nameKey = strtolower(trim($prod->name));

            // Check if already on server by barcode or name
            $remoteId = null;
            if ($barcodeKey !== '' && isset($remoteProducts[$barcodeKey])) {
                $remoteId = $remoteProducts[$barcodeKey];
            } elseif (isset($remoteProducts[$nameKey])) {
                $remoteId = $remoteProducts[$nameKey];
            }

            if ($remoteId) {
                $prodSkipped++;
                continue;
            }

            $this->comment("Syncing product: {$prod->name} (Barcode: {$prod->barcode})");

            // Resolve mapped remote Category ID
            $remoteCatId = 1; // fallback default
            if ($prod->category) {
                $catName = strtolower(trim($prod->category->name));
                if (isset($remoteCategories[$catName])) {
                    $remoteCatId = $remoteCategories[$catName];
                }
            }

            // Resolve mapped remote Brand ID
            $remoteBrandId = null;
            if ($prod->brand) {
                $brandName = strtolower(trim($prod->brand->name));
                if (isset($remoteBrands[$brandName])) {
                    $remoteBrandId = $remoteBrands[$brandName];
                }
            }

            if ($dryRun) {
                $prodSynced++;
                continue;
            }

            try {
                $response = $this->getClient()->post("$baseUrl/products", [
                    'name' => $prod->name,
                    'barcode' => $prod->barcode,
                    'category_id' => $remoteCatId,
                    'brand_id' => $remoteBrandId,
                    'unit_id' => $prod->unit_id ?? 1,
                    'purchase_price' => $prod->purchase_price ?? 0,
                    'selling_price' => $prod->selling_price ?? 0,
                    'dis_selling_price' => $prod->dis_selling_price ?? 0,
                    'discount' => $prod->discount ?? 0,
                    'status' => $prod->status ?? 1,
                    'description' => $prod->description,
                    'is_service' => $prod->is_service ?? 0,
                    'has_serial' => $prod->has_serial,
                    'has_warranty' => $prod->has_warranty ?? 0,
                ]);

                if ($response->successful()) {
                    $prodSynced++;
                } else {
                    $this->error("Failed to sync product '{$prod->name}'. Status: " . $response->status() . " Body: " . $response->body());
                    $prodFailed++;
                }
            } catch (\Exception $e) {
                $this->error("Error syncing product '{$prod->name}': " . $e->getMessage());
                $prodFailed++;
            }
        }
        $results['Products'] = ['Total' => count($localProducts), 'RemoteExist' => count($remote['products']), 'Synced' => $prodSynced, 'Failed' => $prodFailed, 'Skipped' => $prodSkipped];

        // Print final status table
        $this->info("\n=== PUSH Sync Summary ===");
        $summaryHeaders = ['Entity Type', 'Local Count', 'Remote Already Exist', 'New Synced', 'Failed', 'Skipped/Matched'];
        $summaryRows = [];
        foreach ($results as $type => $counts) {
            $summaryRows[] = [
                $type,
                $counts['Total'],
                $counts['RemoteExist'],
                $counts['Synced'],
                $counts['Failed'],
                $counts['Skipped']
            ];
        }
        $this->table($summaryHeaders, $summaryRows);
    }
}
