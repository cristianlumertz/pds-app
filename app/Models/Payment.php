<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_REFUNDED = 'refunded';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'payment_method',
        'status',
        'amount',
        'pagarme_payment_link_id',
        'pagarme_checkout_url',
        'pagarme_order_id',
        'pagarme_charge_id',
        'pagarme_transaction_id',
        'pix_qr_code',
        'pix_expires_at',
        'boleto_url',
        'boleto_barcode',
        'paid_at',
        'cancelled_at',
        'refunded_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'pix_expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(PaymentEvent::class);
    }

    public function paymentEvents(): HasMany
    {
        return $this->events();
    }

    public function isPending(): bool
    {
        return (string) $this->status === self::STATUS_PENDING;
    }

    public function isPaid(): bool
    {
        return (string) $this->status === self::STATUS_PAID;
    }

    public function isCancelled(): bool
    {
        return (string) $this->status === self::STATUS_CANCELLED;
    }

    public function isFailed(): bool
    {
        return (string) $this->status === self::STATUS_FAILED;
    }

    public function isExpired(): bool
    {
        return (string) $this->status === self::STATUS_EXPIRED;
    }

    public function isRefunded(): bool
    {
        return (string) $this->status === self::STATUS_REFUNDED;
    }
}
