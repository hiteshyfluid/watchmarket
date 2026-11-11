<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public const SLUG_TERMS = 'terms-conditions';
    public const SLUG_PRIVACY = 'privacy-policy';
    public const SLUG_COOKIES = 'cookie-policy';

    protected $fillable = [
        'slug',
        'title',
        'content',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
