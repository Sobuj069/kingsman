<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function () {
            Product::clearFrontendCache();
        });
        static::deleted(function () {
            Product::clearFrontendCache();
        });
    }

    //supplier relation
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    //user who created this purchase
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    //purchase items relation
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id', 'id');
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function product_variation()
    {
        return $this->belongsTo(ProductVariation::class,'product_variation_id', 'id');
    }

    public function size()
    {
        return $this->belongsTo('App\Models\ProductSize', 'size_id', 'id');
    }

    public function color()
    {
        return $this->belongsTo('App\Models\ProductColor', 'color_id', 'id');
    }
}
