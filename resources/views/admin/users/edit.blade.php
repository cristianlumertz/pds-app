@extends('layouts.app')

@section('content')
    <section class="mx-auto w-full max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-bold text-[#185FA5] hover:underline">Voltar para usuário</a>
            <h1 class="mt-2 text-2xl font-black text-slate-900">Editar usuário</h1>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 grid gap-4 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div class="md:col-span-2">
                <label for="name" class="text-sm font-semibold text-slate-700">Nome</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="text-sm font-semibold text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="cpf" class="text-sm font-semibold text-slate-700">CPF</label>
                <input id="cpf" name="cpf" type="text" value="{{ old('cpf', $user->cpf) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                @error('cpf')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone" class="text-sm font-semibold text-slate-700">Telefone</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                @error('phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" class="text-sm font-semibold text-slate-700">Status</label>
                <select id="status" name="status" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $user->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2 space-y-3 rounded-2xl bg-slate-50 p-4">
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin)) class="rounded border-slate-300">
                    Usuário administrador
                </label>
                @error('is_admin')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror

                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="newsletter_opt_in" value="1" @checked(old('newsletter_opt_in', $user->newsletter_opt_in)) class="rounded border-slate-300">
                    Recebe newsletter
                </label>
                @error('newsletter_opt_in')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800">
                A senha não é alterada nesta tela.
            </div>

            <div class="md:col-span-2 flex flex-wrap gap-2">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Salvar</button>
                <a href="{{ route('admin.users.show', $user) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Cancelar</a>
            </div>
        </form>
    </section>
@endsection
