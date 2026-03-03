<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipLevel;
use App\Models\MembershipSubscription;
use Illuminate\Http\Request;

class MembershipMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = MembershipSubscription::with(['user', 'level'])
            ->latest('id');

        if ($request->filled('level_id')) {
            $query->where('membership_level_id', $request->integer('level_id'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('user', function ($user) use ($q) {
                    $user->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                })->orWhereHas('level', fn($level) => $level->where('name', 'like', "%{$q}%"));
            });
        }

        $members = $query->paginate(20)->withQueryString();
        $levels = MembershipLevel::orderBy('name')->get(['id', 'name']);

        return view('admin.memberships.members.index', compact('members', 'levels'));
    }
}

