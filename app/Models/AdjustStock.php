<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustStock extends Model
{
    use HasFactory;

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function adjust()
    {
        return $this->belongsTo(AdjustStock::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class,'adjust_by','id');
    }
    
    public function adjust_item()
    {
        return $this->belongsTo(AdjustStockItem::class);
    }
    public function adjustItems()
    {
        return $this->belongsTo(AdjustStockItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_variation()
    {
        return $this->belongsTo(ProductVariation::class);
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
