<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\Brand;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredAdverts = Advert::with(['user', 'brand', 'model', 'year', 'condition', 'paper', 'box'])
            ->where('status', Advert::STATUS_ACTIVE)
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();

        $recentAdverts = Advert::with(['user', 'brand', 'model', 'year', 'condition', 'paper', 'box'])
            ->where('status', Advert::STATUS_ACTIVE)
            ->latest()
            ->take(8)
            ->get();

        $featuredBrands = Brand::query()
            ->parents()
            ->active()
            ->featured()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->take(8)
            ->get();

        return view('welcome', compact('featuredAdverts', 'recentAdverts', 'featuredBrands'));
    }
}
