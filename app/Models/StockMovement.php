<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    public const TYPE_ENTRY = 'entrada';

    public const TYPE_EXIT = 'saida';

    public const TYPE_ADJUSTMENT = 'ajuste';

    public const TYPE_CANCELLATION = 'cancelamento';

    public const TYPE_RESERVATION = 'reserva';

    public const TYPE_RESERVATION_RELEASE = 'liberacao_reserva';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'type',
        'quantity',
        'reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isEntry(): bool
    {
        return (string) $this->type === self::TYPE_ENTRY;
    }

    public function isExit(): bool
    {
        return (string) $this->type === self::TYPE_EXIT;
    }

    public function isAdjustment(): bool
    {
        return (string) $this->type === self::TYPE_ADJUSTMENT;
    }

    public function isCancellation(): bool
    {
        return (string) $this->type === self::TYPE_CANCELLATION;
    }

    public function isReservation(): bool
    {
        return (string) $this->type === self::TYPE_RESERVATION;
    }

    public function isReservationRelease(): bool
    {
        return (string) $this->type === self::TYPE_RESERVATION_RELEASE;
    }
}
