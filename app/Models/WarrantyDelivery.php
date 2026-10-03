<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyDelivery extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function claim() { return $this->belongsTo(WarrantyClaim::class, 'warranty_claim_id'); }
    public function product() { return $this->belongsTo(Product::class, 'delivered_product_id'); }
}
