<div class="grid gap-6 lg:grid-cols-4 lg:items-start">
    <aside class="rounded-lg border border-[#E0E0E0] bg-white p-4 lg:sticky lg:top-40">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-[#1A3A6B]">Filtrar</h2>
            @if ($term !== '' || $category !== '' || $minPrice !== '' || $maxPrice !== '' || $inStock || $onSale)
                <span class="h-2 w-2 rounded-full bg-[#D42B2B]" aria-label="Há filtros ativos"></span>
            @endif
        </div>

        <div class="mt-5">
            <label for="filter-term" class="text-xs font-semibold text-[#444444]">Buscar produto</label>
            <input
                id="filter-term"
                type="search"
                wire:model="term"
                placeholder="Nome do produto"
                class="mt-1.5 w-full rounded border border-[#E0E0E0] bg-white px-3 py-2 text-sm text-[#1A1A1A] outline-none transition placeholder:text-[#767676] focus:border-[#2B5FAA] focus:ring-2 focus:ring-[#2B5FAA]/20"
            >
            @error('term')
                <p class="mt-1 text-xs font-semibold text-[#D42B2B]">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-5 border-t border-[#E0E0E0] pt-5">
            <label for="filter-category" class="text-xs font-semibold uppercase tracking-wide text-[#767676]">Categoria</label>
            <select
                id="filter-category"
                wire:model="category"
                class="mt-2 w-full rounded border border-[#E0E0E0] bg-white px-3 py-2 text-sm text-[#444444] outline-none transition focus:border-[#2B5FAA] focus:ring-2 focus:ring-[#2B5FAA]/20"
            >
                <option value="">Todas as categorias</option>
                @foreach ($categories as $categoryOption)
                    <option value="{{ $categoryOption->slug }}">{{ $categoryOption->name }}</option>
                @endforeach
            </select>
            @error('category')
                <p class="mt-1 text-xs font-semibold text-[#D42B2B]">{{ $message }}</p>
            @enderror
        </div>

        <fieldset class="mt-5 border-t border-[#E0E0E0] pt-5">
            <legend class="text-xs font-semibold uppercase tracking-wide text-[#767676]">Faixa de preço</legend>
            <div class="mt-2 grid grid-cols-2 gap-2">
                <div>
                    <label for="filter-min-price" class="sr-only">Preço mínimo</label>
                    <input
                        id="filter-min-price"
                        type="number"
                        min="0"
                        step="0.01"
                        wire:model="minPrice"
                        placeholder="Mínimo"
                        class="w-full rounded border border-[#E0E0E0] px-2.5 py-2 text-sm text-[#444444] outline-none transition placeholder:text-[#767676] focus:border-[#2B5FAA] focus:ring-2 focus:ring-[#2B5FAA]/20"
                    >
                </div>
                <div>
                    <label for="filter-max-price" class="sr-only">Preço máximo</label>
                    <input
                        id="filter-max-price"
                        type="number"
                        min="0"
                        step="0.01"
                        wire:model="maxPrice"
                        placeholder="Máximo"
                        class="w-full rounded border border-[#E0E0E0] px-2.5 py-2 text-sm text-[#444444] outline-none transition placeholder:text-[#767676] focus:border-[#2B5FAA] focus:ring-2 focus:ring-[#2B5FAA]/20"
                    >
                </div>
            </div>
            @error('minPrice')
                <p class="mt-1 text-xs font-semibold text-[#D42B2B]">{{ $message }}</p>
            @enderror
            @error('maxPrice')
                <p class="mt-1 text-xs font-semibold text-[#D42B2B]">{{ $message }}</p>
            @enderror
        </fieldset>

        <fieldset class="mt-5 border-t border-[#E0E0E0] pt-5">
            <legend class="text-xs font-semibold uppercase tracking-wide text-[#767676]">Status</legend>
            <div class="mt-3 space-y-3">
                <label class="flex cursor-pointer items-center gap-2 text-sm text-[#444444]">
                    <input
                        type="checkbox"
                        wire:model="inStock"
                        class="rounded border-[#E0E0E0] text-[#D42B2B] focus:ring-[#D42B2B]"
                    >
                    <span>Em estoque</span>
                </label>
                <label class="flex cursor-pointer items-center gap-2 text-sm text-[#444444]">
                    <input
                        type="checkbox"
                        wire:model="onSale"
                        class="rounded border-[#E0E0E0] text-[#D42B2B] focus:ring-[#D42B2B]"
                    >
                    <span>Em promoção</span>
                </label>
            </div>
        </fieldset>

        <button
            type="button"
            wire:click="applyFilters"
            class="mt-6 w-full rounded bg-[#D42B2B] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-[#D42B2B] focus:ring-offset-2"
        >
            Aplicar filtros
        </button>

        <button
            type="button"
            wire:click="clearFilters"
            class="mt-3 w-full text-center text-xs font-semibold text-[#2B5FAA] transition hover:underline"
        >
            Limpar filtros
        </button>
    </aside>

    <section class="min-w-0 lg:col-span-3">
        <div class="flex flex-col gap-3 rounded-lg border border-[#E0E0E0] bg-white p-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-[13px] text-[#767676]">
                {{ $products->total() }} produtos encontrados
            </p>

            <div class="flex items-center gap-2">
                <label for="filter-sort" class="shrink-0 text-xs font-semibold text-[#444444]">Ordenar por</label>
                <select
                    id="filter-sort"
                    wire:model.live="sort"
                    class="rounded border border-[#E0E0E0] bg-white px-3 py-2 text-sm text-[#444444] outline-none transition focus:border-[#2B5FAA] focus:ring-2 focus:ring-[#2B5FAA]/20"
                >
                    <option value="relevance">Relevância</option>
                    <option value="price_asc">Menor preço</option>
                    <option value="price_desc">Maior preço</option>
                    <option value="newest">Mais novo</option>
                </select>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($products as $product)
                @include('store.products._product-card', ['product' => $product])
            @empty
                <div class="rounded-lg border border-dashed border-[#E0E0E0] bg-white p-8 text-center text-sm text-[#767676] sm:col-span-2 lg:col-span-3">
                    Nenhum produto encontrado com os filtros selecionados.
                </div>
            @endforelse
        </div>

        @if ($products->hasPages())
            <div class="mt-6 rounded-lg border border-[#E0E0E0] bg-white px-4 py-3">
                {{ $products->links() }}
            </div>
        @endif
    </section>
</div>
