@extends('layouts.app')

@section('content')
    <div class="bg-[#F1EFE8] py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-black uppercase text-[#D42B2B]">Minha conta</p>
                    <h1 class="text-3xl font-black text-[#1A1A1A]">Meu perfil</h1>
                    <p class="mt-2 text-sm text-[#767676]">Gerencie seus dados, segurança da conta, endereços e pedidos.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('orders.index') }}" class="rounded border border-[#C5D4EC] bg-white px-4 py-2 text-sm font-black text-[#1A3A6B] hover:border-[#1A3A6B]">Meus pedidos</a>
                    <a href="{{ route('addresses.index') }}" class="rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white hover:bg-[#14305A]">Meus endereços</a>
                </div>
            </div>

            <section class="mb-6 grid gap-4 md:grid-cols-3">
                <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase text-[#767676]">Pedidos</p>
                    <p class="mt-2 text-3xl font-black text-[#1A3A6B]">{{ $profileStats['orders_count'] }}</p>
                </article>
                <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase text-[#767676]">Endereços</p>
                    <p class="mt-2 text-3xl font-black text-[#1A3A6B]">{{ $profileStats['addresses_count'] }}</p>
                </article>
                <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase text-[#767676]">Total pago</p>
                    <p class="mt-2 text-2xl font-black text-[#1D9E75]">R$ {{ number_format((float) $profileStats['total_spent'], 2, ',', '.') }}</p>
                </article>
            </section>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <section class="rounded-lg border border-[#E0E0E0] bg-white p-6 shadow-sm">
                        @include('profile.partials.update-profile-information-form')
                    </section>

                    <section class="rounded-lg border border-[#E0E0E0] bg-white p-6 shadow-sm">
                        @include('profile.partials.update-password-form')
                    </section>

                    <section class="rounded-lg border border-[#E0E0E0] bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-black text-[#1A3A6B]">Pedidos recentes</h2>
                                <p class="mt-1 text-sm text-[#767676]">Acompanhe suas compras mais recentes.</p>
                            </div>
                            <a href="{{ route('orders.index') }}" class="text-sm font-black text-[#D42B2B] hover:underline">Ver todos</a>
                        </div>

                        <div class="mt-5 space-y-3">
                            @forelse ($latestOrders as $order)
                                <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between gap-4 rounded border border-[#F0F0F0] px-4 py-3 transition hover:border-[#C5D4EC]">
                                    <span>
                                        <span class="block font-black text-[#1A1A1A]">Pedido #{{ $order->id }}</span>
                                        <span class="text-xs text-[#767676]">{{ $order->created_at?->format('d/m/Y H:i') }} · {{ ucfirst((string) $order->status) }}</span>
                                    </span>
                                    <span class="font-black text-[#1A3A6B]">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</span>
                                </a>
                            @empty
                                <div class="rounded border border-dashed border-[#C5D4EC] bg-[#F8FAFC] p-6 text-sm font-semibold text-[#767676]">
                                    Você ainda não tem pedidos recentes.
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <aside class="space-y-6">
                    <section class="rounded-lg border border-[#E0E0E0] bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#1A3A6B]">Endereço padrão</h2>
                        @if ($defaultAddress)
                            <div class="mt-4 space-y-1 text-sm text-[#3D3D3A]">
                                <p class="font-black">{{ $defaultAddress->street }}, {{ $defaultAddress->number }}</p>
                                @if ($defaultAddress->complement)
                                    <p>{{ $defaultAddress->complement }}</p>
                                @endif
                                <p>{{ $defaultAddress->city }}/{{ $defaultAddress->state }}</p>
                                <p>CEP {{ preg_replace('/(\d{5})(\d{3})/', '$1-$2', preg_replace('/\D/', '', (string) $defaultAddress->zip_code)) }}</p>
                            </div>
                        @else
                            <p class="mt-4 text-sm text-[#767676]">Nenhum endereço padrão cadastrado.</p>
                        @endif
                        <a href="{{ route('addresses.index') }}" class="mt-5 inline-flex w-full justify-center rounded bg-[#1A3A6B] px-4 py-2.5 text-sm font-black text-white hover:bg-[#14305A]">
                            Gerenciar endereços
                        </a>
                    </section>

                    <section class="rounded-lg border border-[#E0E0E0] bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#1A3A6B]">Preferências</h2>
                        <div class="mt-4 rounded border border-[#F0F0F0] bg-[#F8FAFC] px-4 py-3">
                            <p class="text-sm font-black text-[#1A1A1A]">Newsletter</p>
                            <p class="mt-1 text-sm text-[#767676]">
                                {{ $user->newsletter_opt_in ? 'Você recebe ofertas e novidades por e-mail.' : 'Você não recebe ofertas por e-mail no momento.' }}
                            </p>
                        </div>
                    </section>

                    <section class="rounded-lg border border-[#D42B2B]/20 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#B02020]">Zona de segurança</h2>
                        <p class="mt-1 text-sm text-[#767676]">Exclusão de conta permanece disponível, mas separada das ações principais.</p>
                        <div class="mt-5">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
@endsection
