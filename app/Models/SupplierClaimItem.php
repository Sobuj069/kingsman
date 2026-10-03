<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierClaimItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function claim() { return $this->belongsTo(SupplierClaim::class, 'supplier_claim_id'); }
    public function warranty_claim() { return $this->belongsTo(WarrantyClaim::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
