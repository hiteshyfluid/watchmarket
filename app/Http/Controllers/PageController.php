<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function terms(): View
    {
        $page = Page::where('slug', Page::SLUG_TERMS)->firstOrFail();

        return view('pages.show', compact('page'));
    }

    public function privacy(): View
    {
        $page = Page::where('slug', Page::SLUG_PRIVACY)->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
