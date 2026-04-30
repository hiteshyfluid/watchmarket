<?php

namespace App\Http\Controllers;

use App\Models\MembershipOrder;
use App\Models\SiteSetting;
use App\Support\SimplePdf;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function download(MembershipOrder $order): Response
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || (int) $order->user_id === (int) $user->id), 403);

        $order->loadMissing(['level', 'user']);

        $companyName = SiteSetting::getValue('invoice_company_name', 'WatchMarket');
        $registeredAddress = SiteSetting::getValue('invoice_registered_address', '--');
        $billingDetails = trim((string) ($order->billing_details ?: $this->fallbackBilling($order)));
        $invoiceDate = $order->ordered_at?->format('F j, Y') ?? $order->created_at?->format('F j, Y') ?? now()->format('F j, Y');
        $item = trim(($order->level?->name ?? 'Membership Package') . ' - ' . $this->singleLine($order->level?->description ?: ''));
        $total = number_format((float) $order->total, 2);

        $pdf = $this->buildStyledInvoicePdf(
            companyName: $companyName,
            registeredAddress: $registeredAddress,
            order: $order,
            invoiceDate: $invoiceDate,
            billingDetails: $billingDetails,
            item: $item,
            total: $total
        );
        $fileName = 'invoice-' . ($order->code ?: $order->id) . '.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    private function fallbackBilling(MembershipOrder $order): string
    {
        if (!$order->user) {
            return '';
        }

        $parts = array_filter([
            trim($order->user->name),
            $order->user->address,
            trim(collect([$order->user->city, $order->user->postal_code])->filter()->implode(' ')),
            $order->user->country,
            $order->user->phone,
        ]);

        return implode("\n", $parts);
    }

    private function singleLine(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags($value)) ?: '');
    }

    private function buildStyledInvoicePdf(
        string $companyName,
        string $registeredAddress,
        MembershipOrder $order,
        string $invoiceDate,
        string $billingDetails,
        string $item,
        string $total
    ): string {
        $pdf = new SimplePdf();
        $left = 45.0;
        $right = 550.0;
        $split = 465.0;
        $y = 790.0;

        $pdf->text($left, $y, $companyName, 36, true);
        $y -= 42;

        $pdf->text($left, $y, 'Registered Address:', 15, true);
        foreach (explode("\n", wordwrap($registeredAddress, 36, "\n", true)) as $line) {
            $pdf->text(260, $y, $line, 15, false);
            $y -= 18;
        }

        $y -= 20;
        $pdf->text($left, $y, 'Invoice #: ' . ($order->code ?: ('ORDER-' . $order->id)), 15, true);
        $y -= 22;
        $pdf->text($left, $y, 'Date: ' . $invoiceDate, 15, true);
        $y -= 22;
        $pdf->text($left, $y, 'Payment Method: ' . ($order->gateway ?: 'Manual Checkout'), 15, true);

        $y -= 46;
        $pdf->text($left, $y, 'Billing Details', 22, true);
        $y -= 30;

        foreach (explode("\n", $billingDetails !== '' ? $billingDetails : '--') as $line) {
            $pdf->text($left, $y, trim($line), 15, false);
            $y -= 18;
        }

        $y -= 16;
        $headerY = $y;
        $pdf->fillRect($left - 2, $headerY - 14, $right - $left + 4, 26, 0.92);
        $pdf->text($left + 4, $headerY, 'Item', 16, true);
        $pdf->text($right - 4, $headerY, 'Price', 16, true, 'right');
        $pdf->line($split, $headerY - 14, $split, $headerY + 12, 0.8, 0.86);

        $row1Y = $headerY - 40;
        $pdf->text($left + 8, $row1Y, $item, 15, false);
        $pdf->text($right - 8, $row1Y, '£' . $total, 15, false, 'right');
        $pdf->line($left, $row1Y - 16, $right, $row1Y - 16);

        $row2Y = $row1Y - 46;
        $pdf->text($left + 8, $row2Y, 'Subtotal', 15, true);
        $pdf->text($right - 8, $row2Y, '£' . $total, 15, false, 'right');
        $pdf->line($left, $row2Y - 16, $right, $row2Y - 16);

        $row3Y = $row2Y - 48;
        $pdf->text($left + 8, $row3Y, 'Total', 18, true);
        $pdf->text($right - 8, $row3Y - 6, '£' . $total, 18, true, 'right');
        $pdf->line($left, $row3Y - 30, $right, $row3Y - 30);

        return $pdf->render();
    }
}
