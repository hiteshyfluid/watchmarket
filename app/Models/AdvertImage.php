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
        return asset('storage/' . $this->image_path);
    }
}
