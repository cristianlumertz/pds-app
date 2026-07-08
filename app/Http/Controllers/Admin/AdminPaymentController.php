<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with(['order.user'])
            ->when($request->query('status'), fn ($query, string $status) => $query->where('status', $status))
            ->when($request->query('method'), fn ($query, string $method) => $query->where('payment_method', $method))
            ->when($request->query('order_id'), fn ($query, string $orderId) => $query->where('order_id', $orderId))
            ->when($request->query('customer'), function ($query, string $customer): void {
                $query->whereHas('order.user', function ($builder) use ($customer): void {
                    $builder
                        ->where('name', 'like', "%{$customer}%")
                        ->orWhere('email', 'like', "%{$customer}%")
                        ->orWhere('cpf', 'like', "%{$customer}%");
                });
            })
            ->when($request->date('from'), fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($request->date('to'), fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(20);

        $stats = Payment::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.payments.index', [
            'payments' => $payments,
            'stats' => $stats,
            'statuses' => $this->statuses(),
            'methods' => $this->methods(),
        ]);
    }

    public function show(Payment $payment): View
    {
        $payment->load([
            'order.user',
            'order.address',
            'events' => fn ($query) => $query->latest(),
        ]);

        return view('admin.payments.show', [
            'payment' => $payment,
            'statuses' => $this->statuses(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function statuses(): array
    {
        return [
            Payment::STATUS_PENDING => 'Pendente',
            Payment::STATUS_PAID => 'Aprovado',
            Payment::STATUS_FAILED => 'Falhou',
            Payment::STATUS_CANCELLED => 'Cancelado',
            Payment::STATUS_EXPIRED => 'Expirado',
            Payment::STATUS_REFUNDED => 'Reembolsado',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function methods(): array
    {
        return [
            Payment::METHOD_PAGARME_CHECKOUT => 'Checkout Pagar.me',
            'hosted_checkout' => 'Checkout Pagar.me',
            'pix' => 'PIX',
            'boleto' => 'Boleto',
            'cartao' => 'Cartao',
        ];
    }
}
