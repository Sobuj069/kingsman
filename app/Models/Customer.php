<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function discountGroup()
    {
        return $this->belongsTo(DiscountGroup::class, 'discount_group_id');
    }
}
