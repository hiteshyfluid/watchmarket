<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'membership_level_id',
        'status',
        'fee_label',
        'start_date',
        'end_date',
        'billing_name',
        'billing_address',
        'billing_phone',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function level()
    {
        return $this->belongsTo(MembershipLevel::class, 'membership_level_id');
    }

    public function orders()
    {
        return $this->hasMany(MembershipOrder::class);
    }
}

