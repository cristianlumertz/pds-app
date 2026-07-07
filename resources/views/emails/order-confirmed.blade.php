@component('mail::message')
# Olá, {{ $order->user->name }}!

Seu pedido **#{{ $order->id }}** foi confirmado em **{{ $order->created_at->format('d/m/Y H:i') }}**.

@component('mail::panel')
**Número do pedido:** #{{ $order->id }}  
**Data:** {{ $order->created_at->format('d/m/Y H:i') }}  
**Forma de pagamento:** {{ $order->paymentMethodLabel() }}  
**Total geral:** R$ {{ number_format($order->total_amount, 2, ',', '.') }}
@endcomponent

## Itens do pedido

@component('mail::table')
| Produto | Qtd | Preço unitário | Subtotal |
| :--- | :--: | ---: | ---: |
@foreach ($order->items as $item)
| {{ $item->product?->name ?? 'Produto removido' }} | {{ $item->quantity }} | R$ {{ number_format($item->price, 2, ',', '.') }} | R$ {{ number_format($item->getSubtotal(), 2, ',', '.') }} |
@endforeach
@endcomponent

## Endereço de entrega

{{ $order->address->street }}, {{ $order->address->number }}  
@if ($order->address->complement)
{{ $order->address->complement }}  
@endif
{{ $order->address->city }}/{{ $order->address->state }}  
CEP {{ preg_replace('/(\d{5})(\d{3})/', '$1-$2', preg_replace('/\D/', '', (string) $order->address->zip_code)) }}

## Pagamento

@if ($order->payment_method === 'boleto')
O boleto será disponibilizado no checkout hospedado da Pagar.me.
@elseif ($order->payment_method === 'pix')
O QR Code PIX será disponibilizado no checkout hospedado da Pagar.me.
@elseif ($order->payment_method === 'cartao')
Os dados do cartão são informados somente no checkout hospedado da Pagar.me.
@else
Seu pagamento será processado pelo checkout hospedado da Pagar.me. Lá você poderá escolher Pix, boleto ou cartão.
@endif

Obrigado por comprar conosco.  
**ShopLaravel — Materiais de Construção**
@endcomponent
