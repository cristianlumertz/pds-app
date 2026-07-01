<section class="rounded-lg border border-[#E0E0E0] bg-white p-4 lg:sticky lg:top-40">
    <h2 class="text-base font-bold text-[#1A3A6B]">Resumo do pedido</h2>

    <div class="mt-5">
        <label for="coupon-code" class="text-xs font-semibold text-[#444444]">Cupom de desconto</label>
        <div class="mt-1.5 flex gap-2">
            <input
                id="coupon-code"
                type="text"
                wire:model.live="couponCode"
                placeholder="Ex.: OBRA10"
                class="min-w-0 flex-1 rounded border border-[#E0E0E0] px-3 py-2 text-sm uppercase text-[#1A1A1A] outline-none transition placeholder:text-[#767676] focus:border-[#2B5FAA] focus:ring-2 focus:ring-[#2B5FAA]/20"
            >
            <button
                type="button"
                wire:click="applyCoupon"
                class="rounded bg-[#2B5FAA] px-3 py-2 text-xs font-bold text-white transition hover:bg-[#1A3A6B]"
            >
                Aplicar
            </button>
        </div>
        @error('couponCode')
            <p class="mt-1 text-xs font-semibold text-[#D42B2B]">{{ $message }}</p>
        @enderror
        @if ($feedback !== '')
            <p class="mt-1 text-xs font-semibold text-[#198754]">{{ $feedback }}</p>
        @endif
    </div>

    <dl class="mt-6 space-y-3 text-sm">
        <div class="flex items-center justify-between gap-4">
            <dt class="text-[#767676]">Subtotal</dt>
            <dd class="font-semibold text-[#1A1A1A]">R$ {{ number_format($subtotal, 2, ',', '.') }}</dd>
        </div>
        <div class="flex items-center justify-between gap-4">
            <dt class="text-[#767676]">Frete</dt>
            <dd class="font-semibold {{ $shippingCost === 0.0 ? 'text-[#198754]' : 'text-[#1A1A1A]' }}">
                {{ $shippingCost === 0.0 ? 'Grátis' : 'R$ '.number_format($shippingCost, 2, ',', '.') }}
            </dd>
        </div>
        <div class="flex items-center justify-between gap-4">
            <dt class="text-[#767676]">Desconto</dt>
            <dd class="font-semibold text-[#198754]">- R$ {{ number_format($discountAmount, 2, ',', '.') }}</dd>
        </div>
        <div class="border-t border-[#E0E0E0] pt-4">
            <div class="flex items-end justify-between gap-4">
                <dt class="font-bold text-[#1A1A1A]">Total</dt>
                <dd class="text-[22px] font-bold leading-none text-[#D42B2B]">R$ {{ number_format($totalAmount, 2, ',', '.') }}</dd>
            </div>
        </div>
    </dl>

    <a
        href="{{ route('checkout.step1') }}"
        class="mt-6 inline-flex w-full items-center justify-center rounded bg-[#D42B2B] px-4 py-3.5 text-sm font-bold text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-[#D42B2B] focus:ring-offset-2"
    >
        Finalizar compra
    </a>

    <a
        href="{{ route('store.products') }}"
        class="mt-2 inline-flex w-full items-center justify-center rounded border border-[#E0E0E0] px-4 py-3 text-sm font-semibold text-[#444444] transition hover:bg-[#F5F5F5] focus:outline-none focus:ring-2 focus:ring-[#2B5FAA] focus:ring-offset-2"
    >
        Continuar comprando
    </a>
</section>
