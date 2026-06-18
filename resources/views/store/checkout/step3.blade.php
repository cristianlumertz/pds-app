@extends('layouts.app')

@section('content')
    @php
        $items = $items ?? ($cart?->items ?? collect());
        $subtotal = $cart ? (float) $cart->total_price : (float) $items->sum(fn ($item) => $item->getSubtotal());
        $shipping = $subtotal > 299 ? 0 : 29.90;
        $total = $subtotal + $shipping;
        $paymentMethod = session('checkout.payment_method');
        $paymentLabels = [
            'cartao' => ['label' => 'Cartão', 'icon' => '💳'],
            'boleto' => ['label' => 'Boleto', 'icon' => '▯'],
            'pix' => ['label' => 'PIX', 'icon' => '◆'],
        ];
        $payment = $paymentLabels[$paymentMethod] ?? ['label' => 'Pagamento', 'icon' => '•'];
        $formattedZipCode = preg_replace('/(\d{5})(\d{3})/', '$1-$2', preg_replace('/\D/', '', (string) $address->zip_code));
    @endphp

    <div class="min-h-screen bg-[#F1EFE8] py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 rounded-3xl bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex flex-1 items-center">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#1D9E75] text-sm font-bold text-white">
                            ✓
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-[#1D9E75]">Endereço</p>
                            <p class="text-xs text-[#3D3D3A]/60">Concluído</p>
                        </div>
                    </div>

                    <div class="mx-4 h-1 flex-1 rounded-full bg-[#1D9E75]"></div>

                    <div class="flex flex-1 items-center">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#1D9E75] text-sm font-bold text-white">
                            ✓
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-[#1D9E75]">Pagamento</p>
                            <p class="text-xs text-[#3D3D3A]/60">Concluído</p>
                        </div>
                    </div>

                    <div class="mx-4 h-1 flex-1 rounded-full bg-[#185FA5]"></div>

                    <div class="flex flex-1 items-center">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#185FA5] text-sm font-bold text-white">
                            3
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-[#185FA5]">Revisão</p>
                            <p class="text-xs text-[#3D3D3A]/60">Ativo</p>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-3xl border border-[#1D9E75]/20 bg-[#1D9E75]/10 px-5 py-4 text-sm font-semibold text-[#1D9E75]">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-8 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-[#3D3D3A]">Endereço de entrega</h1>
                                <div class="mt-4 space-y-1 text-sm text-[#3D3D3A]/75">
                                    <p class="font-bold text-[#3D3D3A]">{{ auth()->user()->name }}</p>
                                    <p>{{ $address->street }}, {{ $address->number }}</p>
                                    @if ($address->complement)
                                        <p>{{ $address->complement }}</p>
                                    @endif
                                    <p>{{ $address->city }}/{{ $address->state }}</p>
                                    <p>CEP {{ $formattedZipCode }}</p>
                                </div>
                            </div>

                            <a
                                href="{{ route('checkout.step1') }}"
                                class="inline-flex items-center justify-center rounded-full border border-[#185FA5] px-4 py-2 text-sm font-bold text-[#185FA5] transition hover:bg-[#185FA5] hover:text-white"
                            >
                                Alterar
                            </a>
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-[#3D3D3A]">Forma de pagamento</h2>
                                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-[#185FA5]/10 px-4 py-2 text-sm font-bold text-[#185FA5]">
                                    <span>{{ $payment['icon'] }}</span>
                                    {{ $payment['label'] }}
                                </span>
                            </div>

                            <a
                                href="{{ route('checkout.step2') }}"
                                class="inline-flex items-center justify-center rounded-full border border-[#185FA5] px-4 py-2 text-sm font-bold text-[#185FA5] transition hover:bg-[#185FA5] hover:text-white"
                            >
                                Alterar
                            </a>
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-[#3D3D3A]">Itens do pedido</h2>
                            <span class="rounded-full bg-[#F1EFE8] px-3 py-1 text-xs font-bold text-[#3D3D3A]/70">
                                {{ $items->sum('quantity') }} itens
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[680px] text-left">
                                <thead>
                                    <tr class="border-b border-[#3D3D3A]/10 text-xs uppercase tracking-wide text-[#3D3D3A]/60">
                                        <th class="pb-3 font-bold">Produto</th>
                                        <th class="pb-3 text-center font-bold">Qtd</th>
                                        <th class="pb-3 text-right font-bold">Preço</th>
                                        <th class="pb-3 text-right font-bold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#3D3D3A]/10">
                                    @foreach ($items as $item)
                                        @php
                                            $product = $item->product;
                                            $image = $product?->primaryImageUrl() ?? $product?->image_url;
                                        @endphp
                                        <tr>
                                            <td class="py-4">
                                                <div class="flex items-center gap-4">
                                                    @if ($image)
                                                        <img
                                                            src="{{ $image }}"
                                                            alt="{{ $product?->name ?? 'Produto' }}"
                                                            class="h-16 w-16 rounded object-cover"
                                                        >
                                                    @else
                                                        <div class="h-16 w-16 rounded bg-[#F1EFE8]"></div>
                                                    @endif

                                                    <div>
                                                        <p class="font-bold text-[#3D3D3A]">{{ $product?->name ?? 'Produto removido' }}</p>
                                                        @if ($product?->sku)
                                                            <p class="mt-1 text-xs text-[#3D3D3A]/60">SKU: {{ $product->sku }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 text-center text-sm font-semibold text-[#3D3D3A]">{{ $item->quantity }}</td>
                                            <td class="py-4 text-right text-sm font-semibold text-[#3D3D3A]">
                                                R$ {{ number_format((float) $item->price, 2, ',', '.') }}
                                            </td>
                                            <td class="py-4 text-right text-sm font-bold text-[#3D3D3A]">
                                                R$ {{ number_format($item->getSubtotal(), 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <div class="rounded-3xl bg-[#F1EFE8] px-6 py-4">
                                <p class="text-sm text-[#3D3D3A]/70">Total geral</p>
                                <p class="mt-1 text-3xl font-black text-[#185FA5]">R$ {{ number_format($total, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="lg:col-span-1">
                    <div class="sticky top-6 space-y-6">
                        <section class="rounded-3xl bg-white p-6 shadow-sm">
                            <h2 class="text-xl font-bold text-[#3D3D3A]">Resumo</h2>

                            <div class="mt-5 space-y-3 text-sm">
                                <div class="flex items-center justify-between text-[#3D3D3A]/75">
                                    <span>Subtotal</span>
                                    <span class="font-bold text-[#3D3D3A]">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                                </div>

                                <div class="flex items-center justify-between text-[#3D3D3A]/75">
                                    <span>Frete</span>
                                    <span class="font-bold text-[#3D3D3A]">
                                        {{ $shipping === 0 ? 'Grátis' : 'R$ '.number_format($shipping, 2, ',', '.') }}
                                    </span>
                                </div>

                                @if ($shipping === 0)
                                    <p class="rounded-full bg-[#1D9E75]/10 px-3 py-2 text-xs font-bold text-[#1D9E75]">
                                        Frete grátis aplicado para compras acima de R$ 299,00.
                                    </p>
                                @endif
                            </div>

                            <div class="mt-5 flex items-center justify-between border-t border-[#3D3D3A]/10 pt-5">
                                <span class="text-base font-bold text-[#3D3D3A]">Total</span>
                                <span class="text-2xl font-black text-[#185FA5]">R$ {{ number_format($total, 2, ',', '.') }}</span>
                            </div>

                            <form action="{{ url('/checkout/confirmar') }}" method="POST" class="mt-6">
                                @csrf

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-full bg-[#1D9E75] px-6 py-4 text-base font-bold text-white shadow-sm transition hover:bg-[#168463] focus:outline-none focus:ring-2 focus:ring-[#1D9E75] focus:ring-offset-2"
                                >
                                    Confirmar pedido
                                </button>

                                <p class="mt-3 text-center text-xs text-[#3D3D3A]/60">
                                    Ao confirmar, você concorda com os termos de compra.
                                </p>
                            </form>
                        </section>

                        <section class="rounded-3xl bg-white p-6 shadow-sm">
                            <h3 class="text-base font-bold text-[#3D3D3A]">Compra segura</h3>

                            <div class="mt-4 grid gap-3">
                                <div class="flex items-center gap-3 rounded-3xl bg-[#F1EFE8] px-4 py-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#185FA5]/10 text-sm font-bold text-[#185FA5]">SSL</span>
                                    <span class="text-sm font-semibold text-[#3D3D3A]">Conexão protegida</span>
                                </div>

                                <div class="flex items-center gap-3 rounded-3xl bg-[#F1EFE8] px-4 py-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#1D9E75]/10 text-sm font-bold text-[#1D9E75]">✓</span>
                                    <span class="text-sm font-semibold text-[#3D3D3A]">Pagamento seguro</span>
                                </div>
                            </div>
                        </section>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection
