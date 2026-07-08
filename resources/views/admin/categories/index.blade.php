@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase text-[#D42B2B]">Catálogo</p>
            <h1 class="text-3xl font-black text-[#1A1A1A]">Categorias</h1>
            <p class="mt-1 text-sm text-[#767676]">Organize categorias e subcategorias do catálogo.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="rounded bg-[#D42B2B] px-4 py-2 text-sm font-black text-white hover:bg-[#B02020]">
            Nova categoria
        </a>
    </div>

    <form method="GET" action="{{ route('admin.categories.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#E0E0E0] bg-white p-4 shadow-sm md:grid-cols-5">
        <input name="q" value="{{ request('q') }}" placeholder="Nome ou slug" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm md:col-span-2">
        <select name="status" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Status</option>
            <option value="active" @selected(request('status') === 'active')>Ativa</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inativa</option>
        </select>
        <select name="parent" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Hierarquia</option>
            <option value="root" @selected(request('parent') === 'root')>Categorias pai</option>
            <option value="child" @selected(request('parent') === 'child')>Subcategorias</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white">Filtrar</button>
            <a href="{{ route('admin.categories.index') }}" class="rounded border border-[#E0E0E0] px-4 py-2 text-sm font-black text-[#3D3D3A]">Limpar</a>
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-[#E0E0E0] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead class="bg-[#F3F5F8] text-xs uppercase text-[#767676]">
                    <tr>
                        <th class="px-4 py-3">Nome</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">Categoria pai</th>
                        <th class="px-4 py-3 text-right">Produtos</th>
                        <th class="px-4 py-3 text-right">Subcategorias</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-black">{{ $category->name }}</p>
                                <p class="text-xs text-[#767676]">{{ Str::limit((string) $category->description, 80) }}</p>
                            </td>
                            <td class="px-4 py-3 font-bold">{{ $category->slug }}</td>
                            <td class="px-4 py-3">{{ $category->parent?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-black">{{ $category->products_count }}</td>
                            <td class="px-4 py-3 text-right font-black">{{ $category->children_count }}</td>
                            <td class="px-4 py-3"><x-admin.status-badge :status="$category->is_active ? 'active' : 'inactive'" /></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('store.products', ['category' => $category->slug]) }}" target="_blank" rel="noopener noreferrer" class="rounded border border-[#E0E0E0] px-3 py-1.5 text-xs font-black text-[#1A3A6B]">Produtos</a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="rounded bg-[#1A3A6B] px-3 py-1.5 text-xs font-black text-white">Editar</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded border border-[#D42B2B]/30 px-3 py-1.5 text-xs font-black text-[#B02020]" @disabled($category->products_count > 0 || $category->children_count > 0)>Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8"><x-admin.empty-state title="Nenhuma categoria encontrada." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <x-admin.pagination :items="$categories" />
@endsection
