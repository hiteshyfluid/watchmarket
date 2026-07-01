<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'email' => 'nullable|email|max:190',
            'phone' => 'required|string|max:40',
            'title' => 'required|string|max:190',
            'message' => 'required|string|max:5000',
        ]);

        $recipients = collect(preg_split('/[,\r\n]+/', SiteSetting::getValue('contact_recipient_emails', 'support@watchmarket.co.uk, andrew@watchmarket.co.uk, henry@watchmarket.co.uk, wp@fluidlabs.co.uk')))
            ->map(fn ($email) => trim($email))
            ->filter()
            ->values()
            ->all();

        Mail::to($recipients)->send(new ContactFormMail(
            $validated['name'],
            $validated['email'] ?? null,
            $validated['phone'],
            $validated['title'],
            $validated['message'],
        ));

        return redirect()->route('contact.show')
            ->with('success', "Thanks for getting in touch! We'll get back to you shortly.");
    }
}
