<div>
    @error('cart')
        <p class="mb-4 rounded border-l-4 border-[#D42B2B] bg-[#FAE8E8] px-4 py-3 text-sm font-semibold text-[#D42B2B]" role="alert">
            {{ $message }}
        </p>
    @enderror

    @if ($feedback !== '')
        <p class="mb-4 rounded border-l-4 border-[#198754] bg-[#198754]/10 px-4 py-3 text-sm font-semibold text-[#198754]" role="status">
            {{ $feedback }}
        </p>
    @endif

    @if ($items->isEmpty())
        <div class="rounded-lg border border-dashed border-[#E0E0E0] bg-white p-8 text-center text-sm text-[#767676]">
            Seu carrinho está vazio no momento.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($items as $item)
                @php
                    $product = $item->product;
                    $imageUrl = $product?->primaryImageUrl();
                @endphp

                <article class="rounded-lg border border-[#E0E0E0] bg-white p-3">
                    <div class="flex gap-3 sm:gap-4">
                        <a href="{{ $product ? route('store.products.show', $product) : '#' }}" class="h-20 w-20 shrink-0 overflow-hidden rounded bg-[#F5F5F5]">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover">
                            @else
                                <span class="flex h-full w-full items-center justify-center text-[#C5D4EC]">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8" aria-hidden="true">
                                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                        <circle cx="9" cy="9" r="2"></circle>
                                        <path d="m21 15-5-5L5 21"></path>
                                    </svg>
                                </span>
                            @endif
                        </a>

                        <div class="min-w-0 flex-1">
                            <h2 class="line-clamp-2 text-sm font-semibold leading-5 text-[#1A1A1A]">
                                @if ($product)
                                    <a href="{{ route('store.products.show', $product) }}" class="transition hover:text-[#2B5FAA]">
                                        {{ $product->name }}
                                    </a>
                                @else
                                    Produto indisponível
                                @endif
                            </h2>
                            <p class="mt-1 text-[11px] text-[#767676]">SKU: {{ $product->sku ?? 'N/A' }}</p>
                            <p class="mt-2 text-sm font-semibold text-[#D42B2B]">
                                R$ {{ number_format((float) $item->price, 2, ',', '.') }} <span class="text-[11px] font-normal text-[#767676]">cada</span>
                            </p>
                        </div>

                        <p class="hidden shrink-0 text-right text-sm font-bold text-[#1A1A1A] sm:block">
                            R$ {{ number_format($item->getSubtotal(), 2, ',', '.') }}
                        </p>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-[#E0E0E0] pt-3">
                        <div>
                            <div class="inline-flex overflow-hidden rounded border border-[#E0E0E0]">
                                <button
                                    type="button"
                                    wire:click="decrease({{ $item->id }})"
                                    class="flex h-9 w-9 items-center justify-center border-r border-[#E0E0E0] text-sm font-bold text-[#444444] transition hover:bg-[#F5F5F5]"
                                    aria-label="Diminuir quantidade de {{ $product->name ?? 'produto' }}"
                                >
                                    −
                                </button>

                                <label for="qty-{{ $item->id }}" class="sr-only">Quantidade</label>
                                <input
                                    id="qty-{{ $item->id }}"
                                    type="number"
                                    min="1"
                                    max="99"
                                    wire:model.live="quantities.{{ $item->id }}"
                                    wire:change="updateQuantity({{ $item->id }})"
                                    class="h-9 w-12 border-0 px-1 text-center text-sm font-semibold text-[#1A1A1A] outline-none focus:ring-0"
                                >

                                <button
                                    type="button"
                                    wire:click="increase({{ $item->id }})"
                                    class="flex h-9 w-9 items-center justify-center border-l border-[#E0E0E0] text-sm font-bold text-[#444444] transition hover:bg-[#F5F5F5]"
                                    aria-label="Aumentar quantidade de {{ $product->name ?? 'produto' }}"
                                >
                                    +
                                </button>
                            </div>

                            @error('quantities.'.$item->id)
                                <p class="mt-1 text-xs font-semibold text-[#D42B2B]">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="button"
                            wire:click="removeItem({{ $item->id }})"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#D42B2B] transition hover:text-[#B02020] hover:underline"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M3 6h18"></path>
                                <path d="M8 6V4h8v2"></path>
                                <path d="m19 6-1 14H6L5 6"></path>
                                <path d="M10 11v5"></path>
                                <path d="M14 11v5"></path>
                            </svg>
                            Remover
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
