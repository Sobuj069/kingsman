<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'period',
        'description',
        'status',
        'branch_id',
        'created_by',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'warranty_id');
    }

    public function getFormattedWarrantyAttribute()
    {
        if ($this->period === 'Lifetime') {
            return 'Lifetime Warranty';
        }
        $unit = $this->period;
        if ($this->duration > 1 && !str_ends_with($unit, 's')) {
            $unit .= 's';
        }
        return "{$this->duration} {$unit}";
    }
}
