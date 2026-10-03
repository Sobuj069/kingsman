<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'shop_name',
        'address',
        'logo',
        'status',
        'created_by',
    ];

    protected static function booted()
    {
        if (env('HIDE_ADMIN_BRANCH') === 'yes') {
            static::addGlobalScope('hideAdminBranch', function ($builder) {
                $hasIdBound = false;
                foreach ($builder->getQuery()->wheres as $where) {
                    if (isset($where['column']) && (
                        $where['column'] === 'id' || 
                        $where['column'] === 'branches.id'
                    )) {
                        $hasIdBound = true;
                        break;
                    }
                }
                if (!$hasIdBound) {
                    $builder->where('branches.id', '!=', 1);
                }
            });
        }
    }
}
