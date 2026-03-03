<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    public const STATUS_DRAFT   = 'draft';
    public const STATUS_ACTIVE  = 'active';
    public const STATUS_PAUSED  = 'paused';
    public const STATUS_SOLD    = 'sold';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'brand_id',
        'model_id',
        'reference_number',
        'category_id',
        'price',
        'price_negotiable',
        'accept_traders',
        'city',
        'postcode',
        'meeting_preference_id',
        'show_phone',
        'main_image',
        'status',
        'is_featured',
        'expiry_date',
        'is_sold',
        'paper_id',
        'box_id',
        'year_id',
        'gender_id',
        'condition_id',
        'case_size_mm',
        'service_history',
        'movement_id',
        'case_material_id',
        'bracelet_material_id',
        'dial_colour_id',
        'case_diameter_id',
        'waterproof_id',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_sold'     => 'boolean',
        'show_phone'  => 'boolean',
        'is_featured' => 'boolean',
        'price_negotiable' => 'boolean',
        'accept_traders' => 'boolean',
    ];

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function model()
    {
        return $this->belongsTo(Brand::class, 'model_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(AdvertImage::class)->orderBy('sort_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'advert_tag');
    }

    // Watch attribute relationships
    public function paper()            { return $this->belongsTo(AttributeOption::class, 'paper_id'); }
    public function box()              { return $this->belongsTo(AttributeOption::class, 'box_id'); }
    public function year()             { return $this->belongsTo(AttributeOption::class, 'year_id'); }
    public function gender()           { return $this->belongsTo(AttributeOption::class, 'gender_id'); }
    public function condition()        { return $this->belongsTo(AttributeOption::class, 'condition_id'); }
    public function meetingPreference(){ return $this->belongsTo(AttributeOption::class, 'meeting_preference_id'); }
    public function movement()         { return $this->belongsTo(AttributeOption::class, 'movement_id'); }
    public function caseMaterial()     { return $this->belongsTo(AttributeOption::class, 'case_material_id'); }
    public function braceletMaterial() { return $this->belongsTo(AttributeOption::class, 'bracelet_material_id'); }
    public function dialColour()       { return $this->belongsTo(AttributeOption::class, 'dial_colour_id'); }
    public function caseDiameter()     { return $this->belongsTo(AttributeOption::class, 'case_diameter_id'); }
    public function waterproof()       { return $this->belongsTo(AttributeOption::class, 'waterproof_id'); }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForSeller($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'active'  => 'bg-green-100 text-green-800',
            'paused'  => 'bg-yellow-100 text-yellow-800',
            'sold'    => 'bg-blue-100 text-blue-800',
            'expired' => 'bg-red-100 text-red-800',
            default   => 'bg-gray-100 text-gray-800',
        };
    }

    public function mainImageUrl(): ?string
    {
        if ($this->main_image) {
            return asset('storage/' . $this->main_image);
        }
        return null;
    }
}
