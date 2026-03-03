<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    // All managed watch attribute types
    public const TYPES = [
        'paper'             => 'Paper',
        'box'               => 'Box',
        'year'              => 'Year',
        'gender'            => 'Gender',
        'condition'         => 'Condition',
        'movement'          => 'Movement',
        'case_material'     => 'Case Material',
        'bracelet_material' => 'Bracelet Material',
        'dial_colour'       => 'Dial Colour',
        'case_diameter'     => 'Case Diameter',
        'waterproof'        => 'Waterproof',
        'meeting_preference'=> 'Meeting Preference',
    ];

    protected $fillable = ['type', 'name', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public static function typeLabel(string $type): string
    {
        return self::TYPES[$type] ?? ucwords(str_replace('_', ' ', $type));
    }

    public static function isValidType(string $type): bool
    {
        return array_key_exists($type, self::TYPES);
    }
}
