<?php

namespace App\Http\Controllers;

use App\Mail\ListingReportMail;
use App\Models\ListingReport;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ListingReportController extends Controller
{
    public function show(): View
    {
        return view('report-listing');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'listing_url'    => 'nullable|string|max:500',
            'issue_type'     => 'required|string|in:counterfeit,stolen,fraudulent,misrepresented,scam_seller,other',
            'description'    => 'required|string|max:5000',
            'serial_number'  => 'nullable|string|max:100',
            'reporter_name'  => 'nullable|string|max:190',
            'reporter_email' => 'required|email|max:190',
        ]);

        $report = ListingReport::create($validated);

        $recipients = collect(preg_split('/[,\r\n]+/', SiteSetting::getValue('contact_recipient_emails', 'support@watchmarket.co.uk, andrew@watchmarket.co.uk, henry@watchmarket.co.uk, wp@fluidlabs.co.uk')))
            ->map(fn ($e) => trim($e))
            ->filter()
            ->values()
            ->all();

        Mail::to($recipients)->send(new ListingReportMail($report));

        return redirect()->route('report-listing.show')
            ->with('success', 'Your report has been submitted. Thank you for helping keep WatchMarket safe.');
    }
}
