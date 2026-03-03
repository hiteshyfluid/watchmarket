<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipOrder extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'membership_level_id',
        'membership_subscription_id',
        'advert_id',
        'total',
        'billing_details',
        'gateway',
        'payment_transaction_id',
        'subscription_transaction_id',
        'status',
        'ordered_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'ordered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function level()
    {
        return $this->belongsTo(MembershipLevel::class, 'membership_level_id');
    }

    public function subscription()
    {
        return $this->belongsTo(MembershipSubscription::class, 'membership_subscription_id');
    }

    public function advert()
    {
        return $this->belongsTo(Advert::class);
    }
}
