<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\MembershipOrder;
use App\Models\MembershipSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    /**
     * Display the user's account dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = (string) $request->query('tab', 'dashboard');
        $listingFilter = (string) $request->query('listing', 'all');

        $statusCounts = [
            'all' => (int) $user->adverts()->count(),
            'active' => (int) $user->adverts()->where('status', Advert::STATUS_ACTIVE)->count(),
            'sold' => (int) $user->adverts()->where('status', Advert::STATUS_SOLD)->count(),
            'expired' => (int) $user->adverts()->where('status', Advert::STATUS_EXPIRED)->count(),
        ];

        $listingsQuery = $user->adverts()
            ->with(['brand', 'model'])
            ->latest();

        if ($listingFilter === 'active') {
            $listingsQuery->where('status', Advert::STATUS_ACTIVE);
        } elseif ($listingFilter === 'sold') {
            $listingsQuery->where('status', Advert::STATUS_SOLD);
        } elseif ($listingFilter === 'expired') {
            $listingsQuery->where('status', Advert::STATUS_EXPIRED);
        }

        $listings = $listingsQuery->paginate(10)->withQueryString();

        $stats = [
            'views' => 0,
            'enquiries' => 0,
            'active' => $statusCounts['active'],
            'sold' => $statusCounts['sold'],
        ];

        $subscriptions = MembershipSubscription::query()
            ->with('level')
            ->where('user_id', $user->id)
            ->latest('id')
            ->get();

        $orders = MembershipOrder::query()
            ->with('level')
            ->where('user_id', $user->id)
            ->latest('ordered_at')
            ->latest('id')
            ->paginate(10, ['*'], 'invoice_page')
            ->withQueryString();

        return view('my-account', compact(
            'user',
            'tab',
            'listings',
            'listingFilter',
            'statusCounts',
            'stats',
            'subscriptions',
            'orders'
        ));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:255'],
        ]);

        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            $validated['email_verified_at'] = null;
        }

        $user->update($validated);

        return redirect()->route('my-account', ['tab' => 'edit-profile'])
            ->with('success', 'Profile updated successfully.');
    }
}
