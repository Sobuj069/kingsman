<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentSchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
}
