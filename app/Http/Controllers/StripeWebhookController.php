<?php

namespace App\Http\Controllers;

use App\Models\MembershipOrder;
use App\Services\MembershipPurchaseService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(
        Request $request,
        StripeCheckoutService $stripeCheckoutService,
        MembershipPurchaseService $membershipPurchaseService
    ) {
        $payload = $request->getContent();
        $signature = (string) $request->header('Stripe-Signature', '');

        try {
            $event = $stripeCheckoutService->constructWebhookEvent($payload, $signature);
        } catch (UnexpectedValueException|SignatureVerificationException $e) {
            return response('Invalid Stripe webhook payload.', 400);
        } catch (\RuntimeException $e) {
            return response($e->getMessage(), 400);
        }

        $session = $event->data->object;
        $orderId = is_object($session) ? $stripeCheckoutService->orderIdFromSession($session) : null;

        if (!$orderId) {
            return response('Webhook ignored.', 200);
        }

        $order = MembershipOrder::query()->find($orderId);

        if (!$order) {
            return response('Order not found.', 200);
        }

        if (in_array($event->type, ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true)) {
            if ($stripeCheckoutService->sessionIsReadyForFulfillment($session)) {
                $membershipPurchaseService->completeOrder(
                    $order,
                    $stripeCheckoutService->paymentDetailsFromSession($session)
                );
            }
        }

        if ($event->type === 'checkout.session.async_payment_failed') {
            $membershipPurchaseService->markOrderFailed(
                $order,
                'Stripe Checkout (' . $stripeCheckoutService->modeLabel() . ')'
            );
        }

        return response('ok');
    }
}
