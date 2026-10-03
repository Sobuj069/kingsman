<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Used extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function usedItems()
    {
        return $this->hasMany(UsedItem::class);
    }
}
