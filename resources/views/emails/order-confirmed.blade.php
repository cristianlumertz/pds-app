@component('mail::message')
# Olá, {{ $order->user->name }}!

Seu pedido **#{{ $order->id }}** foi confirmado em **{{ $order->created_at->format('d/m/Y H:i') }}**.

@component('mail::panel')
**Número do pedido:** #{{ $order->id }}  
**Data:** {{ $order->created_at->format('d/m/Y H:i') }}  
**Forma de pagamento:** {{ ucfirst($order->payment_method) }}  
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
Seu boleto será gerado e enviado por e-mail em instantes.
@elseif ($order->payment_method === 'pix')
Acesse seu e-mail para obter o QR Code do PIX.
@elseif ($order->payment_method === 'cartao')
Seu pagamento está sendo processado.
@else
Seu pagamento está em processamento.
@endif

Obrigado por comprar conosco.  
**ShopLaravel — Materiais de Construção**
@endcomponent
