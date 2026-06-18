<section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">Meu carrinho</h2>
            <p class="mt-1 text-sm text-slate-600">Gerencie quantidades de forma reativa sem recarregar a página.</p>
        </div>
    </div>

    @error('cart')
        <p class="mt-4 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
    @enderror

    @if($feedback !== '')
        <p class="mt-4 text-sm font-semibold text-[#3B6D11]">{{ $feedback }}</p>
    @endif

    @if($items->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-sm text-slate-500">
            Seu carrinho está vazio no momento.
        </div>
    @else
        <div class="mt-6 space-y-4">
            @foreach($items as $item)
                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-black text-slate-900">{{ $item->product->name ?? 'Produto indisponível' }}</p>
                            <p class="mt-1 text-xs text-slate-500">SKU: {{ $item->product->sku ?? 'N/A' }}</p>
                            <p class="mt-2 text-sm font-semibold text-[#185FA5]">
                                Unitário: R$ {{ number_format((float) $item->price, 2, ',', '.') }}
                            </p>
                            <p class="mt-1 text-sm font-black text-slate-900">
                                Subtotal: R$ {{ number_format($item->getSubtotal(), 2, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex w-full flex-wrap items-end gap-2 sm:w-auto sm:flex-nowrap">
                            <button
                                type="button"
                                wire:click="decrease({{ $item->id }})"
                                class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-bold text-slate-700 hover:border-slate-500"
                            >
                                -
                            </button>

                            <div>
                                <label for="qty-{{ $item->id }}" class="sr-only">Quantidade</label>
                                <input
                                    id="qty-{{ $item->id }}"
                                    type="number"
                                    min="1"
                                    max="99"
                                    wire:model.live="quantities.{{ $item->id }}"
                                    wire:change="updateQuantity({{ $item->id }})"
                                    class="w-20 rounded-xl border border-slate-300 px-2 py-2 text-center text-sm outline-none ring-[#185FA5]/20 transition focus:border-[#185FA5] focus:ring-2"
                                >
                                @error('quantities.'.$item->id)
                                    <p class="mt-1 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="button"
                                wire:click="increase({{ $item->id }})"
                                class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-bold text-slate-700 hover:border-slate-500"
                            >
                                +
                            </button>

                            <button
                                type="button"
                                wire:click="removeItem({{ $item->id }})"
                                class="w-full rounded-xl border border-[#993C1D] px-3 py-2 text-sm font-semibold text-[#993C1D] hover:bg-[#993C1D]/10 sm:w-auto"
                            >
                                Remover
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-slate-600">Itens no carrinho: <span class="font-black text-slate-900">{{ $cart?->item_count ?? 0 }}</span></p>
            <p class="mt-1 text-lg font-black text-[#185FA5]">
                Total: R$ {{ number_format((float) ($cart?->total_price ?? 0), 2, ',', '.') }}
            </p>
        </div>
    @endif
</section>
