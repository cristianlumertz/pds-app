<?php

namespace App\Services;

use App\Exceptions\PagarmePaymentException;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PagarmeService
{
    private const USER_AGENT = 'pagarme-skill-generated/1.0';

    /**
     * @return array<string, mixed>
     *
     * @throws PagarmePaymentException
     */
    public function createPaymentLink(Order $order): array
    {
        $secretKey = config('services.pagarme.secret_key');
        $baseUrl = config('services.pagarme.base_url', 'https://sdx-api.pagar.me/core/v5');

        if (! is_string($secretKey) || trim($secretKey) === '') {
            throw new PagarmePaymentException('Chave secreta da Pagar.me não configurada.');
        }

        $secretKey = trim($secretKey);

        if (! str_starts_with($secretKey, 'sk_')) {
            throw new PagarmePaymentException(
                'PAGARME_SECRET_KEY deve ser uma secret key da Pagar.me iniciada por sk_. A chave pública pk_ não autentica no endpoint /paymentlinks.',
                0,
                null,
                ['configured_key_prefix' => substr($secretKey, 0, 7)]
            );
        }

        if (! is_string($baseUrl) || trim($baseUrl) === '') {
            throw new PagarmePaymentException('URL base da Pagar.me não configurada.');
        }

        $baseUrl = trim($baseUrl);
        $endpoint = rtrim($baseUrl, '/').'/paymentlinks';
        $payload = $this->buildPayload($order);

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->withHeaders([
                    'User-Agent' => self::USER_AGENT,
                ])
                ->timeout(15)
                ->post($endpoint, $payload)
                ->throw();
        } catch (ConnectionException $exception) {
            throw new PagarmePaymentException(
                'Não foi possível conectar à Pagar.me para criar o link de pagamento.',
                0,
                $exception,
                $this->exceptionContext($endpoint, $payload)
            );
        } catch (RequestException $exception) {
            throw new PagarmePaymentException(
                'A Pagar.me recusou a criação do link de pagamento: '.$this->responseMessage($exception),
                0,
                $exception,
                $this->exceptionContext($endpoint, $payload, $exception)
            );
        } catch (Throwable $exception) {
            throw new PagarmePaymentException(
                'Erro inesperado ao criar link de pagamento na Pagar.me.',
                0,
                $exception,
                $this->exceptionContext($endpoint, $payload)
            );
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new PagarmePaymentException('Resposta inválida da Pagar.me ao criar link de pagamento.');
        }

        Log::info('Link de pagamento Pagar.me criado.', array_merge([
            'order_id' => $order->id,
            'endpoint' => $endpoint,
            'status_code' => $response->status(),
            'pagarme_payment_link_id' => is_scalar($data['id'] ?? null) ? (string) $data['id'] : null,
        ], $this->payloadDiagnostics($payload)));

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(Order $order): array
    {
        $successUrl = $this->resolveSuccessUrl($order);
        $hasDiscount = (float) $order->discount_amount > 0;
        $totalAmountInCents = $this->toCents((float) $order->total_amount);
        $paymentSettings = [
            'accepted_payment_methods' => $this->acceptedPaymentMethods(),
            'pix_settings' => [
                'expires_in' => 3600,
            ],
            'boleto_settings' => [
                'due_in' => 3,
                'instructions' => 'Pedido Construcerto. Pague até o vencimento.',
            ],
        ];

        if (in_array('credit_card', $paymentSettings['accepted_payment_methods'], true)) {
            $paymentSettings['credit_card_settings'] = [
                'operation_type' => 'auth_and_capture',
                'installments' => [
                    [
                        'number' => 1,
                        'total' => $totalAmountInCents,
                    ],
                ],
            ];
        }

        $payload = [
            'name' => substr('Pedido #'.$order->id.' - Construcerto', 0, 64),
            'order_code' => (string) $order->id,
            'max_paid_sessions' => 1,
            'type' => 'order',
            'payment_settings' => $paymentSettings,
            'cart_settings' => [
                'shipping_cost' => $hasDiscount ? 0 : $this->toCents((float) $order->shipping_amount),
                'items' => $this->buildItems($order),
            ],
            'customer_settings' => [
                'customer' => $this->buildCustomer($order),
            ],
        ];

        if ($successUrl !== null) {
            $payload['flow_settings'] = [
                'success_url' => $successUrl,
            ];
        }

        return $payload;
    }

    /**
     * @return list<string>
     */
    private function acceptedPaymentMethods(): array
    {
        $methods = ['pix', 'boleto'];

        if ((bool) config('services.pagarme.enable_credit_card', true)) {
            array_unshift($methods, 'credit_card');
        }

        return $methods;
    }

    /**
     * @return list<array{name: string, amount: int, description: string, default_quantity: int}>
     */
    private function buildItems(Order $order): array
    {
        $order->loadMissing('items.product');

        if ((float) $order->discount_amount > 0) {
            return [[
                'name' => 'Pedido #'.$order->id,
                'amount' => $this->toCents((float) $order->total_amount),
                'description' => 'Produtos, frete e descontos conforme pedido.',
                'default_quantity' => 1,
            ]];
        }

        $items = $order->items
            ->values()
            ->map(fn (OrderItem $item): array => [
                'name' => $item->product_name ?: ($item->product?->name ?? 'Produto #'.$item->product_id),
                'amount' => $this->toCents((float) $item->price),
                'description' => $item->product_sku
                    ? 'SKU: '.$item->product_sku
                    : ($item->product?->description ?: ($item->product?->name ?? 'Item do pedido #'.$order->id)),
                'default_quantity' => max(1, (int) $item->quantity),
            ])
            ->all();

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCustomer(Order $order): array
    {
        $order->loadMissing('user');
        $user = $order->user;
        $document = preg_replace('/\D/', '', (string) ($user?->cpf ?? ''));
        $phone = preg_replace('/\D/', '', (string) ($user?->phone ?? ''));

        $customer = [
            'name' => substr($user?->name ?: 'Cliente Construcerto', 0, 64),
            'email' => substr($user?->email ?: 'cliente@construcerto.local', 0, 64),
        ];

        if ($document !== '') {
            $customer['document'] = $document;
            $customer['document_type'] = strlen($document) > 11 ? 'CNPJ' : 'CPF';
            $customer['type'] = strlen($document) > 11 ? 'company' : 'individual';
        }

        if (strlen($phone) >= 10) {
            $customer['phones'] = [
                'mobile_phone' => [
                    'country_code' => '55',
                    'area_code' => substr($phone, 0, 2),
                    'number' => substr($phone, 2),
                ],
            ];
        }

        return $customer;
    }

    private function toCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    private function resolveSuccessUrl(Order $order): ?string
    {
        $configuredUrl = config('services.pagarme.success_url');

        if (is_string($configuredUrl) && trim($configuredUrl) !== '') {
            return $this->publicCheckoutUrl($this->formatOrderUrl($configuredUrl, $order));
        }

        $appUrl = config('app.url');

        if (! is_string($appUrl) || trim($appUrl) === '') {
            return null;
        }

        return $this->publicCheckoutUrl(rtrim(trim($appUrl), '/').'/checkout/sucesso/'.$order->id);
    }

    private function formatOrderUrl(string $url, Order $order): string
    {
        return str_replace(
            ['{order_id}', '{order}'],
            [(string) $order->id, (string) $order->id],
            trim($url)
        );
    }

    private function publicCheckoutUrl(string $url): ?string
    {
        $parts = parse_url($url);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($scheme !== 'https' || $host === '' || $this->isLocalHost($host)) {
            return null;
        }

        return $url;
    }

    private function isLocalHost(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            || str_starts_with($host, '127.')
            || str_ends_with($host, '.local');
    }

    private function responseMessage(RequestException $exception): string
    {
        $response = $exception->response;
        $body = $response->json();

        if (is_array($body)) {
            foreach (['message', 'error', 'detail'] as $key) {
                if (isset($body[$key]) && is_string($body[$key])) {
                    return $body[$key];
                }
            }
        }

        return 'HTTP '.$response->status();
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function exceptionContext(string $endpoint, array $payload, ?RequestException $exception = null): array
    {
        $response = $exception?->response;

        return array_merge([
            'endpoint' => $endpoint,
            'status_code' => $response?->status(),
            'response_body' => $this->safeResponseBody($response?->json() ?? $response?->body()),
            'request_payload' => $this->sanitizePayloadForLog($payload),
        ], $this->payloadDiagnostics($payload));
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function payloadDiagnostics(array $payload): array
    {
        return [
            'environment' => app()->environment(),
            'app_url' => config('app.url'),
            'success_url_sent' => isset($payload['flow_settings']['success_url']),
            'success_url' => $payload['flow_settings']['success_url'] ?? null,
            'configured_success_url' => $this->configuredString('services.pagarme.success_url'),
            'configured_cancel_url' => $this->configuredString('services.pagarme.cancel_url'),
            'credit_card_settings_sent' => isset($payload['payment_settings']['credit_card_settings']),
            'credit_card_installments_sent' => isset($payload['payment_settings']['credit_card_settings']['installments']),
        ];
    }

    private function configuredString(string $key): ?string
    {
        $value = config($key);

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function sanitizePayloadForLog(array $payload): array
    {
        if (isset($payload['customer_settings']['customer']) && is_array($payload['customer_settings']['customer'])) {
            foreach (['email', 'document'] as $key) {
                if (isset($payload['customer_settings']['customer'][$key])) {
                    $payload['customer_settings']['customer'][$key] = '[redacted]';
                }
            }

            if (isset($payload['customer_settings']['customer']['phones'])) {
                $payload['customer_settings']['customer']['phones'] = '[redacted]';
            }
        }

        return $payload;
    }

    private function safeResponseBody(mixed $body): mixed
    {
        if (is_string($body)) {
            return substr($body, 0, 2000);
        }

        return $body;
    }
}
