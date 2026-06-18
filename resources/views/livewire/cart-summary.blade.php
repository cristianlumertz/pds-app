<section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">Resumo do pedido</h2>
            <p class="mt-1 text-sm text-slate-600">Acompanhe subtotal, frete, desconto e total em tempo real.</p>
        </div>
    </div>

    <div class="mt-5">
        <label for="coupon-code" class="text-sm font-semibold text-slate-700">Cupom</label>
        <div class="mt-1 flex gap-2">
            <input
                id="coupon-code"
                type="text"
                wire:model.live="couponCode"
                placeholder="Ex.: OBRA10"
                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm uppercase outline-none ring-[#185FA5]/20 transition focus:border-[#185FA5] focus:ring-2"
            >
            <button
                type="button"
                wire:click="applyCoupon"
                class="rounded-xl bg-[#185FA5] px-4 py-2 text-sm font-bold text-white hover:bg-[#174f88]"
            >
                Aplicar
            </button>
        </div>
        @error('couponCode')
            <p class="mt-1 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
        @enderror
        @if($feedback !== '')
            <p class="mt-1 text-xs font-semibold text-[#3B6D11]">{{ $feedback }}</p>
        @endif
    </div>

    <dl class="mt-6 space-y-2 text-sm">
        <div class="flex items-center justify-between">
            <dt class="text-slate-600">Subtotal</dt>
            <dd class="font-semibold text-slate-900">R$ {{ number_format($subtotal, 2, ',', '.') }}</dd>
        </div>
        <div class="flex items-center justify-between">
            <dt class="text-slate-600">Frete</dt>
            <dd class="font-semibold text-slate-900">R$ {{ number_format($shippingCost, 2, ',', '.') }}</dd>
        </div>
        <div class="flex items-center justify-between">
            <dt class="text-slate-600">Desconto</dt>
            <dd class="font-semibold text-[#3B6D11]">- R$ {{ number_format($discountAmount, 2, ',', '.') }}</dd>
        </div>
        <div class="border-t border-slate-200 pt-3">
            <div class="flex items-center justify-between">
                <dt class="text-base font-black text-slate-900">Total</dt>
                <dd class="text-xl font-black text-[#185FA5]">R$ {{ number_format($totalAmount, 2, ',', '.') }}</dd>
            </div>
        </div>
    </dl>

    <div class="mt-5">
        <a href="{{ route('checkout.step1') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-[#185FA5] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#174f88]">
            Continuar checkout
        </a>
    </div>
</section>
