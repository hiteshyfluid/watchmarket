<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipLevel extends Model
{
    protected $fillable = [
        'name',
        'seller_type',
        'private_min_advert_price',
        'private_max_advert_price',
        'trade_max_advert_count',
        'description',
        'confirmation_message',
        'initial_payment',
        'has_recurring',
        'billing_amount',
        'billing_every',
        'billing_period',
        'billing_cycle_limit',
        'has_trial',
        'trial_amount',
        'trial_cycles',
        'has_expiration',
        'expiration_number',
        'expiration_unit',
        'allow_signups',
        'is_active',
    ];

    public const SELLER_TYPE_BOTH = 'both';
    public const SELLER_TYPE_PRIVATE = 'private_seller';
    public const SELLER_TYPE_TRADE = 'trade_seller';

    protected $casts = [
        'initial_payment' => 'decimal:2',
        'private_min_advert_price' => 'decimal:2',
        'private_max_advert_price' => 'decimal:2',
        'has_recurring' => 'boolean',
        'billing_amount' => 'decimal:2',
        'has_trial' => 'boolean',
        'trial_amount' => 'decimal:2',
        'has_expiration' => 'boolean',
        'allow_signups' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(MembershipSubscription::class);
    }

    public function orders()
    {
        return $this->hasMany(MembershipOrder::class);
    }

    public function expirationLabel(): string
    {
        if (!$this->has_expiration || !$this->expiration_number || !$this->expiration_unit) {
            return '--';
        }

        $unit = $this->expiration_number === 1 ? $this->expiration_unit : "{$this->expiration_unit}s";

        return "After {$this->expiration_number} {$unit}";
    }

    public function billingSummary(): string
    {
        $recurringConfigured = $this->has_recurring
            && $this->billing_amount !== null
            && $this->billing_every
            && $this->billing_period;

        if (!$recurringConfigured) {
            return $this->initial_payment > 0
                ? 'One-time payment: £' . number_format((float) $this->initial_payment, 2)
                : 'FREE';
        }

        $amount = '£' . number_format((float) $this->billing_amount, 2);
        $periodLabel = $this->billing_every == 1
            ? $this->billing_period
            : "{$this->billing_every} {$this->billing_period}s";

        if ($this->has_trial && $this->trial_cycles > 0) {
            $trialPeriodLabel = $this->trial_cycles == 1
                ? $this->billing_period
                : "{$this->trial_cycles} {$this->billing_period}s";

            if ((float) $this->trial_amount <= 0) {
                return "The first {$trialPeriodLabel} are free, you will then be charged {$amount} per {$periodLabel} thereafter.";
            }

            $trialAmount = '£' . number_format((float) $this->trial_amount, 2);
            return "The first {$trialPeriodLabel} are {$trialAmount}, you will then be charged {$amount} per {$periodLabel} thereafter.";
        }

        if ((float) $this->initial_payment > 0) {
            $initial = '£' . number_format((float) $this->initial_payment, 2);
            return "Initial payment {$initial}, then {$amount} per {$periodLabel}.";
        }

        return "{$amount} per {$periodLabel}.";
    }

    public function sellerTypeLabel(): string
    {
        return match ($this->seller_type) {
            self::SELLER_TYPE_PRIVATE => 'Private Seller',
            self::SELLER_TYPE_TRADE => 'Trade Seller',
            default => 'Both',
        };
    }

    public function privatePriceRangeLabel(): ?string
    {
        if ($this->seller_type !== self::SELLER_TYPE_PRIVATE) {
            return null;
        }

        if ($this->private_min_advert_price === null || $this->private_max_advert_price === null) {
            return null;
        }

        return 'Private advert range: £' .
            number_format((float) $this->private_min_advert_price, 2) .
            ' - £' .
            number_format((float) $this->private_max_advert_price, 2);
    }

    public function tradeAdvertLimitLabel(): ?string
    {
        if ($this->seller_type !== self::SELLER_TYPE_TRADE || !$this->trade_max_advert_count) {
            return null;
        }

        if ((int) $this->trade_max_advert_count === -1) {
            return 'Trade advert limit: Unlimited';
        }

        return 'Trade advert limit: ' . $this->trade_max_advert_count;
    }
}
