<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MembershipSettingController extends Controller
{
    public function edit()
    {
        $settings = [
            'invoice_company_name' => SiteSetting::getValue('invoice_company_name', 'WatchMarket'),
            'invoice_vat_number' => SiteSetting::getValue('invoice_vat_number', ''),
            'invoice_registered_address' => SiteSetting::getValue('invoice_registered_address', ''),
            'invoice_vat_rate' => SiteSetting::getValue('invoice_vat_rate', '20'),
            'invoice_logo_path' => SiteSetting::getValue('invoice_logo_path', ''),
            'stripe_mode' => SiteSetting::getValue('stripe_mode', (string) config('services.stripe.mode', 'test')),
            'stripe_test_publishable_key' => SiteSetting::getValue('stripe_test_publishable_key', (string) config('services.stripe.test_publishable_key', '')),
            'stripe_test_secret_key' => SiteSetting::getValue('stripe_test_secret_key', (string) config('services.stripe.test_secret_key', '')),
            'stripe_test_webhook_secret' => SiteSetting::getValue('stripe_test_webhook_secret', (string) config('services.stripe.test_webhook_secret', '')),
            'stripe_live_publishable_key' => SiteSetting::getValue('stripe_live_publishable_key', (string) config('services.stripe.live_publishable_key', '')),
            'stripe_live_secret_key' => SiteSetting::getValue('stripe_live_secret_key', (string) config('services.stripe.live_secret_key', '')),
            'stripe_live_webhook_secret' => SiteSetting::getValue('stripe_live_webhook_secret', (string) config('services.stripe.live_webhook_secret', '')),
        ];

        $webhookUrl = route('stripe.webhook');

        return view('admin.memberships.settings.edit', compact('settings', 'webhookUrl'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'invoice_company_name' => 'required|string|max:190',
            'invoice_vat_number' => 'nullable|string|max:120',
            'invoice_registered_address' => 'nullable|string|max:2000',
            'invoice_vat_rate' => 'required|numeric|min:0|max:100',
            'invoice_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_logo' => 'nullable|boolean',
            'stripe_mode' => 'required|in:test,live',
            'stripe_test_publishable_key' => 'nullable|string|max:255',
            'stripe_test_secret_key' => 'nullable|string|max:255',
            'stripe_test_webhook_secret' => 'nullable|string|max:255',
            'stripe_live_publishable_key' => 'nullable|string|max:255',
            'stripe_live_secret_key' => 'nullable|string|max:255',
            'stripe_live_webhook_secret' => 'nullable|string|max:255',
        ]);

        SiteSetting::setValue('invoice_company_name', $validated['invoice_company_name']);
        SiteSetting::setValue('invoice_vat_number', $validated['invoice_vat_number'] ?? '');
        SiteSetting::setValue('invoice_registered_address', $validated['invoice_registered_address'] ?? '');
        SiteSetting::setValue('invoice_vat_rate', (string) $validated['invoice_vat_rate']);
        SiteSetting::setValue('stripe_mode', $validated['stripe_mode']);
        SiteSetting::setValue('stripe_test_publishable_key', trim((string) ($validated['stripe_test_publishable_key'] ?? '')));
        SiteSetting::setValue('stripe_test_secret_key', trim((string) ($validated['stripe_test_secret_key'] ?? '')));
        SiteSetting::setValue('stripe_test_webhook_secret', trim((string) ($validated['stripe_test_webhook_secret'] ?? '')));
        SiteSetting::setValue('stripe_live_publishable_key', trim((string) ($validated['stripe_live_publishable_key'] ?? '')));
        SiteSetting::setValue('stripe_live_secret_key', trim((string) ($validated['stripe_live_secret_key'] ?? '')));
        SiteSetting::setValue('stripe_live_webhook_secret', trim((string) ($validated['stripe_live_webhook_secret'] ?? '')));

        $currentLogoPath = SiteSetting::getValue('invoice_logo_path', '');

        if ($request->boolean('remove_logo') && $currentLogoPath) {
            $this->deleteImagePath($currentLogoPath);
            SiteSetting::setValue('invoice_logo_path', '');
            $currentLogoPath = '';
        }

        if ($request->hasFile('invoice_logo')) {
            if ($currentLogoPath) {
                $this->deleteImagePath($currentLogoPath);
            }
            $path = $this->storeImageInPublic($request->file('invoice_logo'), 'settings');
            SiteSetting::setValue('invoice_logo_path', $path);
        }

        return redirect()->route('admin.membership-settings.edit')
            ->with('success', 'Membership billing and Stripe settings updated.');
    }

    private function storeImageInPublic(UploadedFile $file, string $folder): string
    {
        $folder = trim($folder, '/');
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = uniqid('', true) . '.' . ltrim($ext, '.');
        $relative = 'images/' . $folder . '/' . $filename;
        $fullPath = public_path($relative);
        if (!is_dir(dirname($fullPath))) {
            @mkdir(dirname($fullPath), 0777, true);
        }
        $file->move(dirname($fullPath), basename($fullPath));

        return $relative;
    }

    private function deleteImagePath(?string $path): void
    {
        if (!$path) {
            return;
        }

        $normalized = ltrim($path, '/');
        if (str_starts_with($normalized, 'images/')) {
            $full = public_path($normalized);
            if (is_file($full)) {
                @unlink($full);
            }
            return;
        }

        Storage::disk('public')->delete($normalized);
    }
}
