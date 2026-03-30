<?php

namespace App\Services;

use App\Models\Advert;
use App\Models\MembershipLevel;
use App\Models\MembershipOrder;
use App\Models\MembershipSubscription;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class MembershipPurchaseService
{
    public const TYPE_TRADE = 'trade';
    public const TYPE_PRIVATE = 'private';

    public function createPendingTradeOrder(User $user, MembershipLevel $level, array $billingData, float $total): MembershipOrder
    {
        return DB::transaction(function () use ($user, $level, $billingData, $total) {
            $this->applyBillingDetailsToUser($user, $billingData + ['role' => User::ROLE_TRADE_SELLER]);

            return MembershipOrder::create([
                'code' => strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'membership_level_id' => $level->id,
                'membership_subscription_id' => null,
                'advert_id' => null,
                'total' => $total,
                'billing_details' => $this->billingAddress($billingData),
                'gateway' => null,
                'payment_transaction_id' => null,
                'subscription_transaction_id' => null,
                'status' => MembershipOrder::STATUS_PENDING,
                'ordered_at' => null,
            ]);
        });
    }

    public function createPendingPrivateOrder(User $user, Advert $advert, MembershipLevel $level, array $billingData, float $total): MembershipOrder
    {
        return DB::transaction(function () use ($user, $advert, $level, $billingData, $total) {
            $this->applyBillingDetailsToUser($user, $billingData + ['role' => User::ROLE_PRIVATE_SELLER]);

            return MembershipOrder::create([
                'code' => strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'membership_level_id' => $level->id,
                'membership_subscription_id' => null,
                'advert_id' => $advert->id,
                'total' => $total,
                'billing_details' => $this->billingAddress($billingData),
                'gateway' => null,
                'payment_transaction_id' => null,
                'subscription_transaction_id' => null,
                'status' => MembershipOrder::STATUS_PENDING,
                'ordered_at' => null,
            ]);
        });
    }

    public function markOrderCancelled(MembershipOrder $order): MembershipOrder
    {
        if ($order->status === MembershipOrder::STATUS_PAID) {
            return $order;
        }

        $order->update([
            'status' => MembershipOrder::STATUS_CANCELLED,
        ]);

        return $order->fresh(['level', 'user', 'advert', 'subscription']);
    }

    public function markOrderFailed(MembershipOrder $order, ?string $gateway = null): MembershipOrder
    {
        if ($order->status === MembershipOrder::STATUS_PAID) {
            return $order;
        }

        $order->update([
            'gateway' => $gateway ?: $order->gateway,
            'status' => MembershipOrder::STATUS_FAILED,
        ]);

        return $order->fresh(['level', 'user', 'advert', 'subscription']);
    }

    public function completeOrder(MembershipOrder $order, array $paymentDetails = []): MembershipOrder
    {
        return DB::transaction(function () use ($order, $paymentDetails) {
            /** @var MembershipOrder $lockedOrder */
            $lockedOrder = MembershipOrder::query()
                ->with(['level', 'user', 'advert', 'subscription'])
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->status === MembershipOrder::STATUS_PAID) {
                return $lockedOrder;
            }

            if (!$lockedOrder->level || !$lockedOrder->user) {
                throw new RuntimeException('The order is missing required payment relationships.');
            }

            $orderedAt = $paymentDetails['ordered_at'] ?? now();
            if (!$orderedAt instanceof CarbonInterface) {
                $orderedAt = now();
            }

            if ($lockedOrder->level->seller_type === MembershipLevel::SELLER_TYPE_TRADE) {
                $this->activateTradeOrder($lockedOrder, $orderedAt, $paymentDetails);
            } else {
                $this->activatePrivateOrder($lockedOrder, $orderedAt, $paymentDetails);
            }

            return $lockedOrder->fresh(['level', 'user', 'advert', 'subscription']);
        });
    }

    public function feeLabel(MembershipLevel $level): string
    {
        if ($level->has_recurring && $level->billing_amount !== null && $level->billing_period) {
            return '£' . number_format((float) $level->billing_amount, 2) . ' per ' . $level->billing_period;
        }

        if ((float) $level->initial_payment > 0) {
            return '£' . number_format((float) $level->initial_payment, 2);
        }

        return 'Free';
    }

    public function endDateForLevel(MembershipLevel $level): ?string
    {
        if (!$level->has_expiration || !$level->expiration_number || !$level->expiration_unit) {
            return null;
        }

        $date = now();

        $date = match ($level->expiration_unit) {
            'day' => $date->addDays($level->expiration_number),
            'week' => $date->addWeeks($level->expiration_number),
            'month' => $date->addMonths($level->expiration_number),
            'year' => $date->addYears($level->expiration_number),
            default => $date,
        };

        return $date->toDateString();
    }

    private function activateTradeOrder(MembershipOrder $order, CarbonInterface $orderedAt, array $paymentDetails): void
    {
        MembershipSubscription::query()
            ->where('user_id', $order->user_id)
            ->where('status', MembershipSubscription::STATUS_ACTIVE)
            ->update(['status' => MembershipSubscription::STATUS_REPLACED]);

        $subscription = $order->subscription;

        if (!$subscription) {
            $subscription = MembershipSubscription::create([
                'user_id' => $order->user_id,
                'membership_level_id' => $order->membership_level_id,
                'status' => MembershipSubscription::STATUS_ACTIVE,
                'fee_label' => $this->feeLabel($order->level),
                'start_date' => $orderedAt->toDateString(),
                'end_date' => $this->endDateForLevel($order->level),
                'billing_name' => trim($order->user->first_name . ' ' . $order->user->last_name),
                'billing_address' => $order->billing_details,
                'billing_phone' => $order->user->phone,
            ]);
        } else {
            $subscription->update([
                'status' => MembershipSubscription::STATUS_ACTIVE,
                'fee_label' => $this->feeLabel($order->level),
                'start_date' => $orderedAt->toDateString(),
                'end_date' => $this->endDateForLevel($order->level),
                'billing_name' => trim($order->user->first_name . ' ' . $order->user->last_name),
                'billing_address' => $order->billing_details,
                'billing_phone' => $order->user->phone,
            ]);
        }

        $order->user->update(['role' => User::ROLE_TRADE_SELLER]);

        $order->update([
            'membership_subscription_id' => $subscription->id,
            'gateway' => $paymentDetails['gateway'] ?? $order->gateway ?? 'Stripe Checkout',
            'payment_transaction_id' => $paymentDetails['payment_transaction_id'] ?? $order->payment_transaction_id,
            'subscription_transaction_id' => $paymentDetails['subscription_transaction_id'] ?? $order->subscription_transaction_id,
            'status' => MembershipOrder::STATUS_PAID,
            'ordered_at' => $orderedAt,
        ]);
    }

    private function activatePrivateOrder(MembershipOrder $order, CarbonInterface $orderedAt, array $paymentDetails): void
    {
        $order->user->update(['role' => User::ROLE_PRIVATE_SELLER]);

        if ($order->advert) {
            $order->advert->update([
                'status' => Advert::STATUS_ACTIVE,
                'expiry_date' => $this->endDateForLevel($order->level),
            ]);
        }

        $order->update([
            'gateway' => $paymentDetails['gateway'] ?? $order->gateway ?? 'Stripe Checkout',
            'payment_transaction_id' => $paymentDetails['payment_transaction_id'] ?? $order->payment_transaction_id,
            'subscription_transaction_id' => $paymentDetails['subscription_transaction_id'] ?? $order->subscription_transaction_id,
            'status' => MembershipOrder::STATUS_PAID,
            'ordered_at' => $orderedAt,
        ]);
    }

    private function applyBillingDetailsToUser(User $user, array $billingData): void
    {
        $user->update([
            'first_name' => $billingData['first_name'],
            'last_name' => $billingData['last_name'],
            'address' => $billingData['address'],
            'city' => $billingData['city'],
            'postal_code' => $billingData['postal_code'],
            'country' => $billingData['country'],
            'phone' => $billingData['phone'],
            'email' => $billingData['email'],
            'role' => $billingData['role'] ?? $user->role,
        ]);
    }

    private function billingAddress(array $billingData): string
    {
        return trim(
            $billingData['address'] . "\n" .
            $billingData['city'] . "\n" .
            $billingData['country'] . "\n" .
            $billingData['postal_code']
        );
    }
}
