<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchRack extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
