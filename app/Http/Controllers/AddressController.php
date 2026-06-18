<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $defaultAddress = $addresses->firstWhere('is_default', true);

        return view('store.checkout.step-1-address', compact('addresses', 'defaultAddress'));
    }

    public function store(AddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        DB::transaction(function () use ($user, $data): void {
            $shouldBeDefault = (bool) ($data['is_default'] ?? false);
            $hasAnyAddress = $user->addresses()->exists();

            if ($shouldBeDefault || ! $hasAnyAddress) {
                $user->addresses()->update(['is_default' => false]);
            }

            $user->addresses()->create([
                'street' => $data['street'],
                'number' => $data['number'],
                'complement' => $data['complement'] ?? '',
                'city' => $data['city'],
                'state' => $data['state'],
                'zip_code' => $data['zip_code'],
                'country' => $data['country'] ?? 'Brasil',
                'is_default' => $shouldBeDefault || ! $hasAnyAddress,
            ]);
        });

        return redirect()
            ->route('checkout.step1')
            ->with('status', 'Endereço cadastrado com sucesso.');
    }

    public function update(AddressRequest $request, Address $address): RedirectResponse
    {
        $user = $request->user();
        abort_unless($address->user_id === $user->id, 403, 'Você não tem permissão para alterar este endereço.');

        $data = $request->validated();

        DB::transaction(function () use ($user, $address, $data): void {
            $shouldBeDefault = (bool) ($data['is_default'] ?? false);

            if ($shouldBeDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            $address->update([
                'street' => $data['street'],
                'number' => $data['number'],
                'complement' => $data['complement'] ?? '',
                'city' => $data['city'],
                'state' => $data['state'],
                'zip_code' => $data['zip_code'],
                'country' => $data['country'] ?? 'Brasil',
                'is_default' => $shouldBeDefault ? 1 : $address->is_default,
            ]);
        });

        return redirect()
            ->route('checkout.step1')
            ->with('status', 'Endereço atualizado com sucesso.');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        $user = $request->user();
        abort_unless($address->user_id === $user->id, 403, 'Você não tem permissão para remover este endereço.');

        DB::transaction(function () use ($user, $address): void {
            $wasDefault = (bool) $address->is_default;
            $address->delete();

            if ($wasDefault) {
                $nextAddress = $user->addresses()->latest('id')->first();
                if ($nextAddress) {
                    $nextAddress->update(['is_default' => true]);
                }
            }
        });

        return redirect()
            ->route('checkout.step1')
            ->with('status', 'Endereço removido com sucesso.');
    }
}
