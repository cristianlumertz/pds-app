<div class="inline-flex items-center gap-2">
    @auth
        <a href="{{ route('cart.index') }}" class="inline-flex shrink-0 items-center gap-2 whitespace-nowrap rounded-full bg-[#1D9E75]/10 px-2.5 py-1.5 text-xs font-bold text-[#1D9E75] hover:bg-[#1D9E75]/20 sm:px-3">
            Carrinho
            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#1D9E75] px-1.5 text-[11px] text-white">
                {{ $itemCount }}
            </span>
        </a>
    @else
        <a href="{{ route('login') }}" class="inline-flex shrink-0 items-center gap-2 whitespace-nowrap rounded-full bg-[#1D9E75]/10 px-2.5 py-1.5 text-xs font-bold text-[#1D9E75] hover:bg-[#1D9E75]/20 sm:px-3">
            Carrinho
            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#1D9E75] px-1.5 text-[11px] text-white">
                0
            </span>
        </a>
    @endauth
</div>
