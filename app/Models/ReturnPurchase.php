<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnPurchase extends Model
{
    use HasFactory;
    public function return()
    {
        return $this->belongsTo(ReturnPurchase::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function returnPurchaseItems()
    {
        return $this->hasMany(ReturnPurchaseItem::class, 'rtnPurchase_id', 'id');
    }
}
