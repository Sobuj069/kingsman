<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
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
