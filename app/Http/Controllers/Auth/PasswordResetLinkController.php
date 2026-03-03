<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim((string) $validated['email']));
        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'We could not find an account with that email address.']);
        }

        $otp = (string) random_int(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(5);

        PasswordResetOtp::updateOrCreate(
            ['email' => $email],
            [
                'otp_hash' => Hash::make($otp),
                'expires_at' => $expiresAt,
                'verified_at' => null,
            ]
        );

        Mail::to($email)->send(new PasswordResetOtpMail($otp, 5));

        $request->session()->put('password_reset_email', $email);
        $request->session()->forget(['password_reset_verified_email', 'password_reset_verified_at']);

        return redirect()->route('password.otp.form')
            ->with('status', 'OTP sent to your email. It is valid for 5 minutes.');
    }
}
