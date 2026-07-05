<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminUserController extends Controller
{
    /**
     * @return array<string, string>
     */
    private function statuses(): array
    {
        return [
            'active' => 'Ativo',
            'inactive' => 'Inativo',
            'blocked' => 'Bloqueado',
        ];
    }

    public function index(Request $request): View
    {
        $users = User::query()
            ->withCount(['orders', 'addresses'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = trim((string) $request->query('q'));

                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cpf', 'like', "%{$search}%");
                });
            })
            ->when($request->query('status'), fn ($query, string $status) => $query->where('status', $status))
            ->when($request->query('role') === 'admin', fn ($query) => $query->where('is_admin', true))
            ->when($request->query('role') === 'customer', fn ($query) => $query->where('is_admin', false))
            ->latest()
            ->paginate(15);

        return view('admin.users.index', [
            'users' => $users,
            'statuses' => $this->statuses(),
        ]);
    }

    public function show(User $user): View
    {
        $user->load([
            'addresses',
            'orders' => fn ($query) => $query->latest()->take(10),
        ]);

        $ordersCount = $user->orders()->count();
        $totalSpent = $user->orders()
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->sum('total_amount');

        return view('admin.users.show', [
            'user' => $user,
            'ordersCount' => $ordersCount,
            'totalSpent' => $totalSpent,
            'statuses' => $this->statuses(),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'cpf' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'cpf')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
            'is_admin' => ['nullable', 'boolean'],
            'newsletter_opt_in' => ['nullable', 'boolean'],
        ]);

        if ($request->user()?->is($user) && $data['status'] === 'blocked') {
            throw ValidationException::withMessages([
                'status' => 'Você não pode bloquear o próprio usuário administrador.',
            ]);
        }

        if ($request->user()?->is($user) && ! $request->boolean('is_admin')) {
            throw ValidationException::withMessages([
                'is_admin' => 'Você não pode remover seu próprio acesso administrativo.',
            ]);
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'],
            'is_admin' => $request->boolean('is_admin'),
            'newsletter_opt_in' => $request->boolean('newsletter_opt_in'),
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', 'Usuário atualizado com sucesso.');
    }
}
