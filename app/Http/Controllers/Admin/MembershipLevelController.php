<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MembershipLevelController extends Controller
{
    public function index()
    {
        $levels = MembershipLevel::latest('id')->paginate(25);

        return view('admin.memberships.levels.index', compact('levels'));
    }

    public function create()
    {
        return view('admin.memberships.levels.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        MembershipLevel::create($validated);

        return redirect()->route('admin.membership-levels.index')
            ->with('success', 'Membership level created.');
    }

    public function edit(MembershipLevel $membership_level)
    {
        return view('admin.memberships.levels.edit', ['level' => $membership_level]);
    }

    public function update(Request $request, MembershipLevel $membership_level)
    {
        $validated = $this->validateData($request);

        $membership_level->update($validated);

        return redirect()->route('admin.membership-levels.index')
            ->with('success', 'Membership level updated.');
    }

    public function destroy(MembershipLevel $membership_level)
    {
        $membership_level->delete();

        return redirect()->route('admin.membership-levels.index')
            ->with('success', 'Membership level deleted.');
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'seller_type' => 'required|in:both,private_seller,trade_seller',
            'private_min_advert_price' => 'nullable|numeric|min:0|required_if:seller_type,private_seller',
            'private_max_advert_price' => 'nullable|numeric|min:0|required_if:seller_type,private_seller',
            'trade_max_advert_count' => 'nullable|integer|min:-1|not_in:0|required_if:seller_type,trade_seller',
            'description' => 'nullable|string',
            'confirmation_message' => 'nullable|string',
            'initial_payment' => 'required|numeric|min:0',
            'billing_amount' => 'nullable|numeric|min:0',
            'billing_every' => 'nullable|integer|min:1',
            'billing_period' => 'nullable|in:day,week,month,year',
            'billing_cycle_limit' => 'nullable|integer|min:0',
            'trial_amount' => 'nullable|numeric|min:0',
            'trial_cycles' => 'nullable|integer|min:0',
            'expiration_number' => 'nullable|integer|min:1',
            'expiration_unit' => 'nullable|in:day,week,month,year',
        ]);

        $hasRecurring = $request->boolean('has_recurring');
        $hasTrial = $request->boolean('has_trial');
        $hasExpiration = $request->boolean('has_expiration');

        $validated['has_recurring'] = $hasRecurring;
        $validated['has_trial'] = $hasTrial;
        $validated['has_expiration'] = $hasExpiration;
        $validated['allow_signups'] = $request->boolean('allow_signups', true);
        $validated['is_active'] = $request->boolean('is_active', true);

        if (!$hasRecurring) {
            $validated['billing_amount'] = null;
            $validated['billing_every'] = null;
            $validated['billing_period'] = null;
            $validated['billing_cycle_limit'] = 0;
        } else {
            $validated['billing_cycle_limit'] = $validated['billing_cycle_limit'] ?? 0;
        }

        if (!$hasTrial) {
            $validated['trial_amount'] = 0;
            $validated['trial_cycles'] = 0;
        } else {
            $validated['trial_amount'] = $validated['trial_amount'] ?? 0;
            $validated['trial_cycles'] = $validated['trial_cycles'] ?? 0;
        }

        if (!$hasExpiration) {
            $validated['expiration_number'] = null;
            $validated['expiration_unit'] = null;
        }

        if ($validated['seller_type'] !== 'private_seller') {
            $validated['private_min_advert_price'] = null;
            $validated['private_max_advert_price'] = null;
        } elseif (
            isset($validated['private_min_advert_price'], $validated['private_max_advert_price']) &&
            (float) $validated['private_max_advert_price'] < (float) $validated['private_min_advert_price']
        ) {
            throw ValidationException::withMessages([
                'private_max_advert_price' => 'Maximum advert price must be greater than or equal to minimum advert price.',
            ]);
        }

        if ($validated['seller_type'] !== 'trade_seller') {
            $validated['trade_max_advert_count'] = null;
        }

        return $validated;
    }
}
