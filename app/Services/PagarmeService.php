<?php

namespace App\Services;

use App\Exceptions\PagarmePaymentException;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
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

        if (! is_string($baseUrl) || trim($baseUrl) === '') {
            throw new PagarmePaymentException('URL base da Pagar.me não configurada.');
        }

        $payload = [
            'type' => 'order',
            'payment_settings' => [
                'accepted_payment_methods' => ['credit_card', 'pix', 'boleto'],
            ],
            'cart_settings' => [
                'items' => $this->buildItems($order),
            ],
        ];

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->withHeaders([
                    'User-Agent' => self::USER_AGENT,
                ])
                ->timeout(15)
                ->post(rtrim($baseUrl, '/').'/paymentlinks', $payload)
                ->throw();
        } catch (ConnectionException $exception) {
            throw new PagarmePaymentException(
                'Não foi possível conectar à Pagar.me para criar o link de pagamento.',
                0,
                $exception
            );
        } catch (RequestException $exception) {
            throw new PagarmePaymentException(
                'A Pagar.me recusou a criação do link de pagamento: '.$this->responseMessage($exception),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            throw new PagarmePaymentException(
                'Erro inesperado ao criar link de pagamento na Pagar.me.',
                0,
                $exception
            );
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new PagarmePaymentException('Resposta inválida da Pagar.me ao criar link de pagamento.');
        }

        return $data;
    }

    /**
     * @return list<array{name: string, amount: int, description: string, default_quantity: int}>
     */
    private function buildItems(Order $order): array
    {
        $order->loadMissing('items.product');

        return $order->items
            ->values()
            ->map(fn (OrderItem $item): array => [
                'name' => $item->product?->name ?? 'Produto #'.$item->product_id,
                'amount' => $this->toCents((float) $item->price),
                'description' => $item->product?->description ?: ($item->product?->name ?? 'Item do pedido #'.$order->id),
                'default_quantity' => max(1, (int) $item->quantity),
            ])
            ->all();
    }

    private function toCents(float $amount): int
    {
        return (int) round($amount * 100);
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
}
