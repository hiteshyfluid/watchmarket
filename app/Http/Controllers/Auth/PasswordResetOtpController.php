<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetOtpController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('password_reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', [
            'email' => $request->session()->get('password_reset_email'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $email = (string) $request->session()->get('password_reset_email');
        if ($email === '') {
            return redirect()->route('password.request')->withErrors(['otp' => 'Session expired. Please request OTP again.']);
        }

        $record = PasswordResetOtp::query()->where('email', $email)->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'OTP record not found. Please request a new OTP.']);
        }

        if (Carbon::now()->greaterThan($record->expires_at)) {
            return back()->withErrors(['otp' => 'OTP expired. Please request a new OTP.']);
        }

        if (!Hash::check((string) $validated['otp'], $record->otp_hash)) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        $record->update(['verified_at' => Carbon::now()]);

        $request->session()->put('password_reset_verified_email', $email);
        $request->session()->put('password_reset_verified_at', Carbon::now()->timestamp);

        return redirect()->route('password.reset')
            ->with('status', 'OTP verified. Please set your new password.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = (string) $request->session()->get('password_reset_email');
        if ($email === '') {
            return redirect()->route('password.request');
        }

        $otp = (string) random_int(100000, 999999);

        PasswordResetOtp::updateOrCreate(
            ['email' => $email],
            [
                'otp_hash' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(5),
                'verified_at' => null,
            ]
        );

        Mail::to($email)->send(new PasswordResetOtpMail($otp, 5));

        return redirect()->route('password.otp.form')
            ->with('status', 'A new OTP has been sent to your email. It is valid for 5 minutes.');
    }
}

