@extends('layouts.app')

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Usuários</h1>
                <p class="mt-1 text-sm text-slate-600">Consulte clientes e administradores cadastrados.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="mt-5 grid gap-3 rounded-2xl bg-slate-50 p-4 md:grid-cols-4">
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Nome, email ou CPF"
                class="rounded-xl border border-slate-300 px-3 py-2 text-sm"
            >

            <select name="status" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <option value="">Todos os status</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="role" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <option value="">Admin e clientes</option>
                <option value="admin" @selected(request('role') === 'admin')>Administradores</option>
                <option value="customer" @selected(request('role') === 'customer')>Clientes</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Filtrar</button>
                <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Limpar</a>
            </div>
        </form>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-[1120px] w-full text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-2 py-2">ID</th>
                        <th class="px-2 py-2">Nome</th>
                        <th class="px-2 py-2">Email</th>
                        <th class="px-2 py-2">CPF</th>
                        <th class="px-2 py-2">Telefone</th>
                        <th class="px-2 py-2">Status</th>
                        <th class="px-2 py-2">Perfil</th>
                        <th class="px-2 py-2">Newsletter</th>
                        <th class="px-2 py-2">Cadastro</th>
                        <th class="px-2 py-2 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-b border-slate-100">
                            <td class="px-2 py-3 font-bold text-slate-700">#{{ $user->id }}</td>
                            <td class="px-2 py-3 font-semibold text-slate-800">{{ $user->name }}</td>
                            <td class="px-2 py-3 text-slate-600">{{ $user->email }}</td>
                            <td class="px-2 py-3 text-slate-600">{{ $user->cpf ?: '-' }}</td>
                            <td class="px-2 py-3 text-slate-600">{{ $user->phone ?: '-' }}</td>
                            <td class="px-2 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($user->status === 'blocked' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $statuses[$user->status] ?? ucfirst((string) $user->status) }}
                                </span>
                            </td>
                            <td class="px-2 py-3 text-slate-700">{{ $user->is_admin ? 'Admin' : 'Cliente' }}</td>
                            <td class="px-2 py-3 text-slate-700">{{ $user->newsletter_opt_in ? 'Sim' : 'Não' }}</td>
                            <td class="px-2 py-3 text-slate-600">{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-2 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}" class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">Visualizar</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-2 py-4 text-sm text-slate-500">Nenhum usuário encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $users->withQueryString()->links() }}
        </div>
    </section>
@endsection
