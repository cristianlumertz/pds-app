@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-bold text-[#185FA5] hover:underline">Voltar para usuários</a>
                    <h1 class="mt-2 text-2xl font-black text-slate-900">{{ $user->name }}</h1>
                    <p class="mt-1 text-sm text-slate-600">Usuário #{{ $user->id }}</p>
                </div>
                <a href="{{ route('admin.users.edit', $user) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Editar</a>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-4">
                <article class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pedidos</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $ordersCount }}</p>
                </article>
                <article class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total gasto</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">R$ {{ number_format((float) $totalSpent, 2, ',', '.') }}</p>
                </article>
                <article class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Endereços</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $user->addresses->count() }}</p>
                </article>
                <article class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Perfil</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ $user->is_admin ? 'Admin' : 'Cliente' }}</p>
                </article>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-slate-900">Dados cadastrais</h2>
            <dl class="mt-5 grid gap-4 md:grid-cols-3">
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Nome</dt><dd class="mt-1 font-bold text-slate-800">{{ $user->name }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Email</dt><dd class="mt-1 font-bold text-slate-800">{{ $user->email }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">CPF</dt><dd class="mt-1 font-bold text-slate-800">{{ $user->cpf ?: '-' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Telefone</dt><dd class="mt-1 font-bold text-slate-800">{{ $user->phone ?: '-' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Status</dt><dd class="mt-1 font-bold text-slate-800">{{ $statuses[$user->status] ?? ucfirst((string) $user->status) }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Newsletter</dt><dd class="mt-1 font-bold text-slate-800">{{ $user->newsletter_opt_in ? 'Sim' : 'Não' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Email verificado</dt><dd class="mt-1 font-bold text-slate-800">{{ $user->email_verified_at?->format('d/m/Y H:i') ?? '-' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Criado em</dt><dd class="mt-1 font-bold text-slate-800">{{ $user->created_at?->format('d/m/Y H:i') }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Atualizado em</dt><dd class="mt-1 font-bold text-slate-800">{{ $user->updated_at?->format('d/m/Y H:i') }}</dd></div>
            </dl>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-slate-900">Pedidos recentes</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-2 py-2">Pedido</th>
                            <th class="px-2 py-2">Status</th>
                            <th class="px-2 py-2">Pagamento</th>
                            <th class="px-2 py-2 text-right">Total</th>
                            <th class="px-2 py-2">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user->orders as $order)
                            <tr class="border-b border-slate-100">
                                <td class="px-2 py-3"><a href="{{ route('admin.pedidos.show', $order) }}" class="font-bold text-[#185FA5] hover:underline">#{{ $order->id }}</a></td>
                                <td class="px-2 py-3 text-slate-700">{{ $order->status }}</td>
                                <td class="px-2 py-3 text-slate-700">{{ $order->payment_status }}</td>
                                <td class="px-2 py-3 text-right font-bold text-slate-800">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                                <td class="px-2 py-3 text-slate-600">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-2 py-4 text-sm text-slate-500">Nenhum pedido registrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-slate-900">Endereços cadastrados</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @forelse ($user->addresses as $address)
                    <article class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                        <p class="font-bold text-slate-900">{{ $address->street }}, {{ $address->number }}</p>
                        @if ($address->complement)<p>{{ $address->complement }}</p>@endif
                        <p>{{ $address->city }}/{{ $address->state }}</p>
                        <p>CEP {{ $address->zip_code }}</p>
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Nenhum endereço cadastrado.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
