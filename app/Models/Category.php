<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    use HasFactory;

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            if (file_exists(public_path('uploads/category/' . $this->image))) {
                return asset('uploads/category/' . $this->image);
            }
            if (file_exists(public_path('uploads/categories/' . $this->image))) {
                return asset('uploads/categories/' . $this->image);
            }
        }
        // Fallback to first product's image if available
        $firstProduct = $this->products()->where('is_service', 0)->where('status', 1)->first();
        if ($firstProduct && !empty($firstProduct->image_url)) {
            return $firstProduct->image_url;
        }
        return asset('frontend/images/no-image.svg');
    }

    public function getSlugAttribute(): string
    {
        return \Illuminate\Support\Str::slug($this->name);
    }
}

