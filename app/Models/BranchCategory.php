<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'category_id',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
