<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GeminiService
{
    protected $geminiKey;
    protected $groqKey;

    protected $apiUrl;
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        // Hardcoding Pollinations AI to ensure it works without .env configuration
        // since user explicitly asked for "no api and model" (keyless)
        $this->apiUrl = 'https://text.pollinations.ai/';
        $this->model = 'openai'; 
        $this->apiKey = env('AI_API_KEY', '');
    }

    public function analyzeDiscrepancy($auditData)
    {
        $prompt = $this->generatePrompt($auditData);
        return $this->generateResponse($prompt);
    }

    public function generateResponse($prompt)
    {
        $promptText = strtolower(trim($prompt));
        
        // 1. Basic Greetings & Guide
        if (str_contains($promptText, 'hi') || str_contains($promptText, 'hello') || str_contains($promptText, 'হ্যালো') || str_contains($promptText, 'হাই')) {
            return "হ্যালো! আমি Fast-IT এআই অ্যাসিস্ট্যান্ট। আমি আপনাকে ইনভেন্টরি, আজকের সেলস, ব্যাংক ব্যালেন্স এবং সিস্টেমের অ্যাক্টিভিটি সম্পর্কে তথ্য দিয়ে সাহায্য করতে পারি।";
        }

        if (str_contains($promptText, 'bebohar') || str_contains($promptText, 'use') || str_contains($promptText, 'ব্যবহার') || str_contains($promptText, 'guide') || str_contains($promptText, 'manual')) {
            return "Fast-IT POS ব্যবহার করা খুবই সহজ! আপনি নিচের কাজগুলো করতে পারেন:\n" .
                   "১. **Dashboard:** এখানে আপনি আজকের মোট সেলস, পারচেজ এবং ইনভেন্টরি সামারি দেখতে পাবেন।\n" .
                   "২. **Products:** এখান থেকে নতুন প্রোডাক্ট যোগ করা বা স্টক আপডেট করা যায়।\n" .
                   "৩. **Invoice:** নতুন সেল করার জন্য এই সেকশন ব্যবহার করুন।\n" .
                   "৪. **Reports:** আপনার ব্যবসার সব রিপোর্ট (Daily, Monthly, Profit/Loss) এখানে পাবেন।\n" .
                   "৫. **AI Chat:** আপনি সরাসরি আমাকে প্রশ্ন করতে পারেন, যেমন- 'স্টক কত?' বা 'আজকের সেল কত?'।\n\n" .
                   "আর কিছু জানতে চাইলে আমাকে সরাসরি জিজ্ঞাসা করুন!";
        }

        // 2. Bank Balance Query
        if (str_contains($promptText, 'bank') || str_contains($promptText, 'balance') || str_contains($promptText, 'ব্যাংক') || str_contains($promptText, 'টাকা') || str_contains($promptText, 'ব্যালেন্স')) {
            try {
                $opening = \App\Models\BankAccount::sum('opening_balance');
                $deposits = \App\Models\BankTransaction::where('trans_type', 'deposit')->sum('amount');
                $withdraws = \App\Models\BankTransaction::where('trans_type', 'withdraw')->sum('amount');
                $totalBalance = ($opening + $deposits) - $withdraws;
                return "আপনার সব ব্যাংক অ্যাকাউন্ট মিলিয়ে বর্তমানে মোট " . number_format($totalBalance, 2) . " টাকা ব্যালেন্স আছে।";
            } catch (\Exception $e) {
                return "ব্যাংক ব্যালেন্স ডাটা এই মুহূর্তে পাওয়া যাচ্ছে না।";
            }
        }

        // 3. Delete / Activity Log Query
        if (str_contains($promptText, 'delete') || str_contains($promptText, 'মুছে') || str_contains($promptText, 'activity') || str_contains($promptText, 'অ্যাক্টিভিটি') || str_contains($promptText, 'kora hoiyasy') || str_contains($promptText, 'kaj') || str_contains($promptText, 'history')) {
            try {
                $query = \App\Models\ActivityLog::whereDate('created_at', today())->latest();
                
                // If user specifically asked for deletes
                if (str_contains($promptText, 'delete') || str_contains($promptText, 'মুছে')) {
                    $query->where('action', 'LIKE', '%delete%');
                    $label = "আজকের ডিলিট হওয়া অ্যাক্টিভিটিগুলো";
                } else {
                    $label = "আজকের সিস্টেম অ্যাক্টিভিটিগুলো";
                }

                $recentLogs = $query->take(10)->get();
                if ($recentLogs->count() > 0) {
                    $logMsg = "{$label} নিচে দেওয়া হলো:\n";
                    foreach($recentLogs as $log) {
                        $logMsg .= "- " . ($log->description ?? $log->action) . " (সময়: " . $log->created_at->format('H:i') . ")\n";
                    }
                    return $logMsg;
                }
                return "আজকে এখন পর্যন্ত কোনো " . (str_contains($promptText, 'delete') ? 'ডিলিট করার রেকর্ড' : 'অ্যাক্টিভিটি রেকর্ড') . " পাওয়া যায়নি।";
            } catch (\Exception $e) {
                return "সিস্টেম অ্যাক্টিভিটি চেক করতে সমস্যা হচ্ছে।";
            }
        }

        // 4. Create / New Product Query (Check Activity Log)
        if (str_contains($promptText, 'create') || str_contains($promptText, 'new') || str_contains($promptText, 'যোগ') || str_contains($promptText, 'তৈরি')) {
            try {
                $query = \App\Models\ActivityLog::where('action', 'LIKE', '%create%')
                    ->whereDate('created_at', today());

                // If user specifically asked for "product"
                if (str_contains($promptText, 'product') || str_contains($promptText, 'পণ্য')) {
                    $query->where(function($q) {
                        $q->where('model_type', 'LIKE', '%Product%')
                          ->orWhere('description', 'LIKE', '%Product%');
                    });
                    $label = "আজকের নতুন যোগ করা প্রোডাক্টগুলো";
                } else {
                    $label = "আজকের নতুন আইটেম বা ইনভয়েসগুলো";
                }

                $recentCreates = $query->latest()->take(10)->get();
                
                if ($recentCreates->count() > 0) {
                    $logMsg = "আজকে সিস্টেমে নতুন যোগ করা প্রোডাক্ট বা আইটেমগুলো হলো:\n";
                    foreach($recentCreates as $log) {
                        $logMsg .= "- " . ($log->description ?? $log->action) . " (সময়: " . $log->created_at->format('H:i') . ")\n";
                    }
                    return $logMsg;
                }
                return "আজকে এখন পর্যন্ত কোনো নতুন প্রোডাক্ট যোগ করার রেকর্ড পাওয়া যায়নি।";
            } catch (\Exception $e) {
                // fall through
            }
        }

        // 5. Detailed Stock Out Query (Names of products)
        if (str_contains($promptText, 'stock out') || str_contains($promptText, 'stok out') || (str_contains($promptText, 'stock') && str_contains($promptText, 'out'))) {
            try {
                $outOfStockItems = \App\Models\Product::where('main_qty', '<=', 0)->get(['name']);
                if ($outOfStockItems->count() > 0) {
                    $itemNames = $outOfStockItems->pluck('name')->toArray();
                    return "বর্তমানে এই প্রোডাক্টগুলো স্টকে নেই: " . implode(', ', $itemNames);
                }
                return "বর্তমানে আপনার সব প্রোডাক্ট স্টকে আছে। কোনোটিই আউট-অফ-স্টক নেই।";
            } catch (\Exception $e) {
                // fall through
            }
        }
        
        // 5. Specific Product Stock Check (With Activity Log Fallback)
        if (preg_match('/(stock of|about|product|স্টক|দাম|কোথায়|গেল) (.+)/', $promptText, $matches) || preg_match('/(.+) (stock|স্টক|কোথায়|গেল)/', $promptText, $matches)) {
            $productName = trim($matches[2] ?? $matches[1]);
            if (!in_array($productName, ['of', 'the', 'is', 'what', 'show', 'me', 'please', 'product', 'about'])) {
                // First, check main Product table
                $product = \App\Models\Product::where('name', 'LIKE', '%' . $productName . '%')->first();
                if ($product) {
                    return "হ্যাঁ, '{$product->name}' এর বর্তমান স্টক: {$product->main_qty} পিস। বিক্রয় মূল্য: " . number_format($product->price ?? 0, 2) . " টাকা।";
                }

                // If not found in Products, check Activity Log (maybe it was deleted?)
                $activity = \App\Models\ActivityLog::where('description', 'LIKE', '%' . $productName . '%')
                    ->orWhere('data', 'LIKE', '%' . $productName . '%')
                    ->latest()
                    ->first();

                if ($activity) {
                    return "এই প্রোডাক্টটি ('{$productName}') বর্তমানে স্টকে নেই, তবে অ্যাক্টিভিটি লগ অনুযায়ী এটি সম্পর্কে একটি রেকর্ড পাওয়া গেছে: '" . ($activity->description ?? $activity->action) . "' যা করা হয়েছে " . $activity->created_at->format('d M, Y H:i') . " সময়ে।";
                }
            }
        }

        // 6. General Stock / Inventory Summary
        if (str_contains($promptText, 'stock') || str_contains($promptText, 'inventory') || str_contains($promptText, 'স্টক') || str_contains($promptText, 'মালামাল')) {
            return "আপনার বর্তমান ইনভেন্টরির একটি সামারি:\n\n" . $this->getInventorySummary();
        }
        
        // 7. Sales Query (Avoid "how to" and catch report queries)
        if (!str_contains($promptText, 'kivaby') && !str_contains($promptText, 'how to') && (str_contains($promptText, 'sale') || str_contains($promptText, 'সেল') || str_contains($promptText, 'বিক্রি'))) {
            try {
                $todaySales = \App\Models\Invoice::whereDate('created_at', today())->sum('total_amount');
                $todayCount = \App\Models\Invoice::whereDate('created_at', today())->count();
                return "আজকের মোট সেলস অ্যামাউন্ট: " . number_format($todaySales, 2) . " টাকা। মোট ইনভয়েস: {$todayCount} টি।";
            } catch (\Exception $e) {
                return "সেলস ডাটা আনতে সমস্যা হচ্ছে।";
            }
        }
        
        // 8. Customer Query
        if (str_contains($promptText, 'customer') || str_contains($promptText, 'কাস্টমার') || str_contains($promptText, 'গ্রাহক')) {
            try {
                $totalCustomers = \App\Models\Customer::count();
                return "আপনার সিস্টেমে বর্তমানে মোট {$totalCustomers} জন কাস্টমার রেজিস্টার্ড আছেন।";
            } catch (\Exception $e) {
                // Ignore and fall through
            }
        }

        // Default Fallback Response
        return "দুঃখিত, আমি আপনার কথাটি বুঝতে পারিনি। আমি মূলত স্টক, সেলস, ব্যাংক ব্যালেন্স এবং সিস্টেম অ্যাক্টিভিটি নিয়ে তথ্য দিতে পারি। (যেমন: 'bank balance', 'stock of laptop', 'today sales' বা 'recent deletes')।";
    }

    public function getInventorySummary()
    {
        try {
            $totalProducts = \App\Models\Product::count();
            $outOfStockCount = \App\Models\Product::where('main_qty', '<=', 0)->count();
            $lowStockProducts = \App\Models\Product::where('main_qty', '>', 0)->where('main_qty', '<=', 5)->take(10)->get(['name', 'main_qty']);
            $topStockItems = \App\Models\Product::orderBy('main_qty', 'desc')->take(5)->get(['name', 'main_qty']);
            
            // Get total stock value
            $totalValue = \App\Models\Product::sum(DB::raw('main_qty * purchase_price'));
            
            $summary = "Total unique products: {$totalProducts}\n";
            $summary .= "Out of stock items: {$outOfStockCount}\n";
            $summary .= "Estimated Inventory Value (Purchase Price): " . number_format($totalValue, 2) . "\n";
            
            if ($lowStockProducts->count() > 0) {
                $summary .= "Low Stock (1-5 units):\n";
                foreach($lowStockProducts as $p) {
                    $summary .= "- {$p->name}: {$p->main_qty}\n";
                }
            }
            
            $summary .= "Top 5 products in stock:\n";
            foreach($topStockItems as $p) {
                $summary .= "- {$p->name}: {$p->main_qty}\n";
            }
            
            return $summary;
        } catch (\Exception $e) {
            return "Error fetching inventory summary: " . $e->getMessage();
        }
    }

    public function getGlobalSummary()
    {
        try {
            $summary = "=== POS SYSTEM DATA SNAPSHOT ===\n";
            
            // 1. Inventory & Products
            $totalProducts = \App\Models\Product::count();
            $outOfStock = \App\Models\Product::where('main_qty', '<=', 0)->get(['name']);
            $totalStockValue = \App\Models\Product::sum(DB::raw('main_qty * purchase_price'));
            $summary .= "Inventory: Total {$totalProducts} unique products. Stock Value: " . number_format($totalStockValue, 2) . "\n";
            if ($outOfStock->count() > 0) {
                $summary .= "Out of Stock Items: " . implode(', ', $outOfStock->pluck('name')->toArray()) . "\n";
            }
            
            // 2. Sales Today
            $todaySales = \App\Models\Invoice::whereDate('created_at', today())->sum('total_amount');
            $todayInvoices = \App\Models\Invoice::whereDate('created_at', today())->count();
            $summary .= "Sales Today: Amount " . number_format($todaySales, 2) . " (Invoices: {$todayInvoices})\n";
            
            // 3. Purchases Today
            $todayPurchases = \App\Models\Purchase::whereDate('created_at', today())->sum('total_amount');
            $summary .= "Purchases Today: Amount " . number_format($todayPurchases, 2) . "\n";
            
            // 4. Expenses Today
            $todayExpenses = \App\Models\Expense::whereDate('created_at', today())->sum('amount');
            $summary .= "Expenses Today: Amount " . number_format($todayExpenses, 2) . "\n";
            
            // 5. Financials (Banks)
            $opening = \App\Models\BankAccount::sum('opening_balance');
            $deposits = \App\Models\BankTransaction::where('trans_type', 'deposit')->sum('amount');
            $withdraws = \App\Models\BankTransaction::where('trans_type', 'withdraw')->sum('amount');
            $bankBalance = ($opening + $deposits) - $withdraws;
            $summary .= "Financials: Total Bank Balance " . number_format($bankBalance, 2) . "\n";
            
            // 6. People
            $customers = \App\Models\Customer::count();
            $suppliers = \App\Models\Supplier::count();
            $summary .= "Network: {$customers} customers and {$suppliers} suppliers registered.\n";
            
            // 7. Recent System Activity (Full list of today)
            $recentLogs = \App\Models\ActivityLog::whereDate('created_at', today())->latest()->take(10)->get();
            $summary .= "Recent Activity Logs (Today):\n";
            foreach($recentLogs as $log) {
                $summary .= "- " . ($log->description ?? $log->action) . " (" . $log->created_at->format('H:i') . ")\n";
            }
            
            return $summary;
        } catch (\Exception $e) {
            return "Data summary currently unavailable: " . $e->getMessage();
        }
    }

    private function generatePrompt($data)
    {
        $logSummary = "";
        foreach ($data['logs'] as $log) {
            $logSummary .= "- [{$log->created_at}] {$log->action}: {$log->description}\n";
        }

        $productName = $data['product']->name ?? 'Product';

        return "You are an expert Inventory Auditor.
        Product: {$productName}.
        Theoretical: {$data['theoretical_stock']}, Actual: {$data['actual_stock']}.
        Analyze this and provide a solution in Bengali.";
    }
}
