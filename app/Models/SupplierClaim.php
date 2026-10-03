<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierClaim extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function items() { return $this->hasMany(SupplierClaimItem::class); }
}

// Separate file for SupplierClaimItem
