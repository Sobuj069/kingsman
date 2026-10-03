<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdCost extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
