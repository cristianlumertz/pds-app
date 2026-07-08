<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCouponController extends Controller
{
    public function index(Request $request): View
    {
        $coupons = Coupon::query()
            ->withCount('orders')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = trim((string) $request->query('q'));

                $query
                    ->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->query('discount_type'), fn ($query, string $type) => $query->where('discount_type', $type))
            ->when($request->query('status') === 'active', fn ($query) => $query->where('is_active', true))
            ->when($request->query('status') === 'inactive', fn ($query) => $query->where('is_active', false))
            ->latest()
            ->paginate(15);

        return view('admin.coupons.index', [
            'coupons' => $coupons,
            'discountTypes' => $this->discountTypes(),
        ]);
    }

    public function create(): View
    {
        return view('admin.coupons.create', [
            'discountTypes' => $this->discountTypes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Coupon::query()->create($data);

        return redirect()
            ->route('admin.coupons.index')
            ->with('status', 'Cupom criado com sucesso.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', [
            'coupon' => $coupon,
            'discountTypes' => $this->discountTypes(),
        ]);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $data = $this->validated($request, $coupon);

        $coupon->update($data);

        return redirect()
            ->route('admin.coupons.index')
            ->with('status', 'Cupom atualizado com sucesso.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->forceFill(['is_active' => false])->save();

        return redirect()
            ->route('admin.coupons.index')
            ->with('status', 'Cupom desativado com sucesso.');
    }

    /**
     * @return array<string, string>
     */
    private function discountTypes(): array
    {
        return [
            Coupon::TYPE_PERCENTAGE => 'Percentual',
            Coupon::TYPE_FIXED => 'Valor fixo',
            Coupon::TYPE_FREE_SHIPPING => 'Frete grátis',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Coupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($coupon?->id),
            ],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', Rule::in(array_keys($this->discountTypes()))],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'used_count' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper(trim((string) $data['code']));
        $data['is_active'] = $request->boolean('is_active');

        if ($data['discount_type'] === Coupon::TYPE_FREE_SHIPPING) {
            $data['discount_value'] = 0;
        }

        return $data;
    }
}
