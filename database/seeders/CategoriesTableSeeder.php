<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Branch;
use App\Models\BranchCategory;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Seed storefront and system categories with images.
     *
     * @return void
     */
    public function run()
    {
        $uploadPath = public_path('uploads/category');
        if (!file_exists($uploadPath)) {
            @mkdir($uploadPath, 0755, true);
        }

        $sourcePath = public_path('frontend/images');

        $categories = [
            ['id' => 1, 'name' => 'General', 'image' => null],
            ['id' => 2, 'name' => "Men's Ethnic", 'image' => 'promo_banner_panjabi.jpg'],
            ['id' => 3, 'name' => 'Kabli Set', 'image' => 'promo_banner_kabli.jpg'],
            ['id' => 4, 'name' => 'Shirt', 'image' => 'banner_cat_shirt.jpg'],
            ['id' => 5, 'name' => 'T-Shirt', 'image' => 'banner_cat_tshirt.jpg'],
            ['id' => 6, 'name' => 'Denim Pant', 'image' => 'banner_cat_denim.jpg'],
            ['id' => 7, 'name' => 'Kids & Winter', 'image' => 'banner_cat_kids.jpg'],
            ['id' => 8, 'name' => 'Offer', 'image' => 'banner_hero_eid.jpg'],
            ['id' => 9, 'name' => "Women's Set", 'image' => 'banner_cat_women.jpg'],
        ];

        foreach ($categories as $cat) {
            // Copy image to uploads/category if exists
            if (!empty($cat['image']) && file_exists($sourcePath . '/' . $cat['image'])) {
                if (!file_exists($uploadPath . '/' . $cat['image'])) {
                    @copy($sourcePath . '/' . $cat['image'], $uploadPath . '/' . $cat['image']);
                }
            }

            $category = Category::updateOrCreate(
                ['id' => $cat['id']],
                [
                    'name' => $cat['name'],
                    'image' => $cat['image'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            // Link category to all existing branches
            try {
                if (class_exists(Branch::class) && class_exists(BranchCategory::class)) {
                    $branches = Branch::all();
                    foreach ($branches as $branch) {
                        BranchCategory::updateOrCreate(
                            [
                                'branch_id' => $branch->id,
                                'category_id' => $category->id
                            ]
                        );
                    }
                }
            } catch (\Exception $e) {}
        }
    }
}
