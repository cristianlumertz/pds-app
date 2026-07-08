@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-black uppercase text-[#D42B2B]">Clientes</p>
        <h1 class="text-3xl font-black text-[#1A1A1A]">Usuários</h1>
        <p class="mt-1 text-sm text-[#767676]">Consulte clientes e administradores cadastrados.</p>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#E0E0E0] bg-white p-4 shadow-sm lg:grid-cols-6">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome, email ou CPF" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm lg:col-span-2">
        <select name="status" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Status</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="role" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Perfil</option>
            <option value="admin" @selected(request('role') === 'admin')>Administradores</option>
            <option value="customer" @selected(request('role') === 'customer')>Clientes</option>
        </select>
        <select name="verified" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">E-mail verificado</option>
            <option value="yes" @selected(request('verified') === 'yes')>Sim</option>
            <option value="no" @selected(request('verified') === 'no')>Não</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white">Filtrar</button>
            <a href="{{ route('admin.users.index') }}" class="rounded border border-[#E0E0E0] px-4 py-2 text-sm font-black text-[#3D3D3A]">Limpar</a>
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-[#E0E0E0] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1240px] text-left text-sm">
                <thead class="bg-[#F3F5F8] text-xs uppercase text-[#767676]">
                    <tr>
                        <th class="px-4 py-3">Nome</th>
                        <th class="px-4 py-3">E-mail</th>
                        <th class="px-4 py-3">CPF</th>
                        <th class="px-4 py-3">Telefone</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Admin</th>
                        <th class="px-4 py-3">E-mail verificado</th>
                        <th class="px-4 py-3 text-right">Pedidos</th>
                        <th class="px-4 py-3">Cadastro</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-4 py-3 font-black">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ $user->cpf ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $user->phone ?: '-' }}</td>
                            <td class="px-4 py-3"><x-admin.status-badge :status="$user->status" :label="$statuses[$user->status] ?? ucfirst((string) $user->status)" /></td>
                            <td class="px-4 py-3">{{ $user->is_admin ? 'Sim' : 'Não' }}</td>
                            <td class="px-4 py-3">{{ $user->email_verified_at ? 'Sim' : 'Não' }}</td>
                            <td class="px-4 py-3 text-right font-black">{{ $user->orders_count }}</td>
                            <td class="px-4 py-3 text-[#767676]">{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}" class="rounded bg-[#1A3A6B] px-3 py-1.5 text-xs font-black text-white">Visualizar</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="rounded border border-[#E0E0E0] px-3 py-1.5 text-xs font-black text-[#1A3A6B]">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-4 py-8"><x-admin.empty-state title="Nenhum usuário encontrado." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <x-admin.pagination :items="$users" />
@endsection
