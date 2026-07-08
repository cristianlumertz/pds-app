@extends('layouts.admin')

@section('content')
    <section class="mx-auto w-full max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Novo produto</h1>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-4 md:grid-cols-2">
            @csrf

            <div class="md:col-span-2">
                <label for="name" class="text-sm font-semibold text-slate-700">Nome</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="text-sm font-semibold text-slate-700">Categoria</label>
                <select id="category_id" name="category_id" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                    <option value="">Selecione...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sku" class="text-sm font-semibold text-slate-700">SKU</label>
                <input id="sku" name="sku" type="text" value="{{ old('sku') }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('sku')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="text-sm font-semibold text-slate-700">Preco</label>
                <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price') }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('price')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stock" class="text-sm font-semibold text-slate-700">Estoque</label>
                <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', 0) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                <p class="mt-1 text-xs text-slate-500">A entrada inicial será registrada no histórico de estoque.</p>
                @error('stock')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="slug" class="text-sm font-semibold text-slate-700">Slug (opcional)</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug') }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('slug')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2" x-data="{ imageUrl: @js(old('image_url', '')) }">
                <label for="image_url" class="text-sm font-semibold text-slate-700">URL da imagem</label>
                <input id="image_url" name="image_url" type="url" value="{{ old('image_url') }}" x-model="imageUrl" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3" x-show="imageUrl" x-cloak>
                    <p class="mb-2 text-xs font-semibold uppercase text-slate-500">Pré-visualização</p>
                    <img x-bind:src="imageUrl" alt="Pré-visualização da imagem principal" class="h-36 w-full rounded-lg bg-white object-contain">
                </div>
                @error('image_url')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="images" class="text-sm font-semibold text-slate-700">Imagens do produto</label>
                <input id="images" name="images[]" type="file" accept="image/*" multiple class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <p class="mt-1 text-xs text-slate-500">As imagens serao salvas em ordem de selecao (campo `order`).</p>
                @error('images')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="text-sm font-semibold text-slate-700">Descricao</label>
                <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">{{ old('description') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-slate-300">
                    Produto ativo
                </label>
            </div>

            <div class="md:col-span-2 flex flex-wrap gap-2">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                    Salvar
                </button>
                <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-500">
                    Cancelar
                </a>
            </div>
        </form>
    </section>
@endsection
