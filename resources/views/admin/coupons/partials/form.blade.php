@php
    $coupon = $coupon ?? null;
@endphp

<div class="md:col-span-2">
    <label for="code" class="text-sm font-semibold text-slate-700">Código</label>
    <input id="code" name="code" type="text" value="{{ old('code', $coupon?->code) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm uppercase">
    @error('code')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
</div>

<div class="md:col-span-2">
    <label for="description" class="text-sm font-semibold text-slate-700">Descrição</label>
    <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">{{ old('description', $coupon?->description) }}</textarea>
</div>

<div>
    <label for="discount_type" class="text-sm font-semibold text-slate-700">Tipo</label>
    <select id="discount_type" name="discount_type" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
        @foreach ($discountTypes as $value => $label)
            <option value="{{ $value }}" @selected(old('discount_type', $coupon?->discount_type) === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>

<div>
    <label for="discount_value" class="text-sm font-semibold text-slate-700">Valor</label>
    <input id="discount_value" name="discount_value" type="number" min="0" step="0.01" value="{{ old('discount_value', $coupon?->discount_value ?? 0) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
</div>

<div>
    <label for="min_order_amount" class="text-sm font-semibold text-slate-700">Pedido mínimo</label>
    <input id="min_order_amount" name="min_order_amount" type="number" min="0" step="0.01" value="{{ old('min_order_amount', $coupon?->min_order_amount) }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
</div>

<div>
    <label for="usage_limit" class="text-sm font-semibold text-slate-700">Limite de uso</label>
    <input id="usage_limit" name="usage_limit" type="number" min="1" step="1" value="{{ old('usage_limit', $coupon?->usage_limit) }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
</div>

<div>
    <label for="used_count" class="text-sm font-semibold text-slate-700">Usos atuais</label>
    <input id="used_count" name="used_count" type="number" min="0" step="1" value="{{ old('used_count', $coupon?->used_count ?? 0) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
</div>

<div>
    <label for="starts_at" class="text-sm font-semibold text-slate-700">Início</label>
    <input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', $coupon?->starts_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
</div>

<div>
    <label for="expires_at" class="text-sm font-semibold text-slate-700">Expiração</label>
    <input id="expires_at" name="expires_at" type="datetime-local" value="{{ old('expires_at', $coupon?->expires_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
</div>

<label class="md:col-span-2 flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon?->is_active ?? true)) class="rounded border-slate-300">
    Cupom ativo
</label>

<div class="md:col-span-2 flex flex-wrap gap-2">
    <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Salvar</button>
    <a href="{{ route('admin.coupons.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Voltar</a>
</div>
