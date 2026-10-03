<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Damage extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function damageItems()
    {
        return $this->hasMany(DamageItem::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
