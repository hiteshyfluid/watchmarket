<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertImage extends Model
{
    protected $fillable = ['advert_id', 'image_path', 'sort_order'];

    public function advert()
    {
        return $this->belongsTo(Advert::class);
    }

    public function url(): string
    {
        $path = ltrim((string) $this->image_path, '/');
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }
        return asset('storage/' . $path);
    }
}
