<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     * Works without requiring the user to be logged in.
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Validate the hash matches the user's email
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // Validate the signed URL (checks expiry & signature)
        if (!$request->hasValidSignature()) {
            return redirect()->route('login')->with('status', 'Verification link has expired. Please request a new one.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Your email is already verified. You can log in.');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect()->route('login')->with('status', 'Email verified successfully! You can now log in.');
    }
}
