@if($compact)
    <div class="mt-3">
        <button
            type="button"
            wire:click="add"
            class="inline-flex w-full items-center justify-center rounded-xl bg-[#1D9E75] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#188a67]"
        >
            Adicionar ao carrinho
        </button>

        @error('product')
            <p class="mt-2 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
        @enderror

        @error('stock')
            <p class="mt-2 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
        @enderror

        @error('cart')
            <p class="mt-2 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
        @enderror

        @if($feedback !== '')
            <p class="mt-2 text-xs font-semibold text-[#3B6D11]">{{ $feedback }}</p>
        @endif
    </div>
@else
<section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h3 class="text-sm font-black text-slate-900">Adicionar ao carrinho</h3>
    </div>

    <div class="mt-4 flex flex-wrap items-end gap-3">
        <div>
            <label for="add-to-cart-quantity" class="text-sm font-semibold text-slate-700">Quantidade</label>
            <input
                id="add-to-cart-quantity"
                type="number"
                min="1"
                max="99"
                wire:model.live="quantity"
                class="mt-1 w-28 rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-[#185FA5]/20 transition focus:border-[#185FA5] focus:ring-2"
            >
            @error('quantity')
                <p class="mt-1 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="button"
            wire:click="add"
            class="rounded-xl bg-[#1D9E75] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#188a67]"
        >
            Adicionar ao carrinho
        </button>
    </div>

    @error('product')
        <p class="mt-3 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
    @enderror

    @error('stock')
        <p class="mt-3 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
    @enderror

    @error('cart')
        <p class="mt-3 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
    @enderror

    @if($feedback !== '')
        <p class="mt-3 text-sm font-semibold text-[#3B6D11]">{{ $feedback }}</p>
    @endif
</section>
@endif
