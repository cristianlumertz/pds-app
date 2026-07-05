@extends('layouts.app')

@section('content')
    <section class="mx-auto w-full max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Nova categoria</h1>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="text-sm font-semibold text-slate-700">Nome</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="text-sm font-semibold text-slate-700">Slug (opcional)</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug') }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                @error('slug')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="parent_id" class="text-sm font-semibold text-slate-700">Categoria pai (opcional)</label>
                <select id="parent_id" name="parent_id" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                    <option value="">Nenhuma</option>
                    @foreach($parentCategories as $parentCategory)
                        <option value="{{ $parentCategory->id }}" @selected((string) old('parent_id') === (string) $parentCategory->id)>{{ $parentCategory->name }}</option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="text-sm font-semibold text-slate-700">Descricao</label>
                <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">{{ old('description') }}</textarea>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-slate-300">
                Categoria ativa
            </label>

            <div class="flex flex-wrap gap-2">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                    Salvar
                </button>
                <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-500">
                    Cancelar
                </a>
            </div>
        </form>
    </section>
@endsection
