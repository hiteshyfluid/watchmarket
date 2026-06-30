<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceModeController extends Controller
{
    public function edit(): View
    {
        $enabled  = SiteSetting::getValue('maintenance_mode', '0') === '1';
        $password = SiteSetting::getValue('maintenance_password', 'Watch2026');

        return view('admin.maintenance.edit', compact('enabled', 'password'));
    }

    public function update(Request $request): RedirectResponse
    {
        $enable = $request->boolean('enabled');

        SiteSetting::setValue('maintenance_mode', $enable ? '1' : '0');

        return redirect()->route('admin.maintenance.edit')
            ->with('success', 'Maintenance mode has been ' . ($enable ? 'enabled' : 'disabled') . '.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        SiteSetting::setValue('maintenance_password', $request->input('password'));

        return redirect()->route('admin.maintenance.edit')
            ->with('success', 'Access password updated successfully.');
    }
}
