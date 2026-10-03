<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_racks', 'rack_id', 'branch_id');
    }

    public function branchRacks()
    {
        return $this->hasMany(BranchRack::class, 'rack_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_rack', 'rack_id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
