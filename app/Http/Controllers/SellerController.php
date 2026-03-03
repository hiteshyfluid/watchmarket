<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\MembershipLevel;
use App\Models\MembershipOrder;
use App\Models\MembershipSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SellerController extends Controller
{
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

        return view('seller.trade-checkout', compact('level', 'user'));
    }

    public function processTradeCheckout(Request $request, MembershipLevel $level)
    {
        $this->ensureTradeLevel($level);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:30',
            'country' => 'required|string|max:100',
            'phone' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(auth()->id())],
        ]);

        $user = auth()->user();

        $order = DB::transaction(function () use ($user, $level, $validated) {
            $user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'country' => $validated['country'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'role' => User::ROLE_TRADE_SELLER,
            ]);

            $subscription = MembershipSubscription::create([
                'user_id' => $user->id,
                'membership_level_id' => $level->id,
                'status' => 'active',
                'fee_label' => $this->feeLabel($level),
                'start_date' => now()->toDateString(),
                'end_date' => $this->endDateForLevel($level),
                'billing_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'billing_address' => trim($validated['address'] . "\n" . $validated['city'] . "\n" . $validated['country'] . "\n" . $validated['postal_code']),
                'billing_phone' => $validated['phone'],
            ]);

            return MembershipOrder::create([
                'code' => strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'membership_level_id' => $level->id,
                'membership_subscription_id' => $subscription->id,
                'total' => $level->initial_payment ?? 0,
                'billing_details' => $subscription->billing_address,
                'gateway' => 'Manual Checkout',
                'payment_transaction_id' => 'manual_' . Str::lower(Str::random(12)),
                'subscription_transaction_id' => $level->has_recurring ? 'sub_' . Str::lower(Str::random(12)) : null,
                'status' => 'paid',
                'ordered_at' => now(),
            ]);
        });

        return redirect()->route('seller.trade.thank-you', $order);
    }

    public function tradeThankYou(MembershipOrder $order)
    {
        $user = auth()->user();

        abort_unless($order->user_id === $user->id, 403);

        $order->load(['level', 'user']);

        return view('seller.trade-thank-you', compact('order'));
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

        $this->ensurePrivateLevelForAdvert($level, $advert->price);

        return view('seller.private-checkout', compact('level', 'user', 'advert'));
    }

    public function processPrivateCheckout(Request $request, Advert $advert, MembershipLevel $level)
    {
        $user = auth()->user();
        $this->ensurePrivateAdvertOwner($advert, $user->id);

        if ($this->hasPaidPrivateOrder($advert)) {
            return redirect()->route('adverts.index')
                ->with('error', 'Checkout is already completed for this advert.');
        }

        $this->ensurePrivateLevelForAdvert($level, $advert->price);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:30',
            'country' => 'required|string|max:100',
            'phone' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $order = DB::transaction(function () use ($user, $advert, $level, $validated) {
            $user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'country' => $validated['country'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'role' => User::ROLE_PRIVATE_SELLER,
            ]);

            $billingAddress = trim($validated['address'] . "\n" . $validated['city'] . "\n" . $validated['country'] . "\n" . $validated['postal_code']);

            $order = MembershipOrder::create([
                'code' => strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'membership_level_id' => $level->id,
                'membership_subscription_id' => null,
                'advert_id' => $advert->id,
                'total' => $level->initial_payment ?? 0,
                'billing_details' => $billingAddress,
                'gateway' => 'Manual Checkout',
                'payment_transaction_id' => 'manual_' . Str::lower(Str::random(12)),
                'subscription_transaction_id' => null,
                'status' => 'paid',
                'ordered_at' => now(),
            ]);

            $advert->update([
                'status' => Advert::STATUS_ACTIVE,
                'expiry_date' => $this->endDateForLevel($level),
            ]);

            return $order;
        });

        return redirect()->route('seller.private.thank-you', $order);
    }

    public function privateThankYou(MembershipOrder $order)
    {
        $user = auth()->user();

        abort_unless($order->user_id === $user->id, 403);
        abort_unless($order->level?->seller_type === MembershipLevel::SELLER_TYPE_PRIVATE, 404);

        $order->load(['level', 'user']);

        return view('seller.private-thank-you', compact('order'));
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
            ->where('status', 'paid')
            ->whereHas('level', fn($q) => $q->where('seller_type', MembershipLevel::SELLER_TYPE_PRIVATE))
            ->exists();
    }

    private function endDateForLevel(MembershipLevel $level): ?string
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

    private function feeLabel(MembershipLevel $level): string
    {
        if ($level->has_recurring && $level->billing_amount !== null && $level->billing_period) {
            return '£' . number_format((float) $level->billing_amount, 2) . ' per ' . $level->billing_period;
        }

        if ((float) $level->initial_payment > 0) {
            return '£' . number_format((float) $level->initial_payment, 2);
        }

        return '--';
    }
}
