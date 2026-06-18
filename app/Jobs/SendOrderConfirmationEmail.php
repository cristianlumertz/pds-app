<?php

namespace App\Jobs;

use App\Mail\OrderConfirmed;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [10, 30, 60];

    public function __construct(
        public Order $order
    ) {
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        try {
            $this->order->loadMissing(['user', 'items.product', 'address']);

            Mail::to($this->order->user->email)
                ->send(new OrderConfirmed($this->order));
        } catch (Throwable $exception) {
            Log::error('Erro ao enviar e-mail de confirmação do pedido.', [
                'order_id' => $this->order->id,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
