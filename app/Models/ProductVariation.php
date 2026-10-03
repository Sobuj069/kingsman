<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'size_id', 
        'color_id', 
        'product_id', 
        'variation_id', 
        'image',
        'image_two',
        'selling_price',
        'dis_selling_price',
        'purchase_price'
    ];

    protected static function booted()
    {
        static::saved(function () {
            Product::clearFrontendCache();
        });
        static::deleted(function () {
            Product::clearFrontendCache();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo('App\Models\ProductSize','size_id','id');
        // return $this->belongsTo(ProductSize::class);
    }

    public function color()
    {
        return $this->belongsTo('App\Models\ProductColor','color_id','id');
        // return $this->belongsTo(ProductColor::class);
    }

    /**
     * Get the accessible full image URL for this variation.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        if (file_exists(public_path('uploads/products/' . $this->image))) {
            return asset('uploads/products/' . $this->image);
        }

        if (file_exists(public_path('uploads/product/' . $this->image))) {
            return asset('uploads/product/' . $this->image);
        }

        if (file_exists(public_path('frontend/images/' . $this->image))) {
            return asset('frontend/images/' . $this->image);
        }

        return asset('uploads/products/' . $this->image);
    }

    /**
     * Get the accessible secondary image URL for this variation.
     */
    public function getImageTwoUrlAttribute(): ?string
    {
        if (empty($this->image_two)) {
            return null;
        }

        if (filter_var($this->image_two, FILTER_VALIDATE_URL)) {
            return $this->image_two;
        }

        if (file_exists(public_path('uploads/products/' . $this->image_two))) {
            return asset('uploads/products/' . $this->image_two);
        }

        if (file_exists(public_path('uploads/product/' . $this->image_two))) {
            return asset('uploads/product/' . $this->image_two);
        }

        if (file_exists(public_path('frontend/images/' . $this->image_two))) {
            return asset('frontend/images/' . $this->image_two);
        }

        return asset('uploads/products/' . $this->image_two);
    }

    public function hasTransactions()
    {
        return \App\Models\PurchaseItem::where('product_variation_id', $this->id)->exists() ||
               \App\Models\InvoiceItem::where('product_variation_id', $this->id)->exists() ||
               \App\Models\TransferItem::where('product_variation_id', $this->id)->exists() ||
               \App\Models\AdjustStockItem::where('product_variation_id', $this->id)->exists() ||
               \App\Models\ReturnPurchaseItem::where('product_variation_id', $this->id)->exists();
    }
}
