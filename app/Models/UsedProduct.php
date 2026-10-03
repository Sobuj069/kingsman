<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedProduct extends Model
{
    use HasFactory;
    protected $guarded = [];
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
    public function purchaseItems()
    {
        return $this->hasMany(UsedPurchaseItem::class);
    }
}
