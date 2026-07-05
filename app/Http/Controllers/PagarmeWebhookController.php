<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PagarmeWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();
        $eventType = $this->eventTypeFromPayload($payload);
        $pagarmeEventId = $this->eventIdFromPayload($payload);

        $this->validateSignatureIfConfigured($request);

        Log::info('Webhook Pagar.me recebido.', [
            'event_type' => $eventType,
            'pagarme_event_id' => $pagarmeEventId,
        ]);

        if ($eventType === '') {
            return response()->json(['message' => 'Event type not found.'], 422);
        }

        if ($pagarmeEventId && $this->alreadyProcessed($pagarmeEventId)) {
            Log::info('Webhook Pagar.me duplicado ignorado.', [
                'event_type' => $eventType,
                'pagarme_event_id' => $pagarmeEventId,
            ]);

            return response()->json(['status' => 'ok', 'duplicate' => true]);
        }

        $status = $this->statusFromPayload($payload, $eventType);

        return DB::transaction(function () use ($payload, $eventType, $pagarmeEventId, $status): JsonResponse {
            [$order, $payment] = $this->locateOrderAndPayment($payload);

            if ($order && ! $payment) {
                $payment = $this->ensurePaymentForOrder($order, $payload);
            }

            if ($payment && ! $order) {
                $order = $payment->order;
            }

            $event = PaymentEvent::query()->create([
                'payment_id' => $payment?->id,
                'order_id' => $order?->id,
                'pagarme_event_id' => $pagarmeEventId,
                'event_type' => $eventType,
                'payload' => $payload,
            ]);

            if (! $order && ! $payment) {
                Log::warning('Webhook Pagar.me sem pedido/pagamento localizado.', [
                    'event_type' => $eventType,
                    'pagarme_event_id' => $pagarmeEventId,
                    'ids' => $this->idsFromPayload($payload),
                ]);

                return response()->json(['status' => 'received', 'processed' => false]);
            }

            if (! $status) {
                Log::info('Webhook Pagar.me registrado sem alteração de status.', [
                    'event_type' => $eventType,
                    'order_id' => $order?->id,
                    'payment_id' => $payment?->id,
                ]);

                $event->forceFill(['processed_at' => now()])->save();

                return response()->json(['status' => 'ignored']);
            }

            if ($payment) {
                $this->updatePayment($payment, $payload, $status);
            }

            if ($order) {
                $this->updateOrderPaymentStatus($order, $status);
            }

            $event->forceFill([
                'payment_id' => $payment?->id,
                'order_id' => $order?->id,
                'processed_at' => now(),
            ])->save();

            Log::info('Webhook Pagar.me processado.', [
                'event_type' => $eventType,
                'status' => $status,
                'order_id' => $order?->id,
                'payment_id' => $payment?->id,
            ]);

            return response()->json(['status' => 'ok']);
        });
    }

    private function alreadyProcessed(string $pagarmeEventId): bool
    {
        return PaymentEvent::query()
            ->where('pagarme_event_id', $pagarmeEventId)
            ->whereNotNull('processed_at')
            ->exists();
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array{0: ?Order, 1: ?Payment}
     */
    private function locateOrderAndPayment(array $payload): array
    {
        $ids = $this->idsFromPayload($payload);

        $payment = null;

        if ($ids['payment_link_id']) {
            $payment = Payment::query()
                ->where('pagarme_payment_link_id', $ids['payment_link_id'])
                ->latest('id')
                ->first();
        }

        $payment ??= $this->findPaymentByExternalId('pagarme_order_id', $ids['order_id']);
        $payment ??= $this->findPaymentByExternalId('pagarme_charge_id', $ids['charge_id']);
        $payment ??= $this->findPaymentByExternalId('pagarme_transaction_id', $ids['transaction_id']);

        $order = $payment?->order;

        if (! $order && $ids['payment_link_id']) {
            $order = Order::query()
                ->where('pagarme_payment_link_id', $ids['payment_link_id'])
                ->latest('id')
                ->first();
        }

        if (! $payment && $order) {
            $payment = $order->payments()
                ->latest('id')
                ->first();
        }

        if ($payment || $order) {
            Log::info('Webhook Pagar.me localizado.', [
                'order_id' => $order?->id,
                'payment_id' => $payment?->id,
                'ids' => $ids,
            ]);
        }

        return [$order, $payment];
    }

    private function findPaymentByExternalId(string $column, ?string $value): ?Payment
    {
        if (! $value) {
            return null;
        }

        return Payment::query()
            ->where($column, $value)
            ->latest('id')
            ->first();
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function ensurePaymentForOrder(Order $order, array $payload): Payment
    {
        $payment = $order->payments()
            ->latest('id')
            ->first();

        if ($payment) {
            return $payment;
        }

        return $order->payments()->create(array_filter([
            'payment_method' => $order->payment_method ?: 'pagarme',
            'status' => $order->payment_status ?: Payment::STATUS_PENDING,
            'amount' => $order->total_amount,
            'pagarme_payment_link_id' => $this->idsFromPayload($payload)['payment_link_id'] ?? $order->pagarme_payment_link_id,
            'pagarme_checkout_url' => $this->stringFromPayload($payload, [
                'data.payment_link.url',
                'data.payment_link.checkout_url',
                'data.checkout_url',
            ]) ?? $order->pagarme_checkout_url,
            'pagarme_order_id' => $this->idsFromPayload($payload)['order_id'],
            'pagarme_charge_id' => $this->idsFromPayload($payload)['charge_id'],
            'pagarme_transaction_id' => $this->idsFromPayload($payload)['transaction_id'],
        ], fn ($value): bool => $value !== null && $value !== ''));
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function updatePayment(Payment $payment, array $payload, string $status): void
    {
        $ids = $this->idsFromPayload($payload);
        $attributes = [
            'status' => $status,
        ];

        $this->fillIfPresent($attributes, 'pagarme_payment_link_id', $ids['payment_link_id']);
        $this->fillIfPresent($attributes, 'pagarme_order_id', $ids['order_id']);
        $this->fillIfPresent($attributes, 'pagarme_charge_id', $ids['charge_id']);
        $this->fillIfPresent($attributes, 'pagarme_transaction_id', $ids['transaction_id']);
        $this->fillIfPresent($attributes, 'pagarme_checkout_url', $this->stringFromPayload($payload, [
            'data.payment_link.url',
            'data.payment_link.checkout_url',
            'data.checkout_url',
            'data.url',
        ]));
        $this->fillIfPresent($attributes, 'boleto_url', $this->stringFromPayload($payload, [
            'data.charges.0.last_transaction.url',
            'data.charge.last_transaction.url',
            'data.last_transaction.url',
            'data.boleto.url',
        ]));
        $this->fillIfPresent($attributes, 'boleto_barcode', $this->stringFromPayload($payload, [
            'data.charges.0.last_transaction.barcode',
            'data.charge.last_transaction.barcode',
            'data.last_transaction.barcode',
            'data.boleto.barcode',
        ]));
        $this->fillIfPresent($attributes, 'pix_qr_code', $this->stringFromPayload($payload, [
            'data.charges.0.last_transaction.qr_code',
            'data.charge.last_transaction.qr_code',
            'data.last_transaction.qr_code',
            'data.pix.qr_code',
        ]));
        $this->fillIfPresent($attributes, 'pix_expires_at', $this->stringFromPayload($payload, [
            'data.charges.0.last_transaction.expires_at',
            'data.charge.last_transaction.expires_at',
            'data.last_transaction.expires_at',
            'data.pix.expires_at',
        ]));

        if ($status === Payment::STATUS_PAID) {
            $attributes['paid_at'] = $payment->paid_at ?? now();
        }

        if ($status === Payment::STATUS_CANCELLED || $status === Payment::STATUS_EXPIRED) {
            $attributes['cancelled_at'] = $payment->cancelled_at ?? now();
        }

        if ($status === Payment::STATUS_REFUNDED) {
            $attributes['refunded_at'] = $payment->refunded_at ?? now();
        }

        $payment->forceFill($attributes)->save();
    }

    private function updateOrderPaymentStatus(Order $order, string $status): void
    {
        $attributes = [
            'payment_status' => $status,
        ];

        if ($status === Payment::STATUS_PAID && (string) $order->status === Order::STATUS_PENDING) {
            $attributes['status'] = Order::STATUS_PROCESSING;
        }

        $order->forceFill($attributes)->save();
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function eventTypeFromPayload(array $payload): string
    {
        $event = Arr::get($payload, 'type')
            ?? Arr::get($payload, 'event')
            ?? Arr::get($payload, 'event_type');

        return is_scalar($event) ? (string) $event : '';
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function eventIdFromPayload(array $payload): ?string
    {
        $eventId = Arr::get($payload, 'id')
            ?? Arr::get($payload, 'event_id')
            ?? Arr::get($payload, 'pagarme_event_id');

        return is_scalar($eventId) ? (string) $eventId : null;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function statusFromPayload(array $payload, string $eventType): ?string
    {
        $rawStatus = strtolower((string) (
            Arr::get($payload, 'data.status')
            ?? Arr::get($payload, 'data.payment_status')
            ?? Arr::get($payload, 'data.charges.0.status')
            ?? Arr::get($payload, 'data.charge.status')
            ?? Arr::get($payload, 'data.last_transaction.status')
            ?? ''
        ));

        $normalizedEvent = strtolower($eventType);

        if (str_contains($normalizedEvent, 'paid') || str_contains($normalizedEvent, 'payment_approved') || in_array($rawStatus, ['paid', 'success', 'succeeded', 'approved'], true)) {
            return Payment::STATUS_PAID;
        }

        if (str_contains($normalizedEvent, 'fail') || str_contains($normalizedEvent, 'refused') || in_array($rawStatus, ['failed', 'refused', 'denied', 'not_authorized'], true)) {
            return Payment::STATUS_FAILED;
        }

        if (str_contains($normalizedEvent, 'cancel') || in_array($rawStatus, ['cancelled', 'canceled'], true)) {
            return Payment::STATUS_CANCELLED;
        }

        if (str_contains($normalizedEvent, 'expir') || $rawStatus === 'expired') {
            return Payment::STATUS_EXPIRED;
        }

        if (str_contains($normalizedEvent, 'refund') || $rawStatus === 'refunded') {
            return Payment::STATUS_REFUNDED;
        }

        if (in_array($rawStatus, ['pending', 'waiting_payment'], true)) {
            return Payment::STATUS_PENDING;
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array{payment_link_id: ?string, order_id: ?string, charge_id: ?string, transaction_id: ?string}
     */
    private function idsFromPayload(array $payload): array
    {
        return [
            'payment_link_id' => $this->paymentLinkIdFromPayload($payload),
            'order_id' => $this->stringFromPayload($payload, [
                'data.id',
                'data.order.id',
                'data.pagarme_order_id',
                'data.order_id',
            ]),
            'charge_id' => $this->stringFromPayload($payload, [
                'data.charges.0.id',
                'data.charge.id',
                'data.charge_id',
                'data.id',
            ]),
            'transaction_id' => $this->stringFromPayload($payload, [
                'data.charges.0.last_transaction.id',
                'data.charge.last_transaction.id',
                'data.last_transaction.id',
                'data.transaction.id',
                'data.transaction_id',
            ]),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function paymentLinkIdFromPayload(array $payload): ?string
    {
        $paymentLinkId = Arr::get($payload, 'data.payment_link.id')
            ?? Arr::get($payload, 'data.payment_link_id')
            ?? Arr::get($payload, 'data.paymentLink.id');

        return is_scalar($paymentLinkId) ? (string) $paymentLinkId : null;
    }

    /**
     * @param array<string, mixed> $payload
     * @param list<string> $paths
     */
    private function stringFromPayload(array $payload, array $paths): ?string
    {
        foreach ($paths as $path) {
            $value = Arr::get($payload, $path);

            if (is_scalar($value) && (string) $value !== '') {
                return (string) $value;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function fillIfPresent(array &$attributes, string $key, ?string $value): void
    {
        if ($value !== null && $value !== '') {
            $attributes[$key] = $value;
        }
    }

    private function validateSignatureIfConfigured(Request $request): void
    {
        $secret = config('services.pagarme.webhook_secret');

        if (! is_string($secret) || trim($secret) === '') {
            Log::warning('Webhook Pagar.me sem validação de assinatura: PAGARME_WEBHOOK_SECRET não configurado.');

            return;
        }

        Log::warning('PAGARME_WEBHOOK_SECRET está configurado, mas o formato de assinatura da Pagar.me não foi validado neste projeto. Evento aceito sem bloqueio.');
    }
}
