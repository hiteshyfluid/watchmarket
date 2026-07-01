<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactSettingController extends Controller
{
    public function edit(): View
    {
        $emails = SiteSetting::getValue('contact_recipient_emails', 'support@watchmarket.co.uk, andrew@watchmarket.co.uk, henry@watchmarket.co.uk, wp@fluidlabs.co.uk');

        return view('admin.contact-settings.edit', compact('emails'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'emails' => 'required|string',
        ]);

        $addresses = collect(preg_split('/[,\r\n]+/', $validated['emails']))
            ->map(fn ($email) => trim($email))
            ->filter()
            ->unique()
            ->values();

        if ($addresses->isEmpty()) {
            return back()->withErrors(['emails' => 'Please add at least one recipient email address.'])->withInput();
        }

        foreach ($addresses as $address) {
            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                return back()->withErrors(['emails' => "\"{$address}\" is not a valid email address."])->withInput();
            }
        }

        SiteSetting::setValue('contact_recipient_emails', $addresses->implode(', '));

        return redirect()->route('admin.contact-settings.edit')
            ->with('success', 'Contact inquiry recipient emails updated successfully.');
    }
}
