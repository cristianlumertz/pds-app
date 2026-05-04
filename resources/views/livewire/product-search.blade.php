<section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">Buscar Produtos</h2>
            <p class="mt-1 text-sm text-slate-600">Encontre materiais pelo nome do produto.</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-[#1D9E75]/10 px-3 py-1 text-xs font-bold text-[#1D9E75]">
            ⚡ Atualização reativa via Livewire
        </span>
    </div>

    <div class="mt-5">
        <label for="product-search-term" class="text-sm font-semibold text-slate-700">Nome do produto</label>
        <input
            id="product-search-term"
            type="text"
            wire:model.live="term"
            placeholder="Ex.: Furadeira, Cimento, Trena..."
            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-[#185FA5]/20 transition focus:border-[#185FA5] focus:ring-2"
        >
        @error('term')
            <p class="mt-1 text-xs font-semibold text-[#993C1D]">{{ $message }}</p>
        @enderror
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($products as $product)
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">{{ $product->sku }}</p>
                <h3 class="mt-1 line-clamp-2 text-sm font-black text-slate-900">{{ $product->name }}</h3>
                <p class="mt-2 text-lg font-black text-[#185FA5]">R$ {{ number_format((float) $product->price, 2, ',', '.') }}</p>
                <a href="{{ route('store.products.show', $product) }}" class="mt-3 inline-flex text-sm font-semibold text-slate-700 hover:text-slate-900">
                    Ver detalhes
                </a>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500 sm:col-span-2 lg:col-span-4">
                Nenhum produto encontrado para o termo informado.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</section>
