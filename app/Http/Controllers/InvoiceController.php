<?php

namespace App\Http\Controllers;

use App\Models\MembershipOrder;
use App\Services\InvoicePdfService;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoicePdfService $invoicePdfService,
    ) {
    }

    public function download(MembershipOrder $order): Response
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || (int) $order->user_id === (int) $user->id), 403);

        $pdf = $this->invoicePdfService->render($order);
        $fileName = $this->invoicePdfService->fileName($order);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
