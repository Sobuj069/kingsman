<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAuditItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function stockAudit()
    {
        return $this->belongsTo(StockAudit::class, 'stock_audit_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}
