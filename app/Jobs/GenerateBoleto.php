<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateBoleto implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [30, 60, 120];

    public function __construct(
        public Order $order
    ) {
    }

    public function handle(): void
    {
        $codigo = 'BOL-'.strtoupper(Str::random(12));

        Log::info("Boleto gerado para Order #{$this->order->id}: {$codigo}");

        if (Schema::hasColumn('orders', 'notes')) {
            $this->order->forceFill([
                'notes' => "Boleto gerado: {$codigo}",
            ])->save();
        }
    }
}
