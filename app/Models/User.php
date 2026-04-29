<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_PRIVATE_SELLER = 'private_seller';
    public const ROLE_TRADE_SELLER = 'trade_seller';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isPrivateSeller(): bool
    {
        return $this->role === self::ROLE_PRIVATE_SELLER;
    }

    public function isTradeSeller(): bool
    {
        return $this->role === self::ROLE_TRADE_SELLER;
    }

    public function adverts()
    {
        return $this->hasMany(Advert::class);
    }

    public function membershipSubscriptions()
    {
        return $this->hasMany(MembershipSubscription::class);
    }

    public function membershipOrders()
    {
        return $this->hasMany(MembershipOrder::class);
    }

    public function isSeller(): bool
    {
        return $this->isPrivateSeller() || $this->isTradeSeller();
    }

    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function tradeAdvertUsage(): ?array
    {
        if (!$this->isTradeSeller()) {
            return null;
        }

        $subscription = $this->membershipSubscriptions()
            ->with('level')
            ->where('status', MembershipSubscription::STATUS_ACTIVE)
            ->latest('id')
            ->first();

        $activeCount = $this->adverts()
            ->where('status', Advert::STATUS_ACTIVE)
            ->count();

        if (!$subscription || !$subscription->level || $subscription->level->seller_type !== MembershipLevel::SELLER_TYPE_TRADE) {
            return [
                'active_count' => $activeCount,
                'max' => 0,
                'unlimited' => false,
                'can_create' => false,
                'remaining_count' => 0,
                'display' => "{$activeCount}/0",
                'available_display' => '0',
            ];
        }

        $max = (int) $subscription->level->trade_max_advert_count;
        $unlimited = $max === -1;
        $remainingCount = $unlimited ? null : max($max - $activeCount, 0);

        return [
            'active_count' => $activeCount,
            'max' => $max,
            'unlimited' => $unlimited,
            'can_create' => $unlimited || $activeCount < $max,
            'remaining_count' => $remainingCount,
            'display' => $unlimited ? "{$activeCount}/Unlimited" : "{$activeCount}/{$max}",
            'available_display' => $unlimited ? 'Unlimited' : (string) $remainingCount,
        ];
    }
}
