<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnPurchaseItem extends Model
{
    use HasFactory;

    public function return()
    {
        return $this->belongsTo(ReturnPurchase::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->select('id', 'name', 'phone', 'email', 'address');
    }
    public function product_variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id', 'id');
    }

    public function size()
    {
        return $this->belongsTo('App\Models\ProductSize', 'size_id', 'id');
    }

    public function color()
    {
        return $this->belongsTo('App\Models\ProductColor', 'color_id', 'id');
    }

    //created by
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
