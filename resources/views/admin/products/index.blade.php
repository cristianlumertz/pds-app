@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase text-[#D42B2B]">Catálogo</p>
            <h1 class="text-3xl font-black text-[#1A1A1A]">Produtos</h1>
            <p class="mt-1 text-sm text-[#767676]">Controle de produtos, imagens, preço, status e estoque.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="rounded bg-[#D42B2B] px-4 py-2 text-sm font-black text-white hover:bg-[#B02020]">
            Novo produto
        </a>
    </div>

    <form method="GET" action="{{ route('admin.products.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#E0E0E0] bg-white p-4 shadow-sm md:grid-cols-6">
        <input name="q" value="{{ request('q') }}" placeholder="Nome ou SKU" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm md:col-span-2">
        <select name="category_id" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Categoria</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Status</option>
            <option value="active" @selected(request('status') === 'active')>Ativo</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inativo</option>
        </select>
        <select name="stock" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Estoque</option>
            <option value="low" @selected(request('stock') === 'low')>Baixo</option>
            <option value="out" @selected(request('stock') === 'out')>Zerado</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white">Filtrar</button>
            <a href="{{ route('admin.products.index') }}" class="rounded border border-[#E0E0E0] px-4 py-2 text-sm font-black text-[#3D3D3A]">Limpar</a>
        </div>
        <input name="min_price" value="{{ request('min_price') }}" placeholder="Preço mín." class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input name="max_price" value="{{ request('max_price') }}" placeholder="Preço máx." class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
    </form>

    <section class="overflow-hidden rounded-lg border border-[#E0E0E0] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] text-left text-sm">
                <thead class="bg-[#F3F5F8] text-xs uppercase text-[#767676]">
                    <tr>
                        <th class="px-4 py-3">Imagem</th>
                        <th class="px-4 py-3">Produto</th>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Categoria</th>
                        <th class="px-4 py-3 text-right">Preço</th>
                        <th class="px-4 py-3 text-right">Estoque</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Criado em</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @forelse($products as $product)
                        @php
                            $image = $product->primaryImageUrl();
                            $stockStatus = (int) $product->stock === 0 ? 'Zerado' : ((int) $product->stock <= 5 ? 'Baixo' : 'Normal');
                        @endphp
                        <tr>
                            <td class="px-4 py-3">
                                @if ($image)
                                    <img src="{{ $image }}" alt="{{ $product->name }}" class="h-12 w-12 rounded border border-[#E0E0E0] object-contain">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded bg-[#F3F5F8] text-xs font-black text-[#767676]">IMG</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-black text-[#1A1A1A]">{{ $product->name }}</p>
                                <p class="text-xs text-[#767676]">{{ Str::limit((string) $product->description, 70) }}</p>
                            </td>
                            <td class="px-4 py-3 font-bold">{{ $product->sku }}</td>
                            <td class="px-4 py-3">{{ $product->category?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-black">R$ {{ number_format((float) $product->price, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-black {{ $stockStatus === 'Zerado' ? 'text-[#B02020]' : ($stockStatus === 'Baixo' ? 'text-[#856404]' : 'text-[#16765A]') }}">{{ $product->stock }}</span>
                                <span class="ml-1 text-xs text-[#767676]">{{ $stockStatus }}</span>
                            </td>
                            <td class="px-4 py-3"><x-admin.status-badge :status="$product->is_active ? 'active' : 'inactive'" /></td>
                            <td class="px-4 py-3 text-[#767676]">{{ $product->created_at?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('store.products.show', $product->slug) }}" target="_blank" rel="noopener noreferrer" class="rounded border border-[#E0E0E0] px-3 py-1.5 text-xs font-black text-[#1A3A6B]">Ver</a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="rounded bg-[#1A3A6B] px-3 py-1.5 text-xs font-black text-white">Editar</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded border border-[#D42B2B]/30 px-3 py-1.5 text-xs font-black text-[#B02020]">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8"><x-admin.empty-state title="Nenhum produto encontrado." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <x-admin.pagination :items="$products" />
@endsection
