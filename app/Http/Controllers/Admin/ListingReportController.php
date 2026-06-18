<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingReportController extends Controller
{
    public function index(Request $request): View
    {
        $reports = ListingReport::query()
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('admin.listing-reports.index', compact('reports'));
    }

    public function show(ListingReport $listingReport): View
    {
        return view('admin.listing-reports.show', ['report' => $listingReport]);
    }

    public function updateStatus(Request $request, ListingReport $listingReport): RedirectResponse
    {
        $request->validate(['status' => 'required|in:new,reviewed,resolved']);
        $listingReport->update(['status' => $request->status]);

        return back()->with('success', 'Report status updated.');
    }
}
