<?php

namespace App\Services;

use App\Models\MembershipLevel;
use App\Models\MembershipOrder;
use App\Models\SiteSetting;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use RuntimeException;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeCheckoutService
{
    public const MODE_TEST = 'test';
    public const MODE_LIVE = 'live';

    public function currentMode(): string
    {
        $mode = strtolower((string) $this->setting('stripe_mode', (string) config('services.stripe.mode', self::MODE_TEST)));

        return in_array($mode, [self::MODE_TEST, self::MODE_LIVE], true)
            ? $mode
            : self::MODE_TEST;
    }

    public function modeLabel(): string
    {
        return $this->currentMode() === self::MODE_LIVE ? 'Live' : 'Test';
    }

    public function publishableKey(): ?string
    {
        $key = $this->currentMode() === self::MODE_LIVE
            ? $this->setting('stripe_live_publishable_key', (string) config('services.stripe.live_publishable_key'))
            : $this->setting('stripe_test_publishable_key', (string) config('services.stripe.test_publishable_key'));

        return $this->normalize($key);
    }

    public function secretKey(): ?string
    {
        $key = $this->currentMode() === self::MODE_LIVE
            ? $this->setting('stripe_live_secret_key', (string) config('services.stripe.live_secret_key'))
            : $this->setting('stripe_test_secret_key', (string) config('services.stripe.test_secret_key'));

        return $this->normalize($key);
    }

    public function webhookSecret(): ?string
    {
        $key = $this->currentMode() === self::MODE_LIVE
            ? $this->setting('stripe_live_webhook_secret', (string) config('services.stripe.live_webhook_secret'))
            : $this->setting('stripe_test_webhook_secret', (string) config('services.stripe.test_webhook_secret'));

        return $this->normalize($key);
    }

    public function isConfiguredForCheckout(MembershipLevel $level, string $checkoutType): bool
    {
        if (!$this->requiresStripe($level, $checkoutType)) {
            return true;
        }

        return filled($this->secretKey());
    }

    public function requiresStripe(MembershipLevel $level, string $checkoutType): bool
    {
        if ($this->usesSubscriptionMode($level, $checkoutType)) {
            return true;
        }

        return $this->amountDueNow($level, $checkoutType) > 0;
    }

    public function usesSubscriptionMode(MembershipLevel $level, string $checkoutType): bool
    {
        return $checkoutType === MembershipPurchaseService::TYPE_TRADE
            && $level->has_recurring
            && $level->billing_amount !== null
            && $level->billing_period !== null;
    }

    public function amountDueNow(MembershipLevel $level, string $checkoutType): float
    {
        if (!$this->usesSubscriptionMode($level, $checkoutType)) {
            return round((float) $level->initial_payment, 2);
        }

        $amount = max(0, (float) $level->initial_payment);

        if ($level->has_trial && (int) $level->trial_cycles > 0) {
            $amount += max(0, (float) $level->trial_amount);
        } else {
            $amount += max(0, (float) $level->billing_amount);
        }

        return round($amount, 2);
    }

    public function createCheckoutSession(
        MembershipOrder $order,
        MembershipLevel $level,
        array $billingData,
        string $checkoutType,
        string $successUrl,
        string $cancelUrl
    ): Session {
        $secretKey = $this->secretKey();

        if (!filled($secretKey)) {
            throw new RuntimeException('Stripe is not configured for the active mode.');
        }

        $params = [
            'mode' => $this->usesSubscriptionMode($level, $checkoutType) ? 'subscription' : 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $order->id,
            'customer_email' => (string) ($billingData['email'] ?? $order->user?->email ?? ''),
            'payment_method_types' => ['card'],
            'line_items' => $this->buildLineItems($level, $checkoutType),
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_code' => (string) $order->code,
                'membership_level_id' => (string) $order->membership_level_id,
                'checkout_type' => $checkoutType,
                'advert_id' => $order->advert_id ? (string) $order->advert_id : '',
            ],
        ];

        if ($params['mode'] === 'subscription') {
            $subscriptionData = [
                'metadata' => [
                    'order_id' => (string) $order->id,
                    'order_code' => (string) $order->code,
                ],
            ];

            if ($trialDays = $this->trialPeriodDays($level)) {
                $subscriptionData['trial_period_days'] = $trialDays;
                $subscriptionData['trial_settings'] = [
                    'end_behavior' => [
                        'missing_payment_method' => 'cancel',
                    ],
                ];
            }

            $params['subscription_data'] = $subscriptionData;
        }

        return $this->client($secretKey)->checkout->sessions->create($params);
    }

    public function retrieveCheckoutSession(string $sessionId): Session
    {
        $secretKey = $this->secretKey();

        if (!filled($secretKey)) {
            throw new RuntimeException('Stripe is not configured for the active mode.');
        }

        return $this->client($secretKey)->checkout->sessions->retrieve($sessionId, []);
    }

    /**
     * @throws SignatureVerificationException
     * @throws UnexpectedValueException
     */
    public function constructWebhookEvent(string $payload, string $signatureHeader): Event
    {
        $secret = $this->webhookSecret();

        if (!filled($secret)) {
            throw new RuntimeException('Stripe webhook secret is not configured.');
        }

        return Webhook::constructEvent($payload, $signatureHeader, $secret);
    }

    public function sessionIsReadyForFulfillment(Session $session): bool
    {
        if (($session->status ?? null) !== 'complete') {
            return false;
        }

        return in_array((string) ($session->payment_status ?? ''), ['paid', 'no_payment_required'], true);
    }

    public function paymentDetailsFromSession(Session $session): array
    {
        return [
            'gateway' => 'Stripe Checkout (' . $this->modeLabel() . ')',
            'payment_transaction_id' => $this->normalizeStripeId($session->payment_intent ?? null),
            'subscription_transaction_id' => $this->normalizeStripeId($session->subscription ?? null),
            'ordered_at' => isset($session->created) ? CarbonImmutable::createFromTimestamp((int) $session->created) : now(),
        ];
    }

    public function orderIdFromSession(Session $session): ?int
    {
        $value = Arr::get($session->metadata?->toArray() ?? [], 'order_id');

        return is_numeric($value) ? (int) $value : null;
    }

    private function buildLineItems(MembershipLevel $level, string $checkoutType): array
    {
        if (!$this->usesSubscriptionMode($level, $checkoutType)) {
            return [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'gbp',
                    'unit_amount' => $this->toMinorUnits((float) $level->initial_payment),
                    'product_data' => [
                        'name' => $level->name,
                        'description' => trim(strip_tags((string) $level->description)) ?: $level->sellerTypeLabel() . ' package',
                    ],
                ],
            ]];
        }

        $lineItems = [[
            'quantity' => 1,
            'price_data' => [
                'currency' => 'gbp',
                'unit_amount' => $this->toMinorUnits((float) $level->billing_amount),
                'recurring' => array_filter([
                    'interval' => $level->billing_period,
                    'interval_count' => $level->billing_every ?: null,
                ], fn ($value) => $value !== null),
                'product_data' => [
                    'name' => $level->name,
                    'description' => trim(strip_tags((string) $level->description)) ?: $level->sellerTypeLabel() . ' subscription',
                ],
            ],
        ]];

        if ((float) $level->initial_payment > 0) {
            $lineItems[] = $this->oneTimeLineItem($level, 'Initial payment', (float) $level->initial_payment);
        }

        if ($level->has_trial && (int) $level->trial_cycles > 0 && (float) $level->trial_amount > 0) {
            $lineItems[] = $this->oneTimeLineItem($level, 'Trial payment', (float) $level->trial_amount);
        }

        return $lineItems;
    }

    private function oneTimeLineItem(MembershipLevel $level, string $suffix, float $amount): array
    {
        return [
            'quantity' => 1,
            'price_data' => [
                'currency' => 'gbp',
                'unit_amount' => $this->toMinorUnits($amount),
                'product_data' => [
                    'name' => $level->name . ' - ' . $suffix,
                    'description' => $level->sellerTypeLabel() . ' package',
                ],
            ],
        ];
    }

    private function trialPeriodDays(MembershipLevel $level): ?int
    {
        if (!$level->has_trial || (int) $level->trial_cycles <= 0 || !$level->billing_period) {
            return null;
        }

        $multiplier = max(1, (int) ($level->billing_every ?: 1));
        $cycles = max(1, (int) $level->trial_cycles);
        $trialEndsAt = CarbonImmutable::now();

        $trialEndsAt = match ($level->billing_period) {
            'day' => $trialEndsAt->addDays($multiplier * $cycles),
            'week' => $trialEndsAt->addWeeks($multiplier * $cycles),
            'month' => $trialEndsAt->addMonthsNoOverflow($multiplier * $cycles),
            'year' => $trialEndsAt->addYearsNoOverflow($multiplier * $cycles),
            default => $trialEndsAt,
        };

        $days = max(1, CarbonImmutable::now()->diffInDays($trialEndsAt));

        return min($days, 730);
    }

    private function toMinorUnits(float $amount): int
    {
        return (int) round($amount * 100);
    }

    private function client(string $secretKey): StripeClient
    {
        return new StripeClient($secretKey);
    }

    private function normalize(?string $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value !== '' ? $value : null;
    }

    private function normalizeStripeId(mixed $value): ?string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_object($value) && isset($value->id) && is_string($value->id)) {
            return $value->id;
        }

        return null;
    }

    private function setting(string $key, ?string $fallback = null): ?string
    {
        return SiteSetting::getValue($key, $fallback);
    }
}
