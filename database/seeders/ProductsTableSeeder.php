<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Branch;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\BranchProduct;
use App\Models\BranchCategory;
use App\Models\BranchBrand;
use App\Models\ProductSize;
use App\Models\ProductColor;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\File;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $uploadPath = public_path('uploads/products');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Standard Sizes for Apparel
        $sizes = ['S', 'M', 'L', 'XL', 'XXL', '38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)', '30', '32', '34', '36', '2-3Y', '4-5Y', '6-7Y', '8-9Y', '10-12Y'];
        $sizeModels = [];
        foreach ($sizes as $sName) {
            $sizeModels[$sName] = ProductSize::firstOrCreate(['size' => $sName]);
        }

        // Standard Luxury Colors with Swatches
        $colors = [
            'Noir Black' => '#0B0B0C',
            'Royal Pearl' => '#F8F5F0',
            'Imperial Navy' => '#0F172A',
            'Emerald Teal' => '#0F766E',
            'Rich Burgundy' => '#701A75',
            'Warm Khaki' => '#A16207',
            'Pure White' => '#FFFFFF',
            'Sky Blue' => '#38BDF8',
            'Canary Yellow' => '#EAB308',
            'Tangerine' => '#EA580C',
            'Champagne Pearl' => '#EFE6DB',
            'Deep Forest' => '#1B3022',
            'Royal Blue' => '#1D4ED8',
            'Classic Plum' => '#581C87',
            'Vintage Grey' => '#64748B',
            'Off White' => '#F1EFEA',
            'Light Wash' => '#93C5FD'
        ];
        $colorModels = [];
        foreach ($colors as $cName => $hex) {
            $colorModels[$cName] = ProductColor::firstOrCreate(['color' => $cName]);
        }

        $defaultBrand = Brand::firstOrCreate(['name' => 'ROBE Atelier'], ['slug' => 'robe-atelier']);
        $defaultUnit = Unit::firstOrCreate(['name' => 'pcs'], ['related_to_unit' => 1, 'related_sign' => '*', 'related_by' => 1]);
        $defaultSupplier = Supplier::firstOrCreate(['name' => 'Atelier Silk & Cotton Mill'], ['phone' => '01700000000', 'branch_id' => 1]);
        
        $purchase = Purchase::firstOrCreate(
            ['purchase_no' => 'PUR-INIT-001'],
            [
                'supplier_id' => $defaultSupplier->id,
                'branch_id' => 1,
                'date' => date('Y-m-d'),
                'total_amount' => 1000000,
                'total_paid' => 1000000,
                'total_due' => 0,
                'status' => 1,
                'created_by' => 1,
            ]
        );

        $allBranches = Branch::all();
        foreach ($allBranches as $branch) {
            foreach (Category::all() as $cat) {
                BranchCategory::firstOrCreate(['branch_id' => $branch->id, 'category_id' => $cat->id]);
            }
            BranchBrand::firstOrCreate(['branch_id' => $branch->id, 'brand_id' => $defaultBrand->id]);
        }

        $products = [
            // ================= 1. MEN'S ETHNIC / PANJABI =================
            [
                'name' => 'Imperial Gold Embroidered Royal Panjabi',
                'barcode' => '880101',
                'category_name' => "Men's Ethnic",
                'purchase_price' => 3800,
                'selling_price' => 5850,
                'dis_selling_price' => 6500,
                'discount' => 650,
                'image' => 'panjabi_royal_gold.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 1,
                'description' => 'Constructed from rare extra-long staple Egyptian Giza 100% cotton with a crisp 120s thread count, featuring architectural cut and delicate gold zari embroidery.',
                'sizes' => ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'],
                'colors' => ['Noir Black', 'Royal Pearl', 'Imperial Navy', 'Emerald Teal'],
            ],
            [
                'name' => 'Royal Jacquard Silk Semi-Long Panjabi - Obsidian',
                'barcode' => '880102',
                'category_name' => "Men's Ethnic",
                'purchase_price' => 1400,
                'selling_price' => 1950,
                'dis_selling_price' => 2450,
                'discount' => 500,
                'image' => 'panjabi_jacquard_obsidian.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 1,
                'description' => 'Fine artisanal jacquard silk weave with golden embroidery detailing along the collar and concealed placket.',
                'sizes' => ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'],
                'colors' => ['Noir Black', 'Champagne Pearl', 'Deep Forest'],
            ],
            [
                'name' => 'Artisanal Heritage Panjabi - Emerald Teal',
                'barcode' => '880103',
                'category_name' => "Men's Ethnic",
                'purchase_price' => 1500,
                'selling_price' => 2150,
                'dis_selling_price' => 2650,
                'discount' => 500,
                'image' => 'panjabi_emerald_teal.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 0,
                'description' => 'Tailored festive silhouette in rich emerald teal cotton silk blend with contrast collar border.',
                'sizes' => ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'],
                'colors' => ['Emerald Teal', 'Royal Pearl', 'Noir Black'],
            ],
            [
                'name' => 'Bespoke Pearl Ivory Festive Panjabi',
                'barcode' => '880104',
                'category_name' => "Men's Ethnic",
                'purchase_price' => 1800,
                'selling_price' => 2550,
                'dis_selling_price' => 2950,
                'discount' => 400,
                'image' => 'panjabi_pearl_ivory.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 0,
                'description' => 'Pearl ivory royal panjabi featuring tone-on-tone embroidery and engraved metallic buttons.',
                'sizes' => ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'],
                'colors' => ['Royal Pearl', 'Warm Khaki', 'Pure White'],
            ],
            [
                'name' => 'Midnight Obsidian Zari Neck Panjabi',
                'barcode' => '880105',
                'category_name' => "Men's Ethnic",
                'purchase_price' => 2000,
                'selling_price' => 2850,
                'dis_selling_price' => 3450,
                'discount' => 600,
                'image' => 'panjabi_midnight_zari.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 1,
                'description' => 'Hand-crafted metallic neckline embroidery on jet black breathable luxury fabric.',
                'sizes' => ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'],
                'colors' => ['Noir Black', 'Imperial Navy'],
            ],
            [
                'name' => 'Crimson Wine Imperial Festive Panjabi',
                'barcode' => '880106',
                'category_name' => "Men's Ethnic",
                'purchase_price' => 1900,
                'selling_price' => 2650,
                'dis_selling_price' => 3200,
                'discount' => 550,
                'image' => 'panjabi_crimson_wine.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 0,
                'description' => 'Rich crimson silk cotton blend with handcrafted royal collar embroidery.',
                'sizes' => ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'],
                'colors' => ['Rich Burgundy', 'Noir Black'],
            ],

            // ================= 2. KABLI SET =================
            [
                'name' => 'Special Men\'s Solid Color Kabli Set - Royal Blue',
                'barcode' => '880201',
                'category_name' => 'Kabli Set',
                'purchase_price' => 950,
                'selling_price' => 1390,
                'dis_selling_price' => 1750,
                'discount' => 360,
                'image' => 'kabli_royal_blue.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 1,
                'description' => 'Two-piece tailored royal Kabli set crafted with premium high-density cotton blend and matching pajama.',
                'sizes' => ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'],
                'colors' => ['Royal Blue', 'Emerald Teal', 'Warm Khaki'],
            ],
            [
                'name' => 'Robe Exclusive Kabli Set - Emerald Teal',
                'barcode' => '880202',
                'category_name' => 'Kabli Set',
                'purchase_price' => 1100,
                'selling_price' => 1590,
                'dis_selling_price' => 1890,
                'discount' => 300,
                'image' => 'kabli_emerald_teal.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 1,
                'description' => 'Premium dyed combed cotton tailored Kabli set with regal band collar and comfortable straight cut pajama.',
                'sizes' => ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'],
                'colors' => ['Emerald Teal', 'Noir Black', 'Royal Pearl'],
            ],
            [
                'name' => 'Robe Exclusive Kabli Set - Classic Plum',
                'barcode' => '880203',
                'category_name' => 'Kabli Set',
                'purchase_price' => 1100,
                'selling_price' => 1590,
                'dis_selling_price' => 1890,
                'discount' => 300,
                'image' => 'kabli_classic_plum.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 0,
                'description' => 'Rich plum hue festive two-piece Kabli set with customized button detailing and side pockets.',
                'sizes' => ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'],
                'colors' => ['Classic Plum', 'Noir Black'],
            ],
            [
                'name' => 'Imperial Jet Black Luxury Kabli Set',
                'barcode' => '880204',
                'category_name' => 'Kabli Set',
                'purchase_price' => 1200,
                'selling_price' => 1750,
                'dis_selling_price' => 2150,
                'discount' => 400,
                'image' => 'kabli_jet_black.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 1,
                'description' => 'Royal jet black Kabli set with subtle texture weave and comfortable tailored cut.',
                'sizes' => ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'],
                'colors' => ['Noir Black', 'Imperial Navy'],
            ],

            // ================= 3. SHIRT =================
            [
                'name' => 'Men\'s Cotton Exclusive Formal Shirt - Pure White',
                'barcode' => '880301',
                'category_name' => 'Shirt',
                'purchase_price' => 550,
                'selling_price' => 890,
                'dis_selling_price' => 1150,
                'discount' => 260,
                'image' => 'shirt_pure_white.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 1,
                'description' => 'Crisp business formal shirt crafted from 100% fine cotton with reinforced collar and wrinkle-resistant finish.',
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'colors' => ['Pure White', 'Off White'],
            ],
            [
                'name' => 'Men\'s Cotton Exclusive Formal Shirt - Soft Almond',
                'barcode' => '880302',
                'category_name' => 'Shirt',
                'purchase_price' => 550,
                'selling_price' => 890,
                'dis_selling_price' => 1150,
                'discount' => 260,
                'image' => 'shirt_soft_almond.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 0,
                'description' => 'Sophisticated almond shade luxury formal shirt tailored for executive presentation.',
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'colors' => ['Warm Khaki', 'Champagne Pearl'],
            ],
            [
                'name' => 'Half Sleeve Casual Shirt - Floral Green Print',
                'barcode' => '880303',
                'category_name' => 'Shirt',
                'purchase_price' => 380,
                'selling_price' => 575,
                'dis_selling_price' => 750,
                'discount' => 175,
                'image' => 'shirt_floral_green.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 1,
                'description' => 'Breezy lightweight viscose-cotton blend casual resort shirt with botanical prints.',
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'colors' => ['Emerald Teal', 'Deep Forest'],
            ],
            [
                'name' => 'Cotton Exclusive Formal Shirt - Rich Burgundy',
                'barcode' => '880304',
                'category_name' => 'Shirt',
                'purchase_price' => 480,
                'selling_price' => 760,
                'dis_selling_price' => 980,
                'discount' => 220,
                'image' => 'shirt_rich_burgundy.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 0,
                'description' => 'Striking burgundy executive shirt with smooth sateen weave.',
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'colors' => ['Rich Burgundy', 'Noir Black'],
            ],

            // ================= 4. T-SHIRT =================
            [
                'name' => '220+ GSM Premium Drop Shoulder T-Shirt (Off White)',
                'barcode' => '880401',
                'category_name' => 'T-Shirt',
                'purchase_price' => 195,
                'selling_price' => 325,
                'dis_selling_price' => 450,
                'discount' => 125,
                'image' => 'tshirt_drop_white.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 1,
                'description' => 'Heavyweight 220+ GSM combed cotton with trendy oversized drop shoulder cut.',
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'colors' => ['Off White', 'Pure White'],
            ],
            [
                'name' => 'Half Sleeve T-Shirt (Urban Black Graphic)',
                'barcode' => '880402',
                'category_name' => 'T-Shirt',
                'purchase_price' => 190,
                'selling_price' => 320,
                'dis_selling_price' => 420,
                'discount' => 100,
                'image' => 'tshirt_urban_black.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 0,
                'description' => 'High-density screen printed urban graphic typography on pure black cotton tee.',
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'colors' => ['Noir Black', 'Imperial Navy'],
            ],
            [
                'name' => 'Half Sleeve T-Shirt (Grey Graphic Vintage)',
                'barcode' => '880403',
                'category_name' => 'T-Shirt',
                'purchase_price' => 190,
                'selling_price' => 320,
                'dis_selling_price' => 420,
                'discount' => 100,
                'image' => 'tshirt_grey_vintage.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 0,
                'description' => 'Vintage stone-washed grey tee with distressed heritage logo print.',
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'colors' => ['Vintage Grey', 'Noir Black'],
            ],
            [
                'name' => '220+ GSM Premium Drop Shoulder T-Shirt (Crimson Wine)',
                'barcode' => '880404',
                'category_name' => 'T-Shirt',
                'purchase_price' => 195,
                'selling_price' => 325,
                'dis_selling_price' => 450,
                'discount' => 125,
                'image' => 'tshirt_crimson_wine.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 1,
                'description' => 'Heavyweight drop shoulder tee in rich crimson wine pigment.',
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'colors' => ['Rich Burgundy', 'Noir Black'],
            ],

            // ================= 5. DENIM PANT =================
            [
                'name' => 'Rockless New Denim Jeans - Midnight Blue',
                'barcode' => '880501',
                'category_name' => 'Denim Pant',
                'purchase_price' => 580,
                'selling_price' => 890,
                'dis_selling_price' => 1250,
                'discount' => 360,
                'image' => 'denim_midnight_blue.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 1,
                'description' => 'Premium stretch denim with dark midnight indigo tint and subtle whiskering.',
                'sizes' => ['30', '32', '34', '36'],
                'colors' => ['Imperial Navy', 'Noir Black'],
            ],
            [
                'name' => 'Rockless New Denim Jeans - Classic Indigo',
                'barcode' => '880502',
                'category_name' => 'Denim Pant',
                'purchase_price' => 580,
                'selling_price' => 890,
                'dis_selling_price' => 1250,
                'discount' => 360,
                'image' => 'denim_classic_indigo.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 0,
                'description' => 'Timeless 5-pocket indigo jeans with flexible elastane blend for all-day comfort.',
                'sizes' => ['30', '32', '34', '36'],
                'colors' => ['Royal Blue', 'Imperial Navy'],
            ],
            [
                'name' => 'JACK & JONES Slim Fit Denim Pant Light Wash',
                'barcode' => '880503',
                'category_name' => 'Denim Pant',
                'purchase_price' => 580,
                'selling_price' => 890,
                'dis_selling_price' => 1150,
                'discount' => 260,
                'image' => 'denim_light_wash.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 0,
                'description' => 'Contemporary light stone-washed slim fit denim with reinforced rivets.',
                'sizes' => ['30', '32', '34', '36'],
                'colors' => ['Light Wash', 'Sky Blue'],
            ],
            [
                'name' => 'Robe Premium Regular Fit Raw Denim Pant',
                'barcode' => '880504',
                'category_name' => 'Denim Pant',
                'purchase_price' => 620,
                'selling_price' => 950,
                'dis_selling_price' => 1350,
                'discount' => 400,
                'image' => 'denim_raw_selvedge.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 1,
                'description' => 'Deep dark unwashed raw rigid denim engineered for true denim connoisseurs.',
                'sizes' => ['30', '32', '34', '36'],
                'colors' => ['Noir Black', 'Imperial Navy'],
            ],

            // ================= 6. KIDS & WINTER =================
            [
                'name' => 'Kid\'s Winter Contrast Hoodie - Crimson',
                'barcode' => '880601',
                'category_name' => 'Kids & Winter',
                'purchase_price' => 240,
                'selling_price' => 385,
                'dis_selling_price' => 550,
                'discount' => 165,
                'image' => 'kids_hoodie_crimson.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 0,
                'description' => 'Super-soft combed fleece hoodie with front kangaroo pocket and contrast hood lining.',
                'sizes' => ['2-3Y', '4-5Y', '6-7Y', '8-9Y', '10-12Y'],
                'colors' => ['Rich Burgundy', 'Crimson'],
            ],
            [
                'name' => 'Kid\'s Winter Contrast Hoodie - Canary Yellow',
                'barcode' => '880602',
                'category_name' => 'Kids & Winter',
                'purchase_price' => 240,
                'selling_price' => 385,
                'dis_selling_price' => 550,
                'discount' => 165,
                'image' => 'kids_hoodie_canary.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 0,
                'description' => 'Vibrant bright canary yellow winter hoodie keeping children cozy and stylish.',
                'sizes' => ['2-3Y', '4-5Y', '6-7Y', '8-9Y', '10-12Y'],
                'colors' => ['Canary Yellow', 'Warm Khaki'],
            ],
            [
                'name' => 'Kid\'s Winter Contrast Hoodie - Sky Blue',
                'barcode' => '880603',
                'category_name' => 'Kids & Winter',
                'purchase_price' => 240,
                'selling_price' => 385,
                'dis_selling_price' => 550,
                'discount' => 165,
                'image' => 'kids_hoodie_skyblue.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 1,
                'description' => 'Gentle pastel sky blue warm winter coordinate hoodie.',
                'sizes' => ['2-3Y', '4-5Y', '6-7Y', '8-9Y', '10-12Y'],
                'colors' => ['Sky Blue', 'Imperial Navy'],
            ],

            // ================= 7. WOMEN'S SET =================
            [
                'name' => 'All Over Embroise Print Women Top Bottom Set',
                'barcode' => '880701',
                'category_name' => "Women's Set",
                'purchase_price' => 1200,
                'selling_price' => 1850,
                'dis_selling_price' => 2250,
                'discount' => 400,
                'image' => 'women_set_embroidered.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 1,
                'description' => 'Premium embroidered women coordinate two piece set with soft breathable luxury drape.',
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Emerald Teal', 'Royal Pearl'],
            ],
            [
                'name' => 'All Over Embroise Print Women Top Set - Pastel',
                'barcode' => '880702',
                'category_name' => "Women's Set",
                'purchase_price' => 1200,
                'selling_price' => 1850,
                'dis_selling_price' => 2200,
                'discount' => 350,
                'image' => 'women_set_pastel.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 0,
                'description' => 'Pastel tone printed luxury coordinate set with delicate collar design.',
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Royal Pearl', 'Sky Blue'],
            ],
            [
                'name' => 'Women\'s Crap Silk Bottom Printed Two Piece Set',
                'barcode' => '880703',
                'category_name' => "Women's Set",
                'purchase_price' => 1350,
                'selling_price' => 2050,
                'dis_selling_price' => 2450,
                'discount' => 400,
                'image' => 'women_set_crapsilk.jpg',
                'is_new_arrival' => 1,
                'is_top_selling' => 0,
                'is_featured' => 0,
                'description' => 'Two-piece tailored royal drape crepe silk set with festive printed accents.',
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Royal Blue', 'Imperial Navy'],
            ],
            [
                'name' => 'Women\'s Crap Silk Bottom Printed Two Piece Set - Ruby',
                'barcode' => '880704',
                'category_name' => "Women's Set",
                'purchase_price' => 1350,
                'selling_price' => 2050,
                'dis_selling_price' => 2450,
                'discount' => 400,
                'image' => 'women_set_ruby.jpg',
                'is_new_arrival' => 0,
                'is_top_selling' => 1,
                'is_featured' => 1,
                'description' => 'Ruby festive crepe silk printed set for weddings and special celebrations.',
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Rich Burgundy', 'Noir Black'],
            ],
        ];

        foreach ($products as $pData) {
            $cat = Category::where('name', $pData['category_name'])->first() ?: Category::first();

            $product = Product::updateOrCreate(
                ['barcode' => $pData['barcode']],
                [
                    'name' => $pData['name'],
                    'date' => date('Y-m-d'),
                    'category_id' => $cat->id,
                    'brand_id' => $defaultBrand->id,
                    'unit_id' => $defaultUnit->id,
                    'purchase_price' => $pData['purchase_price'],
                    'selling_price' => $pData['selling_price'],
                    'dis_selling_price' => $pData['dis_selling_price'],
                    'discount' => $pData['discount'],
                    'main_qty' => 0,
                    'is_service' => 0,
                    'status' => 1,
                    'is_new_arrival' => $pData['is_new_arrival'] ?? 0,
                    'is_top_selling' => $pData['is_top_selling'] ?? 0,
                    'is_featured' => $pData['is_featured'] ?? 0,
                    'description' => $pData['description'],
                    'images' => $pData['image'],
                    'created_by' => 1,
                ]
            );

            // Create Variations & Purchase Inventory for each Size and Color
            $pSizes = $pData['sizes'] ?? ['M', 'L', 'XL'];
            $pColors = $pData['colors'] ?? ['Noir Black'];

            $totalStockForProduct = 0;

            foreach ($pColors as $cName) {
                $cModel = $colorModels[$cName] ?? null;
                if (!$cModel) continue;

                foreach ($pSizes as $sName) {
                    $sModel = $sizeModels[$sName] ?? null;
                    if (!$sModel) continue;

                    $variation = ProductVariation::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'size_id' => $sModel->id,
                            'color_id' => $cModel->id,
                        ],
                        [
                            'image' => $pData['image'],
                        ]
                    );

                    $qtyPerVariation = 15;
                    $totalStockForProduct += $qtyPerVariation;

                    PurchaseItem::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'product_variation_id' => $variation->id,
                        ],
                        [
                            'purchase_id' => $purchase->id,
                            'rate' => $pData['purchase_price'],
                            'main_qty' => $qtyPerVariation,
                            'actual_main' => $qtyPerVariation,
                            'stock_qty' => $qtyPerVariation,
                            'subtotal' => $pData['purchase_price'] * $qtyPerVariation,
                            'actual_total' => $pData['purchase_price'] * $qtyPerVariation,
                            'branch_id' => 1,
                            'date' => date('Y-m-d'),
                        ]
                    );
                }
            }

            // Sync main_qty with FIFO sum
            $product->main_qty = $totalStockForProduct;
            $product->save();

            // Assign product to all branches
            foreach ($allBranches as $branch) {
                BranchProduct::firstOrCreate([
                    'product_id' => $product->id,
                    'branch_id' => $branch->id,
                ]);
            }
        }
    }
}
