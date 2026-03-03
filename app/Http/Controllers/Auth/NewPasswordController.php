<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        $verifiedEmail = (string) $request->session()->get('password_reset_verified_email', '');
        if ($verifiedEmail === '') {
            abort(403, 'OTP verification is required before resetting password.');
        }

        return view('auth.reset-password', ['email' => $verifiedEmail]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $sessionEmail = (string) $request->session()->get('password_reset_verified_email', '');
        if ($sessionEmail === '' || strcasecmp($sessionEmail, (string) $validated['email']) !== 0) {
            return back()->withErrors(['email' => 'OTP verification session is invalid. Please try again.']);
        }

        $otpRecord = PasswordResetOtp::query()->where('email', $sessionEmail)->first();
        if (!$otpRecord || !$otpRecord->verified_at) {
            return back()->withErrors(['email' => 'OTP not verified. Please verify OTP again.']);
        }

        if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
            return back()->withErrors(['email' => 'OTP expired. Please request a new OTP.']);
        }

        $user = User::query()->where('email', $sessionEmail)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User account not found.']);
        }

        $user->forceFill([
            'password' => Hash::make((string) $validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        $otpRecord->delete();
        $request->session()->forget([
            'password_reset_email',
            'password_reset_verified_email',
            'password_reset_verified_at',
        ]);

        return redirect()->route('login')->with('status', 'Password reset successfully. Please login.');
    }
}
