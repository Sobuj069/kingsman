<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function transferItems()
    {
        return $this->hasMany(TransferItem::class,'transfer_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'transfer_by')->select('id', 'name');
    }
    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }
    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }
}
