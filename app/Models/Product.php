<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_new_arrival' => 'boolean',
        'is_top_selling' => 'boolean',
        'is_featured' => 'boolean',
        'is_service' => 'integer',
        'has_warranty' => 'integer',
        'has_size_guide' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Clear all frontend catalog, product, and banner caches.
     */
    public static function clearFrontendCache()
    {
        try {
            \Illuminate\Support\Facades\Cache::forget('frontend_catalog_products');
            \Illuminate\Support\Facades\Cache::forget('frontend_new_arrivals');
            \Illuminate\Support\Facades\Cache::forget('frontend_top_selling');
            \Illuminate\Support\Facades\Cache::forget('frontend_home_category_sections');
            \Illuminate\Support\Facades\Cache::forget('frontend_categories');
            \Illuminate\Support\Facades\Cache::forget('frontend_banners_hero');
            \Illuminate\Support\Facades\Cache::forget('frontend_banners_dual');
            \Illuminate\Support\Facades\Cache::forget('frontend_banners_festive');
        } catch (\Exception $e) {}
    }

    protected static function booted()
    {
        static::saved(function () {
            static::clearFrontendCache();
        });
        static::deleted(function () {
            static::clearFrontendCache();
        });
    }

    /**
     * Scope for active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_service', 0)->where('status', 1);
    }

    /**
     * Scope for new arrival products.
     */
    public function scopeNewArrival($query)
    {
        return $query->where('is_new_arrival', 1);
    }

    /**
     * Scope for top selling products.
     */
    public function scopeTopSelling($query)
    {
        return $query->where('is_top_selling', 1);
    }

    /**
     * Scope for featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    /**
     * Get the full URL for the product image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->images)) {
            return asset('frontend/images/no-image.svg');
        }

        if (filter_var($this->images, FILTER_VALIDATE_URL)) {
            return $this->images;
        }

        if (file_exists(public_path('uploads/products/' . $this->images))) {
            return asset('uploads/products/' . $this->images);
        }

        if (file_exists(public_path('uploads/product/' . $this->images))) {
            return asset('uploads/product/' . $this->images);
        }

        if (file_exists(public_path('frontend/images/' . $this->images))) {
            return asset('frontend/images/' . $this->images);
        }

        return asset('frontend/images/no-image.svg');
    }

    /**
     * Get the full URL for the size guide image if present.
     */
    public function getSizeGuideImageUrlAttribute(): ?string
    {
        if (empty($this->size_guide_image)) {
            return null;
        }

        if (filter_var($this->size_guide_image, FILTER_VALIDATE_URL)) {
            return $this->size_guide_image;
        }

        if (file_exists(public_path('uploads/products/' . $this->size_guide_image))) {
            return asset('uploads/products/' . $this->size_guide_image);
        }

        if (file_exists(public_path('uploads/size_guides/' . $this->size_guide_image))) {
            return asset('uploads/size_guides/' . $this->size_guide_image);
        }

        return asset('uploads/products/' . $this->size_guide_image);
    }

    public function product_variations()
    {
        return $this->hasMany(ProductVariation::class);
    }
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function size()
    {
        return $this->belongsTo('App\Models\ProductSize','size_id','id');
    }

    public function color()
    {
        return $this->belongsTo('App\Models\ProductColor','color_id','id');
    }

    public function product_variation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class, 'child_category_id');
    }
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class, 'warranty_id');
    }

    public function racks()
    {
        return $this->belongsToMany(Rack::class, 'product_rack', 'product_id', 'rack_id');
    }

    public function hasTransactions()
    {
        return \App\Models\PurchaseItem::where('product_id', $this->id)->exists() ||
               \App\Models\InvoiceItem::where('product_id', $this->id)->exists() ||
               \App\Models\TransferItem::where('product_id', $this->id)->exists() ||
               \App\Models\AdjustStockItem::where('product_id', $this->id)->exists() ||
               \App\Models\ReturnPurchaseItem::where('product_id', $this->id)->exists() ||
               \App\Models\ReturnItem::where('product_id', $this->id)->exists() ||
               \App\Models\UsedPurchaseItem::where('product_id', $this->id)->exists() ||
               \App\Models\UsedItem::where('product_id', $this->id)->exists() ||
               \App\Models\DamageItem::where('product_id', $this->id)->exists();
    }
}
