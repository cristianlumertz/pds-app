<section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">Filtrar Produtos</h2>
            <p class="mt-1 text-sm text-slate-600">Filtre por categoria, faixa de preço e status.</p>
        </div>

    </div>

    <div class="mt-5 grid gap-3 md:grid-cols-5">
        <div>
            <label for="filter-term" class="text-sm font-semibold text-slate-700">Nome do produto</label>
            <input
                id="filter-term"
                type="text"
                wire:model.live="term"
                placeholder="Ex.: Furadeira, Cimento..."
                class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-[#185FA5]/20 transition focus:border-[#185FA5] focus:ring-2"
            >
            @error('term')
                <p class="mt-1 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="filter-category" class="text-sm font-semibold text-slate-700">Categoria</label>
            <select
                id="filter-category"
                wire:model.live="categoryId"
                class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-[#185FA5]/20 transition focus:border-[#185FA5] focus:ring-2"
            >
                <option value="">Todas</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('categoryId')
                <p class="mt-1 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="filter-min-price" class="text-sm font-semibold text-slate-700">Preço mínimo (R$)</label>
            <input
                id="filter-min-price"
                type="number"
                min="0"
                step="0.01"
                wire:model.live="minPrice"
                class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-[#185FA5]/20 transition focus:border-[#185FA5] focus:ring-2"
            >
            @error('minPrice')
                <p class="mt-1 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="filter-max-price" class="text-sm font-semibold text-slate-700">Preço máximo (R$)</label>
            <input
                id="filter-max-price"
                type="number"
                min="0"
                step="0.01"
                wire:model.live="maxPrice"
                class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-[#185FA5]/20 transition focus:border-[#185FA5] focus:ring-2"
            >
            @error('maxPrice')
                <p class="mt-1 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="filter-status" class="text-sm font-semibold text-slate-700">Status</label>
            <select
                id="filter-status"
                wire:model.live="isActive"
                class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-[#185FA5]/20 transition focus:border-[#185FA5] focus:ring-2"
            >
                <option value="all">Todos</option>
                <option value="1">Ativos</option>
                <option value="0">Inativos</option>
            </select>
            @error('isActive')
                <p class="mt-1 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-4">
        <button
            type="button"
            wire:click="clearFilters"
            class="rounded-full border border-slate-300 px-3 py-1.5 text-xs font-bold text-slate-700 hover:border-slate-500"
        >
            Limpar filtros
        </button>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($products as $product)
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">{{ $product->category->name ?? 'Sem categoria' }}</p>
                <h3 class="mt-1 line-clamp-2 text-sm font-black text-slate-900">{{ $product->name }}</h3>
                <p class="mt-2 text-lg font-black text-[#185FA5]">R$ {{ number_format((float) $product->price, 2, ',', '.') }}</p>
                <span class="mt-2 inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-[#3B6D11]/15 text-[#3B6D11]' : 'bg-[#BA7517]/15 text-[#BA7517]' }}">
                    {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500 sm:col-span-2 lg:col-span-4">
                Nenhum produto encontrado com os filtros selecionados.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</section>
