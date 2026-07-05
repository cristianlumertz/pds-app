<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PagarmeWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $event = (string) ($request->input('type') ?? $request->input('event'));

        if ($event !== 'order.paid') {
            return response()->json(['status' => 'ignored']);
        }

        $paymentLinkId = $this->paymentLinkIdFromPayload($request->all());

        if (! $paymentLinkId) {
            return response()->json(['message' => 'Payment link id not found.'], 422);
        }

        $order = Order::query()
            ->where('pagarme_payment_link_id', $paymentLinkId)
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $order->forceFill([
            'payment_status' => Order::PAYMENT_STATUS_PAID,
            'status' => Order::STATUS_PROCESSING,
        ])->save();

        return response()->json(['status' => 'ok']);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function paymentLinkIdFromPayload(array $payload): ?string
    {
        $paymentLinkId = Arr::get($payload, 'data.payment_link.id')
            ?? Arr::get($payload, 'data.payment_link_id');

        return is_scalar($paymentLinkId) ? (string) $paymentLinkId : null;
    }
}
