@props([
    'status',
    'label' => null,
])

@php
    $status = (string) $status;
    $labels = [
        'pending' => 'Pendente',
        'paid' => 'Pago',
        'processing' => 'Processando',
        'shipped' => 'Enviado',
        'delivered' => 'Entregue',
        'cancelled' => 'Cancelado',
        'canceled' => 'Cancelado',
        'failed' => 'Falhou',
        'expired' => 'Expirado',
        'refunded' => 'Reembolsado',
        'active' => 'Ativo',
        'inactive' => 'Inativo',
        'blocked' => 'Bloqueado',
        'entrada' => 'Entrada',
        'saida' => 'Saída',
        'ajuste' => 'Ajuste',
        'cancelamento' => 'Cancelamento',
        'reserva' => 'Reserva',
        'liberacao_reserva' => 'Liberação',
    ];
    $classes = match ($status) {
        'paid', 'delivered', 'active', 'entrada', 'liberacao_reserva' => 'bg-[#1D9E75]/10 text-[#16765A]',
        'pending', 'reserva' => 'bg-[#FFF3CD] text-[#856404]',
        'processing', 'shipped', 'ajuste' => 'bg-[#1A3A6B]/10 text-[#1A3A6B]',
        'failed', 'cancelled', 'canceled', 'blocked', 'saida', 'cancelamento' => 'bg-[#D42B2B]/10 text-[#B02020]',
        default => 'bg-[#F3F5F8] text-[#3D3D3A]',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2.5 py-1 text-xs font-black {$classes}"]) }}>
    {{ $label ?? ($labels[$status] ?? ucfirst($status)) }}
</span>
