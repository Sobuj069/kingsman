<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use Illuminate\Support\Facades\File;

class BannersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targetUploadPath = public_path('uploads/banners');
        if (!File::exists($targetUploadPath)) {
            File::makeDirectory($targetUploadPath, 0755, true);
        }

        $sourceImagesPath = public_path('frontend/images');

        $banners = [
            // 1. HERO SLIDER BANNERS
            [
                'title' => 'Grand Eid Festive Collection 2026',
                'position' => 'hero',
                'image' => 'banner_hero_eid.jpg',
                'link' => '/category/kabli-set',
                'order' => 1,
                'status' => 1,
            ],
            [
                'title' => 'Urban Contemporary & Denim Collection',
                'position' => 'hero',
                'image' => 'banner_hero_denim.jpg',
                'link' => '/category/t-shirt',
                'order' => 2,
                'status' => 1,
            ],

            // 2. DUAL PROMO SHOWCASE BANNERS (MID-PAGE SECTION 1)
            [
                'title' => 'Exclusive Festive Punjabi Showcase',
                'position' => 'promo_dual',
                'image' => 'promo_banner_panjabi.jpg',
                'link' => '/category/kabli-set',
                'order' => 1,
                'status' => 1,
            ],
            [
                'title' => 'Eid Festive Royal Kabli Showcase',
                'position' => 'promo_dual',
                'image' => 'promo_banner_kabli.jpg',
                'link' => '/category/kabli-set',
                'order' => 2,
                'status' => 1,
            ],

            // 3. ASYMMETRICAL 3-GRID PROMO BANNERS (MID-PAGE SECTION 2)
            [
                'title' => 'This Eid Collection - Panoramic Panjabi Banner',
                'position' => 'promo_festive',
                'image' => 'promo_banner_left.png',
                'link' => '#kabli-section',
                'order' => 1,
                'status' => 1,
            ],
            [
                'title' => 'Premium Punjabi Celebration - Top Right',
                'position' => 'promo_festive',
                'image' => 'promo_banner_right_top.png',
                'link' => '#kabli-section',
                'order' => 2,
                'status' => 1,
            ],
            [
                'title' => 'Exclusive Punjabi Edition - Bottom Right',
                'position' => 'promo_festive',
                'image' => 'promo_banner_right_bottom.png',
                'link' => '#kabli-section',
                'order' => 3,
                'status' => 1,
            ],

            // 4. CATEGORY PROMO BANNERS
            [
                'title' => 'Men\'s Ethnic & Panjabi Header Banner',
                'position' => 'category',
                'image' => 'promo_banner_panjabi.jpg',
                'link' => '/category/panjabi',
                'order' => 1,
                'status' => 1,
            ],
            [
                'title' => 'Kabli Set Header Banner',
                'position' => 'category',
                'image' => 'promo_banner_kabli.jpg',
                'link' => '/category/kabli-set',
                'order' => 2,
                'status' => 1,
            ],
            [
                'title' => 'Exclusive Shirts Header Banner',
                'position' => 'category',
                'image' => 'banner_cat_shirt.jpg',
                'link' => '/category/shirts',
                'order' => 3,
                'status' => 1,
            ],
            [
                'title' => 'T-Shirt & Drop Shoulder Header Banner',
                'position' => 'category',
                'image' => 'banner_cat_tshirt.jpg',
                'link' => '/category/t-shirt',
                'order' => 4,
                'status' => 1,
            ],
            [
                'title' => 'Denim Jeans Pant Header Banner',
                'position' => 'category',
                'image' => 'banner_cat_denim.jpg',
                'link' => '/category/denim-pant',
                'order' => 5,
                'status' => 1,
            ],
            [
                'title' => 'Kids Hoodie & Winter Header Banner',
                'position' => 'category',
                'image' => 'banner_cat_kids.jpg',
                'link' => '/category/kids-item',
                'order' => 6,
                'status' => 1,
            ],
        ];

        foreach ($banners as $bannerData) {
            // Copy image from frontend images to uploads/banners if it exists
            $sourceFile = $sourceImagesPath . '/' . $bannerData['image'];
            $destFile = $targetUploadPath . '/' . $bannerData['image'];

            if (File::exists($sourceFile) && !File::exists($destFile)) {
                File::copy($sourceFile, $destFile);
            }

            Banner::updateOrCreate(
                [
                    'title' => $bannerData['title'],
                    'position' => $bannerData['position'],
                ],
                [
                    'image' => $bannerData['image'],
                    'link' => $bannerData['link'],
                    'order' => $bannerData['order'],
                    'status' => $bannerData['status'],
                ]
            );
        }
    }
}
