<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SuperAdminLoginController extends Controller
{
    public function create(): View
    {
        return view('auth.superadmin-login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        if (! $user->isAdmin()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['login_email' => 'Access denied. This portal is restricted to administrators only.'])
                ->withInput($request->only('login_email'));
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
}
