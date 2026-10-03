<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAudit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'audited_by');
    }

    public function items()
    {
        return $this->hasMany(StockAuditItem::class, 'stock_audit_id');
    }
}
