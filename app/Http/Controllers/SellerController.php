<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\MembershipLevel;
use App\Models\MembershipOrder;
use App\Models\User;
use App\Services\MembershipPurchaseService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Stripe\Exception\ApiErrorException;

class SellerController extends Controller
{
    public function __construct(
        private MembershipPurchaseService $membershipPurchaseService,
        private StripeCheckoutService $stripeCheckoutService,
    ) {
    }

    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isSeller()) {
            return redirect()->route('adverts.index');
        }

        return redirect()->route('seller.choose-account-type');
    }

    public function chooseAccountType()
    {
        return view('seller.choose-account-type');
    }

    public function updateAccountType(Request $request)
    {
        $request->validate([
            'role' => 'required|in:private_seller,trade_seller',
        ]);

        if ($request->role === User::ROLE_TRADE_SELLER) {
            return redirect()->route('seller.trade.packages');
        }

        $user = auth()->user();
        $user->role = $request->role;
        $user->save();

        return redirect()->route('adverts.index')->with('status', 'Your account has been updated to ' . str_replace('_', ' ', $request->role) . '.');
    }

    public function tradePackages()
    {
        $levels = MembershipLevel::query()
            ->where('seller_type', MembershipLevel::SELLER_TYPE_TRADE)
            ->where('is_active', true)
            ->where('allow_signups', true)
            ->orderBy('initial_payment')
            ->orderBy('name')
            ->get();

        return view('seller.trade-packages', compact('levels'));
    }

    public function tradeCheckout(MembershipLevel $level)
    {
        $this->ensureTradeLevel($level);

        $user = auth()->user();
        $requiresStripe = $this->stripeCheckoutService->requiresStripe($level, MembershipPurchaseService::TYPE_TRADE);
        $stripeMode = $this->stripeCheckoutService->modeLabel();

        return view('seller.trade-checkout', compact('level', 'user', 'requiresStripe', 'stripeMode'));
    }

    public function processTradeCheckout(Request $request, MembershipLevel $level)
    {
        $this->ensureTradeLevel($level);

        $validated = $this->validateBillingData($request, (int) auth()->id());
        $user = auth()->user();
        $checkoutType = MembershipPurchaseService::TYPE_TRADE;
        $requiresStripe = $this->stripeCheckoutService->requiresStripe($level, $checkoutType);

        if ($requiresStripe && !$this->stripeCheckoutService->isConfiguredForCheckout($level, $checkoutType)) {
            return back()->withInput()->with('error', 'Stripe is not configured for the active mode yet. Please ask the admin to add the Stripe keys first.');
        }

        $order = $this->membershipPurchaseService->createPendingTradeOrder(
            $user,
            $level,
            $validated,
            $this->stripeCheckoutService->amountDueNow($level, $checkoutType)
        );

        if (!$requiresStripe) {
            $this->membershipPurchaseService->completeOrder($order, [
                'gateway' => 'Free Checkout',
                'ordered_at' => now(),
            ]);

            return redirect()->route('seller.trade.thank-you', $order)
                ->with('success', 'Subscription activated successfully.');
        }

        try {
            $session = $this->stripeCheckoutService->createCheckoutSession(
                $order,
                $level,
                $validated,
                $checkoutType,
                route('seller.trade.thank-you', $order) . '?session_id={CHECKOUT_SESSION_ID}',
                route('seller.trade.checkout.cancel', [$level, $order])
            );
        } catch (ApiErrorException|\RuntimeException $e) {
            $this->membershipPurchaseService->markOrderFailed($order, 'Stripe Checkout (' . $this->stripeCheckoutService->modeLabel() . ')');

            return back()->withInput()->with('error', 'We could not start Stripe checkout right now. Please try again.');
        }

        $order->update([
            'gateway' => 'Stripe Checkout (' . $this->stripeCheckoutService->modeLabel() . ')',
        ]);

        return redirect()->away($session->url);
    }

    public function tradeThankYou(Request $request, MembershipOrder $order)
    {
        $user = auth()->user();

        abort_unless($order->user_id === $user->id, 403);
        abort_unless($order->level?->seller_type === MembershipLevel::SELLER_TYPE_TRADE, 404);

        $resolvedOrder = $this->resolvePendingOrderAfterCheckout(
            $order,
            $request,
            'seller.trade.checkout',
            [$order->level]
        );

        if ($resolvedOrder instanceof RedirectResponse) {
            return $resolvedOrder;
        }

        $resolvedOrder->load(['level', 'user']);

        return view('seller.trade-thank-you', ['order' => $resolvedOrder]);
    }

    public function cancelTradeCheckout(MembershipLevel $level, MembershipOrder $order)
    {
        $this->ensureTradeLevel($level);

        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless((int) $order->membership_level_id === (int) $level->id, 404);

        $this->membershipPurchaseService->markOrderCancelled($order);

        return redirect()->route('seller.trade.checkout', $level)
            ->with('error', 'Stripe checkout was cancelled. You can try again whenever you are ready.');
    }

    public function privatePackages(Advert $advert)
    {
        $user = auth()->user();
        $this->ensurePrivateAdvertOwner($advert, $user->id);

        if ($this->hasPaidPrivateOrder($advert)) {
            return redirect()->route('adverts.index')
                ->with('error', 'Checkout is already completed for this advert.');
        }

        $levels = MembershipLevel::query()
            ->where('seller_type', MembershipLevel::SELLER_TYPE_PRIVATE)
            ->where('is_active', true)
            ->where('allow_signups', true)
            ->where(function ($q) use ($advert) {
                $q->whereNull('private_min_advert_price')
                    ->orWhere('private_min_advert_price', '<=', $advert->price);
            })
            ->where(function ($q) use ($advert) {
                $q->whereNull('private_max_advert_price')
                    ->orWhere('private_max_advert_price', '>=', $advert->price);
            })
            ->orderBy('initial_payment')
            ->orderBy('name')
            ->get();

        return view('seller.private-packages', compact('levels', 'advert'));
    }

    public function privateCheckout(Advert $advert, MembershipLevel $level)
    {
        $user = auth()->user();
        $this->ensurePrivateAdvertOwner($advert, $user->id);

        if ($this->hasPaidPrivateOrder($advert)) {
            return redirect()->route('adverts.index')
                ->with('error', 'Checkout is already completed for this advert.');
        }

        $this->ensurePrivateLevelForAdvert($level, (float) $advert->price);

        $requiresStripe = $this->stripeCheckoutService->requiresStripe($level, MembershipPurchaseService::TYPE_PRIVATE);
        $stripeMode = $this->stripeCheckoutService->modeLabel();

        return view('seller.private-checkout', compact('level', 'user', 'advert', 'requiresStripe', 'stripeMode'));
    }

    public function processPrivateCheckout(Request $request, Advert $advert, MembershipLevel $level)
    {
        $user = auth()->user();
        $this->ensurePrivateAdvertOwner($advert, $user->id);

        if ($this->hasPaidPrivateOrder($advert)) {
            return redirect()->route('adverts.index')
                ->with('error', 'Checkout is already completed for this advert.');
        }

        $this->ensurePrivateLevelForAdvert($level, (float) $advert->price);

        $validated = $this->validateBillingData($request, $user->id);
        $checkoutType = MembershipPurchaseService::TYPE_PRIVATE;
        $requiresStripe = $this->stripeCheckoutService->requiresStripe($level, $checkoutType);

        if ($requiresStripe && !$this->stripeCheckoutService->isConfiguredForCheckout($level, $checkoutType)) {
            return back()->withInput()->with('error', 'Stripe is not configured for the active mode yet. Please ask the admin to add the Stripe keys first.');
        }

        $order = $this->membershipPurchaseService->createPendingPrivateOrder(
            $user,
            $advert,
            $level,
            $validated,
            $this->stripeCheckoutService->amountDueNow($level, $checkoutType)
        );

        if (!$requiresStripe) {
            $this->membershipPurchaseService->completeOrder($order, [
                'gateway' => 'Free Checkout',
                'ordered_at' => now(),
            ]);

            return redirect()->route('seller.private.thank-you', $order)
                ->with('success', 'Advert activated successfully.');
        }

        try {
            $session = $this->stripeCheckoutService->createCheckoutSession(
                $order,
                $level,
                $validated,
                $checkoutType,
                route('seller.private.thank-you', $order) . '?session_id={CHECKOUT_SESSION_ID}',
                route('seller.private.checkout.cancel', [$advert, $level, $order])
            );
        } catch (ApiErrorException|\RuntimeException $e) {
            $this->membershipPurchaseService->markOrderFailed($order, 'Stripe Checkout (' . $this->stripeCheckoutService->modeLabel() . ')');

            return back()->withInput()->with('error', 'We could not start Stripe checkout right now. Please try again.');
        }

        $order->update([
            'gateway' => 'Stripe Checkout (' . $this->stripeCheckoutService->modeLabel() . ')',
        ]);

        return redirect()->away($session->url);
    }

    public function privateThankYou(Request $request, MembershipOrder $order)
    {
        $user = auth()->user();

        abort_unless($order->user_id === $user->id, 403);
        abort_unless($order->level?->seller_type === MembershipLevel::SELLER_TYPE_PRIVATE, 404);

        $resolvedOrder = $this->resolvePendingOrderAfterCheckout(
            $order,
            $request,
            'seller.private.checkout',
            [$order->advert, $order->level]
        );

        if ($resolvedOrder instanceof RedirectResponse) {
            return $resolvedOrder;
        }

        $resolvedOrder->load(['level', 'user']);

        return view('seller.private-thank-you', ['order' => $resolvedOrder]);
    }

    public function cancelPrivateCheckout(Advert $advert, MembershipLevel $level, MembershipOrder $order)
    {
        $this->ensurePrivateAdvertOwner($advert, (int) auth()->id());
        $this->ensurePrivateLevelForAdvert($level, (float) $advert->price);

        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless((int) $order->membership_level_id === (int) $level->id, 404);
        abort_unless((int) $order->advert_id === (int) $advert->id, 404);

        $this->membershipPurchaseService->markOrderCancelled($order);

        return redirect()->route('seller.private.checkout', [$advert, $level])
            ->with('error', 'Stripe checkout was cancelled. You can try again whenever you are ready.');
    }

    private function ensureTradeLevel(MembershipLevel $level): void
    {
        abort_unless(
            $level->seller_type === MembershipLevel::SELLER_TYPE_TRADE
                && $level->allow_signups
                && $level->is_active,
            404
        );
    }

    private function ensurePrivateLevelForAdvert(MembershipLevel $level, float $price): void
    {
        abort_unless(
            $level->seller_type === MembershipLevel::SELLER_TYPE_PRIVATE
                && $level->allow_signups
                && $level->is_active,
            404
        );

        if ($level->private_min_advert_price !== null && $price < (float) $level->private_min_advert_price) {
            abort(422, 'This package does not support the advert price range.');
        }

        if ($level->private_max_advert_price !== null && $price > (float) $level->private_max_advert_price) {
            abort(422, 'This package does not support the advert price range.');
        }
    }

    private function ensurePrivateAdvertOwner(Advert $advert, int $userId): void
    {
        abort_unless($advert->user_id === $userId, 403);
    }

    private function hasPaidPrivateOrder(Advert $advert): bool
    {
        return MembershipOrder::query()
            ->where('advert_id', $advert->id)
            ->where('user_id', auth()->id())
            ->where('status', MembershipOrder::STATUS_PAID)
            ->whereHas('level', fn($q) => $q->where('seller_type', MembershipLevel::SELLER_TYPE_PRIVATE))
            ->exists();
    }

    private function validateBillingData(Request $request, int $userId): array
    {
        return $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:30',
            'country' => 'required|string|max:100',
            'phone' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
        ]);
    }

    private function resolvePendingOrderAfterCheckout(
        MembershipOrder $order,
        Request $request,
        string $fallbackRoute,
        array $fallbackRouteParameters
    ): MembershipOrder|RedirectResponse {
        if ($order->status === MembershipOrder::STATUS_PAID) {
            return $order;
        }

        if (in_array($order->status, [MembershipOrder::STATUS_CANCELLED, MembershipOrder::STATUS_FAILED], true)) {
            return redirect()->route($fallbackRoute, $fallbackRouteParameters)
                ->with('error', 'This checkout was not completed. Please start a new payment attempt.');
        }

        $sessionId = trim((string) $request->query('session_id'));

        if ($sessionId === '') {
            return redirect()->route($fallbackRoute, $fallbackRouteParameters)
                ->with('error', 'Stripe has not confirmed this payment yet. Please try again if you were not charged.');
        }

        try {
            $session = $this->stripeCheckoutService->retrieveCheckoutSession($sessionId);
        } catch (ApiErrorException|\RuntimeException $e) {
            return redirect()->route($fallbackRoute, $fallbackRouteParameters)
                ->with('error', 'We could not verify your Stripe payment just yet. Please contact support if you were charged.');
        }

        abort_unless($this->stripeCheckoutService->orderIdFromSession($session) === $order->id, 403);

        if (!$this->stripeCheckoutService->sessionIsReadyForFulfillment($session)) {
            return redirect()->route($fallbackRoute, $fallbackRouteParameters)
                ->with('error', 'Stripe has not marked this payment as complete yet.');
        }

        return $this->membershipPurchaseService->completeOrder(
            $order,
            $this->stripeCheckoutService->paymentDetailsFromSession($session)
        );
    }
}
