<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustStockItem extends Model
{
    use HasFactory;

    public function adjust()
    {
        return $this->belongsTo(AdjustStock::class);
    }

        public function branch()
    {
        return $this->belongsTo(Branch::class);
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
