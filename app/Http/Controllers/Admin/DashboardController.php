<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_sellers' => User::whereIn('role', [User::ROLE_PRIVATE_SELLER, User::ROLE_TRADE_SELLER])->count(),
            'total_adverts' => Advert::count(),
            'active_adverts' => Advert::where('status', Advert::STATUS_ACTIVE)->count(),
            'sold_adverts' => Advert::where('status', Advert::STATUS_SOLD)->count(),
            'total_brands' => Brand::parents()->count(),
        ];

        $recentAdverts = Advert::with(['user', 'brand'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentAdverts'));
    }
}
