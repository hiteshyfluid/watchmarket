<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'is_active', 'is_featured', 'is_popular', 'image_path', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Brand::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Brand::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function adverts()
    {
        return $this->hasMany(Advert::class, 'brand_id');
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function isModel(): bool
    {
        return !is_null($this->parent_id);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeModels($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeModelsOf($query, $brandId)
    {
        return $query->where('parent_id', $brandId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function imageUrl(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        $path = ltrim($this->image_path, '/');
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }
}
