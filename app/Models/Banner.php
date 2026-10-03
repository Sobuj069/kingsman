<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Scope for active banners only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope by position.
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope ordered by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'desc');
    }

    /**
     * Get the full URL for the banner image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('frontend/images/banner_hero_eid.jpg');
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        if (file_exists(public_path('uploads/banners/' . $this->image))) {
            return asset('uploads/banners/' . $this->image);
        }

        if (file_exists(public_path('frontend/images/' . $this->image))) {
            return asset('frontend/images/' . $this->image);
        }

        return asset('uploads/banners/' . $this->image);
    }

    /**
     * Associated branch if any.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
