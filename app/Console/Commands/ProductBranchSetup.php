<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\BranchBrand;
use App\Models\BranchProduct;
use App\Models\BranchCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductBranchSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:product-branch-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $branches = Branch::all();
        $categories = Product::all();

        if ($branches->isEmpty() || $categories->isEmpty()) {
            $this->error('No branches or categories found.');
            return;
        }

        $this->info("Generating combinations...");

        DB::transaction(function () use ($branches, $categories) {
            foreach ($branches as $branch) {
                foreach ($categories as $category) {
                    BranchProduct::firstOrCreate([
                        'branch_id' => $branch->id,
                        'product_id' => $category->id,
                    ]);
                }
            }
        });

        $this->info("✅ All branch-category combinations generated successfully!");
    }
}
