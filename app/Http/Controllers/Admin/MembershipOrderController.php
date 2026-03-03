<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipLevel;
use App\Models\MembershipOrder;
use Illuminate\Http\Request;

class MembershipOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = MembershipOrder::with(['user', 'level'])
            ->latest('ordered_at')
            ->latest('id');

        if ($request->filled('level_id')) {
            $query->where('membership_level_id', $request->integer('level_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($order) use ($q) {
                $order->where('code', 'like', "%{$q}%")
                    ->orWhere('payment_transaction_id', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($user) use ($q) {
                        $user->where('first_name', 'like', "%{$q}%")
                            ->orWhere('last_name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $orders = $query->paginate(20)->withQueryString();
        $levels = MembershipLevel::orderBy('name')->get(['id', 'name']);
        $statuses = MembershipOrder::query()
            ->select('status')
            ->distinct()
            ->whereNotNull('status')
            ->orderBy('status')
            ->pluck('status');

        return view('admin.memberships.orders.index', compact('orders', 'levels', 'statuses'));
    }
}

