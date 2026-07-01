<div
    x-data="{
        badgeScaled: false,
        animationTimer: null,
        animateBadge() {
            this.badgeScaled = false;
            clearTimeout(this.animationTimer);

            this.$nextTick(() => {
                this.badgeScaled = true;
                this.animationTimer = setTimeout(() => {
                    this.badgeScaled = false;
                }, 300);
            });
        }
    }"
    x-on:cart:item-added.window="animateBadge()"
    class="shrink-0"
>
    <a
        href="{{ route('cart.index') }}"
        class="group flex flex-col items-center justify-center text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#1A3A6B]"
        aria-label="Carrinho com {{ $itemCount }} {{ $itemCount === 1 ? 'item' : 'itens' }}"
    >
        <span class="relative inline-flex h-8 w-9 items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6 transition group-hover:text-white/80" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>

            @if ($itemCount > 0)
                <span
                    x-bind:class="badgeScaled ? 'scale-125' : 'scale-100'"
                    class="absolute right-0 top-0 inline-flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-[#D42B2B] px-1 text-center text-[10px] font-bold leading-none text-white transition-transform duration-300"
                    aria-hidden="true"
                >
                    {{ $itemCount > 99 ? '99+' : $itemCount }}
                </span>
            @endif
        </span>

        <span class="hidden text-[10px] font-semibold leading-none text-white sm:block">Carrinho</span>
    </a>
</div>
