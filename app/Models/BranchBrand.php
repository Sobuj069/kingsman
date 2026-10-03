<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchBrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'brand_id',
    ];
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
