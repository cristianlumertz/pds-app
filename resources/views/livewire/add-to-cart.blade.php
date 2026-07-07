@if($compact)
    <div class="mt-auto">
        <button
            type="button"
            wire:click="add"
            class="inline-flex w-full items-center justify-center rounded-none bg-[#D42B2B] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#B02020]"
        >
            {{ $buttonLabel }}
        </button>

        @error('product')
            <p class="px-3 py-2 text-xs font-semibold text-[#D42B2B]">{{ $message }}</p>
        @enderror

        @error('stock')
            <p class="px-3 py-2 text-xs font-semibold text-[#D42B2B]">{{ $message }}</p>
        @enderror

        @error('cart')
            <p class="px-3 py-2 text-xs font-semibold text-[#D42B2B]">{{ $message }}</p>
        @enderror

        @if($feedback !== '')
            <p class="px-3 py-2 text-xs font-semibold text-[#198754]">{{ $feedback }}</p>
        @endif
    </div>
@else
<section>
    <div>
        <div>
            <label for="add-to-cart-quantity" class="text-sm font-semibold text-[#444444]">Quantidade</label>
            <input
                id="add-to-cart-quantity"
                type="number"
                min="1"
                max="99"
                wire:model.live="quantity"
                class="mt-1 block w-24 rounded border border-[#E0E0E0] px-3 py-2 text-sm text-[#1A1A1A] outline-none transition focus:border-[#2B5FAA] focus:ring-2 focus:ring-[#2B5FAA]/20"
            >
            @error('quantity')
                <p class="mt-1 text-xs font-semibold text-[#D42B2B]">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="button"
            wire:click="add"
            class="mt-3 w-full rounded bg-[#D42B2B] px-4 py-3.5 text-sm font-bold text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-[#D42B2B] focus:ring-offset-2"
    >
        {{ $buttonLabel }}
    </button>
    </div>

    @error('product')
        <p class="mt-3 text-sm font-semibold text-[#D42B2B]">{{ $message }}</p>
    @enderror

    @error('stock')
        <p class="mt-3 text-sm font-semibold text-[#D42B2B]">{{ $message }}</p>
    @enderror

    @error('cart')
        <p class="mt-3 text-sm font-semibold text-[#D42B2B]">{{ $message }}</p>
    @enderror

    @if($feedback !== '')
        <p class="mt-3 text-sm font-semibold text-[#198754]">{{ $feedback }}</p>
    @endif
</section>
@endif
