@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase text-[#D42B2B]">Promoções</p>
            <h1 class="text-3xl font-black text-[#1A1A1A]">Cupons</h1>
            <p class="mt-1 text-sm text-[#767676]">Gerencie descontos, frete grátis, validade e limite de uso.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="rounded bg-[#D42B2B] px-4 py-2 text-sm font-black text-white hover:bg-[#B02020]">Novo cupom</a>
    </div>

    <form method="GET" action="{{ route('admin.coupons.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#E0E0E0] bg-white p-4 shadow-sm md:grid-cols-5">
        <input name="q" value="{{ request('q') }}" placeholder="Código ou descrição" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm md:col-span-2">
        <select name="discount_type" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Tipo</option>
            @foreach ($discountTypes as $value => $label)
                <option value="{{ $value }}" @selected(request('discount_type') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Status</option>
            <option value="active" @selected(request('status') === 'active')>Ativo</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inativo</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white">Filtrar</button>
            <a href="{{ route('admin.coupons.index') }}" class="rounded border border-[#E0E0E0] px-4 py-2 text-sm font-black text-[#3D3D3A]">Limpar</a>
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-[#E0E0E0] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] text-left text-sm">
                <thead class="bg-[#F3F5F8] text-xs uppercase text-[#767676]">
                    <tr>
                        <th class="px-4 py-3">Código</th>
                        <th class="px-4 py-3">Descrição</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3 text-right">Valor</th>
                        <th class="px-4 py-3 text-right">Pedido mínimo</th>
                        <th class="px-4 py-3">Vigência</th>
                        <th class="px-4 py-3 text-right">Uso</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @forelse ($coupons as $coupon)
                        <tr>
                            <td class="px-4 py-3 font-black text-[#1A3A6B]">{{ $coupon->code }}</td>
                            <td class="px-4 py-3">{{ $coupon->description ?: '-' }}</td>
                            <td class="px-4 py-3 font-bold">{{ $discountTypes[$coupon->discount_type] ?? $coupon->discount_type }}</td>
                            <td class="px-4 py-3 text-right font-black">
                                {{ $coupon->discount_type === 'percentage' ? number_format((float) $coupon->discount_value, 2, ',', '.').'%' : 'R$ '.number_format((float) $coupon->discount_value, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right">{{ $coupon->min_order_amount ? 'R$ '.number_format((float) $coupon->min_order_amount, 2, ',', '.') : '-' }}</td>
                            <td class="px-4 py-3 text-xs text-[#767676]">{{ $coupon->starts_at?->format('d/m/Y') ?? '-' }} até {{ $coupon->expires_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-bold">{{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                            <td class="px-4 py-3"><x-admin.status-badge :status="$coupon->is_active ? 'active' : 'inactive'" /></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="rounded bg-[#1A3A6B] px-3 py-1.5 text-xs font-black text-white">Editar</a>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded border border-[#D42B2B]/30 px-3 py-1.5 text-xs font-black text-[#B02020]">Desativar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8"><x-admin.empty-state title="Nenhum cupom encontrado." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <x-admin.pagination :items="$coupons" />
@endsection
