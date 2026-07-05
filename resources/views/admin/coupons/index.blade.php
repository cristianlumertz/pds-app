@extends('layouts.app')

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Cupons</h1>
                <p class="mt-1 text-sm text-slate-600">Gerencie descontos usados no checkout.</p>
            </div>
            <a href="{{ route('admin.coupons.create') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Novo cupom</a>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-2 py-2">Código</th>
                        <th class="px-2 py-2">Tipo</th>
                        <th class="px-2 py-2 text-right">Valor</th>
                        <th class="px-2 py-2 text-right">Mínimo</th>
                        <th class="px-2 py-2 text-right">Uso</th>
                        <th class="px-2 py-2">Vigência</th>
                        <th class="px-2 py-2">Status</th>
                        <th class="px-2 py-2 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($coupons as $coupon)
                        <tr class="border-b border-slate-100">
                            <td class="px-2 py-3 font-black text-slate-800">{{ $coupon->code }}</td>
                            <td class="px-2 py-3 text-slate-600">{{ $coupon->discount_type }}</td>
                            <td class="px-2 py-3 text-right text-slate-700">{{ $coupon->discount_type === 'percentage' ? number_format((float) $coupon->discount_value, 2, ',', '.').'%' : 'R$ '.number_format((float) $coupon->discount_value, 2, ',', '.') }}</td>
                            <td class="px-2 py-3 text-right text-slate-700">{{ $coupon->min_order_amount ? 'R$ '.number_format((float) $coupon->min_order_amount, 2, ',', '.') : '-' }}</td>
                            <td class="px-2 py-3 text-right text-slate-700">{{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                            <td class="px-2 py-3 text-xs text-slate-600">{{ $coupon->starts_at?->format('d/m/Y') ?? '-' }} até {{ $coupon->expires_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-2 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $coupon->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $coupon->is_active ? 'Ativo' : 'Inativo' }}</span>
                            </td>
                            <td class="px-2 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700">Editar</a>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full border border-rose-300 px-3 py-1 text-xs font-semibold text-rose-700">Desativar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-2 py-4 text-sm text-slate-500">Nenhum cupom cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $coupons->links() }}</div>
    </section>
@endsection
