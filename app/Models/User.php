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
}
