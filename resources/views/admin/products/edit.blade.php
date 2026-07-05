@extends('layouts.app')

@section('content')
    <section class="mx-auto w-full max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Editar produto</h1>

        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="mt-6 grid gap-4 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div class="md:col-span-2">
                <label for="name" class="text-sm font-semibold text-slate-700">Nome</label>
                <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="text-sm font-semibold text-slate-700">Categoria</label>
                <select id="category_id" name="category_id" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sku" class="text-sm font-semibold text-slate-700">SKU</label>
                <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('sku')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="text-sm font-semibold text-slate-700">Preco</label>
                <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('price')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stock" class="text-sm font-semibold text-slate-700">Estoque</label>
                <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                <p class="mt-1 text-xs text-slate-500">Alterações de estoque geram movimento de ajuste.</p>
                @error('stock')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="slug" class="text-sm font-semibold text-slate-700">Slug (opcional)</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $product->slug) }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('slug')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="image_url" class="text-sm font-semibold text-slate-700">URL da imagem</label>
                <input id="image_url" name="image_url" type="url" value="{{ old('image_url', $product->image_url) }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('image_url')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="images" class="text-sm font-semibold text-slate-700">Adicionar novas imagens</label>
                <input id="images" name="images[]" type="file" accept="image/*" multiple class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <p class="mt-1 text-xs text-slate-500">As novas imagens entram no final da galeria seguindo o campo `order`.</p>
                @error('images')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            @if($product->productImages->isNotEmpty())
                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-slate-700">Galeria atual</p>
                    <div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($product->productImages as $image)
                            <article class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                <img src="{{ $image->url }}" alt="{{ $image->alt_text ?: $product->name }}" class="h-28 w-full object-cover">
                                <p class="px-2 py-1 text-xs text-slate-600">Ordem: {{ $image->order }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="md:col-span-2">
                <label for="description" class="text-sm font-semibold text-slate-700">Descricao</label>
                <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="rounded border-slate-300">
                    Produto ativo
                </label>
            </div>

            <div class="md:col-span-2 flex flex-wrap gap-2">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                    Atualizar
                </button>
                <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-500">
                    Voltar
                </a>
            </div>
        </form>
    </section>

    <section class="mx-auto mt-6 w-full max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-black text-slate-900">Últimas movimentações de estoque</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-2 py-2">Tipo</th>
                        <th class="px-2 py-2 text-right">Qtd</th>
                        <th class="px-2 py-2">Motivo</th>
                        <th class="px-2 py-2">Pedido</th>
                        <th class="px-2 py-2">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->stockMovements as $movement)
                        <tr class="border-b border-slate-100">
                            <td class="px-2 py-2 text-slate-700">{{ $movement->type }}</td>
                            <td class="px-2 py-2 text-right font-bold text-slate-800">{{ $movement->quantity }}</td>
                            <td class="px-2 py-2 text-slate-600">{{ $movement->reason ?: '-' }}</td>
                            <td class="px-2 py-2">
                                @if($movement->order)
                                    <a href="{{ route('admin.pedidos.show', $movement->order) }}" class="font-semibold text-[#185FA5] hover:underline">#{{ $movement->order_id }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-2 py-2 text-slate-600">{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-2 py-4 text-sm text-slate-500">Nenhuma movimentação registrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
